@extends('layouts.app')

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
    z-index: -1; /* Letak di belakang kandungan */
    background-image: url("{{ asset('images/background.jpeg') }}");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    background-repeat: no-repeat;
    
    /* Laraskan tahap ketelusan di sini (0.0 hingga 1.0) */
    opacity: 0.3; 
}
</style>
    <div class="hero-strip">
        <h2 class="fw-bold mb-1">Senarai Permohonan Aset</h2>
        <p class="mb-0 opacity-75">Urus dan semak permohonan bajet daripada jabatan dan bahagian.</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="page-card p-3 border-start border-primary border-4">
                <small class="text-muted fw-bold">JUMLAH REKOD</small>
                <h4 class="mb-0">{{ $requests->count() }} Permohonan</h4>
            </div>
        </div>
    </div>

    <div class="page-card p-4 mb-4">
        <form class="row g-3">
            <div class="col-md-2">
                <label class="form-label fw-semibold">Tahun</label>
                <select name="tahun" class="form-select">
                    <option value="">Semua Tahun</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" @selected(($filters['tahun'] ?? '') == $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Bahagian</label>
                <select name="bahagian" class="form-select">
                    <option value="">Semua Bahagian</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit }}" @selected(($filters['bahagian'] ?? '') === $unit)>{{ $unit }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach ($statusOptions as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}" @selected(($filters['status'] ?? '') === $statusValue)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Jenis Aset</label>
                <input type="text" name="jenis_aset" value="{{ $filters['jenis_aset'] ?? '' }}" class="form-control" placeholder="Cari kod atau perincian aset">
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button class="btn btn-primary w-100">Cari</button>
                <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="page-card p-4">
        @php
            $printFilters = array_filter([
                'tahun' => $filters['tahun'] ?? null,
                'bahagian' => $filters['bahagian'] ?? null,
                'status' => $filters['status'] ?? null,
            ], fn ($value) => filled($value));
        @endphp
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Senarai Permohonan</h5>
            <form method="GET" action="{{ route('admin.requests.print') }}" target="_blank" class="d-flex align-items-center gap-2 flex-wrap">
                @foreach ($printFilters as $filterKey => $filterValue)
                    <input type="hidden" name="{{ $filterKey }}" value="{{ $filterValue }}">
                @endforeach
                <select name="orientation" class="form-select form-select-sm" style="width: 160px;">
                    <option value="portrait">Potret</option>
                    <option value="landscape" selected>Landskap</option>
                </select>
                <button class="btn btn-primary">
                    <i class="bi bi-printer me-1"></i> Cetak Laporan
                </button>
                <button
                    class="btn btn-success"
                    formaction="{{ route('admin.requests.excel') }}"
                    formtarget="_self"
                >
                    <i class="bi bi-file-earmark-excel me-1"></i> Excel
                </button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead style="background-color: #1e3a8a; color: white;">
                    <tr>
                        <th>ID</th>
                        <th>Pemohon</th>
                        <th>Tahun</th>
                        <th>Bahagian</th>
                        <th>Jumlah</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $request)
                        <tr>
                            <td class="fw-bold text-primary">#{{ $request->id }}</td>
                            <td>{{ $request->user?->username }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $request->tahun }}</span></td>
                            <td>{{ strtoupper($request->unit) }}</td>
                            <td class="fw-bold text-danger">RM {{ number_format($request->items->sum('jumlah'), 2) }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.requests.show', $request) }}" class="btn btn-sm btn-outline-primary">Perincian</a>
                                    <form method="POST" action="{{ route('admin.requests.destroy', $request) }}" onsubmit="return confirm('Anda pasti mahu padam permohonan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Padam">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Tiada rekod dijumpai untuk carian tersebut.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
