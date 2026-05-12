@extends('layouts.app')

@push('styles')
<style>
    /*TOP NAVBAR*/
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 60px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        font-size: 18px;
        color: var(--green-dark);
    }

    .navbar-brand img {
        width: 36px;
        height: 36px;
    }

    .navbar-brand span {
        color: var(--yellow);
    }

    .navbar-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-masuk {
        padding: 10px 22px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        color: var(--green-dark);
        background: transparent;
        border: 1.5px solid #ddd;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-masuk:hover {
        border-color: var(--green);
        color: var(--green);
    }

    .btn-daftar {
        padding: 10px 22px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        color: var(--white);
        background: var(--green-dark);
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-daftar:hover {
        background: var(--green);
    }

    /* HERO */
    .hero {
        min-height: 80vh;
        background: linear-gradient(135deg, var(--green-dark) 0%, var(--green) 60%, #2a8a6a 100%);
        display: flex;
        align-items: center;
        padding: 120px 60px 80px;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.04) 1px, transparent 1px),
            radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
        background-size: 60px 60px;
    }

    /* GELEMBUNG-GELEMBUNG PUTIH*/
    .hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle, rgba(255, 255, 255, 0.04) 50px, transparent 50px);
        background-size: 120px 120px;
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        max-width: 620px;
    }

    .hero-content h1 {
        font-size: clamp(36px, 5vw, 56px);
        font-weight: 800;
        line-height: 1.15;
        color: var(--white);
        margin-bottom: 20px;
    }

    .hero-content h1 span {
        color: var(--yellow-light);
    }

    .hero-content p {
        font-size: 16px;
        line-height: 1.7;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 36px;
        max-width: 480px;
    }

    .btn-hero {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 16px 32px;
        border-radius: 10px;
        background: var(--yellow-light);
        color: var(--text-black);
        font-weight: 700;
        font-size: 16px;
        border: none;
        cursor: pointer;
        transition: all 0.25s;
        box-shadow: 0 4px 20px rgba(245, 166, 35, 0.4);
    }

    .btn-hero:hover {
        background: #e09515;
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(245, 166, 35, 0.5);
    }

    /* SECTION: UNTUK SIAPA - UMKM ATAU BUYER DESKRIPSI */
    .section-cards {
        padding: 85px 60px;
        background: var(--white);
    }

    .cards-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 28px;
        max-width: 1100px;
        margin: 0 auto;
    }

    .card-umkm {
        background: var(--green-dark);
        color: var(--white);
        border-radius: 20px;
        padding: 48px 40px;
        position: relative;
        overflow: hidden;
    }

    .card-buyer {
        background: var(--yellow-light);
        color: var(--text-black);
        border-radius: 20px;
        padding: 48px 40px;
        position: relative;
        overflow: hidden;
    }

    .card-umkm::before,
    .card-buyer::before {
        content: '';
        position: absolute;
        right: 20px;
        top: -40px;
        width: 150px;
        height: 150px;
        border-radius: 50%;
    }

    .card-umkm::after,
    .card-buyer::after {
        content: '';
        position: absolute;
        right: -60px;
        bottom: -60px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
    }

    .card-umkm::before,
    .card-umkm::after {
        background: rgba(255, 255, 255, 0.05);
    }

    .card-buyer::before,
    .card-buyer::after {
        background: rgba(0, 0, 0, 0.05);
    }

    .card-title {
        font-size: 24px;
        font-weight: 800;
        margin-bottom: 14px;
        position: relative;
        z-index: 1;
    }

    .card-desc {
        font-size: 14px;
        line-height: 1.7;
        opacity: 0.85;
        margin-bottom: 28px;
        position: relative;
        z-index: 1;
    }

    .card-list {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 10px;
        position: relative;
        z-index: 1;
    }

    .card-list li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 14px;
    }

    .card-list li::before {
        content: '✓';
        font-weight: 700;
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* SECTION: FITUR*/
    .section-fitur {
        padding: 85px;
        background: var(--gray-bg);
    }

    .section-fitur .container {
        max-width: 1100px;
        margin: 0 auto;
    }

    .fitur-label {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--green);
        margin-bottom: 12px;
    }

    .fitur-title {
        font-size: clamp(28px, 4vw, 40px);
        font-weight: 800;
        margin-bottom: 12px;
    }

    .fitur-desc {
        font-size: 15px;
        color: var(--text-muted);
        margin-bottom: 56px;
        max-width: 420px;
    }

    .fitur-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .fitur-card {
        background: var(--white);
        border-radius: 16px;
        padding: 25px 15px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        transition: all 0.25s;
        height: 242px;
    }

    .fitur-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
    }

    .fitur-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 20px;
        background: var(--green-light);
    }

    .fitur-card-title {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .fitur-card-desc {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.65;
        margin-bottom: 16px;
    }

    .fitur-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        color: var(--yellow-dark);
        background: var(--yellow-soft);
        padding: 4px 10px;
        border-radius: 20px;
    }

    .fitur-badge::before {
        content: '✦';
        font-size: 10px;
        color: #448AFF;
    }

    /* SECTION: BOTTOM */
    .section-cta {
        padding: 60px 60px;
        background: var(--green-darkmore);
        text-align: center;
    }

    .section-cta h2 {
        font-size: clamp(30px, 4vw, 48px);
        font-weight: 800;
        color: var(--white);
        margin-bottom: 16px;
    }

    .section-cta p {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 48px;
    }

    .cta-buttons {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .btn-cta-primary {
        padding: 16px 32px;
        border-radius: 10px;
        background: var(--yellow-light);
        color: var(--text-black);
        font-weight: 700;
        font-size: 15px;
        border: none;
        cursor: pointer;
        transition: all 0.25s;
        box-shadow: 0 4px 20px rgba(245, 166, 35, 0.3);
    }

    .btn-cta-primary:hover {
        background: #e09515;
        transform: translateY(-2px);
    }

    .btn-cta-outline {
        padding: 16px 32px;
        border-radius: 10px;
        background: transparent;
        color: var(--yellow-light);
        font-weight: 700;
        font-size: 15px;
        background: var(--white);
        cursor: pointer;
        transition: all 0.25s;
    }

    .btn-cta-outline:hover {
        background: rgba(245, 166, 35, 0.1);
        transform: translateY(-2px);
    }

    @media (max-width: 900px) {
        .navbar {
            padding: 16px 24px;
        }

        .hero {
            padding: 100px 24px 60px;
        }

        .section-cards,
        .section-fitur,
        .section-cta {
            padding: 60px 24px;
        }

        .cards-grid {
            grid-template-columns: 1fr;
        }

        .fitur-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 600px) {
        .fitur-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

{{-- NAVBAR --}}
<nav class="navbar">
    <a href="/" class="navbar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="UMKM SIAP"
            style="height: 52px; width: auto; object-fit: contain;">
    </a>
    <div class="navbar-actions">
       <a href="{{ route('login.umkm') }}" class="btn-masuk">Masuk</a>
<a href="{{ route('register.umkm') }}" class="btn-daftar">Daftar Gratis</a>
    </div>
</nav>

{{-- HERO --}}
<section class="hero">
    <div class="hero-circle hero-circle-1"></div>
    <div class="hero-circle hero-circle-2"></div>
    <div class="hero-circle hero-circle-3"></div>
    <div class="hero-content">
        <h1>Ekspor Produk Indonesia ke <span>Dunia Global</span></h1>
        <p>Platform AI pertama yang membantu UMKM Indonesia siap ekspor — dari penilaian kesiapan, riset pasar, hingga terhubung dengan pembeli internasional.</p>
        <a href="{{ route('register.umkm') }}" class="btn-hero">Mulai Ekspor Sekarang →</a>
    </div>
</section>

{{-- UNTUK SIAPA --}}
<section class="section-cards">
    <div class="cards-grid">
        <div class="card-umkm">
            <div class="card-title">Untuk UMKM Indonesia</div>
            <div class="card-desc">Siap ekspor ke pasar global? Platform kami membantu Anda dari nol sampai deal pertama dengan buyer internasional.</div>
            <ul class="card-list">
                <li>Penilaian kesiapan ekspor berbasis AI</li>
                <li>Riset pasar & rekomendasi negara target</li>
                <li>Katalog produk B2B untuk buyer global</li>
            </ul>
        </div>
        <div class="card-buyer">
            <div class="card-title">Untuk Buyer Internasional</div>
            <div class="card-desc">Cari produk berkualitas dari Indonesia? Temukan ribuan UMKM terverifikasi siap ekspor dengan kapasitas jelas.</div>
            <ul class="card-list">
                <li>Akses katalog 5.000+ produk verified</li>
                <li>Komunikasi langsung dengan eksportir</li>
                <li>Transparansi kapasitas & sertifikasi</li>
            </ul>
        </div>
    </div>
</section>

{{-- FITUR UNGGULAN --}}
<section class="section-fitur">
    <div class="container">
        <div class="fitur-label">Fitur Unggulan</div>
        <h2 class="fitur-title">Teknologi AI untuk Ekspor</h2>
        <p class="fitur-desc">4 Fitur utama yang saling terhubung untuk membawa UMKM Indonesia ke pasar global.</p>
        <div class="fitur-grid">
            <div class="fitur-card">
                <div class="fitur-icon">🔐</div>
                <div class="fitur-card-title">User Auth (JWT)</div>
                <div class="fitur-card-desc">UMKM dan Buyer mendaftar dengan role berbeda. Akses fitur disesuaikan dengan peran masing-masing.</div>
            </div>
            <div class="fitur-card">
                <div class="fitur-icon">📊</div>
                <div class="fitur-card-title">Export Readiness Assessment</div>
                <div class="fitur-card-desc">Kuis kesiapan ekspor yang dianalisis oleh Gemini AI untuk memberikan skor dan rekomendasi personal.</div>
                <span class="fitur-badge">Gemini AI</span>
            </div>
            <div class="fitur-card">
                <div class="fitur-icon">🌍</div>
                <div class="fitur-card-title">Market Intelligence</div>
                <div class="fitur-card-desc">Analisis pasar global menggunakan data ekspor dan insight Gemini AI untuk menentukan target negara.</div>
                <span class="fitur-badge">Gemini AI</span>
            </div>
            <div class="fitur-card">
                <div class="fitur-icon">🛒</div>
                <div class="fitur-card-title">B2B E-Commerce Catalog</div>
                <div class="fitur-card-desc">UMKM upload produk. Buyer browse dan kontak langsung. Platform katalog khusus ekspor impor.</div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="section-cta">
    <h2>Siap Mulai Ekspor Hari Ini?</h2>
    <p>Bergabung dengan ribuan UMKM Indonesia yang sudah ekspor ke pasar global.</p>
    <div class="cta-buttons">
<a href="{{ route('register.umkm') }}" class="btn-cta-primary">Daftar Sebagai UMKM →</a>
<a href="{{ route('register.buyer') }}" class="btn-cta-outline">Daftar Sebagai Buyer →</a>
    </div>
</section>

@endsection
