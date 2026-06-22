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
        opacity: 0.25; 
    }

    /* Card Styling */
    .notification-item {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(5px);
    }

    .notification-item:hover {
        transform: translateX(5px);
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-left-color: #0d6efd; /* Warna biru bila hover */
    }

    .notif-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        flex-shrink: 0;
    }

    .time-stamp {
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .page-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
</style>

<div class="hero-strip mb-4">
    <h2 class="fw-bold mb-1">
        <i class="bi bi-bell-fill me-2"></i>
        {{ auth()->user()->role === 'admin' ? 'Notifikasi Admin' : 'Notifikasi Staff' }}
    </h2>
    <p class="mb-0 opacity-75">Semua notifikasi berkaitan akaun dan permohonan anda.</p>
</div>

<div class="page-card p-4 bg-white shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0 text-dark">Notifikasi Terkini</h5>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary rounded-pill px-3">{{ $notifications->count() }} Mesej</span>
            @if ($notifications->isNotEmpty())
                <form
                    method="POST"
                    action="{{ route('notifications.destroy-all') }}"
                    class="js-confirm-form"
                    data-confirm-variant="danger"
                    data-confirm-title="Padam Semua Notifikasi?"
                    data-confirm-message="Semua mesej notifikasi untuk akaun anda sahaja akan dipadam. Notifikasi akaun lain tidak akan terkesan."
                    data-confirm-button="Ya, padam semua"
                >
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash3 me-1"></i> Padam Semua
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="notification-container">
        @forelse ($notifications as $notification)
            <div class="notification-item p-3 mb-3 border rounded-3 d-flex align-items-start gap-3">
                <div class="notif-icon">
                    <i class="bi bi-chat-left-text"></i>
                </div>
                
                <div class="flex-grow-1">
                    <div class="text-dark mb-1" style="line-height: 1.5;">
                        {{ $notification->message }}
                    </div>
                    <div class="time-stamp text-muted">
                        <i class="bi bi-clock"></i>
                        {{ $notification->created_at?->diffForHumans() ?? 'Baru sahaja' }}
                        <span class="mx-1 text-opacity-25">|</span>
                        <span style="font-size: 0.7rem;">{{ $notification->created_at?->format('d M Y, h:i A') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <div class="mb-3 opacity-25">
                    <i class="bi bi-bell-slash" style="font-size: 4rem;"></i>
                </div>
                <h6 class="text-muted fw-normal">Tiada notifikasi buat masa ini.</h6>
                <p class="small text-muted opacity-75">Kami akan maklumkan jika ada perkembangan baru.</p>
            </div>
        @endforelse
    </div>
</div>

<p class="text-center mt-4 small text-muted">
    <i class="bi bi-shield-check me-1"></i> Notifikasi ini dijana secara automatik oleh sistem.
</p>

@endsection
