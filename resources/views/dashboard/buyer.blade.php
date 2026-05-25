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
    .page-header {
        margin-bottom: 24px;
    }
    .page-header h1 { font-size: 24px; font-weight: 800; color: #1A3530; margin-bottom: 4px; }
    .page-header p  { font-size: 14px; color: #7FA09A; }

    /* ===== SEARCH BAR ===== */
    .search-wrap {
        position: relative;
        margin-bottom: 28px;
    }
    .search-wrap svg {
        position: absolute;
        left: 16px;
        top: 50%;
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

    @media (max-width: 1280px) {
        .product-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 960px) {
        .product-grid { grid-template-columns: repeat(2, 1fr); }
    }

    /* ===== PRODUCT CARD ===== */
    .product-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #D8E5E2;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .product-card:hover {
        box-shadow: 0 8px 28px rgba(11,110,94,0.10);
        transform: translateY(-2px);
    }

    /* Bagian atas card: gambar + badge */
    .card-image-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 4/3;
        overflow: hidden;
        background: linear-gradient(145deg, #C8EDE7 0%, #A0D8CF 100%);
    }
    .card-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .card-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7FA09A;
    }

    .card-badges {
        position: absolute;
        top: 10px;
        left: 10px;
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    .badge {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }
    .badge-sni  { background: #fff; color: #0B6E5E; border: 1px solid #B2DDD5; }
    .badge-halal { background: #FFF7E6; color: #D97706; border: 1px solid #FBBF24; }
    .badge-default { background: #fff; color: #555; border: 1px solid #D8E5E2; }

    /* Bagian bawah card: info */
    .card-body {
        padding: 14px 16px 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .card-name {
        font-size: 15px;
        font-weight: 700;
        color: #1A3530;
        margin-bottom: 3px;
        line-height: 1.3;
    }
    .card-company {
        font-size: 12px;
        color: #7FA09A;
        margin-bottom: 12px;
    }
    .card-capacity-label {
        font-size: 11px;
        color: #aab0bb;
        font-weight: 500;
        margin-bottom: 2px;
    }
    .card-capacity-value {
        font-size: 13px;
        font-weight: 700;
        color: #0B6E5E;
        margin-bottom: 14px;
    }

    /* Footer card: love + kontak */
    .card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 4px;
    }

    /* ===== LOVE BUTTON ===== */
    .btn-love {
        background: #FFF5F5; /* Beri background soft agar estetik mirip mockup */
        border: none;
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: background 0.15s, transform 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-love:hover { background: #FFF0F0; transform: scale(1.12); }
    .btn-love svg {
        width: 20px; height: 20px;
        transition: fill 0.2s, stroke 0.2s;
        fill: none;
        stroke: #e54b4b;
        stroke-width: 2;
    }

    /* FIX: Spesifik targetkan path di dalam tombol jika berstatus aktif */
    .btn-love.wishlisted svg {
        fill: #e54b4b !important;
        stroke: #e54b4b !important;
    }

    /* ===== KONTAK BUTTON ===== */
    .btn-kontak {
        background: #0B6E5E;
        color: white;
        font-size: 13px;
        font-weight: 700;
        padding: 9px 22px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: opacity 0.15s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .btn-kontak:hover { opacity: 0.85; }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        grid-column: 1 / -1;
        background: white;
        border-radius: 14px;
        border: 1px solid #D8E5E2;
        padding: 60px 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .empty-title { color: #1A3530; font-size: 16px; font-weight: bold; margin-bottom: 8px; }
    .empty-sub   { color: #7FA09A; font-size: 13px; max-width: 340px; line-height: 1.6; }

    /* Toast notif kecil */
    #toast {
        position: fixed;
        bottom: 28px;
        right: 28px;
        background: #1A3530;
        color: white;
        font-size: 13px;
        font-weight: 600;
        padding: 12px 20px;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        opacity: 0;
        transform: translateY(12px);
        transition: opacity 0.25s, transform 0.25s;
        z-index: 999;
        pointer-events: none;
    }
    #toast.show { opacity: 1; transform: translateY(0); }
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

        {{-- Tentukan apakah produk ini ada di wishlist user --}}
        @php
            $isWishlisted = in_array($product->id, $wishlistIds ?? []);
            $certifications = $product->certifications ?? [];
        @endphp

        <div class="product-card">

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

                {{-- Badge Sertifikasi --}}
                @if(!empty($certifications))
                <div class="card-badges">
                    @foreach($certifications as $cert)
                        @php $certUpper = strtoupper(trim($cert)); @endphp
                        @if($certUpper === 'SNI')
                            <span class="badge badge-sni">SNI</span>
                        @elseif($certUpper === 'HALAL')
                            <span class="badge badge-halal">Halal</span>
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
                <div class="card-capacity-value">
                    {{ $product->production_capacity ?? '-' }}
                </div>

                <div class="card-footer">
                    {{-- FIX: Ditambahkan onclick event handler agar fungsi JavaScript-mu terpanggil --}}
                    <button
                        class="btn-love {{ $isWishlisted ? 'wishlisted' : '' }}"
                        data-product-id="{{ $product->id }}"
                        onclick="toggleWishlist(this, '{{ $product->id }}')"
                        title="{{ $isWishlisted ? 'Hapus dari tersimpan' : 'Simpan produk' }}"
                    >
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>

                    {{-- KONTAK BUTTON --}}
                    <a href="mailto:{{ $product->user->email ?? '#' }}" class="btn-kontak">
                        Kontak
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

@push('scripts')
<script>
    const WISHLIST_URL = "{{ route('wishlist.toggle') }}";
    const CSRF_TOKEN   = "{{ csrf_token() }}";

    async function toggleWishlist(btn, productId) {
        // Optimistic UI: langsung toggle tampilan secara instan sebelum response database selesai
        btn.classList.toggle('wishlisted');

        const isNowWishlisted = btn.classList.contains('wishlisted');
        showToast(isNowWishlisted ? '❤️ Disimpan ke Produk Tersimpan' : 'Dihapus dari Produk Tersimpan');

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
                // Rollback status warna jika koneksi backend bermasalah/gagal
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

    // Live search: submit form saat user berhenti mengetik
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
