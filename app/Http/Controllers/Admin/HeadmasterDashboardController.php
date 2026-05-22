<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentPayment;
use App\Models\TeacherDetail;
use App\Models\TeacherHonor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HeadmasterDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        // KPI cards
        $kpi = [
            'students' => (int) Student::query()->where('status', 'aktif')->count(),
            'teachers' => (int) TeacherDetail::query()->where('status', 'active')->count(),
            'classes' => (int) SchoolClass::query()->count(),
            'unpaid_invoices' => (int) StudentPayment::query()->whereIn('status', ['pending', 'failed'])->count(),
        ];

        // Donut kehadiran murid bulan ini per status.
        $attendanceCounts = StudentAttendance::query()
            ->selectRaw('status, COUNT(*) AS total')
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $donutData = [
            'labels' => ['Hadir', 'Izin', 'Sakit', 'Alpa'],
            'data' => [
                (int) ($attendanceCounts['hadir'] ?? 0),
                (int) ($attendanceCounts['izin'] ?? 0),
                (int) ($attendanceCounts['sakit'] ?? 0),
                (int) ($attendanceCounts['alpa'] ?? 0),
            ],
        ];

        // Line trend honor 6 bulan terakhir
        $trendStart = $now->copy()->subMonths(5)->startOfMonth();
        $trendRows = DB::table('teacher_honors')
            ->selectRaw('year, month, SUM(amount) AS total')
            ->where(function ($q) use ($trendStart, $now) {
                $q->where('year', '>', $trendStart->year)
                    ->orWhere(function ($q2) use ($trendStart) {
                        $q2->where('year', $trendStart->year)->where('month', '>=', $trendStart->month);
                    });
            })
            ->where(function ($q) use ($now) {
                $q->where('year', '<', $now->year)
                    ->orWhere(function ($q2) use ($now) {
                        $q2->where('year', $now->year)->where('month', '<=', $now->month);
                    });
            })
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn ($r) => sprintf('%04d-%02d', $r->year, $r->month));

        $trendLabels = [];
        $trendValues = [];
        $cursor = $trendStart->copy();
        while ($cursor->lte($now)) {
            $key = $cursor->format('Y-m');
            $trendLabels[] = $cursor->translatedFormat('M Y');
            $trendValues[] = (float) ($trendRows->get($key)->total ?? 0);
            $cursor->addMonth();
        }

        return view('dashboard.headmaster.index', [
            'kpi' => $kpi,
            'donutData' => $donutData,
            'trendData' => ['labels' => $trendLabels, 'values' => $trendValues],
            'now' => $now,
        ]);
    }
}
