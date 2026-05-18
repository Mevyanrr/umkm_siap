@extends('layouts.app')

@push('styles')
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        background: #F7FAF9;
        display: flex;
        min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
        width: 224px;
        flex-shrink: 0;
        background: #fff;
        border-right: 1px solid #E8F0EE;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
    }

    .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 24px 24px 20px;
        border-bottom: 1px solid #E8F0EE;
    }

    .sidebar-logo img {
        height: 34px;
        width: auto;
        object-fit: contain;
    }

    .sidebar-logo .badge {
        background: #E6F5F2;
        color: #0B6E5E;
        font-size: 10px;
        font-weight: bold;
        padding: 2px 8px;
        border-radius: 10px;
        white-space: nowrap;
    }

    .sidebar-nav {
        flex: 1;
        padding: 20px 12px;
        display: flex;
        flex-direction: column;
    }

    .nav-label {
        color: #7FA09A;
        font-size: 10px;
        font-weight: bold;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        padding-left: 12px;
    }

    .nav-item {
        display: block;
        color: #3D6B63;
        font-size: 13.5px;
        text-decoration: none;
        padding: 9px 12px;
        border-radius: 10px;
        margin-bottom: 2px;
        transition: background 0.15s;
    }

    .nav-item:hover {
        background: #F0FAF7;
    }

    .nav-item.active {
        background: #E6F5F2;
        color: #0B6E5E;
        font-weight: 600;
    }

    .nav-spacer {
        margin-bottom: 16px;
    }

    /* ===== USER INFO ===== */
    .sidebar-user {
        padding: 14px 12px;
        border-top: 1px solid #E8F0EE;
    }

    .user-card {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #F7FAF9;
        border-radius: 12px;
        padding: 10px 12px;
        margin-bottom: 6px;
    }

    .user-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(160deg, #0B6E5E, #13A085);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 13px;
        font-weight: bold;
        flex-shrink: 0;
    }

    .user-name {
        color: #1A3530;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 120px;
    }

    .user-role {
        color: #7FA09A;
        font-size: 11px;
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 120px;
    }

    .btn-logout {
        display: block;
        width: 100%;
        background: none;
        border: none;
        color: #7FA09A;
        font-size: 13px;
        text-align: left;
        padding: 8px 12px;
        border-radius: 10px;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .btn-logout:hover {
        background: #FFF0F0;
        color: #E24B4A;
    }

    /* ===== MAIN ===== */
    .main {
        margin-left: 224px;
        flex: 1;
        padding: 36px 40px;
        background: #F7FAF9;
    }

    .page-title {
        color: #1A3530;
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 4px;
    }

    .page-sub {
        color: #7FA09A;
        font-size: 13.5px;
        margin-bottom: 28px;
    }

    .page-sub span {
        color: #0B6E5E;
        font-weight: 600;
    }

    /* ===== PROGRESS CARD ===== */
    .progress-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #084D42 0%, #0D8F7C 100%);
        border-radius: 18px;
        padding: 28px 32px;
        margin-bottom: 24px;
    }

    .progress-card h2 {
        color: #fff;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 6px;
    }

    .progress-card p {
        color: rgba(255,255,255,0.7);
        font-size: 12px;
        margin-bottom: 18px;
    }

    .progress-bar-wrap {
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        height: 8px;
        width: 260px;
        margin-bottom: 6px;
    }

    .progress-bar-fill {
        background: #F5B800;
        border-radius: 10px;
        height: 8px;
        width: 65%;
    }

    .progress-pct {
        color: rgba(255,255,255,0.8);
        font-size: 12px;
        margin-bottom: 18px;
    }

    .btn-assessment {
        background: #F5B800;
        color: #1A1A00;
        font-size: 12px;
        font-weight: bold;
        padding: 8px 18px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: opacity 0.15s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .btn-assessment:hover {
        opacity: 0.88;
    }

    .score-circle {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.11);
        border: 2px solid rgba(255,255,255,0.25);
        border-radius: 50%;
        width: 96px;
        height: 96px;
        flex-shrink: 0;
    }

    .score-num {
        color: #F5B800;
        font-size: 38px;
        font-weight: bold;
        line-height: 1;
    }

    .score-label {
        color: rgba(255,255,255,0.7);
        font-size: 11px;
        margin-top: 3px;
    }

    /* ===== TODO LIST ===== */
    .todo-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #D8E5E2;
        overflow: hidden;
    }

    .todo-header {
        padding: 18px 22px;
        border-bottom: 1px solid #D8E5E2;
        color: #1A3530;
        font-size: 14px;
        font-weight: bold;
    }

    .todo-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 22px;
        border-bottom: 1px solid #F0F4F3;
    }

    .todo-item:last-child {
        border-bottom: none;
    }

    .check-done {
        width: 22px;
        height: 22px;
        background: #0B6E5E;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: white;
        font-size: 11px;
        font-weight: bold;
    }

    .check-pending {
        width: 22px;
        height: 22px;
        border: 2px solid #D8E5E2;
        border-radius: 7px;
        flex-shrink: 0;
    }

    .todo-title-done {
        color: #A0B8B3;
        font-size: 13px;
        text-decoration: line-through;
    }

    .todo-title-pending {
        color: #1A3530;
        font-size: 13px;
    }

    .todo-sub {
        color: #7FA09A;
        font-size: 11px;
        margin-top: 2px;
    }
</style>
@endpush

@section('content')

<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('images/logo.png') }}" alt="UMKM SIAP"/>
        <span class="badge">UMKM</span>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-label">Menu Utama</span>

        <a href="{{ route('dashboard.umkm') }}" class="nav-item active">
            Dashboard
        </a>

        <a href="#" class="nav-item">
            Assessment
        </a>

        <a href="#" class="nav-item">
            Market Intelligence
        </a>

        <a href="#" class="nav-item">
            Katalog B2B
        </a>

        <a href="#" class="nav-item nav-spacer">
            Produk Saya
        </a>

        <span class="nav-label">Akun</span>

        <a href="#" class="nav-item">
            Profil UMKM
        </a>

        <a href="#" class="nav-item">
            Pengaturan
        </a>
    </nav>

    <div class="sidebar-user">

        @auth
        <div class="user-card">

            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>

            <div style="min-width: 0;">

                <div class="user-name">
                    {{ Auth::user()->name }}
                </div>

                <div class="user-role">
                    @if(Auth::user()->provinsi)
                        UMKM · {{ Auth::user()->provinsi }}
                    @else
                        UMKM
                    @endif
                </div>

            </div>
        </div>
        @endauth

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="btn-logout">
                Keluar
            </button>
        </form>

    </div>
</aside>

<main class="main">

    <div class="page-title">
        Dashboard UMKM
    </div>

    <div class="page-sub">
        Selamat datang kembali,

        @auth
            <span>{{ Auth::user()->name }}</span>
        @endauth

        ! Lihat progres ekspor Anda.
    </div>

    <div class="progress-card">

        <div>
            <h2>Progres Kesiapan Ekspor Anda</h2>

            <p>
                Selesaikan langkah-langkah berikut untuk 100% siap ekspor
            </p>

            <div class="progress-bar-wrap">
                <div class="progress-bar-fill"></div>
            </div>

            <div class="progress-pct">
                65% Lengkap
            </div>

            <a href="#" class="btn-assessment">
                Lanjutkan Assessment →
            </a>
        </div>

        <div class="score-circle">
            <span class="score-num">65</span>
            <span class="score-label">Score</span>
        </div>

    </div>

    <div class="todo-card">

        <div class="todo-header">
            To-Do List: Menuju 100% Siap Ekspor
        </div>

        <div class="todo-item">
            <div class="check-done">✓</div>

            <div>
                <div class="todo-title-done">
                    Lengkapi profil UMKM
                </div>

                <div class="todo-sub">
                    Produk, kapasitas, sertifikasi
                </div>
            </div>
        </div>

        <div class="todo-item">
            <div class="check-done">✓</div>

            <div>
                <div class="todo-title-done">
                    Selesaikan assessment kesiapan ekspor
                </div>

                <div class="todo-sub">
                    Score: 65/100
                </div>
            </div>
        </div>

        <div class="todo-item">
            <div class="check-pending"></div>

            <div>
                <div class="todo-title-pending">
                    Upload minimal 3 produk ke katalog
                </div>

                <div class="todo-sub">
                    1 produk diupload
                </div>
            </div>
        </div>

        <div class="todo-item">
            <div class="check-pending"></div>

            <div>
                <div class="todo-title-pending">
                    Tambahkan sertifikasi (Halal, SNI, Organic)
                </div>

                <div class="todo-sub">
                    Belum ada sertifikasi
                </div>
            </div>
        </div>

        <div class="todo-item">
            <div class="check-pending"></div>

            <div>
                <div class="todo-title-pending">
                    Kontak minimal 2 buyer potensial
                </div>

                <div class="todo-sub">
                    0 buyer dihubungi
                </div>
            </div>
        </div>

    </div>

</main>

@endsection