<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem Permohonan Aset' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --spa-blue: #1e3a8a;
            --spa-red: #a30000;
            --spa-bg: #f4f7f6;
            --sidebar-bg: #f8fafc;
            --text-main: #1e293b;
        }

        body {
            background: var(--spa-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .shell {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 0;
        }

        .sidebar-header {
            padding: 25px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #e2e8f0;
            background: white;
        }

        .logo-box {
            width: 40px;
            height: 40px;
            background-color: var(--spa-red);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.2);
        }

        .sidebar-header h4 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--spa-blue);
            letter-spacing: -0.5px;
        }

        .nav-container {
            flex-grow: 1;
            padding: 20px 12px;
            overflow-y: auto;
        }

        .nav-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #94a3b8;
            padding: 0 12px 10px;
            letter-spacing: 1px;
        }

        .sidebar .nav-link {
            border-radius: 12px;
            color: #475569;
            padding: 10px 14px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
            color: #64748b;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: #eff6ff;
            color: var(--spa-blue);
        }

        .sidebar-footer {
            padding: 20px 12px;
            border-top: 1px solid #e2e8f0;
            background: white;
        }

        .btn-logout {
            color: #dc2626 !important;
            background: #fef2f2;
            border: none;
        }

        .btn-logout:hover {
            background-color: #dc2626 !important;
            color: white !important;
        }

        .role-badge {
            font-size: 0.65rem;
            padding: 2px 8px;
            background: #e2e8f0;
            border-radius: 20px;
            color: #475569;
            font-weight: 600;
        }

        .content {
            flex: 1;
            padding: 36px;
            margin-left: 260px;
        }

        .page-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .hero-strip {
            background: linear-gradient(135deg, var(--spa-blue), #1d4ed8);
            color: #fff;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 24px;
            box-shadow: 0 10px 20px rgba(30, 58, 138, 0.15);
        }

        .password-toggle {
            border: none;
            background: transparent;
            color: #64748b;
        }

        .password-toggle:hover,
        .password-toggle:focus {
            background: transparent;
            color: var(--spa-blue);
            box-shadow: none;
        }

        @media (max-width: 991px) {
            .shell {
                display: block;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .content {
                padding: 20px;
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
        <div class="shell">
            @include('partials.sidebar')
            <main class="content">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    @else
        @include('partials.flash')
        @yield('content')
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('click', event => {
            const toggleButton = event.target.closest('[data-password-toggle]');

            if (!toggleButton) {
                return;
            }

            const input = document.querySelector(toggleButton.dataset.passwordToggle);

            if (!input) {
                return;
            }

            const isVisible = input.type === 'text';
            input.type = isVisible ? 'password' : 'text';
            toggleButton.innerHTML = isVisible
                ? '<i class="bi bi-eye"></i>'
                : '<i class="bi bi-eye-slash"></i>';
            toggleButton.setAttribute('aria-label', isVisible ? 'Lihat kata laluan' : 'Sembunyikan kata laluan');
        });
    </script>
    @stack('scripts')
</body>
</html>
