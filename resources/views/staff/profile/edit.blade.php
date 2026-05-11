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
        z-index: -1;
        background-image: url("{{ asset('images/background1.jfif') }}");
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        opacity: 0.3;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="hero-strip">
    <h2 class="fw-bold mb-1">Profil Saya</h2>
    <p class="mb-0 opacity-75">Kemaskini maklumat akaun staff anda bila-bila masa.</p>
</div>

<div class="page-card p-4">
    <h5 class="fw-bold mb-4">
        <i class="bi bi-person-lines-fill me-2"></i>Kemaskini Maklumat Akaun
    </h5>

    <form method="POST" action="{{ route('staff.profile.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Nama Penuh</label>
            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="profile-grid">
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold">Nama Pengguna</label>
                <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control @error('username') is-invalid @enderror" required>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="unit" class="form-label fw-semibold">Bahagian / Jabatan</label>
                <select id="unit" name="unit" class="form-select @error('unit') is-invalid @enderror" required>
                    <option value="" disabled>Pilih Bahagian</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit }}" @selected(old('unit', $user->unit) === $unit)>{{ $unit }}</option>
                    @endforeach
                </select>
                @error('unit')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Alamat Emel</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="profile-grid">
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Kata Laluan Baharu</label>
                <div class="input-group">
                    <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Biarkan kosong jika tidak mahu tukar">
                    <button type="button" class="input-group-text password-toggle" data-password-toggle="#password" aria-label="Lihat kata laluan">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label fw-semibold">Sahkan Kata Laluan Baharu</label>
                <div class="input-group">
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Ulang kata laluan baharu">
                    <button type="button" class="input-group-text password-toggle" data-password-toggle="#password_confirmation" aria-label="Lihat kata laluan">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
