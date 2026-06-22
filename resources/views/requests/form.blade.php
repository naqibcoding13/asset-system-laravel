@extends('layouts.app')

@php
    $isEdit = isset($assetRequest);
    $categoryOptions = collect($assetCategories);
    $categoryCodes = $categoryOptions->keys()->map(fn ($code) => (string) $code);
    $resolveAssetCode = function ($value) use ($categoryCodes) {
        $rawValue = trim((string) $value);

        if ($categoryCodes->contains($rawValue)) {
            return $rawValue;
        }

        return $categoryCodes->first(fn ($code) => str_starts_with($rawValue, (string) $code)) ?? '';
    };
    $rowItems = old('jenis_aset')
        ? collect(old('jenis_aset'))->map(function ($value, $index) use ($assetCategories, $otherDetailValue, $resolveAssetCode) {
            $jenisAset = $resolveAssetCode($value);
            $detailOptions = data_get($assetCategories, "{$jenisAset}.details", []);
            $oldDetail = old('perincian_aset')[$index] ?? '';
            $customDetail = old('custom_perincian_aset')[$index] ?? '';

            if ($oldDetail && ! in_array($oldDetail, array_merge($detailOptions, [$otherDetailValue]), true)) {
                $customDetail = $customDetail ?: $oldDetail;
                $oldDetail = $otherDetailValue;
            }

            return [
                'jenis_aset' => $jenisAset,
                'perincian_aset' => $oldDetail,
                'custom_perincian_aset' => $customDetail,
                'kuantiti' => old('kuantiti')[$index] ?? '',
                'harga' => old('harga')[$index] ?? '',
                'justifikasi' => old('justifikasi')[$index] ?? '',
                'quotation' => old('existing_quotation')[$index] ?? null,
            ];
        })->values()->all()
        : ($isEdit
            ? $assetRequest->items->map(function ($item) use ($assetCategories, $otherDetailValue, $resolveAssetCode) {
                $jenisAset = $resolveAssetCode($item->jenis_aset);
                $detailOptions = data_get($assetCategories, "{$jenisAset}.details", []);
                $detailValue = in_array($item->perincian_aset, $detailOptions, true) ? $item->perincian_aset : ($item->perincian_aset ? $otherDetailValue : '');

                return [
                    'jenis_aset' => $jenisAset,
                    'perincian_aset' => $detailValue,
                    'custom_perincian_aset' => $detailValue === $otherDetailValue ? $item->perincian_aset : '',
                    'kuantiti' => $item->kuantiti,
                    'harga' => $item->harga_seunit,
                    'justifikasi' => $item->justifikasi,
                    'quotation' => $item->quotation,
                ];
            })->values()->all()
            : [[
                'jenis_aset' => '',
                'perincian_aset' => '',
                'custom_perincian_aset' => '',
                'kuantiti' => '',
                'harga' => '',
                'justifikasi' => '',
                'quotation' => null,
            ]]);
@endphp

@section('content')

<style>
    body {
        position: relative;
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1; 
        background-image: url("{{ asset('images/background1.jfif') }}");
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        opacity: 0.3; 
    }

    /* Justifikasi column width adjustment */
    .col-ulasan {
        min-width: 300px;
    }
</style>

    <div class="hero-strip">
        <h2 class="fw-bold mb-1">{{ $isEdit ? 'Kemaskini Permohonan Aset' : 'Permohonan Aset Bagi Penyediaan Bajet' }}</h2>
        <p class="mb-0 opacity-75">Isi maklumat jabatan dan pilih kod aset 35000 berserta perincian item yang dimohon.</p>
    </div>

    <div class="page-card p-4">
        <form method="POST" enctype="multipart/form-data" action="{{ $isEdit ? route('staff.requests.update', $assetRequest) : route('staff.requests.store') }}">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $isEdit ? $assetRequest->jabatan : $department) }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Bahagian</label>
                    <select name="unit" class="form-select" required>
                        <option value="">-- Pilih Bahagian --</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit }}" @selected(old('unit', $isEdit ? $assetRequest->unit : auth()->user()->unit) === $unit)>{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tahun Permohonan</label>
                    <select name="tahun" class="form-select" required>
                        <option value="">-- Pilih Tahun --</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}" @selected((string) old('tahun', $isEdit ? $assetRequest->tahun : '') === (string) $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="alert alert-light border mb-4">
                <div class="fw-semibold">Kategori utama aset</div>
                <div>{{ $assetMainCategory['code'] }} - {{ $assetMainCategory['label'] }}</div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle" id="assetTable">
                    <thead class="table-light">
                        <tr>
                            <th>Bil</th>
                            <th style="width: 200px;">Kod / Jenis Aset</th>
                            <th style="width: 200px;">Perincian Aset</th>
                            <th style="width: 80px;">Kuantiti</th>
                            <th style="width: 120px;">Harga (RM)</th>
                            <th style="width: 120px;">Jumlah (RM)</th>
                            <th class="col-ulasan">Ulasan</th>
                            <th style="width: 150px;">Quotation</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rowItems as $index => $row)
                            <tr>
                                <td class="row-number">{{ $index + 1 }}</td>
                                <td>
                                    <select name="jenis_aset[]" class="form-select asset-type" required>
                                        <option value="">-- Pilih Kod Aset --</option>
                                        @foreach ($assetCategories as $code => $category)
                                            <option value="{{ $code }}" @selected((string) $row['jenis_aset'] === (string) $code)>{{ $code }} - {{ $category['label'] }}</option>
                                        @endforeach
                                    </select>
                                    @error("jenis_aset.$index")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <select
                                        name="perincian_aset[]"
                                        class="form-select asset-detail"
                                        data-selected="{{ $row['perincian_aset'] }}"
                                        required
                                    >
                                        <option value="">-- Pilih Perincian --</option>
                                    </select>
                                    <input
                                        type="text"
                                        name="custom_perincian_aset[]"
                                        class="form-control mt-2 custom-detail-input @error("custom_perincian_aset.$index") is-invalid @enderror"
                                        placeholder="Masukkan perincian aset"
                                        value="{{ $row['custom_perincian_aset'] }}"
                                        @if ($row['perincian_aset'] !== $otherDetailValue) style="display:none;" @endif
                                    >
                                    @error("perincian_aset.$index")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @error("custom_perincian_aset.$index")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td><input type="number" min="1" name="kuantiti[]" class="form-control qty" value="{{ $row['kuantiti'] }}" required></td>
                                <td><input type="number" min="0" step="0.01" name="harga[]" class="form-control price" value="{{ $row['harga'] }}" required></td>
                                <td><input type="text" class="form-control total" readonly value="{{ is_numeric($row['kuantiti']) && is_numeric($row['harga']) ? number_format($row['kuantiti'] * $row['harga'], 2, '.', '') : '' }}"></td>
                                <td>
                                    <textarea name="justifikasi[]" class="form-control" rows="3" placeholder="Sila nyatakan ulasan/justifikasi">{{ $row['justifikasi'] }}</textarea>
                                </td>
                                <td>
                                    <input type="hidden" name="existing_quotation[]" value="{{ $row['quotation'] }}">
                                    <input type="file" name="quotation[]" class="form-control form-control-sm">
                                    @if (! empty($row['quotation']) && $isEdit && $assetRequest->items->has($index))
                                        @php($existingItem = $assetRequest->items[$index])
                                        <a href="{{ route('quotations.show', $existingItem) }}" target="_blank" class="small d-inline-block mt-2 {{ ! $existingItem->hasQuotationFile() ? 'text-danger' : '' }}">
                                            {{ ! $existingItem->hasQuotationFile() ? 'Fail sedia ada tidak dijumpai' : 'Lihat fail sedia ada' }}
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row">Buang</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3">
                <button type="button" class="btn btn-outline-primary" id="addRowBtn">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Baris
                </button>
                <div class="text-end">
                    <div class="text-muted small text-uppercase">Anggaran Keseluruhan</div>
                    <div class="fs-4 fw-bold text-danger">RM <span id="grandTotal">0.00</span></div>
                </div>
            </div>

            <div class="text-end mt-4">
                <button class="btn btn-primary px-4">{{ $isEdit ? 'Kemaskini Permohonan' : 'Hantar Permohonan' }}</button>
            </div>
        </form>
    </div>

    <template id="rowTemplate">
        <tr>
            <td class="row-number"></td>
            <td>
                <select name="jenis_aset[]" class="form-select asset-type" required>
                    <option value="">-- Pilih Kod Aset --</option>
                    @foreach ($assetCategories as $code => $category)
                        <option value="{{ $code }}">{{ $code }} - {{ $category['label'] }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="perincian_aset[]" class="form-select asset-detail" required>
                    <option value="">-- Pilih Perincian --</option>
                </select>
                <input type="text" name="custom_perincian_aset[]" class="form-control mt-2 custom-detail-input" placeholder="Masukkan perincian aset" style="display:none;">
            </td>
            <td><input type="number" min="1" name="kuantiti[]" class="form-control qty" required></td>
            <td><input type="number" min="0" step="0.01" name="harga[]" class="form-control price" required></td>
            <td><input type="text" class="form-control total" readonly></td>
            <td><textarea name="justifikasi[]" class="form-control" rows="3" placeholder="Sila nyatakan ulasan/justifikasi"></textarea></td>
            <td>
                <input type="hidden" name="existing_quotation[]" value="">
                <input type="file" name="quotation[]" class="form-control form-control-sm">
            </td>
            <td><button type="button" class="btn btn-outline-danger btn-sm remove-row">Buang</button></td>
        </tr>
    </template>
@endsection

@push('scripts')
    <script>
        const tableBody = document.querySelector('#assetTable tbody');
        const grandTotalEl = document.getElementById('grandTotal');
        const rowTemplate = document.getElementById('rowTemplate');
        const otherDetailValue = @json($otherDetailValue);
        const assetCategories = @json($assetCategories);

        function syncAssetDetailOptions(row) {
            const assetTypeSelect = row.querySelector('.asset-type');
            const detailSelect = row.querySelector('.asset-detail');
            const customDetailInput = row.querySelector('.custom-detail-input');
            const selectedCategory = assetTypeSelect.value;
            const detailConfig = assetCategories[selectedCategory]?.details ?? [];
            const previousValue = detailSelect.dataset.selected || detailSelect.value || (customDetailInput.value.trim() ? otherDetailValue : '');

            detailSelect.innerHTML = '<option value="">-- Pilih Perincian --</option>';

            detailConfig.forEach(detail => {
                const option = document.createElement('option');
                option.value = detail;
                option.textContent = detail;
                option.selected = previousValue === detail;
                detailSelect.appendChild(option);
            });

            const otherOption = document.createElement('option');
            otherOption.value = otherDetailValue;
            otherOption.textContent = 'Lain-lain';
            otherOption.selected = previousValue === otherDetailValue;
            detailSelect.appendChild(otherOption);

            detailSelect.disabled = !selectedCategory;
            detailSelect.dataset.selected = detailSelect.value;

            const isOtherSelected = detailSelect.value === otherDetailValue;
            customDetailInput.style.display = isOtherSelected ? '' : 'none';
            customDetailInput.required = isOtherSelected;

            if (!isOtherSelected) {
                customDetailInput.value = '';
            }
        }

        function updateTotals() {
            let grandTotal = 0;

            tableBody.querySelectorAll('tr').forEach((row, index) => {
                row.querySelector('.row-number').textContent = index + 1;
                syncAssetDetailOptions(row);
                const qty = parseFloat(row.querySelector('.qty').value || 0);
                const price = parseFloat(row.querySelector('.price').value || 0);
                const total = qty * price;
                row.querySelector('.total').value = total ? total.toFixed(2) : '';
                grandTotal += total;
            });

            grandTotalEl.textContent = grandTotal.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        document.getElementById('addRowBtn').addEventListener('click', () => {
            tableBody.appendChild(rowTemplate.content.firstElementChild.cloneNode(true));
            updateTotals();
        });

        tableBody.addEventListener('input', event => {
            if (event.target.classList.contains('qty') || event.target.classList.contains('price')) {
                updateTotals();
            }
        });

        tableBody.addEventListener('change', event => {
            if (event.target.classList.contains('asset-type')) {
                const row = event.target.closest('tr');
                const detailSelect = row.querySelector('.asset-detail');
                detailSelect.dataset.selected = '';
                detailSelect.value = '';
                syncAssetDetailOptions(row);
            }

            if (event.target.classList.contains('asset-detail')) {
                const row = event.target.closest('tr');
                event.target.dataset.selected = event.target.value;
                syncAssetDetailOptions(row);
            }
        });

        tableBody.addEventListener('click', event => {
            if (!event.target.classList.contains('remove-row')) {
                return;
            }

            if (tableBody.querySelectorAll('tr').length === 1) {
                return;
            }

            event.target.closest('tr').remove();
            updateTotals();
        });

        tableBody.querySelectorAll('tr').forEach(row => syncAssetDetailOptions(row));
        updateTotals();
    </script>
@endpush
