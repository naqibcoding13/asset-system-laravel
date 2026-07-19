@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-blue: #1e3a8a;
        --accent-red: #dc2626;
        --soft-bg: #f8fafc;
    }

    .landing-body {
        background: var(--soft-bg);
        font-family: 'Inter', sans-serif;
    }

    /* HERO SECTION */
    .hero {
        background: linear-gradient(135deg, var(--primary-blue) 0%, #172554 60%, var(--accent-red) 100%);
        color: white;
        padding: 120px 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .hero::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
        opacity: 0.1;
    }

    .hero-logo {
        width: 140px;
        filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.3));
        margin-bottom: 25px;
        position: relative;
        z-index: 1;
        /* --- TAMBAHAN ANIMASI MODERN (LOAD) --- */
        animation: fadeInDown 1s ease-out;
    }

    .hero h1 {
        font-size: 3rem;
        letter-spacing: -1px;
        position: relative;
        z-index: 1;
        /* --- TAMBAHAN ANIMASI MODERN (LOAD) --- */
        animation: fadeInUp 1s ease-out 0.3s both;
    }

    .hero p {
        position: relative;
        z-index: 1;
        /* --- TAMBAHAN ANIMASI MODERN (LOAD) --- */
        animation: fadeInUp 1s ease-out 0.5s both;
    }

    .btn-login {
        background: var(--accent-red);
        color: white;
        font-weight: 600;
        border-radius: 50px;
        padding: 15px 40px;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);
        position: relative;
        z-index: 1;
        /* --- TAMBAHAN ANIMASI MODERN (LOAD) --- */
        animation: fadeInUp 1s ease-out 0.7s both;
    }

    .btn-login:hover {
        background: white;
        color: var(--accent-red);
        transform: translateY(-5px) scale(1.05); /* --- UPDATE HOVER MODERN --- */
        box-shadow: 0 8px 25px rgba(220, 38, 38, 0.5);
    }

    /* SECTION GENERAL */
    .section {
        padding: 80px 0;
    }

    .section-title {
        font-weight: 800;
        margin-bottom: 45px;
        text-align: center;
        position: relative;
        color: var(--primary-blue);
    }

    .section-title::after {
        content: "";
        width: 60px;
        height: 4px;
        background: var(--accent-red);
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        border-radius: 2px;
    }

    /* CARDS - VISI MISI */
    .info-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* --- UPDATE TRANSITION MODERN --- */
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .info-card:hover {
        transform: translateY(-15px); /* --- UPDATE HOVER MODERN --- */
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    /* --- TAMBAHAN HOVER MODERN PADA GAMBAR KAD --- */
    .info-card img {
        transition: transform 0.6s ease;
    }

    .info-card:hover img {
        transform: scale(1.05);
    }

    /* STAFF SCROLLBAR */
    .scroll-container {
        display: flex;
        overflow-x: auto;
        scroll-behavior: smooth;
        gap: 25px;
        padding: 30px 10px;
    }

    .scroll-container::-webkit-scrollbar {
        height: 6px;
    }

    .scroll-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .staff-card {
        width: 220px;
        flex: 0 0 auto;
        background: white;
        border-radius: 20px;
        padding: 20px;
        text-align: center;
        border-bottom: 4px solid var(--primary-blue);
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        position: relative;
        overflow: hidden;
    }

    .staff-card:hover {
        border-bottom-color: var(--accent-red);
        transform: translateY(-5px); /* --- UPDATE HOVER MODERN --- */
    }

    .staff-card img {
        width: 130px;
        height: 160px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 15px;
        cursor: zoom-in;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    /* --- TAMBAHAN HOVER MODERN PADA STAFF IMG --- */
    .staff-card:hover img {
        transform: scale(1.05);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }

    .staff-card h6 {
        font-size: 14px;
        color: var(--primary-blue);
        height: 40px;
        overflow: hidden;
        transition: color 0.3s ease;
    }

    .staff-card:hover h6 {
        color: var(--accent-red);
    }

    /* BUTTONS */
    .scroll-btn {
        background: white;
        color: var(--primary-blue);
        border: 1px solid #e2e8f0;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        z-index: 10;
    }

    .scroll-btn:hover {
        background: var(--primary-blue);
        color: white;
        transform: scale(1.1);
    }

    /* MODAL */
    .image-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        inset: 0;
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(6px);
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .image-modal img {
        max-width: 90vw;
        max-height: 90vh;
        width: auto;
        height: auto;
        object-fit: contain;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        animation: zoomIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); /* --- UPDATE ANIMASI MODAL --- */
    }

    .image-modal .close {
        position: absolute;
        top: 20px;
        right: 30px;
        font-size: 40px;
        color: white;
        cursor: pointer;
        z-index: 10000;
    }

    /* FOOTER */
    .footer {
        background: #0f172a;
        color: #94a3b8;
        padding: 30px;
        border-top: 3px solid var(--accent-red);
    }

    /* --- TAMBAHAN ANIMASI SCROLL REVEAL MODERN --- */
    .reveal {
        opacity: 0;
        transform: translateY(50px) scale(0.9); /* --- UPDATE KESAN MODERN --- */
        transition: all 1s cubic-bezier(0.22, 1, 0.36, 1);
        visibility: hidden;
    }

    .reveal.active {
        opacity: 1;
        transform: translateY(0) scale(1);
        visibility: visible;
    }
    /* ------------------------------ */

    /* --- KEYFRAMES BARU --- */
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>

<div class="landing-body">

    <section class="hero">
        <div class="container">
            <img src="{{ asset('images/logo.png') }}" class="hero-logo">
            <h1 class="fw-bold mb-2">Sistem Permohonan Aset</h1>
            <p class="lead opacity-75 mb-4">Pejabat Tanah & Jajahan Bachok</p>
            <a href="{{ route('login') }}" class="btn btn-login">
                LOG MASUK SISTEM
            </a>
        </div>
    </section>

    <section class="section reveal">
        <div class="container">
            <h2 class="section-title">Visi & Misi</h2>
            <div class="row g-5 text-center">
                <div class="col-md-6">
                    <div class="info-card p-3">
                        <img src="{{ asset('images/visi.jpeg') }}" class="img-fluid rounded shadow-sm mb-3">
                        <h5 class="fw-bold text-dark">PELAN STRATEGIK PEJABAT TANAH & JAJAHAN BACHOK 2025 - 2030</h5>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card p-3">
                        <img src="{{ asset('images/objektif.jpeg') }}" class="img-fluid rounded shadow-sm mb-3">
                        <h5 class="fw-bold text-dark">OBJEKTIF PEJABAT TANAH & JAJAHAN BACHOK</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section bg-white reveal">
        <div class="container">
            <h2 class="section-title">Pengurusan Atasan</h2>
            <div class="position-relative px-md-5">
                <button onclick="scrollStaffLeft()" class="scroll-btn left position-absolute top-50 start-0 translate-middle-y">❮</button>
                <div class="scroll-container" id="scrollContainer">
                    @php
                    $staff = [
                    ['img' => '1.jpg', 'name' => 'TUAN HAJI MOHD ZAKI BIN YUSOFF', 'role' => 'Ketua Jajahan'],
                    ['img' => '2.jpeg', 'name' => 'EN. HASANUL HARIZ BIN AB. LLAH ZAWAWI', 'role' => 'Timbalan Ketua Jajahan'],
                    ['img' => 'KOSONG.png', 'name' => 'KOSONG', 'role' => 'Ketua Penolong Ketua Jajahan (Khidmat Pengurusan)'],
                    ['img' => '4.jpeg', 'name' => 'PUAN MASLEZA BINTI MUHAMMAD', 'role' => 'Ketua Penolong Ketua Jajahan (Pembangunan)'],
                    ['img' => '5.png', 'name' => 'En. MOHD HAFIZIE BIN HASHIM', 'role' => 'Penolong Ketua Jajahan (Pembangunan & Pelupusan Tanah)'],
                    ['img' => '6.jpeg', 'name' => 'EN. AHMAD HUDZAIFAH BIN ABDUL HAKIM', 'role' => 'Penolong Ketua Jajahan (Pendaftaran)'],
                    ['img' => '7.png', 'name' => 'EN. AIMAN HAFIY BIN ABDULLAH', 'role' => 'Penolong Ketua Jajahan (Pembangunan Masyarakat)'],
                    ['img' => 'KOSONG.png', 'name' => 'KOSONG', 'role' => 'Pegawai Pembangunan Sosio Ekonomi'],
                    ['img' => 'KOSONG.png', 'name' => 'KOSONG', 'role' => 'Penolong Ketua Jajahan (Pembangunan Fizikal)'],
                    ['img' => '10.jpeg', 'name' => 'EN. WAN AHMAD ZAEEM BIN WAN MOHAMAD', 'role' => 'Penolong Ketua Jajahan (Penguatkuasaan Teknikal & Hasil)'],
                    ['img' => '11.jpeg', 'name' => 'EN. WAN EDY MARWAN BIN WAN ABDULLAH', 'role' => 'Juruukur']
                    ];
                    @endphp

                    @foreach($staff as $s)
                    <div class="staff-card">
                        <img src="{{ asset('images/'.$s['img']) }}" onclick="openModal(this.src)" class="cursor-pointer">
                        <h6 class="fw-bold">{{ $s['name'] }}</h6>
                        <small class="text-muted d-block mt-1">{{ $s['role'] }}</small>
                    </div>
                    @endforeach
                </div>
                <button onclick="scrollStaffRight()" class="scroll-btn right position-absolute top-50 end-0 translate-middle-y">❯</button>
            </div>
        </div>
    </section>

    <section class="section reveal">
        <div class="container text-center">
            <h2 class="section-title">Carta Organisasi</h2>
            <div class="info-card d-inline-block p-2">
                <img src="{{ asset('images/carta.jpeg') }}" class="img-fluid rounded-4">
            </div>
        </div>
    </section>

    <section class="section bg-white reveal">
        <div class="container">
            <h2 class="section-title">Video Rasmi</h2>
            <div class="ratio ratio-16x9 shadow-lg rounded-4 overflow-hidden border border-5 border-white">
                <iframe src="https://www.youtube.com/embed/bEsMg5i4XbI" allowfullscreen></iframe>
            </div>
        </div>
    </section>

    <div id="imageModal" class="image-modal" onclick="closeModal(event)">
        <span class="close text-white" onclick="closeModal(event)">&times;</span>
        <img id="modalImage" class="modal-content animate__animated animate__zoomIn">
    </div>

    <div class="footer text-center">
        <p class="mb-0">&copy; 2024-2026 <strong>Pejabat Tanah & Jajahan Bachok</strong>. Hak Cipta Terpelihara.</p>
    </div>

</div>

<script>
    function scrollStaffLeft() {
        document.getElementById('scrollContainer').scrollBy({
            left: -300,
            behavior: 'smooth'
        });
    }

    function scrollStaffRight() {
        document.getElementById('scrollContainer').scrollBy({
            left: 300,
            behavior: 'smooth'
        });
    }

    function openModal(src) {
        const modal = document.getElementById("imageModal");
        const img = document.getElementById("modalImage");

        modal.style.display = "flex";
        img.src = src;
    }

    function closeModal(event) {
        const modal = document.getElementById("imageModal");
        if (!event || event.target.id === "imageModal" || event.target.classList.contains("close")) {
            modal.style.display = "none";
        }
    }

    /* --- TAMBAHAN LOGIK SCROLL REVEAL MODERN (MENGGUNAKAN OBSERVER) --- */
    function reveal() {
        var reveals = document.querySelectorAll(".reveal");
        
        // Guna IntersectionObserver untuk performance lebih baik daripada scroll event
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add("active");
                }
            });
        }, {
            threshold: 0.15 // Trigger bila 15% elemen masuk skrin
        });

        reveals.forEach(function(reveal) {
            observer.observe(reveal);
        });
    }

    // Jalankan reveal bila DOM sedia
    document.addEventListener("DOMContentLoaded", reveal);
</script>
@endsection
