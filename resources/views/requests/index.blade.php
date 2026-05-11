@extends('layouts.app')

@section('content')

<style>
    .request-card {
        border: 1px solid #dbe5f0;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .request-card + .request-card {
        margin-top: 1.25rem;
    }

    .request-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 1.25rem;
        background: linear-gradient(135deg, #eff6ff, #f8fafc);
        border-bottom: 1px solid #dbe5f0;
    }

    .request-meta {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.85rem;
        margin-top: 0.9rem;
    }

    .request-meta-box {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem 0.9rem;
    }

    .request-meta-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 0.2rem;
    }

    .request-meta-value {
        font-weight: 700;
        color: #0f172a;
    }

    .item-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .item-table th,
    .item-table td {
        padding: 0.95rem 0.85rem;
        vertical-align: top;
        border-bottom: 1px solid #e2e8f0;
    }

    .item-table thead th {
        background: #f8fafc;
        color: #334155;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .item-table tbody tr:last-child td {
        border-bottom: none;
    }

    .item-table .ulasan-col {
        min-width: 340px;
        white-space: normal;
        word-break: break-word;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0.4rem 0.8rem;
        font-size: 0.82rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-pill.text-bg-warning {
        color: #854d0e !important;
        background: #fef3c7 !important;
    }

    .status-pill.text-bg-success {
        background: #dcfce7 !important;
        color: #166534 !important;
    }

    .status-pill.text-bg-danger {
        background: #fee2e2 !important;
        color: #991b1b !important;
    }

    .request-total {
        font-size: 0.92rem;
        font-weight: 700;
        color: #b91c1c;
    }

    @media (max-width: 991px) {
        .request-meta {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .request-card-header {
            flex-direction: column;
        }

        .request-meta {
            grid-template-columns: 1fr;
        }
    }

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
    z-index: -1; /* Letak di belakang kandungan */
    background-image: url("{{ asset('images/background1.jfif') }}");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    background-repeat: no-repeat;
    
    /* Laraskan tahap ketelusan di sini (0.0 hingga 1.0) */
    opacity: 0.3; 
}
</style>
    <div class="hero-strip">
        <h2 class="fw-bold mb-1">Permohonan Saya</h2>
        <p class="mb-0 opacity-75">Senarai semua permohonan dan status item yang telah dihantar.</p>
    </div>

    <div class="page-card p-4">
        @php
            $printFilters = array_filter($filters ?? [], fn ($value) => filled($value));
        @endphp

        <form class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tahun</label>
                <select name="tahun" class="form-select">
                    <option value="">Semua Tahun</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" @selected(($filters['tahun'] ?? '') === $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Bahagian</label>
                <select name="bahagian" class="form-select">
                    <option value="">Semua Bahagian</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit }}" @selected(($filters['bahagian'] ?? '') === $unit)>{{ $unit }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach ($statusOptions as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}" @selected(($filters['status'] ?? '') === $statusValue)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('staff.requests.index') }}" class="btn btn-outline-secondary">Set Semula</a>
                <button class="btn btn-primary">Tapis</button>
            </div>
        </form>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-list-stars me-2"></i>Senarai Permohonan Saya
            </h5>
            <form method="GET" action="{{ route('staff.requests.print') }}" target="_blank" class="d-flex align-items-center gap-2 flex-wrap">
                @foreach ($printFilters as $filterKey => $filterValue)
                    <input type="hidden" name="{{ $filterKey }}" value="{{ $filterValue }}">
                @endforeach
                <select name="orientation" class="form-select form-select-sm" style="width: 160px;">
                    <option value="portrait">Potret</option>
                    <option value="landscape" selected>Landskap</option>
                </select>
                <button class="btn btn-danger">
                    <i class="bi bi-printer me-1"></i> Cetak Laporan
                </button>
            </form>
        </div>
        @forelse ($requests as $request)
            <div class="request-card">
                <div class="request-card-header">
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <h6 class="fw-bold mb-0 text-primary">Permohonan #{{ $request->id }}</h6>
                            <span class="badge rounded-pill text-bg-light border">Tahun {{ $request->tahun }}</span>
                        </div>
                        <div class="request-meta">
                            <div class="request-meta-box">
                                <div class="request-meta-label">Jabatan</div>
                                <div class="request-meta-value">{{ $request->jabatan }}</div>
                            </div>
                            <div class="request-meta-box">
                                <div class="request-meta-label">Bahagian</div>
                                <div class="request-meta-value">{{ $request->unit }}</div>
                            </div>
                            <div class="request-meta-box">
                                <div class="request-meta-label">Jumlah Item</div>
                                <div class="request-meta-value">{{ $request->items->count() }}</div>
                            </div>
                            <div class="request-meta-box">
                                <div class="request-meta-label">Jumlah Permohonan</div>
                                <div class="request-meta-value request-total">RM {{ number_format($request->items->sum('jumlah'), 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="text-md-end">
                        @if ($request->non_pending_items_count === 0)
                            <a href="{{ route('staff.requests.edit', $request) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square me-1"></i>Edit
                            </a>
                        @else
                            <div class="small text-muted">Dikunci selepas semakan admin</div>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="item-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Bil</th>
                                <th style="min-width: 260px;">Jenis Aset</th>
                                <th style="width: 90px;">Kuantiti</th>
                                <th style="width: 160px;">Harga Seunit</th>
                                <th style="width: 150px;">Jumlah</th>
                                <th class="ulasan-col">Ulasan</th>
                                <th style="width: 140px;">Quotation</th>
                                <th style="width: 150px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($request->items as $item)
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $item->displayName() }}</td>
                                    <td>{{ $item->kuantiti }}</td>
                                    <td>RM {{ number_format($item->harga_seunit, 2) }}</td>
                                    <td class="fw-bold text-danger">RM {{ number_format($item->jumlah, 2) }}</td>
                                    <td class="ulasan-col">{{ $item->justifikasi ?: '-' }}</td>
                                    <td>
                                        @if ($item->quotation)
                                            <a href="{{ route('quotations.show', $item) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-earmark me-1"></i>Lihat
                                            </a>
                                        @else
                                            <span class="text-muted">Tiada</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-pill {{ $item->statusBadgeClass() }}">
                                            {{ $item->statusLabel() }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">Tiada permohonan dijumpai.</div>
        @endforelse
    </div>

    <p class="text-black fw-bold mt-3 small text-center">
    <i class="bi bi-info-circle me-1"></i>
    Data dipaparkan berdasarkan permohonan terkini anda.
</p>
@endsection
