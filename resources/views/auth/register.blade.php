@extends('layouts.app')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        .auth-page {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top left, #1e3a8a 0%, #1e40af 40%, #8b0000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 480px;
            padding: 40px;
        }

        .brand-logo {
            margin-bottom: 20px;
        }

        .brand-logo img {
            height: 60px;
            width: auto;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        .title {
            color: #0f172a;
            font-weight: 800;
            text-align: center;
            letter-spacing: -0.025em;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            font-size: 0.8rem;
            margin-bottom: 6px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .input-group {
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.2s ease;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .input-group:focus-within {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.08);
            background-color: #fff;
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding-left: 15px;
            font-size: 1.1rem;
        }

        .form-control, .form-select {
            border: none !important;
            background: transparent !important;
            padding: 10px 12px;
            font-size: 0.95rem;
            color: #1e293b;
            box-shadow: none !important;
        }

        .form-select {
            cursor: pointer;
            color: #64748b;
        }

        .btn-register {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border: none;
            padding: 14px;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 12px;
            width: 100%;
            margin-top: 10px;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(30, 58, 138, 0.3);
            filter: brightness(1.1);
            color: white;
        }

        .login-link {
            color: #b91c1c;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 15px;
        }

        @media (max-width: 576px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .register-card {
                padding: 30px 20px;
            }
        }
    </style>

    <div class="auth-page">
        <div class="register-card">
            <div class="brand-logo text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </div>

            <h3 class="title">Daftar Akaun</h3>
            <p class="subtitle">Sertai Sistem Permohonan Aset</p>

            <form method="POST" action="{{ route('register.store') }}">
                @csrf

                <div class="mb-1">
                    <label class="form-label">Nama Penuh</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Masukkan Nama Penuh" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="mb-1">
                        <label class="form-label">Nama Pengguna</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                            <input type="text" name="username" value="{{ old('username') }}" class="form-control" placeholder="Masukkan Nama Pengguna" required>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Bahagian / Jabatan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                            <select name="unit" class="form-select" required>
                                <option value="" selected disabled>Pilih Bahagian</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit }}" @selected(old('unit') === $unit)>{{ $unit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-1">
                    <label class="form-label">Alamat Emel</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="contoh@gmail.com">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="mb-1">
                        <label class="form-label">Kata Laluan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input id="register-password" type="password" name="password" class="form-control" placeholder="********" required>
                            <button type="button" class="input-group-text password-toggle" data-password-toggle="#register-password" aria-label="Lihat kata laluan">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Sahkan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                            <input id="register-password-confirmation" type="password" name="password_confirmation" class="form-control" placeholder="********" required>
                            <button type="button" class="input-group-text password-toggle" data-password-toggle="#register-password-confirmation" aria-label="Lihat kata laluan">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-register">
                    Cipta Akaun Sekarang
                </button>

                <div class="mt-4 text-center">
                    <p class="mb-0" style="font-size: 0.9rem; color: #64748b;">
                        Sudah mempunyai akaun?
                        <a href="{{ route('login') }}" class="login-link">Log Masuk di sini</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection
