<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundSource;
use App\Models\FundTransaction;
use App\Models\TeacherHonor;
use App\Services\FundBalanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BendaharaHonorController extends Controller
{
    public function index(Request $request, FundBalanceService $balanceService): View
    {
        // Tab: daftar (semua honor) atau bayar (hanya unpaid).
        $tab = $request->input('tab', 'bayar');
        if (!in_array($tab, ['daftar', 'bayar'], true)) {
            $tab = 'bayar';
        }

        $query = TeacherHonor::query()->with(['teacher.user']);
        if ($tab === 'bayar') {
            $query->whereNull('payment_date');
        }

        $search = (string) $request->input('search', '');
        if ($search !== '') {
            $query->whereHas('teacher', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $bulan = $request->input('bulan'); // format YYYY-MM
        if ($bulan && preg_match('/^(\d{4})-(\d{2})$/', $bulan, $m)) {
            $query->where('year', (int) $m[1])->where('month', (int) $m[2]);
        }

        $perPage = (int) $request->input('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100], true)) {
            $perPage = 15;
        }

        $honors = $query
            ->orderBy('payment_date', 'asc')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->appends($request->query());

        $tabCounts = [
            'bayar' => (int) TeacherHonor::query()->whereNull('payment_date')->count(),
            'daftar' => (int) TeacherHonor::query()->count(),
        ];

        return view('dashboard.admin.bendahara-honors', [
            'honors' => $honors,
            'tab' => $tab,
            'search' => $search,
            'bulan' => $bulan ?? '',
            'per_page' => $perPage,
            'totalBalance' => $balanceService->totalBalance(),
            'tabCounts' => $tabCounts,
        ]);
    }

    public function show(TeacherHonor $teacherHonor, FundBalanceService $balanceService): View
    {
        $teacherHonor->loadMissing(['teacher.user', 'allowances']);

        $sources = FundSource::query()
            ->active()
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        $balances = $balanceService->balancesAll();

        // Komponen gaji yang ditampilkan ke bendahara (override-able).
        $components = $this->resolveComponents($teacherHonor);

        return view('dashboard.admin.bendahara-honor-detail', [
            'honor' => $teacherHonor,
            'teacher' => $teacherHonor->teacher,
            'components' => $components,
            'sources' => $sources,
            'balances' => $balances,
            'isLocked' => !is_null($teacherHonor->payment_date),
        ]);
    }

    /**
     * Override nilai komponen gaji (Pokok / Tunjangan / Potongan).
     * Setelah honor di-pay (payment_date != null), endpoint ini menolak update.
     */
    public function updateComponent(Request $request, TeacherHonor $teacherHonor): RedirectResponse
    {
        if (!is_null($teacherHonor->payment_date)) {
            throw ValidationException::withMessages([
                'lock' => ['Honor sudah dibayar dan tidak dapat diubah.'],
            ]);
        }

        $validated = $request->validate([
            'component' => ['required', 'in:pokok,tunjangan,potongan'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $amount = (float) $validated['amount'];

        match ($validated['component']) {
            'pokok' => $teacherHonor->update([
                'rate_snapshot' => $this->splitToRate($amount, (int) $teacherHonor->effective_attendance_count),
            ]),
            'tunjangan' => $teacherHonor->update(['allowance_total' => $amount]),
            'potongan' => $teacherHonor->update([
                // Total potongan disimpan di late_penalty (sebagai bucket override).
                // permission_penalty dikosongkan agar tidak double count.
                'late_penalty' => $amount,
                'permission_penalty' => 0,
            ]),
        };

        // Hitung ulang amount snapshot.
        $teacherHonor->refresh();
        $effective = (int) ($teacherHonor->effective_attendance_count ?? 0);
        $newAmount = ($effective * (float) $teacherHonor->rate_snapshot)
            + (float) $teacherHonor->allowance_total
            - (float) $teacherHonor->late_penalty
            - (float) $teacherHonor->permission_penalty
            + (float) ($teacherHonor->manual_adjustment ?? 0);
        $teacherHonor->update(['amount' => max(0, $newAmount)]);

        return redirect()
            ->route('admin.bendahara.honors.show', $teacherHonor)
            ->with('success', 'Komponen gaji diperbarui.');
    }

    /**
     * Bayar honor: split nominal ke 1+ fund_sources (manual pilih oleh bendahara).
     * - Warning jika saldo source tidak cukup, tapi tetap allow (sesuai Q3).
     * - Total split harus sama dengan teacher_honor.amount.
     * - Setelah pay: tulis fund_transactions(out) per split + set payment_date (LOCK).
     */
    public function pay(Request $request, TeacherHonor $teacherHonor): RedirectResponse
    {
        if (!is_null($teacherHonor->payment_date)) {
            throw ValidationException::withMessages([
                'lock' => ['Honor sudah dibayar sebelumnya.'],
            ]);
        }

        $validated = $request->validate([
            'splits' => ['required', 'array', 'min:1'],
            'splits.*.id_fund_source' => ['required', 'integer', 'exists:fund_sources,id'],
            'splits.*.amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
        ]);

        $totalSplit = array_sum(array_map(fn ($s) => (float) ($s['amount'] ?? 0), $validated['splits']));
        $honorAmount = (float) ($teacherHonor->amount ?? 0);

        if (abs($totalSplit - $honorAmount) > 0.01) {
            throw ValidationException::withMessages([
                'splits' => ["Total alokasi (Rp " . number_format($totalSplit, 0, ',', '.') . ") tidak sama dengan nominal honor (Rp " . number_format($honorAmount, 0, ',', '.') . ")."],
            ]);
        }

        $teacherName = $teacherHonor->teacher?->name ?? 'guru';
        $periodLabel = sprintf('%02d/%d', (int) $teacherHonor->month, (int) $teacherHonor->year);

        DB::transaction(function () use ($teacherHonor, $validated, $teacherName, $periodLabel) {
            foreach ($validated['splits'] as $split) {
                FundTransaction::create([
                    'id_fund_source' => (int) $split['id_fund_source'],
                    'direction' => 'out',
                    'amount' => (float) $split['amount'],
                    'transaction_date' => $validated['payment_date'],
                    'description' => "Pembayaran honor {$teacherName} periode {$periodLabel}",
                    'attachment_path' => null,
                    'reference_type' => 'teacher_honor',
                    'reference_id' => (int) $teacherHonor->id_honors,
                    'created_by' => auth()->id(),
                ]);
            }

            $teacherHonor->update(['payment_date' => $validated['payment_date']]);
        });

        return redirect()
            ->route('admin.bendahara.honors.index')
            ->with('success', "Honor {$teacherName} (Rp " . number_format($honorAmount, 0, ',', '.') . ") berhasil dibayar dan saldo dipotong.");
    }

    /**
     * Mapping snapshot teacher_honors → 3 komponen (Pokok / Tunjangan / Potongan)
     * sesuai bentuk UI Bendahara.
     */
    private function resolveComponents(TeacherHonor $honor): array
    {
        $effective = (int) ($honor->effective_attendance_count ?? 0);
        $rate = (float) ($honor->rate_snapshot ?? 0);
        $pokok = $effective * $rate;
        $tunjangan = (float) ($honor->allowance_total ?? 0);
        $potongan = (float) ($honor->late_penalty ?? 0) + (float) ($honor->permission_penalty ?? 0);

        return [
            'pokok' => [
                'label' => 'Gaji Pokok',
                'amount' => $pokok,
                'detail' => "{$effective} hari × Rp " . number_format($rate, 0, ',', '.'),
            ],
            'tunjangan' => [
                'label' => 'Tunjangan',
                'amount' => $tunjangan,
                'detail' => 'Tunjangan jabatan',
            ],
            'potongan' => [
                'label' => 'Potongan',
                'amount' => $potongan,
                'detail' => 'Telat + izin >2 hari berturut',
            ],
        ];
    }

    /**
     * Saat bendahara override "Gaji Pokok" jadi nominal X, simpan ke rate_snapshot
     * dengan rate = X / effective_attendance_count (kalau effective > 0).
     * Kalau effective 0, simpan amount langsung ke allowance_total (workaround).
     */
    private function splitToRate(float $amount, int $effectiveAttendance): float
    {
        if ($effectiveAttendance > 0) {
            return round($amount / $effectiveAttendance, 2);
        }
        return $amount; // edge case
    }
}
