@extends('layouts.app')

@section('content')
    <style>
        /* Import Google Font */
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        .auth-page {
            font-family: 'Plus Jakarta Sans', sans-serif;
            /* Gradien yang lebih smooth antara Biru dan Merah */
            background: radial-gradient(circle at top left, #1e3a8a 0%, #1e40af 40%, #8b0000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 420px;
            padding: 45px 35px;
            transition: transform 0.3s ease;
        }

        .brand-logo {
            margin-bottom: 25px;
        }

        .brand-logo img {
            height: 70px; /* Saiz lebih konsisten */
            width: auto;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        .login-title {
            color: #0f172a;
            font-weight: 800;
            text-align: center;
            letter-spacing: -0.025em;
            margin-bottom: 8px;
        }

        .login-subtitle {
            text-align: center;
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 35px;
            line-height: 1.5;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            font-size: 0.85rem;
            margin-bottom: 8px;
            display: block;
        }

        .input-group {
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s ease;
            overflow: hidden;
        }

        .input-group:focus-within {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1);
            background-color: #fff;
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #64748b;
            padding-left: 15px;
        }

        .form-control {
            border: none;
            background: transparent;
            padding: 12px 15px 12px 5px;
            font-size: 0.95rem;
            color: #1e293b;
        }

        .form-control:focus {
            box-shadow: none;
            background: transparent;
        }

        .btn-login {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border: none;
            padding: 14px;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 12px;
            width: 100%;
            margin-top: 15px;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(30, 58, 138, 0.35);
            filter: brightness(1.1);
            color: white;
        }

        .register-link {
            color: #b91c1c; /* Merah untuk link supaya seimbang dengan tema */
            font-weight: 600;
            text-decoration: none;
        }

        .register-link:hover {
            text-decoration: underline;
            color: #991b1b;
        }

        .footer-text {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 30px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>

    <div class="auth-page">
        <div class="login-card">
            <div class="brand-logo text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </div>

            <h2 class="login-title">Selamat Kembali</h2>
            <p class="login-subtitle">Sistem Permohonan Aset<br>Pejabat Tanah & Jajahan Bachok</p>

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nama Pengguna</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="username" value="{{ old('username') }}" class="form-control" placeholder="Masukkan Nama Pengguna" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Kata Laluan</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                        <input id="login-password" type="password" name="password" class="form-control" placeholder="Masukkan Kata Laluan" required>
                        <button type="button" class="input-group-text password-toggle" data-password-toggle="#login-password" aria-label="Lihat kata laluan">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-login">
                    Log Masuk Sistem
                </button>

                <div class="mt-4 text-center">
                    <p class="mb-0" style="font-size: 0.9rem; color: #64748b;">
                        Belum mempunyai akaun? 
                        <a href="{{ route('register') }}" class="register-link">Daftar Sekarang</a>
                    </p>
                </div>
            </form>

            <div class="text-center footer-text">
                &copy; Pejabat Tanah & Jajahan Bachok
            </div>
        </div>
    </div>
@endsection
