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
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-1">Pengurusan Pengguna</h2>
                <p class="mb-0 opacity-75">Senarai kakitangan yang mempunyai akses ke dalam sistem.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-light fw-semibold">
                <i class="bi bi-person-plus-fill me-2"></i> Tambah Pengguna
            </a>
        </div>
    </div>

    <div class="page-card p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 80px;">ID</th>
                        <th>Nama Penuh</th>
                        <th>Nama Pengguna</th>
                        <th>Peranan (Role)</th>
                        <th class="text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="text-center text-muted fw-bold">#{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light text-secondary fw-bold d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="fw-bold">{{ strtoupper($user->name) }}</div>
                                </div>
                            </td>
                            <td><code class="text-primary">{{ $user->username }}</code></td>
                            <td>
                                <span class="badge rounded-pill {{ $user->role === 'admin' ? 'text-bg-danger' : 'text-bg-info' }}">
                                    <i class="bi bi-shield-lock me-1"></i> {{ strtoupper($user->role) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.destroy', $user) }}"
                                        class="d-inline js-confirm-form"
                                        data-confirm-variant="danger"
                                        data-confirm-title="Padam Pengguna?"
                                        data-confirm-message="Akaun {{ $user->username }} akan dipadam daripada sistem. Pastikan akses pengguna ini tidak lagi diperlukan."
                                        data-confirm-button="Ya, padam"
                                    >
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
                            <td colspan="5" class="text-center py-5 text-muted">Tiada pengguna ditemui.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 text-center">
    <span class="text-black fw-bold">Jumlah Pengguna Berdaftar: {{ $users->count() }}</span>
</div>
@endsection
