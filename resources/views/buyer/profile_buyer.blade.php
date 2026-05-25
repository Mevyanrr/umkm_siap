@extends('layouts.app')

@push('styles')
<style>
    :root { --sidebar-w: 240px; }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        display: flex;
        min-height: 100vh;
        background: #F7FAF9;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* SIDEBAR */
    .sidebar {
        width: var(--sidebar-w);
        background: #fff;
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
        color: #fff; background: #0B6E5E;
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
    .nav-item:hover { background: #f0f2f5; color: #1A3530; }
    .nav-item.active { background: #E6F5F2; color: #0B6E5E; font-weight: 600; }
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
    .user-info .user-name { font-size: 13px; font-weight: 600; color: #1A3530; }
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

    /* MAIN */
    .main-content {
        margin-left: var(--sidebar-w);
        flex: 1;
        padding: 40px 48px;
        max-width: 800px;
    }

    .page-title { font-size: 24px; font-weight: 800; color: #1A3530; margin-bottom: 4px; }
    .page-sub   { font-size: 14px; color: #7FA09A; margin-bottom: 32px; }

    .alert-success {
        background: #E6F5F2; color: #0B6E5E;
        border: 1px solid #B2DDD5; border-radius: 10px;
        padding: 12px 18px; margin-bottom: 24px;
        font-size: 13px; font-weight: 600;
    }
    .alert-error {
        background: #FFF0F0; color: #e54b4b;
        border: 1px solid #f5b8b8; border-radius: 10px;
        padding: 12px 18px; margin-bottom: 24px;
        font-size: 13px; font-weight: 600;
    }

    /* AVATAR CARD */
    .avatar-card {
        background: white; border-radius: 14px;
        border: 1px solid #D8E5E2; padding: 28px;
        display: flex; align-items: center; gap: 24px;
        margin-bottom: 20px;
    }
    .avatar-big {
        width: 72px; height: 72px; border-radius: 50%;
        background: linear-gradient(160deg, #0B6E5E, #13A085);
        color: white; display: flex; align-items: center; justify-content: center;
        font-size: 28px; font-weight: 700; flex-shrink: 0;
    }
    .avatar-info h2 { font-size: 18px; font-weight: 700; color: #1A3530; margin-bottom: 4px; }
    .avatar-info p  { font-size: 13px; color: #7FA09A; }

    /* FORM CARD */
    .form-card {
        background: white; border-radius: 14px;
        border: 1px solid #D8E5E2; padding: 28px;
        margin-bottom: 20px;
    }
    .form-card-title {
        font-size: 15px; font-weight: 700; color: #1A3530;
        margin-bottom: 20px; padding-bottom: 16px;
        border-bottom: 1px solid #F0F4F3;
    }
    .form-row {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 16px; margin-bottom: 16px;
    }
    .form-group { margin-bottom: 16px; }
    .form-group:last-child { margin-bottom: 0; }
    .form-label {
        display: block; font-size: 13px; font-weight: 600;
        color: #1A3530; margin-bottom: 6px;
    }
    .form-input {
        width: 100%; border: 1px solid #D8E5E2;
        border-radius: 10px; padding: 10px 14px;
        font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif;
        color: #1A3530; outline: none; transition: border 0.15s; background: #fff;
    }
    .form-input:focus { border-color: #0B6E5E; }
    .form-input:disabled {
        background: #F7FAF9; color: #7FA09A; cursor: not-allowed;
    }
    .form-hint  { font-size: 11px; color: #7FA09A; margin-top: 4px; }
    .form-error { font-size: 11px; color: #e54b4b; margin-top: 4px; }

    .form-actions {
        display: flex; justify-content: flex-end;
        gap: 12px; margin-top: 24px;
    }
    .btn-save {
        background: #0B6E5E; color: white;
        font-size: 14px; font-weight: 700;
        padding: 11px 28px; border-radius: 10px;
        border: none; cursor: pointer; transition: opacity 0.15s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .btn-save:hover { opacity: 0.88; }
    .btn-cancel {
        background: none; border: 1px solid #D8E5E2;
        border-radius: 10px; padding: 11px 24px;
        font-size: 14px; font-weight: 600; color: #7FA09A;
        cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .btn-cancel:hover { background: #F7FAF9; }
</style>
@endpush

@section('content')

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="UMKM SIAP">
        <span class="role-badge">Buyer</span>
    </div>

    <span class="sidebar-section-label">Menu Utama</span>
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard.buyer') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
        <a href="{{ route('buyer.saved') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            Produk Tersimpan
        </a>
    </nav>

    <div class="sidebar-spacer"></div>

    <span class="sidebar-section-label">Akun</span>
    <nav class="sidebar-nav">
        <a href="{{ route('buyer.profile') }}" class="nav-item active">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Profil Saya
        </a>
        <a href="#" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Pengaturan
        </a>
    </nav>

    <div style="height: 12px"></div>

    @auth
    <a href="{{ route('buyer.profile') }}" class="sidebar-user" style="text-decoration:none;">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <div class="user-info">
            <div class="user-name">{{ Auth::user()->name }}</div>
            <div class="user-sub">Buyer</div>
        </div>
    </a>
    @endauth

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="sidebar-logout">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Keluar
        </button>
    </form>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <div class="page-title">Profil Saya</div>
    <div class="page-sub">Kelola informasi akun Anda.</div>

    @if(session('success'))
        <div class="alert-success">✓ {{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            @foreach($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    <!-- Avatar Card -->
    <div class="avatar-card">
        <div class="avatar-big">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="avatar-info">
            <h2>{{ $user->name }}</h2>
            <p>{{ $user->email }} · Buyer</p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('buyer.profile.update') }}">
        @csrf
        @method('PUT')

        <div class="form-card">
            <div class="form-card-title">Informasi Dasar</div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Pengusaha <span style="color:#e54b4b">*</span></label>
                    <input type="text" name="name" class="form-input"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email <span style="color:#e54b4b">*</span></label>
                    <input type="email" name="email" class="form-input"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Role</label>
                <input type="text" class="form-input" value="Buyer" disabled>
                <div class="form-hint">Role tidak dapat diubah.</div>
            </div>
        </div>
        
        <div class="form-card">
            <div class="form-card-title">Ganti Password</div>
            <div class="form-hint" style="margin-bottom:16px;">Kosongkan jika tidak ingin mengganti password.</div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-input"
                           placeholder="Minimal 6 karakter" autocomplete="new-password">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-input"
                           placeholder="Ulangi password baru" autocomplete="new-password">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('dashboard.buyer') }}" class="btn-cancel">Batal</a>
            <button type="submit" class="btn-save">Simpan Perubahan</button>
        </div>

    </form>
</div>

@endsection