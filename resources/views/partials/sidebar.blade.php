@php
    $user = auth()->user();
    $unread = $user->systemNotifications()->where('status', 'unread')->count();
@endphp

<style>
    :root {
        /* Warna Sidebar (Biru) */
        --sidebar-bg: #1e3a8a; 
        --sidebar-header: #172554;
        
        /* Warna Hover (Merah) */
        --nav-hover-bg: #dc2626; 
        
        /* Warna Teks & Icon */
        --text-white: #ffffff;
        --text-muted: #bfdbfe;
    }

    /* Sidebar Background & Border */
    .sidebar {
        background-color: var(--sidebar-bg) !important;
        color: var(--text-white) !important;
        border-right: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    /* Header & Footer (Biru Lebih Gelap) */
    .sidebar-header, .sidebar-footer {
        background-color: var(--sidebar-header) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    /* Logo Box (Putih) */
    .logo-box {
        background-color: white !important;
        color: var(--sidebar-bg) !important;
    }

    /* Header Text */
    .sidebar-header h4 {
        color: white !important;
    }

    /* Label Menu (Staff/Admin) */
    .nav-label {
        color: var(--text-muted) !important;
        opacity: 0.8;
    }

    /* Menu Navigasi */
    .sidebar .nav-link {
        color: var(--text-white) !important;
        transition: all 0.3s ease !important;
    }

    .sidebar .nav-link i {
        color: var(--text-muted) !important;
    }

    /* KESAN HOVER: TUKAR MERAH */
    .sidebar .nav-link:hover {
        background-color: var(--nav-hover-bg) !important;
        color: white !important;
    }

    .sidebar .nav-link:hover i {
        color: white !important;
    }

    /* Menu Aktif */
    .sidebar .nav-link.active {
        background-color: rgba(255, 255, 255, 0.15) !important;
        border-left: 4px solid white !important;
    }

    /* Logout Button */
    .btn-logout {
        color: white !important;
        background: rgba(255, 255, 255, 0.1) !important;
        border: none !important;
    }

    .btn-logout:hover {
        background-color: #ef4444 !important;
    }

    /* Badge Role */
    .role-badge {
        background: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
    }
</style>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-box">A</div>
        <div>
            <h4>Aset Sistem</h4>
            <span class="role-badge">{{ strtoupper($user->role) }}</span>
        </div>
    </div>

    <div class="nav-container">
        @if ($user->role === 'staff')
            <div class="nav-label">Menu Utama</div>
        @endif

        @if ($user->role === 'admin')
            <div class="nav-label">Administration</div>
        @endif

        <nav class="nav flex-column">
            @if ($user->role === 'staff')
                <a class="nav-link {{ request()->routeIs('staff.requests.create') ? 'active' : '' }}" href="{{ route('staff.requests.create') }}">
                    <i class="bi bi-plus-square-fill"></i> Mohon Aset
                </a>
                <a class="nav-link {{ request()->routeIs('staff.requests.index') ? 'active' : '' }}" href="{{ route('staff.requests.index') }}">
                    <i class="bi bi-folder2-open"></i> Permohonan Saya
                </a>
                <a class="nav-link {{ request()->routeIs('staff.profile.*') ? 'active' : '' }}" href="{{ route('staff.profile.edit') }}">
                    <i class="bi bi-person-lines-fill"></i> Profil Saya
                </a>
            @endif

            @if ($user->role === 'admin')
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i> Papan Pemuka
                </a>
                <a class="nav-link {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}" href="{{ route('admin.requests.index') }}">
                    <i class="bi bi-card-list"></i> Senarai Permohonan
                </a>
                <a class="nav-link {{ request()->routeIs('admin.archive.*') ? 'active' : '' }}" href="{{ route('admin.archive.index') }}">
                    <i class="bi bi-archive-fill"></i> Arkib
                </a>
                <a class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                    <i class="bi bi-bar-chart-fill"></i> Laporan
                </a>
                <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people-fill"></i> Pengurusan Pengguna
                </a>
            @endif

            <a class="nav-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                <i class="bi bi-bell-fill"></i> Notifikasi
                @if ($unread > 0)
                    <span class="badge text-bg-danger ms-auto">{{ $unread }}</span>
                @endif
            </a>
        </nav>
    </div>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-logout w-100 rounded-3">
                <i class="bi bi-box-arrow-right me-2"></i> Log Keluar
            </button>
        </form>
    </div>
</aside>