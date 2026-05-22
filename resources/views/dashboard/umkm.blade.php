@extends('layouts.app')

@push('styles')
<style>
    :root { --sidebar-w: 240px; }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        display: flex;
        min-height: 100vh;
        background: #f7f8fa;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ===== SIDEBAR (sama persis kayak assessment Flo) ===== */
    .sidebar {
        width: var(--sidebar-w);
        background: var(--white);
        border-right: 1px solid #e8ecef;
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 0; left: 0; bottom: 0;
        z-index: 100;
        padding: 28px 0 24px;
    }

    .sidebar-brand {
        display: flex; align-items: center; gap: 10px;
        padding: 0 24px 28px;
        border-bottom: 1px solid #e8ecef;
    }
    .sidebar-brand img { height: 36px; width: auto; object-fit: contain; }
    .sidebar-brand .role-badge {
        font-size: 10px; font-weight: 700;
        color: var(--white); background: var(--green);
        padding: 3px 8px; border-radius: 20px; letter-spacing: 0.5px;
    }

    .sidebar-section-label {
        font-size: 10px; font-weight: 700; letter-spacing: 1px;
        color: #aab0bb; text-transform: uppercase;
        padding: 20px 24px 8px;
    }

    .sidebar-nav { display: flex; flex-direction: column; gap: 2px; padding: 0 12px; }

    .nav-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 14px; border-radius: 10px;
        font-size: 14px; font-weight: 500; color: #555;
        cursor: pointer; text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }
    .nav-item:hover { background: #f0f2f5; color: var(--text-black); }
    .nav-item.active { background: var(--green-light); color: var(--green-dark); font-weight: 600; }
    .nav-item svg { width: 18px; height: 18px; flex-shrink: 0; }

    .sidebar-spacer { flex: 1; }

    .sidebar-user {
        margin: 0 12px;
        padding: 12px 14px;
        display: flex; align-items: center; gap: 10px;
        border-radius: 12px; background: #f7f8fa;
    }
    .user-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: linear-gradient(160deg, #0B6E5E, #13A085);
        color: white; display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 700; flex-shrink: 0;
    }
    .user-info .user-name { font-size: 13px; font-weight: 600; color: var(--text-black); }
    .user-info .user-sub  { font-size: 11px; color: #aab0bb; }

    .sidebar-logout {
        display: flex; align-items: center; gap: 8px;
        margin: 8px 12px 0;
        padding: 10px 14px; border-radius: 10px;
        font-size: 13px; color: #e54b4b; font-weight: 500;
        cursor: pointer; transition: background 0.15s;
        background: none; border: none; width: 100%; text-align: left;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .sidebar-logout:hover { background: #fff0f0; }

    /* ===== MAIN ===== */
    .main-content {
        margin-left: var(--sidebar-w);
        flex: 1;
        padding: 40px 48px;
        background: #F7FAF9;
    }

    .page-header { margin-bottom: 28px; }
    .page-header h1 { font-size: 26px; font-weight: 800; color: var(--text-black); margin-bottom: 6px; }
    .page-header p  { font-size: 14px; color: var(--text-muted); }
    .page-header p span { color: #0B6E5E; font-weight: 600; }

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

    .progress-card h2 { color: #fff; font-size: 20px; font-weight: bold; margin-bottom: 6px; }
    .progress-card p  { color: rgba(255,255,255,0.75); font-size: 12px; margin-bottom: 18px; }

    .progress-bar-wrap {
        background: rgba(255,255,255,0.2);
        border-radius: 10px; height: 8px; width: 280px; margin-bottom: 6px;
    }
    .progress-bar-fill { background: #F5B800; border-radius: 10px; height: 8px; width: 65%; }
    .progress-pct { color: rgba(255,255,255,0.8); font-size: 12px; margin-bottom: 18px; }

    .btn-assessment {
        background: #F5B800; color: #1A1A00;
        font-size: 12px; font-weight: bold;
        padding: 8px 18px; border-radius: 8px; border: none;
        cursor: pointer; text-decoration: none; display: inline-block;
        transition: opacity 0.15s; font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .btn-assessment:hover { opacity: 0.88; }

    .score-circle {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        background: rgba(255,255,255,0.11);
        border: 2px solid rgba(255,255,255,0.25);
        border-radius: 50%; width: 96px; height: 96px; flex-shrink: 0;
    }
    .score-num { color: #F5B800; font-size: 38px; font-weight: bold; line-height: 1; }
    .score-label { color: rgba(255,255,255,0.7); font-size: 11px; margin-top: 3px; }

    /* ===== TODO LIST ===== */
    .todo-card {
        background: #fff; border-radius: 14px;
        border: 1px solid #D8E5E2; overflow: hidden;
    }
    .todo-header {
        padding: 18px 22px; border-bottom: 1px solid #D8E5E2;
        color: #1A3530; font-size: 14px; font-weight: bold;
    }
    .todo-item {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 22px; border-bottom: 1px solid #F0F4F3;
    }
    .todo-item:last-child { border-bottom: none; }

    .check-done {
        width: 22px; height: 22px; background: #0B6E5E;
        border-radius: 7px; display: flex; align-items: center;
        justify-content: center; flex-shrink: 0;
        color: white; font-size: 11px; font-weight: bold;
    }
    .check-pending {
        width: 22px; height: 22px;
        border: 2px solid #D8E5E2; border-radius: 7px; flex-shrink: 0;
    }
    .todo-title-done { color: #A0B8B3; font-size: 13px; text-decoration: line-through; }
    .todo-title-pending { color: #1A3530; font-size: 13px; }
    .todo-sub { color: #7FA09A; font-size: 11px; margin-top: 2px; }
</style>
@endpush

@section('content')

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="UMKM SIAP">
        <span class="role-badge">UMKM</span>
    </div>

    <span class="sidebar-section-label">Menu Utama</span>
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard.umkm') }}" class="nav-item active">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="{{ route('umkm.assessment') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Assessment
        </a>
        <a href="#" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Market Intelligence
        </a>
        <a href="{{ route('umkm.catalog') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            Katalog B2B
        </a>
        <a href="{{ route('umkm.produk_saya') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Produk Saya
        </a>
    </nav>

    <div class="sidebar-spacer"></div>

    <span class="sidebar-section-label">Akun</span>
    <nav class="sidebar-nav">
        <a href="{{ route('umkm.profile') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Profil UMKM
        </a>
        <a href="#" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan
        </a>
    </nav>

    <div style="height: 12px"></div>

    @auth
    <a href="{{ route('umkm.profile') }}" class="sidebar-user" style="text-decoration:none;">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <div class="user-info">
            <div class="user-name">{{ Auth::user()->name }}</div>
            <div class="user-sub">
                @if(Auth::user()->provinsi)
                    UMKM · {{ Auth::user()->provinsi }}
                @else
                    UMKM
                @endif
            </div>
        </div>
    </a>
    @endauth

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="sidebar-logout">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Keluar
        </button>
    </form>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="page-header">
        <h1>Dashboard UMKM</h1>
        <p>
            Selamat datang kembali,
            @auth<span>{{ Auth::user()->name }}</span>@endauth!
            Lihat progres ekspor Anda.
        </p>
    </div>

    <!-- Progress Card -->
    <div class="progress-card">
        <div>
            <h2>Progres Kesiapan Ekspor Anda</h2>
            <p>Selesaikan langkah-langkah berikut untuk 100% siap ekspor</p>
            <div class="progress-bar-wrap">
                <div class="progress-bar-fill"></div>
            </div>
            <div class="progress-pct">65% Lengkap</div>
            <a href="{{ route('umkm.assessment') }}" class="btn-assessment">
                Lanjutkan Assessment →
            </a>
        </div>
        <div class="score-circle">
            <span class="score-num">65</span>
            <span class="score-label">Score</span>
        </div>
    </div>

    <!-- To-Do List -->
    <div class="todo-card">
        <div class="todo-header">To-Do List: Menuju 100% Siap Ekspor</div>

        <div class="todo-item">
            <div class="check-done">✓</div>
            <div>
                <div class="todo-title-done">Lengkapi profil UMKM</div>
                <div class="todo-sub">Produk, kapasitas, sertifikasi</div>
            </div>
        </div>

        <div class="todo-item">
            <div class="check-done">✓</div>
            <div>
                <div class="todo-title-done">Selesaikan assessment kesiapan ekspor</div>
                <div class="todo-sub">Score: 65/100</div>
            </div>
        </div>

        <div class="todo-item">
            <div class="check-pending"></div>
            <div>
                <div class="todo-title-pending">Upload minimal 3 produk ke katalog</div>
                <div class="todo-sub">1 produk diupload</div>
            </div>
        </div>

        <div class="todo-item">
            <div class="check-pending"></div>
            <div>
                <div class="todo-title-pending">Tambahkan sertifikasi (Halal, SNI, Organic)</div>
                <div class="todo-sub">Belum ada sertifikasi</div>
            </div>
        </div>

        <div class="todo-item">
            <div class="check-pending"></div>
            <div>
                <div class="todo-title-pending">Kontak minimal 2 buyer potensial</div>
                <div class="todo-sub">0 buyer dihubungi</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    @auth
    @if(session('jwt_token'))
        localStorage.setItem('token', '{{ session('jwt_token') }}');
        localStorage.setItem('user', JSON.stringify({
            name: '{{ Auth::user()->name }}',
            role: '{{ Auth::user()->role }}',
            provinsi: '{{ Auth::user()->provinsi ?? '' }}'
        }));
    @endif
    @endauth
</script>
@endpush