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

        .confirm-modal .modal-content {
            border: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
        }

        .confirm-modal__bar {
            height: 6px;
            background: linear-gradient(90deg, var(--spa-blue), #2563eb);
        }

        .confirm-modal[data-confirm-variant="danger"] .confirm-modal__bar {
            background: linear-gradient(90deg, var(--spa-red), #dc2626);
        }

        .confirm-modal[data-confirm-variant="warning"] .confirm-modal__bar {
            background: linear-gradient(90deg, #b45309, #f59e0b);
        }

        .confirm-modal[data-confirm-variant="success"] .confirm-modal__bar {
            background: linear-gradient(90deg, #047857, #10b981);
        }

        .confirm-modal__icon {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: var(--spa-blue);
            font-size: 1.55rem;
            flex: 0 0 auto;
        }

        .confirm-modal[data-confirm-variant="danger"] .confirm-modal__icon {
            background: #fef2f2;
            color: var(--spa-red);
        }

        .confirm-modal[data-confirm-variant="warning"] .confirm-modal__icon {
            background: #fffbeb;
            color: #b45309;
        }

        .confirm-modal[data-confirm-variant="success"] .confirm-modal__icon {
            background: #ecfdf5;
            color: #047857;
        }

        .confirm-modal .btn-confirm-action {
            background: var(--spa-blue);
            border-color: var(--spa-blue);
            color: #fff;
        }

        .confirm-modal[data-confirm-variant="danger"] .btn-confirm-action {
            background: var(--spa-red);
            border-color: var(--spa-red);
        }

        .confirm-modal[data-confirm-variant="warning"] .btn-confirm-action {
            background: #b45309;
            border-color: #b45309;
        }

        .confirm-modal[data-confirm-variant="success"] .btn-confirm-action {
            background: #047857;
            border-color: #047857;
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

    <div class="modal fade confirm-modal" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionTitle" aria-hidden="true" data-confirm-variant="warning">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="confirm-modal__bar"></div>
                <div class="modal-body p-4">
                    <div class="d-flex gap-3">
                        <div class="confirm-modal__icon">
                            <i class="bi bi-exclamation-triangle-fill" id="confirmActionIcon"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-2 text-dark" id="confirmActionTitle">Sahkan tindakan</h5>
                            <p class="text-muted mb-0" id="confirmActionMessage">Adakah anda pasti mahu meneruskan tindakan ini?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-confirm-action px-4" id="confirmActionButton">Ya, teruskan</button>
                </div>
            </div>
        </div>
    </div>

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

        document.addEventListener('DOMContentLoaded', () => {
            const confirmModalElement = document.getElementById('confirmActionModal');

            if (!confirmModalElement) {
                return;
            }

            const confirmModal = bootstrap.Modal.getOrCreateInstance(confirmModalElement);
            const title = document.getElementById('confirmActionTitle');
            const message = document.getElementById('confirmActionMessage');
            const icon = document.getElementById('confirmActionIcon');
            const confirmButton = document.getElementById('confirmActionButton');
            const variantIcons = {
                danger: 'bi-trash-fill',
                warning: 'bi-exclamation-triangle-fill',
                success: 'bi-arrow-counterclockwise',
                primary: 'bi-check-circle-fill',
            };
            let pendingForm = null;

            document.addEventListener('submit', event => {
                const form = event.target.closest('.js-confirm-form');

                if (!form) {
                    return;
                }

                event.preventDefault();
                pendingForm = form;

                const variant = form.dataset.confirmVariant || 'warning';
                confirmModalElement.dataset.confirmVariant = variant;
                title.textContent = form.dataset.confirmTitle || 'Sahkan tindakan';
                message.textContent = form.dataset.confirmMessage || 'Adakah anda pasti mahu meneruskan tindakan ini?';
                confirmButton.textContent = form.dataset.confirmButton || 'Ya, teruskan';
                icon.className = `bi ${variantIcons[variant] || variantIcons.warning}`;

                confirmModal.show();
            });

            confirmButton.addEventListener('click', () => {
                if (!pendingForm) {
                    return;
                }

                const form = pendingForm;
                pendingForm = null;
                confirmModal.hide();
                form.submit();
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
