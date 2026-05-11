<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherAttendance;
use App\Models\TeacherAttendanceRate;
use App\Models\TeacherDetail;
use App\Models\TeacherHonor;
use App\Models\TeacherPosition;
use App\Models\PositionAllowance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeacherHonorManagementController extends Controller
{
    public function attendanceSummary(Request $request)
    {
        $validated = $request->validate([
            'id_teacher' => ['required', 'integer', 'exists:teacher_details,id_teacher'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
        ]);

        $periodStart = Carbon::parse($validated['period_start'])->startOfDay();
        $periodEnd = Carbon::parse($validated['period_end'])->endOfDay();

        $summary = $this->buildHonorSummary((int) $validated['id_teacher'], $periodStart, $periodEnd);

        $data = array_merge($summary['counts'], [
            'total' => (int) $summary['total'],
            'rate' => (float) $summary['rate'],
            'allowance_total' => (float) $summary['allowance_total'],
            'allowances' => $summary['allowances'],
            'estimated_amount' => (float) $summary['estimated_amount'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Display a listing of teacher honors with search and filters.
     */
    public function index(Request $request)
    {
        $query = TeacherHonor::query()->with(['teacher.user']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id_honors', 'like', "%{$search}%")
                    ->orWhere('month', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhereHas('teacher', function ($qt) use ($search) {
                        $qt->where('name', 'like', "%{$search}%")
                            ->orWhere('phone_num', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = $request->input('status');
            if ($status === 'paid') {
                $query->whereNotNull('payment_date');
            }
            if ($status === 'unpaid') {
                $query->whereNull('payment_date');
            }
        }

        if ($request->filled('month') && $request->input('month') !== 'all') {
            $query->where('month', (int)$request->input('month'));
        }

        if ($request->filled('year') && $request->input('year') !== 'all') {
            $query->where('year', (int)$request->input('year'));
        }

        $query->orderByDesc('year')->orderByDesc('month')->orderByDesc('created_at');

        $perPage = $request->input('per_page', 10);
        $honors = $query->paginate($perPage)->appends($request->query());

        $years = TeacherHonor::query()
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('dashboard.admin.teacher-honors', [
            'honors' => $honors,
            'years' => $years,
            'search' => $request->input('search', ''),
            'status' => $request->input('status', 'all'),
            'month' => $request->input('month', 'all'),
            'year' => $request->input('year', 'all'),
            'per_page' => $perPage,
        ]);
    }

    public function create()
    {
        $teachers = TeacherDetail::query()
            ->with(['user'])
            ->orderBy('name', 'asc')
            ->get(['id_teacher', 'id_user', 'name', 'status', 'phone_num', 'email']);

        return response()->json(['view' => view('components.dashboard.admin.modal-form', [
            'type' => 'teacher-honor',
            'action' => 'create',
            'teacherHonor' => null,
            'teachers' => $teachers,
        ])->render()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_teacher' => ['required', 'integer', 'exists:teacher_details,id_teacher'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'manual_adjustment' => ['nullable', 'numeric'],
            'payment_date' => ['nullable', 'date'],
        ]);

        $periodStart = Carbon::parse($validated['period_start'])->startOfDay();
        $periodEnd = Carbon::parse($validated['period_end'])->endOfDay();

        $summary = $this->buildHonorSummary((int) $validated['id_teacher'], $periodStart, $periodEnd);
        $manualAdjustment = (float) ($validated['manual_adjustment'] ?? 0);

        $attendanceCount = (int) ($summary['counts']['hadir'] ?? 0);
        $amount = ($attendanceCount * (float) $summary['rate']) + (float) $summary['allowance_total'] + $manualAdjustment;

        $teacherHonor = TeacherHonor::create([
            'id_teacher' => (int) $validated['id_teacher'],
            'month' => (int) $periodStart->month,
            'year' => (int) $periodStart->year,
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'attendance_count' => (int) ($summary['counts']['hadir'] ?? 0),
            'permission_count' => (int) ($summary['counts']['izin'] ?? 0),
            'sickness_count' => (int) ($summary['counts']['sakit'] ?? 0),
            'absence_count' => (int) ($summary['counts']['alpa'] ?? 0),
            'rate_snapshot' => (float) $summary['rate'],
            'allowance_total' => (float) $summary['allowance_total'],
            'manual_adjustment' => $manualAdjustment,
            'amount' => max(0, $amount),
            'payment_date' => $validated['payment_date'] ?? null,
        ]);

        if (!empty($summary['allowances'])) {
            $teacherHonor->allowances()->createMany($summary['allowances']);
        }

        return response()->json(['success' => true, 'message' => 'Honor guru berhasil ditambahkan']);
    }

    public function edit(TeacherHonor $teacherHonor)
    {
        $teacherHonor->loadMissing(['teacher.user']);

        $teachers = TeacherDetail::query()
            ->with(['user'])
            ->orderBy('name', 'asc')
            ->get(['id_teacher', 'id_user', 'name', 'status', 'phone_num', 'email']);

        return response()->json(['view' => view('components.dashboard.admin.modal-form', [
            'type' => 'teacher-honor',
            'action' => 'edit',
            'teacherHonor' => $teacherHonor,
            'teachers' => $teachers,
        ])->render()]);
    }

    public function show(TeacherHonor $teacherHonor)
    {
        $teacherHonor->loadMissing(['teacher.user']);

        return response()->json(['view' => view('components.dashboard.admin.modal-detail', [
            'type' => 'teacher-honor',
            'teacherHonor' => $teacherHonor,
            'teacher' => $teacherHonor->teacher,
        ])->render()]);
    }

    public function update(Request $request, TeacherHonor $teacherHonor)
    {
        $validated = $request->validate([
            'id_teacher' => ['required', 'integer', 'exists:teacher_details,id_teacher'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'manual_adjustment' => ['nullable', 'numeric'],
            'payment_date' => ['nullable', 'date'],
        ]);

        $periodStart = Carbon::parse($validated['period_start'])->startOfDay();
        $periodEnd = Carbon::parse($validated['period_end'])->endOfDay();

        $summary = $this->buildHonorSummary((int) $validated['id_teacher'], $periodStart, $periodEnd);
        $manualAdjustment = (float) ($validated['manual_adjustment'] ?? ($teacherHonor->manual_adjustment ?? 0));

        $attendanceCount = (int) ($summary['counts']['hadir'] ?? 0);
        $amount = ($attendanceCount * (float) $summary['rate']) + (float) $summary['allowance_total'] + $manualAdjustment;

        $teacherHonor->update([
            'id_teacher' => (int) $validated['id_teacher'],
            'month' => (int) $periodStart->month,
            'year' => (int) $periodStart->year,
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'attendance_count' => (int) ($summary['counts']['hadir'] ?? 0),
            'permission_count' => (int) ($summary['counts']['izin'] ?? 0),
            'sickness_count' => (int) ($summary['counts']['sakit'] ?? 0),
            'absence_count' => (int) ($summary['counts']['alpa'] ?? 0),
            'rate_snapshot' => (float) $summary['rate'],
            'allowance_total' => (float) $summary['allowance_total'],
            'manual_adjustment' => $manualAdjustment,
            'amount' => max(0, $amount),
            'payment_date' => $validated['payment_date'] ?? null,
        ]);

        $teacherHonor->allowances()->delete();
        if (!empty($summary['allowances'])) {
            $teacherHonor->allowances()->createMany($summary['allowances']);
        }

        return response()->json(['success' => true, 'message' => 'Honor guru berhasil diperbarui']);
    }

    public function export(Request $request)
    {
        $query = TeacherHonor::query()->with(['teacher.user']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id_honors', 'like', "%{$search}%")
                    ->orWhere('month', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhereHas('teacher', function ($qt) use ($search) {
                        $qt->where('name', 'like', "%{$search}%")
                            ->orWhere('phone_num', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = $request->input('status');
            if ($status === 'paid') {
                $query->whereNotNull('payment_date');
            }
            if ($status === 'unpaid') {
                $query->whereNull('payment_date');
            }
        }

        if ($request->filled('month') && $request->input('month') !== 'all') {
            $query->where('month', (int)$request->input('month'));
        }

        if ($request->filled('year') && $request->input('year') !== 'all') {
            $query->where('year', (int)$request->input('year'));
        }

        $honors = $query->orderByDesc('year')->orderByDesc('month')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="teacher_honors_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function () use ($honors) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Guru',
                'Periode Mulai',
                'Periode Akhir',
                'Hadir',
                'Izin',
                'Sakit',
                'Alpa',
                'Rate/Hadir',
                'Total Tunjangan',
                'Penyesuaian',
                'Nominal',
                'Payment Date',
                'Dibuat Tanggal',
            ]);

            foreach ($honors as $h) {
                $teacherName = $h->teacher?->name ?? '-';

                fputcsv($file, [
                    $h->id_honors,
                    $teacherName,
                    $h->period_start?->format('Y-m-d') ?? '-',
                    $h->period_end?->format('Y-m-d') ?? '-',
                    (int)($h->attendance_count ?? 0),
                    (int)($h->permission_count ?? 0),
                    (int)($h->sickness_count ?? 0),
                    (int)($h->absence_count ?? 0),
                    (float)($h->rate_snapshot ?? 0),
                    (float)($h->allowance_total ?? 0),
                    (float)($h->manual_adjustment ?? 0),
                    (string)($h->amount ?? 0),
                    $h->payment_date?->format('Y-m-d') ?? '-',
                    $h->created_at?->format('Y-m-d H:i:s') ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildHonorSummary(int $teacherId, Carbon $periodStart, Carbon $periodEnd): array
    {
        $rows = TeacherAttendance::query()
            ->selectRaw('status, COUNT(*) as total')
            ->where('id_teacher', $teacherId)
            ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'hadir' => (int) ($rows['hadir'] ?? 0),
            'izin' => (int) ($rows['izin'] ?? 0),
            'sakit' => (int) ($rows['sakit'] ?? 0),
            'alpa' => (int) ($rows['alpa'] ?? 0),
        ];

        $rate = TeacherAttendanceRate::query()
            ->where('id_teacher', $teacherId)
            ->whereDate('effective_from', '<=', $periodEnd)
            ->where(function ($q) use ($periodStart) {
                $q->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $periodStart);
            })
            ->orderByDesc('effective_from')
            ->first();

        $rateValue = (float) ($rate?->amount_per_attendance ?? 0);

        $positions = TeacherPosition::query()
            ->with(['position'])
            ->where('id_teacher', $teacherId)
            ->whereDate('effective_from', '<=', $periodEnd)
            ->where(function ($q) use ($periodStart) {
                $q->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $periodStart);
            })
            ->get();

        $positionIds = $positions->pluck('id_position')->filter()->values();
        $allowances = collect();

        if ($positionIds->isNotEmpty()) {
            $allowances = PositionAllowance::query()
                ->with(['allowanceType', 'position'])
                ->whereIn('id_position', $positionIds)
                ->whereDate('effective_from', '<=', $periodEnd)
                ->where(function ($q) use ($periodStart) {
                    $q->whereNull('effective_to')
                        ->orWhereDate('effective_to', '>=', $periodStart);
                })
                ->get();
        }

        $allowanceSnapshots = $allowances->map(function ($row) {
            $label = trim((string) ($row->allowanceType?->name ?? 'Tunjangan'));
            if ($label === '') {
                $label = 'Tunjangan';
            }

            return [
                'allowance_label' => $label,
                'amount' => (float) ($row->amount ?? 0),
                'source_position' => (string) ($row->position?->name ?? ''),
            ];
        })->values();

        $allowanceTotal = (float) $allowanceSnapshots->sum('amount');
        $attendanceCount = (int) ($counts['hadir'] ?? 0);
        $estimated = ($attendanceCount * $rateValue) + $allowanceTotal;

        return [
            'counts' => $counts,
            'total' => array_sum($counts),
            'rate' => $rateValue,
            'allowance_total' => $allowanceTotal,
            'allowances' => $allowanceSnapshots->all(),
            'estimated_amount' => $estimated,
        ];
    }
}
