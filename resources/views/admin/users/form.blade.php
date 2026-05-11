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
    <div class="d-flex justify-content-center">
        <div class="page-card p-4" style="width: 100%; max-width: 650px; border-top: 5px solid #a30000;">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-primary mb-1">{{ $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h3>
                <p class="text-muted small mb-0">
                    {{ $isEdit ? 'Kemaskini maklumat akaun kakitangan di bawah.' : 'Sila isi maklumat akaun kakitangan di bawah.' }}
                </p>
            </div>

            <form method="POST" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Penuh</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nama Pengguna</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">{{ $isEdit ? 'Password Baru' : 'Password' }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input id="admin-user-password" type="password" name="password" class="form-control" {{ $isEdit ? '' : 'required' }}>
                            <button type="button" class="input-group-text password-toggle" data-password-toggle="#admin-user-password" aria-label="Lihat kata laluan">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @if ($isEdit)
                            <small class="text-muted">Kosongkan jika tidak mahu tukar password.</small>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Emel</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Bahagian</label>
                    <select name="unit" class="form-select">
                        <option value="">-- Pilih Bahagian --</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit }}" @selected(old('unit', $user->unit) === $unit)>{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Peranan (Role)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                        <select name="role" class="form-select" required>
                            <option value="staff" @selected(old('role', $user->role) === 'staff')>Staff (Pengguna Biasa)</option>
                            <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin (Pengurus Sistem)</option>
                        </select>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light me-md-2">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>{{ $isEdit ? 'Kemaskini Pengguna' : 'Simpan Pengguna' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
