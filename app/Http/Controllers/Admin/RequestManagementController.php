<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetRequest;
use App\Models\RequestItem;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class RequestManagementController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'tahun' => ['nullable', 'string', Rule::in(config('asset_system.application_years'))],
            'bahagian' => ['nullable', 'string', Rule::in(config('asset_system.units'))],
            'jenis_aset' => ['nullable', 'string'],
            'status' => ['nullable', 'string', Rule::in(array_keys(RequestItem::statusOptions()))],
        ]);

        $requests = AssetRequest::query()
            ->active()
            ->with(['user', 'items'])
            ->when($filters['tahun'] ?? null, fn ($query, $tahun) => $query->where('tahun', $tahun))
            ->when($filters['bahagian'] ?? null, fn ($query, $bahagian) => $query->where('unit', $bahagian))
            ->when($filters['jenis_aset'] ?? null, function ($query, $jenisAset) {
                $query->whereHas('items', function ($itemQuery) use ($jenisAset) {
                    $itemQuery->where('jenis_aset', 'like', '%' . $jenisAset . '%')
                        ->orWhere('perincian_aset', 'like', '%' . $jenisAset . '%');
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->whereHas('items', fn ($itemQuery) => $itemQuery->where('status', $status)))
            ->latest()
            ->get();

        return view('admin.requests.index', [
            'requests' => $requests,
            'filters' => $filters,
            'units' => config('asset_system.units'),
            'years' => config('asset_system.application_years'),
            'statusOptions' => RequestItem::statusOptions(),
            'isArchive' => false,
        ]);
    }

    public function archiveIndex(Request $request)
    {
        $filters = $request->validate([
            'tahun' => ['nullable', 'string', Rule::in(config('asset_system.application_years'))],
            'bahagian' => ['nullable', 'string', Rule::in(config('asset_system.units'))],
            'jenis_aset' => ['nullable', 'string'],
            'status' => ['nullable', 'string', Rule::in(array_keys(RequestItem::statusOptions()))],
        ]);

        $requests = AssetRequest::query()
            ->archived()
            ->with(['user', 'items'])
            ->when($filters['tahun'] ?? null, fn ($query, $tahun) => $query->where('tahun', $tahun))
            ->when($filters['bahagian'] ?? null, fn ($query, $bahagian) => $query->where('unit', $bahagian))
            ->when($filters['jenis_aset'] ?? null, function ($query, $jenisAset) {
                $query->whereHas('items', function ($itemQuery) use ($jenisAset) {
                    $itemQuery->where('jenis_aset', 'like', '%' . $jenisAset . '%')
                        ->orWhere('perincian_aset', 'like', '%' . $jenisAset . '%');
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->whereHas('items', fn ($itemQuery) => $itemQuery->where('status', $status)))
            ->latest('archived_at')
            ->get();

        return view('admin.requests.index', [
            'requests' => $requests,
            'filters' => $filters,
            'units' => config('asset_system.units'),
            'years' => config('asset_system.application_years'),
            'statusOptions' => RequestItem::statusOptions(),
            'isArchive' => true,
        ]);
    }

    public function show(AssetRequest $assetRequest)
    {
        $assetRequest->load(['user', 'items']);

        return view('admin.requests.show', compact('assetRequest'));
    }

    public function destroy(AssetRequest $assetRequest)
    {
        $assetRequest->delete();

        return redirect()->route('admin.requests.index')->with('success', 'Permohonan berjaya dipadam.');
    }

    public function archive(AssetRequest $assetRequest)
    {
        $assetRequest->update([
            'archived_at' => now(),
        ]);

        return redirect()->route('admin.requests.index')->with('success', 'Permohonan berjaya diarkibkan.');
    }

    public function restore(AssetRequest $assetRequest)
    {
        $assetRequest->update([
            'archived_at' => null,
        ]);

        return redirect()->route('admin.archive.index')->with('success', 'Permohonan berjaya dipulihkan.');
    }

    public function print(Request $request)
    {
        $filters = $request->validate([
            'tahun' => ['nullable', 'string', Rule::in(config('asset_system.application_years'))],
            'bahagian' => ['nullable', 'string', Rule::in(config('asset_system.units'))],
            'status' => ['nullable', 'string', Rule::in(array_keys(RequestItem::statusOptions()))],
            'orientation' => ['nullable', 'string', Rule::in(['portrait', 'landscape'])],
        ]);

        $status = $filters['status'] ?? null;

        $requests = AssetRequest::query()
            ->active()
            ->with([
                'user',
                'items' => fn ($query) => $query->when(
                    $status,
                    fn ($itemQuery, $selectedStatus) => $itemQuery->where('status', $selectedStatus)
                ),
            ])
            ->when($filters['tahun'] ?? null, fn ($query, $tahun) => $query->where('tahun', $tahun))
            ->when($filters['bahagian'] ?? null, fn ($query, $bahagian) => $query->where('unit', $bahagian))
            ->when($status, fn ($query, $selectedStatus) => $query->whereHas('items', fn ($itemQuery) => $itemQuery->where('status', $selectedStatus)))
            ->latest()
            ->get()
            ->filter(fn ($assetRequest) => $assetRequest->items->isNotEmpty())
            ->values();

        return view('admin.requests.print', [
            'printGroups' => $this->buildPrintGroups($requests),
            'orientation' => $filters['orientation'] ?? 'landscape',
            'assetCategories' => config('asset_system.asset_categories'),
            'assetMainCategory' => config('asset_system.asset_main_category'),
            'departmentName' => config('asset_system.department'),
            'departmentCode' => '0006',
            'ptjCode' => '02060000',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->validate([
            'tahun' => ['nullable', 'string', Rule::in(config('asset_system.application_years'))],
            'bahagian' => ['nullable', 'string', Rule::in(config('asset_system.units'))],
            'status' => ['nullable', 'string', Rule::in(array_keys(RequestItem::statusOptions()))],
            'orientation' => ['nullable', 'string', Rule::in(['portrait', 'landscape'])],
        ]);

        $status = $filters['status'] ?? null;

        $requests = AssetRequest::query()
            ->active()
            ->with([
                'user',
                'items' => fn ($query) => $query->when(
                    $status,
                    fn ($itemQuery, $selectedStatus) => $itemQuery->where('status', $selectedStatus)
                ),
            ])
            ->when($filters['tahun'] ?? null, fn ($query, $tahun) => $query->where('tahun', $tahun))
            ->when($filters['bahagian'] ?? null, fn ($query, $bahagian) => $query->where('unit', $bahagian))
            ->when($status, fn ($query, $selectedStatus) => $query->whereHas('items', fn ($itemQuery) => $itemQuery->where('status', $selectedStatus)))
            ->latest()
            ->get()
            ->filter(fn ($assetRequest) => $assetRequest->items->isNotEmpty())
            ->values();

        $content = view('admin.requests.excel', [
            'printGroups' => $this->buildPrintGroups($requests),
            'assetMainCategory' => config('asset_system.asset_main_category'),
            'departmentName' => config('asset_system.department'),
            'departmentCode' => '0006',
            'ptjCode' => '02060000',
        ])->render();

        $fileName = 'laporan-permohonan-aset-' . now()->format('Ymd-His') . '.xls';

        return response("\xEF\xBB\xBF" . $content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function updateStatus(Request $request, RequestItem $requestItem)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Pending,Approved,Rejected'],
        ]);

        $requestItem->update([
            'status' => $validated['status'],
        ]);

        SystemNotification::create([
            'user_id' => $requestItem->request->user_id,
            'message' => 'Status permohonan item ' . $requestItem->displayName() . ' telah dikemaskini kepada ' . (RequestItem::statusOptions()[$validated['status']] ?? $validated['status']) . '.',
            'status' => 'unread',
        ]);

        return back()->with('success', 'Status item berjaya dikemaskini.');
    }

    private function buildPrintGroups(Collection $requests): Collection
    {
        return $requests
            ->groupBy(fn ($assetRequest) => $assetRequest->unit ?: 'Tanpa Bahagian')
            ->map(function (Collection $unitRequests, string $unit) {
                $items = $unitRequests
                    ->flatMap(function ($assetRequest) {
                        return $assetRequest->items->map(function ($item) use ($assetRequest) {
                            return [
                                'year' => $assetRequest->tahun,
                                'code' => $item->jenis_aset,
                                'category' => data_get(config('asset_system.asset_categories'), "{$item->jenis_aset}.label", $item->jenis_aset),
                                'detail' => $item->perincian_aset ?: '-',
                                'quantity' => $item->kuantiti,
                                'price' => $item->harga_seunit,
                                'total' => $item->jumlah,
                                'review' => $item->justifikasi ?: '-',
                            ];
                        });
                    })
                    ->values();

                return [
                    'unit' => $unit,
                    'years' => $unitRequests->pluck('tahun')->filter()->unique()->sort()->values()->all(),
                    'items' => $items,
                    'total' => $items->sum('total'),
                ];
            })
            ->values();
    }
}