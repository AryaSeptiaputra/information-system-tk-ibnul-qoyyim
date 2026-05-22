<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundTransaction;
use App\Models\TeacherAttendance;
use App\Models\TeacherDetail;
use App\Models\TeacherHonor;
use App\Services\FundBalanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class HeadmasterReportController extends Controller
{
    public function index(Request $request, FundBalanceService $balanceService): View
    {
        $periode = $this->resolvePeriode($request->input('bulan'));
        $start = $periode->copy()->startOfMonth();
        $end = $periode->copy()->endOfMonth();

        $summary = $this->buildSummary($start, $end, $balanceService);
        $attendanceTable = $this->buildAttendanceTable($start, $end);

        return view('dashboard.headmaster.reports', [
            'periode' => $periode,
            'periodeLabel' => $periode->translatedFormat('F Y'),
            'summary' => $summary,
            'attendanceTable' => $attendanceTable,
        ]);
    }

    public function export(Request $request, FundBalanceService $balanceService): Response
    {
        $periode = $this->resolvePeriode($request->input('bulan'));
        $start = $periode->copy()->startOfMonth();
        $end = $periode->copy()->endOfMonth();

        $summary = $this->buildSummary($start, $end, $balanceService);
        $attendanceTable = $this->buildAttendanceTable($start, $end);

        $filename = 'laporan-bulanan-' . $periode->format('Y-m') . '.csv';

        $callback = function () use ($periode, $summary, $attendanceTable) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Laporan Bulanan: ' . $periode->translatedFormat('F Y')]);
            fputcsv($file, []);

            fputcsv($file, ['== Ringkasan Keuangan ==']);
            fputcsv($file, ['Dana Masuk', $summary['fund_in']]);
            fputcsv($file, ['Dana Keluar', $summary['fund_out']]);
            fputcsv($file, ['Net', $summary['fund_net']]);
            fputcsv($file, ['Saldo Akhir', $summary['fund_balance']]);
            fputcsv($file, []);

            fputcsv($file, ['== Ringkasan Honor Guru ==']);
            fputcsv($file, ['Total Honor Periode', $summary['honor_total']]);
            fputcsv($file, ['Sudah Dibayar', $summary['honor_paid'], $summary['honor_paid_count'] . ' guru']);
            fputcsv($file, ['Belum Dibayar', $summary['honor_unpaid'], $summary['honor_unpaid_count'] . ' guru']);
            fputcsv($file, []);

            fputcsv($file, ['== Kehadiran Guru ==']);
            fputcsv($file, ['Guru', 'Hadir', 'Izin', 'Sakit', 'Alpa', 'Telat']);
            foreach ($attendanceTable as $row) {
                fputcsv($file, [$row['name'], $row['hadir'], $row['izin'], $row['sakit'], $row['alpa'], $row['late']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function resolvePeriode(?string $bulan): Carbon
    {
        if ($bulan && preg_match('/^(\d{4})-(\d{2})$/', $bulan, $m)) {
            return Carbon::createFromDate((int) $m[1], (int) $m[2], 1);
        }
        return now()->startOfMonth();
    }

    private function buildSummary(Carbon $start, Carbon $end, FundBalanceService $balanceService): array
    {
        $fundIn = (float) FundTransaction::query()
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->where('direction', 'in')
            ->sum('amount');
        $fundOut = (float) FundTransaction::query()
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->where('direction', 'out')
            ->sum('amount');

        $honors = TeacherHonor::query()
            ->where('month', $start->month)
            ->where('year', $start->year)
            ->get();

        $honorTotal = (float) $honors->sum('amount');
        $honorPaid = (float) $honors->whereNotNull('payment_date')->sum('amount');
        $honorUnpaid = (float) $honors->whereNull('payment_date')->sum('amount');
        $honorPaidCount = $honors->whereNotNull('payment_date')->count();
        $honorUnpaidCount = $honors->whereNull('payment_date')->count();

        return [
            'fund_in' => $fundIn,
            'fund_out' => $fundOut,
            'fund_net' => $fundIn - $fundOut,
            'fund_balance' => $balanceService->totalBalance(),
            'honor_total' => $honorTotal,
            'honor_paid' => $honorPaid,
            'honor_unpaid' => $honorUnpaid,
            'honor_paid_count' => $honorPaidCount,
            'honor_unpaid_count' => $honorUnpaidCount,
        ];
    }

    private function buildAttendanceTable(Carbon $start, Carbon $end): array
    {
        $teachers = TeacherDetail::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id_teacher', 'name']);

        $rows = TeacherAttendance::query()
            ->selectRaw('id_teacher, status, COUNT(*) AS total, SUM(CASE WHEN is_late = 1 THEN 1 ELSE 0 END) AS late_total')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('id_teacher', 'status')
            ->get()
            ->groupBy('id_teacher');

        return $teachers->map(function ($t) use ($rows) {
            $byStatus = $rows->get($t->id_teacher) ?? collect();
            $find = fn (string $s) => (int) ($byStatus->firstWhere('status', $s)?->total ?? 0);
            $late = (int) $byStatus->sum('late_total');

            return [
                'id_teacher' => (int) $t->id_teacher,
                'name' => $t->name,
                'hadir' => $find('hadir'),
                'izin' => $find('izin'),
                'sakit' => $find('sakit'),
                'alpa' => $find('alpa'),
                'late' => $late,
            ];
        })->all();
    }
}
