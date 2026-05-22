<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundSource;
use App\Models\FundTransaction;
use App\Models\TeacherHonor;
use App\Services\FundBalanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BendaharaDashboardController extends Controller
{
    public function index(Request $request, FundBalanceService $balanceService): View
    {
        $sources = FundSource::query()
            ->active()
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        $balances = $balanceService->balancesAll();

        // Honor outstanding (belum dibayar) — preview 5 teratas.
        $honorsOutstanding = TeacherHonor::query()
            ->with('teacher')
            ->whereNull('payment_date')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('amount')
            ->limit(5)
            ->get();

        $honorsOutstandingTotal = (float) TeacherHonor::query()
            ->whereNull('payment_date')
            ->sum('amount');

        $honorsOutstandingCount = (int) TeacherHonor::query()
            ->whereNull('payment_date')
            ->count();

        // 5 transaksi terbaru (campur in/out).
        $recentTransactions = FundTransaction::query()
            ->with(['fundSource', 'creator'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('dashboard.admin.bendahara-dashboard', [
            'sources' => $sources,
            'balances' => $balances,
            'totalBalance' => array_sum($balances),
            'honorsOutstanding' => $honorsOutstanding,
            'honorsOutstandingTotal' => $honorsOutstandingTotal,
            'honorsOutstandingCount' => $honorsOutstandingCount,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
