<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundSource;
use App\Models\FundTransaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BendaharaTransactionController extends Controller
{
    /**
     * Halaman utama: chart-only (donut per sumber + line trend 6 bulan + summary).
     */
    public function index(Request $request): View
    {
        $periode = $this->resolvePeriode($request->input('bulan'));
        $start = $periode->copy()->startOfMonth();
        $end = $periode->copy()->endOfMonth();

        $sources = FundSource::query()
            ->active()
            ->orderBy('display_order')
            ->get(['id', 'code', 'name']);

        // Donut per sumber — saldo masuk bersih periode terpilih (in − out).
        $donutData = $this->aggregateBySource($start, $end, $sources);

        // Line trend 6 bulan terakhir — masuk vs keluar per bulan.
        $trendData = $this->aggregateTrend(now()->subMonths(5)->startOfMonth(), now()->endOfMonth());

        // Summary total periode.
        $totalIn = (float) FundTransaction::query()
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->where('direction', 'in')
            ->sum('amount');
        $totalOut = (float) FundTransaction::query()
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->where('direction', 'out')
            ->sum('amount');

        return view('dashboard.admin.bendahara-transactions', [
            'periode' => $periode,
            'periodeLabel' => $periode->translatedFormat('F Y'),
            'sources' => $sources,
            'donutData' => $donutData,
            'trendData' => $trendData,
            'totalIn' => $totalIn,
            'totalOut' => $totalOut,
            'totalNet' => $totalIn - $totalOut,
        ]);
    }

    /**
     * AJAX endpoint: list transaksi 1 sumber dalam periode (untuk drill-down modal).
     */
    public function bySource(Request $request, FundSource $fundSource): JsonResponse
    {
        $periode = $this->resolvePeriode($request->input('bulan'));
        $start = $periode->copy()->startOfMonth();
        $end = $periode->copy()->endOfMonth();

        $rows = FundTransaction::query()
            ->with('creator:id,name')
            ->where('id_fund_source', $fundSource->id)
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $items = $rows->map(fn ($r) => [
            'date' => $r->transaction_date?->format('Y-m-d'),
            'direction' => $r->direction,
            'amount' => (float) $r->amount,
            'description' => $r->description,
            'creator' => $r->creator?->name,
        ]);

        return response()->json([
            'source' => $fundSource->only(['id', 'code', 'name']),
            'periode' => $periode->translatedFormat('F Y'),
            'count' => $rows->count(),
            'items' => $items,
        ]);
    }

    /**
     * Export CSV transaksi periode terpilih.
     */
    public function export(Request $request): Response
    {
        $periode = $this->resolvePeriode($request->input('bulan'));
        $start = $periode->copy()->startOfMonth();
        $end = $periode->copy()->endOfMonth();

        $rows = FundTransaction::query()
            ->with(['fundSource:id,name', 'creator:id,name'])
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('transaction_date')
            ->get();

        $filename = 'riwayat-dana-' . $periode->format('Y-m') . '.csv';

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Sumber', 'Arah', 'Nominal', 'Keterangan', 'Dicatat oleh']);
            foreach ($rows as $r) {
                fputcsv($file, [
                    $r->transaction_date?->format('Y-m-d') ?? '-',
                    $r->fundSource?->name ?? '-',
                    $r->direction === 'in' ? 'Masuk' : 'Keluar',
                    (float) $r->amount,
                    $r->description ?? '-',
                    $r->creator?->name ?? 'Sistem',
                ]);
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

    /**
     * @return array{labels: string[], datasets: array{in: float[], out: float[], net: float[]}}
     */
    private function aggregateBySource(Carbon $start, Carbon $end, $sources): array
    {
        $rows = DB::table('fund_transactions')
            ->selectRaw('id_fund_source,
                COALESCE(SUM(CASE WHEN direction = ? THEN amount ELSE 0 END), 0) AS total_in,
                COALESCE(SUM(CASE WHEN direction = ? THEN amount ELSE 0 END), 0) AS total_out', ['in', 'out'])
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('id_fund_source')
            ->get()
            ->keyBy('id_fund_source');

        $labels = [];
        $in = [];
        $out = [];
        $net = [];

        foreach ($sources as $s) {
            $row = $rows->get($s->id);
            $labels[] = $s->name;
            $in[] = (float) ($row->total_in ?? 0);
            $out[] = (float) ($row->total_out ?? 0);
            $net[] = (float) (($row->total_in ?? 0) - ($row->total_out ?? 0));
        }

        return [
            'labels' => $labels,
            'sourceIds' => $sources->pluck('id')->all(),
            'in' => $in,
            'out' => $out,
            'net' => $net,
        ];
    }

    /**
     * @return array{labels: string[], in: float[], out: float[]}
     */
    private function aggregateTrend(Carbon $start, Carbon $end): array
    {
        $rows = DB::table('fund_transactions')
            ->selectRaw("DATE_FORMAT(transaction_date, '%Y-%m') AS bulan,
                COALESCE(SUM(CASE WHEN direction = ? THEN amount ELSE 0 END), 0) AS total_in,
                COALESCE(SUM(CASE WHEN direction = ? THEN amount ELSE 0 END), 0) AS total_out", ['in', 'out'])
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $labels = [];
        $in = [];
        $out = [];

        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $key = $cursor->format('Y-m');
            $row = $rows->get($key);
            $labels[] = $cursor->translatedFormat('M Y');
            $in[] = (float) ($row->total_in ?? 0);
            $out[] = (float) ($row->total_out ?? 0);
            $cursor->addMonth();
        }

        return ['labels' => $labels, 'in' => $in, 'out' => $out];
    }
}
