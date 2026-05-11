<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetRequest;
use App\Models\RequestItem;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private const LEGACY_ASSET_KEY = '__legacy__';

    public function index()
    {
        $assetCategories = config('asset_system.asset_categories');
        $assetCodes = array_keys($assetCategories);
        $applicationYears = collect(config('asset_system.application_years'))
            ->map(fn ($year) => (string) $year)
            ->values();

        $summaries = AssetRequest::query()
            ->leftJoin('request_items', 'requests.id', '=', 'request_items.request_id')
            ->select(
                'requests.tahun',
                'requests.unit',
                DB::raw('COUNT(DISTINCT requests.id) as total_requests'),
                DB::raw('COALESCE(SUM(request_items.jumlah), 0) as total_budget')
            )
            ->groupBy('requests.tahun', 'requests.unit')
            ->orderByDesc('requests.tahun')
            ->orderBy('requests.unit')
            ->get();

        $yearData = AssetRequest::query()
            ->select('tahun', DB::raw('COUNT(*) as total'))
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->pluck('total', 'tahun');

        $yearLabels = $applicationYears->all();
        $yearTotals = $applicationYears
            ->map(fn ($year) => (int) ($yearData[$year] ?? 0))
            ->all();
        $totalRequestsAllYears = array_sum($yearTotals);
        $yearPercentages = collect($yearTotals)
            ->map(fn ($total) => $totalRequestsAllYears > 0 ? round(($total / $totalRequestsAllYears) * 100, 1) : 0)
            ->all();

        $assetCounts = array_fill_keys($assetCodes, 0);
        $assetCounts[self::LEGACY_ASSET_KEY] = 0;
        RequestItem::query()
            ->select('jenis_aset', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_aset')
            ->get()
            ->each(function ($row) use (&$assetCounts) {
                if (array_key_exists($row->jenis_aset, $assetCounts)) {
                    $assetCounts[$row->jenis_aset] = (int) $row->total;
                } else {
                    $assetCounts[self::LEGACY_ASSET_KEY] += (int) $row->total;
                }
            });

        $totalAssets = array_sum($assetCounts);
        $assetChartData = $this->buildChartSeries($assetCounts, $assetCategories, $totalAssets);

        $rejectCounts = array_fill_keys($assetCodes, 0);
        $rejectCounts[self::LEGACY_ASSET_KEY] = 0;
        RequestItem::query()
            ->select('jenis_aset', DB::raw('COUNT(*) as total'))
            ->where('status', 'Rejected')
            ->groupBy('jenis_aset')
            ->get()
            ->each(function ($row) use (&$rejectCounts) {
                if (array_key_exists($row->jenis_aset, $rejectCounts)) {
                    $rejectCounts[$row->jenis_aset] = (int) $row->total;
                } else {
                    $rejectCounts[self::LEGACY_ASSET_KEY] += (int) $row->total;
                }
            });

        $statusByYearRaw = RequestItem::query()
            ->join('requests', 'requests.id', '=', 'request_items.request_id')
            ->select(
                'requests.tahun',
                'request_items.status',
                DB::raw('COUNT(request_items.id) as total')
            )
            ->whereIn('request_items.status', ['Approved', 'Rejected'])
            ->groupBy('requests.tahun', 'request_items.status')
            ->get()
            ->groupBy('tahun');

        $approvedByYear = [];
        $rejectedByYear = [];
        $approvedPercentages = [];
        $rejectedPercentages = [];

        foreach ($yearLabels as $year) {
            $rows = collect($statusByYearRaw->get($year, []))->keyBy('status');
            $approvedTotal = (int) optional($rows->get('Approved'))->total;
            $rejectedTotal = (int) optional($rows->get('Rejected'))->total;
            $yearlyStatusTotal = $approvedTotal + $rejectedTotal;

            $approvedByYear[] = $approvedTotal;
            $rejectedByYear[] = $rejectedTotal;
            $approvedPercentages[] = $yearlyStatusTotal > 0 ? round(($approvedTotal / $yearlyStatusTotal) * 100, 1) : 0;
            $rejectedPercentages[] = $yearlyStatusTotal > 0 ? round(($rejectedTotal / $yearlyStatusTotal) * 100, 1) : 0;
        }

        $rejectChartData = $this->buildChartSeries($rejectCounts, $assetCategories, array_sum($rejectCounts));

        return view('admin.reports.index', [
            'summaries' => $summaries,
            'yearLabels' => $yearLabels,
            'yearTotals' => $yearTotals,
            'yearPercentages' => $yearPercentages,
            'assetLabels' => $assetChartData['labels'],
            'assetCodes' => $assetChartData['codes'],
            'assetTotals' => $assetChartData['totals'],
            'assetPercentages' => $assetChartData['percentages'],
            'rejectLabels' => $rejectChartData['labels'],
            'rejectCodes' => $rejectChartData['codes'],
            'rejectTotals' => $rejectChartData['totals'],
            'rejectPercentages' => $rejectChartData['percentages'],
            'approvedByYear' => $approvedByYear,
            'rejectedByYear' => $rejectedByYear,
            'approvedPercentages' => $approvedPercentages,
            'rejectedPercentages' => $rejectedPercentages,
        ]);
    }

    private function assetLabel(string $code, array $assetCategories): string
    {
        if ($code === self::LEGACY_ASSET_KEY) {
            return 'Rekod lama / Tidak dipetakan';
        }

        return trim($code . ' - ' . data_get($assetCategories, "{$code}.label", $code));
    }

    private function buildChartSeries(array $counts, array $assetCategories, int $seriesTotal): array
    {
        $filteredCounts = collect($counts)
            ->filter(fn ($total) => $total > 0)
            ->all();

        return [
            'labels' => collect(array_keys($filteredCounts))
                ->map(fn ($code) => $this->assetLabel($code, $assetCategories))
                ->values()
                ->all(),
            'codes' => collect(array_keys($filteredCounts))
                ->map(fn ($code) => $code === self::LEGACY_ASSET_KEY ? 'LAIN' : $code)
                ->values()
                ->all(),
            'totals' => array_values($filteredCounts),
            'percentages' => collect($filteredCounts)
                ->map(fn ($total) => $seriesTotal > 0 ? round(($total / $seriesTotal) * 100, 1) : 0)
                ->values()
                ->all(),
        ];
    }
}
