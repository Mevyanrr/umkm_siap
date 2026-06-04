{{-- FILE: resources/views/dashboard/buyer.blade.php --}}

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

    /* ===== SIDEBAR ===== */
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

    /* ===== MAIN ===== */
    .main-content {
        margin-left: var(--sidebar-w);
        flex: 1;
        padding: 40px 48px;
    }

    /* ===== HEADER ===== */
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-size: 24px; font-weight: 800; color: #1A3530; margin-bottom: 4px; }
    .page-header p  { font-size: 14px; color: #7FA09A; }

    /* ===== SEARCH BAR ===== */
    .search-wrap {
        position: relative;
        margin-bottom: 28px;
    }
    .search-wrap svg {
        position: absolute;
        left: 16px; top: 50%;
        transform: translateY(-50%);
        color: #aab0bb;
        width: 18px; height: 18px;
    }
    .search-input {
        width: 100%;
        border: 1px solid #D8E5E2;
        border-radius: 12px;
        padding: 13px 16px 13px 46px;
        font-size: 14px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #1A3530;
        background: #fff;
        outline: none;
        transition: border 0.15s, box-shadow 0.15s;
    }
    .search-input:focus {
        border-color: #0B6E5E;
        box-shadow: 0 0 0 3px rgba(11,110,94,0.08);
    }
    .search-input::placeholder { color: #aab0bb; }

    /* ===== PRODUCT GRID ===== */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    @media (max-width: 1280px) { .product-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 960px)  { .product-grid { grid-template-columns: repeat(2, 1fr); } }

    /* ===== PRODUCT CARD ===== */
    .product-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #D8E5E2;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        cursor: pointer;
        transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
    }
    .product-card:hover {
        box-shadow: 0 8px 28px rgba(11,110,94,0.13);
        transform: translateY(-3px);
        border-color: rgba(11,110,94,0.22);
    }

    .card-image-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 4/3;
        overflow: hidden;
        background: linear-gradient(145deg, #C8EDE7 0%, #A0D8CF 100%);
    }
    .card-image-wrap img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 0.35s;
    }
    .product-card:hover .card-image-wrap img { transform: scale(1.04); }
    .card-image-placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        color: #7FA09A;
    }

    .card-badges {
        position: absolute;
        top: 10px; left: 10px;
        display: flex; gap: 6px; flex-wrap: wrap;
    }
    .badge {
        font-size: 10px; font-weight: 700;
        padding: 3px 9px; border-radius: 20px; letter-spacing: 0.3px;
    }
    .badge-sni     { background: #fff; color: #0B6E5E; border: 1px solid #B2DDD5; }
    .badge-halal   { background: #FFF7E6; color: #D97706; border: 1px solid #FBBF24; }
    .badge-iso     { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; }
    .badge-organic { background: #F0FDF4; color: #166534; border: 1px solid #BBF7D0; }
    .badge-default { background: #fff; color: #555; border: 1px solid #D8E5E2; }

    .card-body {
        padding: 14px 16px 16px;
        flex: 1; display: flex; flex-direction: column;
    }
    .card-name { font-size: 15px; font-weight: 700; color: #1A3530; margin-bottom: 3px; line-height: 1.3; }
    .card-company { font-size: 12px; color: #7FA09A; margin-bottom: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .card-capacity-label { font-size: 11px; color: #aab0bb; font-weight: 500; margin-bottom: 2px; }
    .card-capacity-value { font-size: 13px; font-weight: 700; color: #0B6E5E; margin-bottom: 14px; }

    .card-footer {
        display: flex; align-items: center;
        justify-content: space-between;
        padding-top: 4px;
    }

    /* ===== LOVE BUTTON ===== */
    .btn-love {
        background: #FFF5F5; border: none; cursor: pointer;
        padding: 8px; border-radius: 50%;
        transition: background 0.15s, transform 0.15s;
        display: flex; align-items: center; justify-content: center;
    }
    .btn-love:hover { background: #FFF0F0; transform: scale(1.12); }
    .btn-love svg {
        width: 20px; height: 20px;
        transition: fill 0.2s, stroke 0.2s;
        fill: none; stroke: #e54b4b; stroke-width: 2;
    }
    .btn-love.wishlisted svg { fill: #e54b4b !important; stroke: #e54b4b !important; }

    /* ===== KONTAK BUTTON ===== */
    .btn-kontak {
        background: #0B6E5E; color: white;
        font-size: 13px; font-weight: 700;
        padding: 9px 22px; border-radius: 10px; border: none;
        cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif;
        transition: background 0.15s; text-decoration: none;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .btn-kontak:hover { background: #13A085; }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        grid-column: 1 / -1;
        background: white; border-radius: 14px;
        border: 1px solid #D8E5E2; padding: 60px 40px;
        display: flex; flex-direction: column;
        align-items: center; text-align: center;
    }
    .empty-title { color: #1A3530; font-size: 16px; font-weight: bold; margin-bottom: 8px; }
    .empty-sub   { color: #7FA09A; font-size: 13px; max-width: 340px; line-height: 1.6; }

    /* ===== TOAST ===== */
    #toast {
        position: fixed; bottom: 28px; right: 28px;
        background: #1A3530; color: white;
        font-size: 13px; font-weight: 600;
        padding: 12px 20px; border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        opacity: 0; transform: translateY(12px);
        transition: opacity 0.25s, transform 0.25s;
        z-index: 999; pointer-events: none;
    }
    #toast.show { opacity: 1; transform: translateY(0); }

    /* ===== MODAL ===== */
    .modal-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(10, 30, 24, 0.55);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index: 200;
        align-items: center; justify-content: center;
        padding: 24px;
    }
    .modal-overlay.open { display: flex; }

    .modal {
        background: #fff;
        border-radius: 24px;
        max-width: 780px; width: 100%;
        max-height: 90vh; overflow: hidden;
        box-shadow: 0 32px 100px rgba(0,0,0,0.28), 0 0 0 1px rgba(255,255,255,0.08);
        animation: modalIn 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex; flex-direction: column;
    }
    @keyframes modalIn {
        from { opacity: 0; transform: translateY(24px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .modal-inner { display: flex; overflow: hidden; flex: 1; min-height: 0; }

    .modal-left {
        width: 280px; flex-shrink: 0; position: relative;
        background: linear-gradient(160deg, #0B6E5E 0%, #13A085 100%);
        display: flex; flex-direction: column;
    }
    .modal-left img { width: 100%; height: 220px; object-fit: cover; flex-shrink: 0; }
    .modal-left-placeholder {
        width: 100%; height: 220px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .modal-left-info { padding: 20px; flex: 1; }
    .modal-left-name { font-size: 17px; font-weight: 800; color: #fff; line-height: 1.3; margin-bottom: 6px; }
    .modal-left-seller {
        font-size: 12px; color: rgba(255,255,255,0.65);
        margin-bottom: 16px; display: flex; align-items: center; gap: 5px;
    }
    .modal-left-badges { display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 20px; }

    .modal-price-box {
        background: rgba(255,255,255,0.12); border-radius: 12px;
        padding: 12px 14px; border: 1px solid rgba(255,255,255,0.15);
    }
    .modal-price-label {
        font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.55);
        text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 3px;
    }
    .modal-price-value { font-size: 18px; font-weight: 800; color: #f4c430; }

    .modal-right { flex: 1; overflow-y: auto; padding: 24px 26px; display: flex; flex-direction: column; gap: 0; }
    .modal-right::-webkit-scrollbar { width: 4px; }
    .modal-right::-webkit-scrollbar-track { background: transparent; }
    .modal-right::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

    .modal-close-btn {
        position: absolute; top: 14px; right: 14px;
        width: 32px; height: 32px; border-radius: 50%;
        background: rgba(255,255,255,0.18); border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.18s; z-index: 10; color: #fff;
    }
    .modal-close-btn:hover { background: rgba(255,255,255,0.32); }

    .detail-section { margin-bottom: 18px; }
    .detail-section-label {
        font-size: 10px; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px;
    }
    .detail-section-value { font-size: 14px; font-weight: 600; color: #334155; line-height: 1.6; }
    .detail-section-value.accent { font-size: 16px; font-weight: 800; color: #0B6E5E; }

    .detail-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 18px; }
    .detail-box { background: #f8fafc; border-radius: 12px; padding: 12px 14px; border: 1px solid #f1f5f9; }

    .chip-row { display: flex; flex-wrap: wrap; gap: 6px; }
    .chip { font-size: 12px; font-weight: 600; padding: 5px 12px; border-radius: 20px; }
    .chip-country { background: #E6F5F2; border: 1px solid rgba(11,110,94,0.2); color: #0B6E5E; }
    .chip-cert    { background: #fff8e1; border: 1px solid rgba(244,196,48,0.3); color: #7a5c00; }

    .modal-photos { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 18px; }
    .modal-photo-thumb {
        width: 70px; height: 70px; border-radius: 10px; object-fit: cover;
        border: 2px solid #e2e8f0; cursor: pointer; transition: border-color 0.18s;
    }
    .modal-photo-thumb:hover { border-color: #0B6E5E; }

    .modal-action-bar {
        padding: 16px 26px; border-top: 1px solid #f1f5f9;
        display: flex; gap: 10px; background: #fff; flex-shrink: 0;
    }
    .btn-hubungi {
        flex: 1; padding: 13px; background: #0B6E5E; color: #fff;
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 700;
        border: none; border-radius: 12px; cursor: pointer; transition: background 0.18s;
        display: flex; align-items: center; justify-content: center;
        gap: 7px; text-decoration: none;
    }
    .btn-hubungi:hover { background: #13A085; }
    .btn-close-modal {
        padding: 13px 20px; background: #f1f5f9; color: #64748b;
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 600;
        border: none; border-radius: 12px; cursor: pointer; transition: background 0.18s;
    }
    .btn-close-modal:hover { background: #e2e8f0; }

    /* ===== MODAL WISHLIST BUTTON ===== */
    .modal-wishlist-btn {
        padding: 13px 16px; border-radius: 12px; border: 1.5px solid #e2e8f0;
        background: #fff; cursor: pointer; transition: all 0.18s;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .modal-wishlist-btn svg {
        width: 20px; height: 20px; fill: none; stroke: #e54b4b; stroke-width: 2;
        transition: fill 0.2s, stroke 0.2s;
    }
    .modal-wishlist-btn.wishlisted svg { fill: #e54b4b; stroke: #e54b4b; }
    .modal-wishlist-btn:hover { border-color: #e54b4b; background: #fff5f5; }
</style>
@endpush

@section('content')

<div class="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="UMKM SIAP">
        <span class="role-badge">Buyer</span>
    </div>

    <span class="sidebar-section-label">Menu Utama</span>
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard.buyer') }}" class="nav-item active">
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
        <a href="{{ route('buyer.profile') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Profil Buyer
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
    <a href="#" class="sidebar-user" style="text-decoration:none;">
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

<div class="main-content">

    <div class="page-header">
        <h1>Cari Produk</h1>
        <p>Browse ribuan produk UMKM Indonesia yang siap diekspor ke negara Anda.</p>
    </div>

    {{-- SEARCH BAR --}}
    <form method="GET" action="{{ route('dashboard.buyer') }}">
        <div class="search-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                name="search"
                class="search-input"
                placeholder="Cari produk..."
                value="{{ request('search') }}"
                autocomplete="off"
            >
        </div>
    </form>

    {{-- PRODUCT GRID --}}
    <div class="product-grid">

        @forelse($products as $product)

        @php
            $isWishlisted = in_array($product->id, $wishlistIds ?? []);
            $certifications = $product->certifications ?? [];
        @endphp

        <div class="product-card" onclick="openModal('{{ $product->id }}')">

            {{-- IMAGE + BADGES --}}
            <div class="card-image-wrap">
                @if(!empty($product->images) && isset($product->images[0]))
                    <img src="{{ $product->images[0] }}" alt="{{ $product->name }}">
                @else
                    <div class="card-image-placeholder">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif

                @if(!empty($certifications))
                <div class="card-badges">
                    @foreach($certifications as $cert)
                        @php $certUpper = strtoupper(trim($cert)); @endphp
                        @if($certUpper === 'SNI')
                            <span class="badge badge-sni">SNI</span>
                        @elseif($certUpper === 'HALAL')
                            <span class="badge badge-halal">Halal</span>
                        @elseif($certUpper === 'ISO')
                            <span class="badge badge-iso">ISO</span>
                        @elseif($certUpper === 'ORGANIC')
                            <span class="badge badge-organic">Organic</span>
                        @else
                            <span class="badge badge-default">{{ $cert }}</span>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>

            {{-- CARD BODY --}}
            <div class="card-body">
                <div class="card-name">{{ $product->name }}</div>
                <div class="card-company">{{ $product->user->name ?? '-' }}</div>

                <div class="card-capacity-label">Kapasitas</div>
                <div class="card-capacity-value">{{ $product->production_capacity ?? '-' }}</div>

                <div class="card-footer">
                    <button
                        class="btn-love {{ $isWishlisted ? 'wishlisted' : '' }}"
                        data-product-id="{{ $product->id }}"
                        onclick="event.stopPropagation(); toggleWishlist(this, '{{ $product->id }}')"
                        title="{{ $isWishlisted ? 'Hapus dari tersimpan' : 'Simpan produk' }}"
                    >
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>

                                        
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $product->user->phone ?? '') }}"
                    target="_blank"
                    class="btn-kontak"
                    onclick="event.stopPropagation()">
                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.532 5.855L0 24l6.352-1.503A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.004-1.371l-.357-.213-3.712.877.938-3.614-.234-.371A9.796 9.796 0 012.182 12C2.182 6.574 6.574 2.182 12 2.182c5.427 0 9.818 4.392 9.818 9.818 0 5.427-4.391 9.818-9.818 9.818z"/>
                        </svg>
                        Hubungi
                    </a>
                </div>
            </div>
        </div>

        @empty
        <div class="empty-state">
            <div class="empty-title">Belum ada produk tersedia</div>
            <div class="empty-sub">
                @if(request('search'))
                    Tidak ada produk yang cocok dengan pencarian "<strong>{{ request('search') }}</strong>".
                @else
                    Belum ada produk yang diupload oleh UMKM saat ini.
                @endif
            </div>
        </div>
        @endforelse

    </div>
</div>

{{-- Toast notifikasi --}}
<div id="toast"></div>

{{-- Modal Detail Produk --}}
<div class="modal-overlay" id="productModal" onclick="closeModal(event)">
    <div class="modal" id="modalContent"></div>
</div>

@push('scripts')
<script>
    const productsData = @json(collect($products)->values());
    const wishlistIds  = @json($wishlistIds ?? []);

    const WISHLIST_URL = "{{ route('wishlist.toggle') }}";
    const CSRF_TOKEN   = "{{ csrf_token() }}";

    const countryNames = {
        MY:'Malaysia', SG:'Singapura', JP:'Jepang', US:'Amerika Serikat',
        AU:'Australia', AE:'UAE', DE:'Jerman', CN:'China', KR:'Korea', GB:'Inggris'
    };

    const waIconSvg = '<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.532 5.855L0 24l6.352-1.503A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.004-1.371l-.357-.213-3.712.877.938-3.614-.234-.371A9.796 9.796 0 012.182 12C2.182 6.574 6.574 2.182 12 2.182c5.427 0 9.818 4.392 9.818 9.818 0 5.427-4.391 9.818-9.818 9.818z"/></svg>';

    const heartSvg = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>';

    const placeholderSvg = '<svg width="64" height="64" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" style="color:rgba(255,255,255,0.3)"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>';

    function openModal(id) {
        const p = productsData.find(x => x.id == id);
        if (!p) return;

        const certs     = p.certifications   ?? [];
        const images    = p.images           ?? [];
        const countries = p.target_countries ?? [];
        const capDisplay = p.production_capacity || '—';
        const priceStr   = p.price_usd > 0 ? 'USD ' + Number(p.price_usd).toFixed(2) : 'Nego';
        const isWishlisted = wishlistIds.includes(p.id);

        // Badges HTML (modal kiri)
        const badgesHtml = certs.map(c => {
            const cls = c.toUpperCase() === 'SNI'     ? 'badge-sni'
                      : c.toUpperCase() === 'HALAL'   ? 'badge-halal'
                      : c.toUpperCase() === 'ISO'     ? 'badge-iso'
                      : c.toUpperCase() === 'ORGANIC' ? 'badge-organic'
                      : 'badge-default';
            return '<span class="badge ' + cls + '">' + c + '</span>';
        }).join('');

        // Chips negara & sertifikasi (modal kanan)
        const countriesHtml = countries.length
            ? countries.map(c => '<span class="chip chip-country">' + (countryNames[c] ?? c) + '</span>').join('')
            : '<span style="color:#94a3b8;font-size:13px">—</span>';

        const certsHtml = certs.length
            ? certs.map(c => '<span class="chip chip-cert">' + c + '</span>').join('')
            : '<span style="color:#94a3b8;font-size:13px">—</span>';

        // Gambar utama
        const imgHtml = images[0]
            ? '<img id="modalMainImg" src="' + images[0] + '" alt="' + p.name + '" style="width:100%;height:220px;object-fit:cover;flex-shrink:0;">'
            : '<div class="modal-left-placeholder">' + placeholderSvg + '</div>';

        // Galeri foto (jika lebih dari 1)
        const photosHtml = images.length > 1
            ? '<div class="detail-section"><div class="detail-section-label">Foto Produk</div><div class="modal-photos">' +
              images.map(src => '<img class="modal-photo-thumb" src="' + src + '" onclick="document.getElementById(\'modalMainImg\').src=\'' + src + '\'">').join('') +
              '</div></div>'
            : '';

        // Deskripsi
        const descHtml = p.description
            ? '<div class="detail-section"><div class="detail-section-label">Deskripsi Produk</div><div class="detail-section-value" style="font-weight:400;color:#475569;line-height:1.7">' + p.description + '</div></div>'
            : '';

        // Seller info — buyer.blade pakai p.user
        const sellerName = (p.user && p.user.name)  ? p.user.name  : '—';
        const phone      = (p.user && p.user.phone) ? p.user.phone.replace(/[^0-9]/g, '') : null;
        const email      = (p.user && p.user.email) ? p.user.email : null;

        // Tombol hubungi: prioritas WA, fallback email
        const hubungiBtn = phone
            ? '<a href="https://wa.me/' + phone + '" target="_blank" class="btn-hubungi">' + waIconSvg + ' Hubungi via WhatsApp</a>'
            : '<span class="btn-hubungi" style="opacity:.45;cursor:not-allowed">Kontak tidak tersedia</span>';

        // Tombol wishlist di action bar modal
        const wishlistBtnClass = 'modal-wishlist-btn' + (isWishlisted ? ' wishlisted' : '');
        const wishlistBtn = '<button class="' + wishlistBtnClass + '" id="modalWishlistBtn" onclick="toggleWishlistModal(this, \'' + p.id + '\')" title="' + (isWishlisted ? 'Hapus dari tersimpan' : 'Simpan produk') + '">' + heartSvg + '</button>';

        const catLabel = p.category ? p.category.charAt(0).toUpperCase() + p.category.slice(1) : '—';

        document.getElementById('modalContent').innerHTML =
            '<div class="modal-inner">' +
                '<div class="modal-left">' +
                    '<button class="modal-close-btn" onclick="document.getElementById(\'productModal\').classList.remove(\'open\')">' +
                        '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>' +
                    '</button>' +
                    imgHtml +
                    '<div class="modal-left-info">' +
                        '<div class="modal-left-name">' + p.name + '</div>' +
                        '<div class="modal-left-seller">' +
                            '<svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg> ' +
                            sellerName +
                        '</div>' +
                        (badgesHtml ? '<div class="modal-left-badges">' + badgesHtml + '</div>' : '') +
                        '<div class="modal-price-box">' +
                            '<div class="modal-price-label">Harga</div>' +
                            '<div class="modal-price-value">' + priceStr + '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="modal-right">' +
                    '<div class="detail-row-2">' +
                        '<div class="detail-box"><div class="detail-section-label">Kapasitas Produksi</div><div class="detail-section-value accent">' + capDisplay + '</div></div>' +
                        '<div class="detail-box"><div class="detail-section-label">Kategori</div><div class="detail-section-value">' + catLabel + '</div></div>' +
                    '</div>' +
                    descHtml +
                    '<div class="detail-section"><div class="detail-section-label">Negara Tujuan</div><div class="chip-row">' + countriesHtml + '</div></div>' +
                    '<div class="detail-section"><div class="detail-section-label">Sertifikasi</div><div class="chip-row">' + certsHtml + '</div></div>' +
                    photosHtml +
                '</div>' +
            '</div>' +
            '<div class="modal-action-bar">' +
                hubungiBtn +
                wishlistBtn +
                '<button class="btn-close-modal" onclick="document.getElementById(\'productModal\').classList.remove(\'open\')">Tutup</button>' +
            '</div>';

        document.getElementById('productModal').classList.add('open');
    }

    function closeModal(event) {
        if (event.target === document.getElementById('productModal')) {
            document.getElementById('productModal').classList.remove('open');
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') document.getElementById('productModal').classList.remove('open');
    });

    // Toggle wishlist dari modal action bar
    async function toggleWishlistModal(btn, productId) {
        btn.classList.toggle('wishlisted');
        const isNow = btn.classList.contains('wishlisted');
        showToast(isNow ? '❤️ Disimpan ke Produk Tersimpan' : 'Dihapus dari Produk Tersimpan');

        // Sync tombol love di card grid
        const cardBtn = document.querySelector('.btn-love[data-product-id="' + productId + '"]');
        if (cardBtn) {
            if (isNow) cardBtn.classList.add('wishlisted');
            else cardBtn.classList.remove('wishlisted');
        }

        // Sync array wishlistIds
        if (isNow) { if (!wishlistIds.includes(productId)) wishlistIds.push(productId); }
        else { const idx = wishlistIds.indexOf(productId); if (idx > -1) wishlistIds.splice(idx, 1); }

        try {
            const res = await fetch(WISHLIST_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: productId }),
            });
            if (!res.ok) {
                btn.classList.toggle('wishlisted');
                if (cardBtn) cardBtn.classList.toggle('wishlisted');
                showToast('⚠️ Gagal menyimpan, coba lagi.');
            }
        } catch (err) {
            btn.classList.toggle('wishlisted');
            if (cardBtn) cardBtn.classList.toggle('wishlisted');
            showToast('⚠️ Terjadi kesalahan jaringan.');
        }
    }

    // Toggle wishlist dari card grid (tanpa buka modal)
    async function toggleWishlist(btn, productId) {
        btn.classList.toggle('wishlisted');
        const isNow = btn.classList.contains('wishlisted');
        showToast(isNow ? '❤️ Disimpan ke Produk Tersimpan' : 'Dihapus dari Produk Tersimpan');

        if (isNow) { if (!wishlistIds.includes(productId)) wishlistIds.push(productId); }
        else { const idx = wishlistIds.indexOf(productId); if (idx > -1) wishlistIds.splice(idx, 1); }

        try {
            const res = await fetch(WISHLIST_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_id: productId }),
            });
            if (!res.ok) {
                btn.classList.toggle('wishlisted');
                showToast('⚠️ Gagal menyimpan, coba lagi.');
            }
        } catch (err) {
            btn.classList.toggle('wishlisted');
            showToast('⚠️ Terjadi kesalahan jaringan.');
        }
    }

    let toastTimer;
    function showToast(msg) {
        const el = document.getElementById('toast');
        el.textContent = msg;
        el.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => el.classList.remove('show'), 2200);
    }

    // Live search
    let searchTimer;
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => this.closest('form').submit(), 500);
        });
    }
</script>
@endpush

@endsection