@extends('layouts.app')

@section('content')

<style>
    /* Background setup */
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
        background-image: url("{{ asset('images/background.jpeg') }}");
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        opacity: 0.15;
        /* Reduced opacity for better readability */
    }

    /* Table & Card Refinement */
    .page-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: none;
    }

    .detail-table thead {
        background-color: #1e3a8a;
        color: white;
    }

    .detail-table thead th {
        padding: 1.2rem 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        border: none;
    }

    .detail-table tbody td {
        padding: 1.2rem 0.75rem;
        border-bottom: 1px solid #f0f2f5;
    }

    /* Handling the 'Messy' Columns */
    .ulasan-col {
        min-width: 250px;
        max-width: 300px;
        font-size: 0.9rem;
        color: #6c757d;
        line-height: 1.5;
    }

    .status-select-group {
        min-width: 200px;
    }

    /* Style the select for a cleaner look */
    .form-select-custom {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        font-size: 0.85rem;
    }

    .btn-save-sm {
        padding: 0.25rem 0.75rem;
        border-radius: 8px;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.requests.index') }}" class="btn btn-white shadow-sm btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <h3 class="fw-bold mb-0 text-dark">Permohonan Terperinci</h3>
        </div>
        <span class="badge bg-white text-primary border rounded-pill px-3 py-2 shadow-sm">
            ID PERMOHONAN: #{{ $assetRequest->id }}
        </span>
    </div>

    <div class="page-card p-4 mb-4 border-start border-primary border-5">
        <div class="row align-items-center">
            <div class="col-md-2 border-end mb-3 mb-md-0">
                <div class="text-muted small text-uppercase fw-bold mb-1">Tahun</div>
                <div class="h5 fw-bold mb-0">{{ $assetRequest->tahun }}</div>
            </div>
            <div class="col-md-5 border-end mb-3 mb-md-0">
                <div class="text-muted small text-uppercase fw-bold mb-1">Jabatan</div>
                <div class="fw-semibold text-dark">{{ strtoupper($assetRequest->jabatan) }}</div>
            </div>
            <div class="col-md-5">
                <div class="text-muted small text-uppercase fw-bold mb-1">Bahagian / Unit</div>
                <div class="fw-semibold text-dark">{{ strtoupper($assetRequest->unit) }}</div>
            </div>
        </div>
    </div>

    <div class="page-card overflow-hidden">
        <div class="p-4 border-bottom bg-light d-flex align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-box-seam me-2 text-primary"></i>Senarai Item Dimohon
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover detail-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;"><b>#</b></th>
                        <th><b>Jenis Aset</b></th>
                        <th class="text-center"><b>Unit</b></th>
                        <th><b>Harga Seunit</b></th>
                        <th><b>Jumlah Keseluruhan</b></th>
                        <th><b>Justifikasi / Ulasan</b></th>
                        <th class="text-center"><b>Dokumen</b></th>
                        <th style="width: 250px;"><b>Tindakan Status</b></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assetRequest->items as $item)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <span class="fw-bold text-dark d-block">{{ $item->displayName() }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary rounded-pill">{{ $item->kuantiti }}</span>
                        </td>
                        <td><small class="text-muted">RM</small> {{ number_format($item->harga_seunit, 2) }}</td>
                        <td class="fw-bold text-primary">RM {{ number_format($item->jumlah, 2) }}</td>
                        <td>
                            <div class="ulasan-col">
                                {{ $item->justifikasi ?: '—' }}
                            </div>
                        </td>
                        <td class="text-center">
                            @if ($item->quotation)
                            <a href="{{ route('quotations.show', $item) }}" target="_blank" class="btn btn-sm btn-outline-danger border-0">
                                <i class="bi bi-file-pdf fs-5"></i>
                            </a>
                            @else
                            <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.requests.status', $item) }}" class="d-flex gap-2 status-select-group">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm form-select-custom shadow-sm">
                                    @foreach (\App\Models\RequestItem::statusOptions() as $statusValue => $statusLabel)
                                    <option value="{{ $statusValue }}" @selected($item->status === $statusValue)>{{ $statusLabel }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-primary btn-save-sm shadow-sm">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection