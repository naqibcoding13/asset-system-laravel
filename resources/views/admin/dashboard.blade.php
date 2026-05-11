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
    <div class="hero-strip d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">Selamat Datang, Admin</h2>
            <p class="mb-0 opacity-75">Ringkasan statistik permohonan aset Pejabat Tanah & Jajahan Bachok.</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="page-card p-4 h-100 border-start border-primary border-4">
                <div class="text-muted small text-uppercase fw-bold">Jumlah Permohonan</div>
                <div class="fs-2 fw-bold">{{ $summary['total_requests'] }}</div>
                <div class="mt-2 text-primary small">
                    <i class="bi bi-arrow-up-right me-1"></i> Rekod Berdaftar
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="page-card p-4 h-100">
                <div class="text-muted small text-uppercase fw-bold">Aset Dimohon</div>
                <div class="fs-2 fw-bold">{{ $summary['total_assets'] }}</div>
                <div class="mt-2 text-success small">
                    <i class="bi bi-check-circle me-1"></i> Item Aset
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="page-card p-4 h-100 border-bottom border-danger border-4">
                <div class="text-muted small text-uppercase fw-bold">Anggaran Bajet</div>
                <div class="fs-2 fw-bold text-danger">RM {{ number_format($summary['total_budget'], 2) }}</div>
                <div class="mt-2 text-danger small">
                    <i class="bi bi-info-circle me-1"></i> Jumlah Terkumpul
                </div>
            </div>
        </div>
    </div>

    <div class="page-card p-4">
        <h4 class="fw-bold mb-3">Permohonan Terkini</h4>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead style="background-color: #1e3a8a; color: white;">
                    <tr>
                        <th>ID</th>
                        <th>Pemohon</th>
                        <th>Tahun</th>
                        <th>Bahagian</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestRequests as $request)
                        <tr>
                            <td>#{{ $request->id }}</td>
                            <td>{{ $request->user?->username }}</td>
                            <td>{{ $request->tahun }}</td>
                            <td>{{ $request->unit }}</td>
                            <td>RM {{ number_format($request->items->sum('jumlah'), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada data permohonan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="page-card p-4 mt-4">
        <h5 class="fw-bold"><i class="bi bi-lightbulb me-2 text-warning"></i>Panduan Admin</h5>
        <p class="text-muted small mb-0">Gunakan menu di sebelah untuk menyemak permohonan, melihat laporan, dan mengurus pengguna dengan lebih tersusun.</p>
    </div>
@endsection
