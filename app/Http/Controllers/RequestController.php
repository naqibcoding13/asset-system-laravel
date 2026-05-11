<?php

namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\RequestItem;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RequestController extends Controller
{
    private const OTHER_DETAIL_VALUE = '__other_detail__';

    public function create()
    {
        return view('requests.form', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        DB::transaction(function () use ($request, $validated) {
            $assetRequest = AssetRequest::create([
                'tahun' => $validated['tahun'],
                'jabatan' => $validated['jabatan'],
                'unit' => $validated['unit'],
                'user_id' => $request->user()->id,
            ]);

            foreach ($validated['resolved_items'] as $index => $itemData) {
                $quotationPath = null;
                if ($request->hasFile("quotation.$index")) {
                    $quotationPath = $request->file("quotation.$index")->store('quotations', 'public');
                }

                $assetRequest->items()->create([
                    'jenis_aset' => $itemData['jenis_aset'],
                    'perincian_aset' => $itemData['perincian_aset'],
                    'kuantiti' => $validated['kuantiti'][$index],
                    'harga_seunit' => $validated['harga'][$index],
                    'jumlah' => $validated['kuantiti'][$index] * $validated['harga'][$index],
                    'justifikasi' => $validated['justifikasi'][$index] ?? null,
                    'quotation' => $quotationPath,
                    'status' => 'Pending',
                ]);
            }

            SystemNotification::create([
                'user_id' => $request->user()->id,
                'message' => 'Permohonan anda telah berjaya dihantar.',
                'status' => 'unread',
            ]);

            foreach (User::query()->where('role', 'admin')->pluck('id') as $adminId) {
                SystemNotification::create([
                    'user_id' => $adminId,
                    'message' => 'Permohonan baru telah dihantar oleh staff.',
                    'status' => 'unread',
                ]);
            }
        });

        return redirect()->route('staff.requests.index')->with('success', 'Permohonan berjaya dihantar.');
    }

    public function index(Request $request)
    {
        $filters = $this->validateListingFilters($request);
        $requests = $this->filteredRequests($request, $filters);

        return view('requests.index', [
            'requests' => $requests,
            'filters' => $filters,
            'years' => config('asset_system.application_years'),
            'units' => config('asset_system.units'),
            'statusOptions' => RequestItem::statusOptions(),
        ]);
    }

    public function print(Request $request)
    {
        $filters = $this->validateListingFilters($request);
        $requests = $this->filteredRequests($request, $filters);

        return view('requests.print', [
            'printGroups' => $this->buildPrintGroups($requests),
            'orientation' => $filters['orientation'] ?? 'landscape',
            'assetCategories' => config('asset_system.asset_categories'),
            'assetMainCategory' => config('asset_system.asset_main_category'),
            'departmentName' => config('asset_system.department'),
            'departmentCode' => '0006',
            'ptjCode' => '02060000',
        ]);
    }

    public function edit(AssetRequest $assetRequest)
    {
        abort_unless((int) $assetRequest->user_id === (int) auth()->id(), 403);
        abort_if($assetRequest->items()->where('status', '!=', 'Pending')->exists(), 403);

        $assetRequest->load('items');

        return view('requests.edit', array_merge($this->formData(), [
            'assetRequest' => $assetRequest,
        ]));
    }

    public function update(Request $request, AssetRequest $assetRequest)
    {
        abort_unless((int) $assetRequest->user_id === (int) auth()->id(), 403);
        abort_if($assetRequest->items()->where('status', '!=', 'Pending')->exists(), 403);

        $validated = $this->validateRequest($request, true);

        DB::transaction(function () use ($request, $validated, $assetRequest) {
            $assetRequest->update([
                'tahun' => $validated['tahun'],
                'jabatan' => $validated['jabatan'],
                'unit' => $validated['unit'],
            ]);

            $oldPaths = $assetRequest->items()->pluck('quotation')->filter()->all();
            $keptPaths = [];

            $assetRequest->items()->delete();

            foreach ($validated['resolved_items'] as $index => $itemData) {
                $quotationPath = $validated['existing_quotation'][$index] ?? null;
                if ($request->hasFile("quotation.$index")) {
                    $quotationPath = $request->file("quotation.$index")->store('quotations', 'public');
                }

                if ($quotationPath) {
                    $keptPaths[] = $quotationPath;
                }

                $assetRequest->items()->create([
                    'jenis_aset' => $itemData['jenis_aset'],
                    'perincian_aset' => $itemData['perincian_aset'],
                    'kuantiti' => $validated['kuantiti'][$index],
                    'harga_seunit' => $validated['harga'][$index],
                    'jumlah' => $validated['kuantiti'][$index] * $validated['harga'][$index],
                    'justifikasi' => $validated['justifikasi'][$index] ?? null,
                    'quotation' => $quotationPath,
                    'status' => 'Pending',
                ]);
            }

            foreach (array_diff($oldPaths, $keptPaths) as $obsoletePath) {
                Storage::disk('public')->delete($obsoletePath);
            }

            SystemNotification::create([
                'user_id' => $request->user()->id,
                'message' => 'Permohonan anda telah berjaya dikemaskini.',
                'status' => 'unread',
            ]);
        });

        return redirect()->route('staff.requests.index')->with('success', 'Permohonan berjaya dikemaskini.');
    }

    private function validateRequest(Request $request, bool $isUpdate = false): array
    {
        $years = config('asset_system.application_years');
        $assetCategories = config('asset_system.asset_categories');
        $allowedAssetCategories = array_keys($assetCategories);
        $allowedDetailValues = collect($assetCategories)
            ->pluck('details')
            ->flatten()
            ->push(self::OTHER_DETAIL_VALUE)
            ->unique()
            ->values()
            ->all();

        $validator = Validator::make($request->all(), [
            'jabatan' => ['required', 'string', Rule::in([config('asset_system.department')])],
            'unit' => ['required', 'string', Rule::in(config('asset_system.units'))],
            'tahun' => ['required', 'string', Rule::in($years)],
            'jenis_aset' => ['required', 'array', 'min:1'],
            'jenis_aset.*' => ['required', 'string', Rule::in($allowedAssetCategories)],
            'perincian_aset' => ['required', 'array', 'min:1'],
            'perincian_aset.*' => ['required', 'string', Rule::in($allowedDetailValues)],
            'custom_perincian_aset' => ['nullable', 'array'],
            'custom_perincian_aset.*' => ['nullable', 'string', 'max:255'],
            'kuantiti' => ['required', 'array', 'min:1'],
            'kuantiti.*' => ['required', 'integer', 'min:1'],
            'harga' => ['required', 'array', 'min:1'],
            'harga.*' => ['required', 'numeric', 'min:0'],
            'justifikasi' => ['nullable', 'array'],
            'justifikasi.*' => ['nullable', 'string'],
            'quotation' => ['nullable', 'array'],
            'quotation.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'existing_quotation' => [$isUpdate ? 'nullable' : 'sometimes', 'array'],
            'existing_quotation.*' => ['nullable', 'string'],
        ]);

        $validator->after(function ($validator) use ($request, $assetCategories) {
            foreach ($request->input('jenis_aset', []) as $index => $assetType) {
                $allowedDetails = data_get($assetCategories, "{$assetType}.details", []);
                $selectedDetail = (string) $request->input("perincian_aset.$index", '');

                if (! in_array($selectedDetail, array_merge($allowedDetails, [self::OTHER_DETAIL_VALUE]), true)) {
                    $validator->errors()->add("perincian_aset.$index", 'Sila pilih perincian aset yang sah.');
                    continue;
                }

                if ($selectedDetail !== self::OTHER_DETAIL_VALUE) {
                    continue;
                }

                $customDetail = trim((string) $request->input("custom_perincian_aset.$index", ''));
                if ($customDetail === '') {
                    $validator->errors()->add("custom_perincian_aset.$index", 'Sila masukkan perincian aset lain.');
                }
            }
        });

        $validated = $validator->validate();
        $validated['resolved_items'] = collect($validated['jenis_aset'])->map(
            function ($assetType, $index) use ($request, $assetCategories) {
                $selectedDetail = $request->input("perincian_aset.$index");
                $detail = $selectedDetail !== self::OTHER_DETAIL_VALUE
                    ? $selectedDetail
                    : trim((string) $request->input("custom_perincian_aset.$index", ''));

                return [
                    'jenis_aset' => $assetType,
                    'jenis_aset_label' => $this->formatAssetCategoryLabel(
                        $assetType,
                        data_get($assetCategories, "{$assetType}.label", $assetType)
                    ),
                    'perincian_aset' => $detail,
                ];
            }
        )->all();

        return $validated;
    }

    private function formData(): array
    {
        $assetCategories = config('asset_system.asset_categories');

        return [
            'department' => config('asset_system.department'),
            'units' => config('asset_system.units'),
            'assetMainCategory' => config('asset_system.asset_main_category'),
            'assetCategories' => $assetCategories,
            'otherDetailValue' => self::OTHER_DETAIL_VALUE,
            'years' => config('asset_system.application_years'),
        ];
    }

    private function formatAssetCategoryLabel(string $code, string $label): string
    {
        return trim($code . ' - ' . $label);
    }

    private function validateListingFilters(Request $request): array
    {
        return $request->validate([
            'tahun' => ['nullable', 'string', Rule::in(config('asset_system.application_years'))],
            'bahagian' => ['nullable', 'string', Rule::in(config('asset_system.units'))],
            'status' => ['nullable', 'string', Rule::in(array_keys(RequestItem::statusOptions()))],
            'orientation' => ['nullable', 'string', Rule::in(['portrait', 'landscape'])],
        ]);
    }

    private function filteredRequests(Request $request, array $filters)
    {
        $status = $filters['status'] ?? null;

        return $request->user()
            ->requests()
            ->withCount([
                'items as non_pending_items_count' => fn ($query) => $query->where('status', '!=', RequestItem::STATUS_PENDING),
            ])
            ->with([
                'items' => fn ($query) => $query->when(
                    $status,
                    fn ($itemQuery, $selectedStatus) => $itemQuery->where('status', $selectedStatus)
                ),
                'user',
            ])
            ->when($filters['tahun'] ?? null, fn ($query, $tahun) => $query->where('tahun', $tahun))
            ->when($filters['bahagian'] ?? null, fn ($query, $bahagian) => $query->where('unit', $bahagian))
            ->when($status, fn ($query, $selectedStatus) => $query->whereHas('items', fn ($itemQuery) => $itemQuery->where('status', $selectedStatus)))
            ->latest()
            ->get()
            ->filter(fn ($assetRequest) => $assetRequest->items->isNotEmpty())
            ->values();
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
