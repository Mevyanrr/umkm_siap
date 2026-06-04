@extends('layouts.app')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --sidebar-w: 240px;
        --green:       #0B6E5E;
        --green-dark:  #084D42;
        --green-light: #E8F5F2;
        --gold:        #F5B800;
        --white:       #ffffff;
        --bg:          #F4F7F6;
        --text:        #1A2E2B;
        --muted:       #7A9590;
        --border:      #D8E5E2;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        display: flex;
        min-height: 100vh;
        background: var(--bg);
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--text);
    }

    /* ─── SIDEBAR ─── */
    .sidebar {
        width: var(--sidebar-w);
        background: var(--white);
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        position: fixed;
        inset: 0 auto 0 0;
        z-index: 100;
        padding: 24px 0;
    }

    .sidebar-brand {
        display: flex; align-items: center; gap: 10px;
        padding: 0 20px 20px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 8px;
    }
    .sidebar-brand img { height: 34px; width: auto; object-fit: contain; }
    .role-badge {
        font-size: 10px; font-weight: 700; letter-spacing: 0.4px;
        color: var(--white); background: var(--green);
        padding: 3px 8px; border-radius: 20px;
    }

    .sidebar-section {
        font-size: 10px; font-weight: 700; letter-spacing: 1px;
        color: #B0BEC5; text-transform: uppercase;
        padding: 16px 20px 6px;
    }

    .sidebar-nav { display: flex; flex-direction: column; gap: 2px; padding: 0 10px; }

    .nav-item {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 12px; border-radius: 9px;
        font-size: 13.5px; font-weight: 500; color: #607D8B;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }
    .nav-item:hover  { background: #F0F4F3; color: var(--text); }
    .nav-item.active { background: var(--green-light); color: var(--green); font-weight: 700; }
    .nav-item svg    { width: 17px; height: 17px; flex-shrink: 0; }

    .sidebar-spacer { flex: 1; }

    .sidebar-user {
        margin: 0 10px 4px;
        padding: 10px 12px;
        display: flex; align-items: center; gap: 10px;
        border-radius: 10px; background: var(--bg);
        text-decoration: none;
    }
    .user-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        background: linear-gradient(145deg, #0B6E5E, #13A085);
        color: white; display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; flex-shrink: 0;
    }
    .user-name { font-size: 13px; font-weight: 600; color: var(--text); }
    .user-sub  { font-size: 11px; color: var(--muted); }

    .sidebar-logout {
        display: flex; align-items: center; gap: 8px;
        margin: 0 10px;
        padding: 9px 12px; border-radius: 9px;
        font-size: 13px; color: #E53935; font-weight: 500;
        background: none; border: none; width: calc(100% - 20px);
        cursor: pointer; font-family: inherit;
        transition: background 0.15s;
    }
    .sidebar-logout:hover { background: #FFF3F3; }

    /* ─── MAIN ─── */
    .main {
        margin-left: var(--sidebar-w);
        flex: 1;
        padding: 36px 44px;
    }

    .page-title   { font-size: 24px; font-weight: 800; margin-bottom: 4px; }
    .page-subtitle { font-size: 13.5px; color: var(--muted); margin-bottom: 28px; }
    .page-subtitle strong { color: var(--green); font-weight: 700; }

    /* ─── PROGRESS CARD ─── */
    .progress-card {
        display: flex; justify-content: space-between; align-items: center; gap: 24px;
        background: linear-gradient(135deg, #084D42 0%, #0E9E88 100%);
        border-radius: 20px; padding: 28px 32px; margin-bottom: 24px;
    }
    .pc-left { flex: 1; }
    .pc-heading { color: #fff; font-size: 18px; font-weight: 800; margin-bottom: 4px; }
    .pc-sub     { color: rgba(255,255,255,0.65); font-size: 12px; margin-bottom: 16px; }

    .pbar-wrap { background: rgba(255,255,255,0.2); border-radius: 8px; height: 7px; width: 300px; margin-bottom: 6px; }
    .pbar-fill { background: var(--gold); border-radius: 8px; height: 7px; transition: width 0.6s ease; }
    .pbar-pct  { color: rgba(255,255,255,0.75); font-size: 11.5px; margin-bottom: 18px; }

    .btn-start {
        display: inline-block; text-decoration: none;
        background: var(--gold); color: #1A1000;
        font-size: 12px; font-weight: 700;
        padding: 8px 18px; border-radius: 8px;
        transition: opacity 0.15s;
    }
    .btn-start:hover { opacity: 0.85; }

    /* Score circle */
    .score-circle {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        background: rgba(255,255,255,0.1);
        border: 2px solid rgba(255,255,255,0.22);
        border-radius: 50%; width: 100px; height: 100px; flex-shrink: 0;
    }
    .score-num   { color: var(--gold); font-size: 40px; font-weight: 800; line-height: 1; }
    .score-label { color: rgba(255,255,255,0.65); font-size: 11px; margin-top: 3px; letter-spacing: 0.5px; }

    /* ─── TODO CARD ─── */
    .todo-card {
        background: var(--white); border-radius: 16px;
        border: 1px solid var(--border); overflow: hidden;
    }
    .todo-hdr {
        padding: 16px 22px; border-bottom: 1px solid var(--border);
        font-size: 14px; font-weight: 700; color: var(--text);
    }

    .todo-item {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 22px; border-bottom: 1px solid #EEF3F2;
        transition: background 0.12s;
    }
    .todo-item:last-child { border-bottom: none; }
    .todo-item:hover { background: #F9FBFB; }

    .chk-done {
        width: 22px; height: 22px; background: var(--green);
        border-radius: 7px; display: flex; align-items: center;
        justify-content: center; flex-shrink: 0;
        color: white; font-size: 12px; font-weight: 700;
    }
    .chk-open {
        width: 22px; height: 22px;
        border: 2px solid var(--border); border-radius: 7px; flex-shrink: 0;
    }
    .todo-ttl-done { color: #A8BEBB; font-size: 13px; text-decoration: line-through; }
    .todo-ttl      { color: var(--text); font-size: 13px; font-weight: 600; }
    .todo-sub      { color: var(--muted); font-size: 11.5px; margin-top: 2px; }
</style>
@endpush

@section('content')

{{-- ── SIDEBAR ── --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="UMKM SIAP">
        <span class="role-badge">UMKM</span>
    </div>

    <span class="sidebar-section">Menu Utama</span>
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard.umkm') }}" class="nav-item active">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="{{ route('umkm.assessment') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Assessment
        </a>
        <a href="/umkm/market" class="nav-item">
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

    <span class="sidebar-section">Akun</span>
    <nav class="sidebar-nav">
        <a href="{{ route('umkm.profile') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Profil UMKM
        </a>
        <a href="#" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan
        </a>
    </nav>

    <div style="height:10px"></div>

    @auth
    <a href="{{ route('umkm.profile') }}" class="sidebar-user">
        <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
        <div>
            <div class="user-name">{{ Auth::user()->name }}</div>
            <div class="user-sub">UMKM</div>
        </div>
    </a>
    @endauth

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="sidebar-logout">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Keluar
        </button>
    </form>
</aside>

{{-- ── MAIN ── --}}
<div class="main">

    <h1 class="page-title">Dashboard UMKM</h1>
    <p class="page-subtitle">
        Selamat datang kembali,
        @auth<strong>{{ Auth::user()->name }}</strong>@endauth!
        Pantau progres ekspor kamu di sini.
    </p>

    {{-- Progress Card --}}
    <div class="progress-card">
        <div class="pc-left">
            <div class="pc-heading">Progres Kesiapan Ekspor</div>
            <div class="pc-sub">Selesaikan semua langkah untuk 100% siap ekspor</div>
            <div class="pbar-wrap">
                <div class="pbar-fill" style="width: {{ $todoProgress['pct'] }}%"></div>
            </div>
            <div class="pbar-pct">{{ $todoProgress['pct'] }}% Lengkap — {{ $todoProgress['done'] }}/{{ $todoProgress['total'] }} selesai</div>
            <a href="{{ route('umkm.assessment') }}" class="btn-start">
                {{ $todoProgress['assessment_selesai'] ? 'Lihat Hasil Assessment →' : 'Mulai Assessment →' }}
            </a>
        </div>
        <div class="score-circle">
            <span class="score-num">{{ $score }}</span>
            <span class="score-label">Score</span>
        </div>
    </div>

    {{-- To-Do --}}
    <div class="todo-card">
        <div class="todo-hdr">To-Do List: Menuju 100% Siap Ekspor</div>

        {{-- 1. Profil --}}
        <div class="todo-item">
            @if($todoProgress['profil_lengkap'])
                <div class="chk-done">✓</div>
                <div>
                    <div class="todo-ttl-done">Lengkapi profil UMKM</div>
                    <div class="todo-sub">Nama, email, dan nomor HP sudah terisi</div>
                </div>
            @else
                <div class="chk-open"></div>
                <div>
                    <div class="todo-ttl">Lengkapi profil UMKM</div>
                    <div class="todo-sub">Isi nama, email, dan nomor HP kamu</div>
                </div>
            @endif
        </div>

        {{-- 2. Assessment --}}
        <div class="todo-item">
            @if($todoProgress['assessment_selesai'])
                <div class="chk-done">✓</div>
                <div>
                    <div class="todo-ttl-done">Selesaikan assessment kesiapan ekspor</div>
                    <div class="todo-sub">Skor: {{ $score }}/100</div>
                </div>
            @else
                <div class="chk-open"></div>
                <div>
                    <div class="todo-ttl">Selesaikan assessment kesiapan ekspor</div>
                    <div class="todo-sub">Belum ada assessment</div>
                </div>
            @endif
        </div>

        {{-- 3. Produk — minimal 3 --}}
        <div class="todo-item">
            @if($todoProgress['produk_cukup'])
                <div class="chk-done">✓</div>
                <div>
                    <div class="todo-ttl-done">Upload minimal 3 produk ke katalog</div>
                    <div class="todo-sub">{{ $todoProgress['produk_count'] }} produk diupload</div>
                </div>
            @else
                <div class="chk-open"></div>
                <div>
                    <div class="todo-ttl">Upload minimal 3 produk ke katalog</div>
                    <div class="todo-sub">{{ $todoProgress['produk_count'] }} dari 3 produk diupload</div>
                </div>
            @endif
        </div>

        {{-- 4. Sertifikasi --}}
        <div class="todo-item">
            @if($todoProgress['ada_sertifikasi'])
                <div class="chk-done">✓</div>
                <div>
                    <div class="todo-ttl-done">Tambahkan sertifikasi (Halal / SNI)</div>
                    <div class="todo-sub">Sertifikasi sudah ada</div>
                </div>
            @else
                <div class="chk-open"></div>
                <div>
                    <div class="todo-ttl">Tambahkan sertifikasi (Halal / SNI)</div>
                    <div class="todo-sub">Jawab pertanyaan Halal & SNI di assessment</div>
                </div>
            @endif
        </div>

        {{-- 5. Buyer --}}
        <div class="todo-item">
            @if($todoProgress['ada_buyer'])
                <div class="chk-done">✓</div>
                <div>
                    <div class="todo-ttl-done">Kontak minimal 2 buyer potensial</div>
                    <div class="todo-sub">Sudah memiliki buyer potensial</div>
                </div>
            @else
                <div class="chk-open"></div>
                <div>
                    <div class="todo-ttl">Kontak minimal 2 buyer potensial</div>
                    <div class="todo-sub">Jawab pertanyaan buyer potensial di assessment</div>
                </div>
            @endif
        </div>

    </div>
</div>

@endsection

@auth
    @if(session('jwt_token'))
        @push('scripts')
        <script>
            localStorage.setItem('token', '{{ session("jwt_token") }}');
            localStorage.setItem('user', JSON.stringify({
                name: '{{ Auth::user()->name }}',
                role: '{{ Auth::user()->role }}'
            }));
        </script>
        @endpush
    @endif
@endauth