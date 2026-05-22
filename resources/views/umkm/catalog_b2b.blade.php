@extends('layouts.app')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --green-darkmore: #0d3d30;
        --green-dark:     #1a5c46;
        --green:          #2a8a6a;
        --green-mid:      #3aaa84;
        --green-light:    #e8f5f0;
        --green-xlight:   #f0faf6;
        --yellow:         #f4c430;
        --yellow-soft:    #fff8e1;
        --white:          #ffffff;
        --gray-50:        #f8fafc;
        --gray-100:       #f1f5f9;
        --gray-200:       #e2e8f0;
        --gray-400:       #94a3b8;
        --gray-500:       #64748b;
        --gray-700:       #334155;
        --gray-900:       #0f172a;
        --sidebar-w:      248px;
        --radius-card:    16px;
        --shadow-card:    0 2px 12px rgba(0,0,0,0.07);
        --shadow-hover:   0 8px 32px rgba(26,92,70,0.14);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: var(--gray-50);
        color: var(--gray-900);
        display: flex;
        min-height: 100vh;
    }

/* ===== SIDEBAR (same as assessment.blade.php) ===== */
    .sidebar {
        width: var(--sidebar-w); background: var(--white);
        border-right: 1px solid #e8ecef; display: flex;
        flex-direction: column; position: fixed;
        top: 0; left: 0; bottom: 0; z-index: 100; padding: 28px 0 24px;
    }
    .sidebar-brand { display: flex; align-items: center; gap: 10px; padding: 0 24px 28px; border-bottom: 1px solid #e8ecef; }
    .sidebar-brand img { height: 36px; }
    .sidebar-brand .role-badge { font-size: 10px; font-weight: 700; color: var(--white); background: var(--green); padding: 3px 8px; border-radius: 20px; letter-spacing: 0.5px; }
    .sidebar-section-label { font-size: 10px; font-weight: 700; letter-spacing: 1px; color: #aab0bb; text-transform: uppercase; padding: 20px 24px 8px; }
    .sidebar-nav { display: flex; flex-direction: column; gap: 2px; padding: 0 12px; }
    .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 10px; font-size: 14px; font-weight: 500; color: #555; cursor: pointer; text-decoration: none; transition: background 0.15s, color 0.15s; }
    .nav-item:hover { background: #f0f2f5; color: var(--text-black); }
    .nav-item.active { background: var(--green-light); color: var(--green-dark); font-weight: 600; }
    .nav-item svg { width: 18px; height: 18px; flex-shrink: 0; }
    .sidebar-spacer { flex: 1; }
    .sidebar-user { margin: 0 12px; padding: 12px 14px; display: flex; align-items: center; gap: 10px; border-radius: 12px; background: #f7f8fa; }
    .user-avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--green-dark); color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0; }
    .user-info .user-name { font-size: 13px; font-weight: 600; color: var(--text-black); }
    .user-info .user-sub  { font-size: 11px; color: #aab0bb; }
    .sidebar-logout { display: flex; align-items: center; gap: 8px; margin: 8px 12px 0; padding: 10px 14px; border-radius: 10px; font-size: 13px; color: #e54b4b; font-weight: 500; cursor: pointer; transition: background 0.15s; }
    .sidebar-logout:hover { background: #fff0f0; }

    /* ===================================================
       MAIN CONTENT
    =================================================== */
    .main-content {
        margin-left: var(--sidebar-w);
        flex: 1;
        padding: 36px 40px;
        max-width: calc(100vw - var(--sidebar-w));
    }

    .page-header {
        margin-bottom: 28px;
    }

    .page-title {
        font-size: 26px;
        font-weight: 800;
        color: var(--gray-900);
        letter-spacing: -0.01em;
        line-height: 1.2;
    }

    .page-subtitle {
        font-size: 14px;
        color: var(--gray-500);
        margin-top: 4px;
    }

    /* ===================================================
       SEARCH
    =================================================== */
    .toolbar {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }

    .search-wrap {
        flex: 1;
        min-width: 240px;
        position: relative;
    }

    .search-wrap svg {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
        pointer-events: none;
        width: 17px; height: 17px;
    }

    .search-input {
        width: 100%;
        padding: 12px 16px 12px 42px;
        border: 1.5px solid var(--gray-200);
        border-radius: 12px;
        font-family: inherit;
        font-size: 14px;
        color: var(--gray-700);
        background: var(--white);
        outline: none;
        transition: border-color 0.18s, box-shadow 0.18s;
    }

    .search-input::placeholder { color: var(--gray-400); }

    .search-input:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 3px rgba(42,138,106,0.12);
    }

    .results-count {
        font-size: 13px;
        color: var(--gray-400);
        white-space: nowrap;
        font-weight: 500;
    }

    /* ===================================================
       PRODUCT GRID
    =================================================== */
    .catalog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 20px;
    }

    .product-card {
        background: var(--white);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        border: 1.5px solid transparent;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.22s, box-shadow 0.22s, border-color 0.22s;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-hover);
        border-color: rgba(42,138,106,0.18);
    }

    .card-img-wrap {
        position: relative;
        height: 170px;
        background: linear-gradient(135deg, #d6ede6 0%, #b0ddd0 100%);
        overflow: hidden;
        flex-shrink: 0;
    }

    .card-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s;
    }

    .product-card:hover .card-img-wrap img {
        transform: scale(1.04);
    }

    .card-img-placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        font-size: 42px;
    }

    .card-badges {
        position: absolute;
        top: 10px; left: 10px;
        display: flex; gap: 5px;
        flex-wrap: wrap;
    }

    .badge {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.04em;
        padding: 3px 8px;
        border-radius: 6px;
        line-height: 1.4;
    }

    .badge-sni     { background: var(--green-dark); color: var(--white); }
    .badge-halal   { background: var(--yellow); color: #7a5c00; }
    .badge-iso     { background: #3b82f6; color: var(--white); }
    .badge-organic { background: #84cc16; color: #365314; }

    .card-body {
        padding: 16px 16px 14px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .card-name {
        font-size: 15px;
        font-weight: 700;
        color: var(--gray-900);
        line-height: 1.35;
        margin-bottom: 4px;
    }

    .card-seller {
        font-size: 12px;
        color: var(--gray-400);
        margin-bottom: 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-capacity-label {
        font-size: 11px;
        color: var(--gray-400);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 2px;
    }

    .card-capacity-value {
        font-size: 14px;
        font-weight: 700;
        color: var(--green-dark);
    }

    .card-footer {
        padding: 0 16px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: auto;
    }

    .card-category-chip {
        font-size: 11px;
        font-weight: 600;
        color: var(--green);
        background: var(--green-xlight);
        padding: 4px 10px;
        border-radius: 20px;
        border: 1px solid rgba(42,138,106,0.15);
    }

    .card-contact-btn {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 7px 13px;
        background: var(--green-dark);
        color: var(--white);
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.18s;
        text-decoration: none;
        white-space: nowrap;
    }

    .card-contact-btn:hover { background: var(--green); }

    /* ===================================================
       EMPTY STATE
    =================================================== */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 80px 20px;
        color: var(--gray-400);
    }

    .empty-state-icon  { font-size: 56px; margin-bottom: 16px; }
    .empty-state-title { font-size: 18px; font-weight: 700; color: var(--gray-500); margin-bottom: 8px; }
    .empty-state-desc  { font-size: 14px; max-width: 360px; margin: 0 auto; line-height: 1.6; }

    /* ===================================================
       MODAL — Product Detail
    =================================================== */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10, 30, 24, 0.55);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index: 200;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    .modal-overlay.open { display: flex; }

    .modal {
        background: var(--white);
        border-radius: 24px;
        max-width: 780px;
        width: 100%;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 32px 100px rgba(0,0,0,0.28), 0 0 0 1px rgba(255,255,255,0.08);
        animation: modalIn 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: flex;
        flex-direction: column;
    }

    @keyframes modalIn {
        from { opacity: 0; transform: translateY(24px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .modal-inner {
        display: flex;
        overflow: hidden;
        flex: 1;
        min-height: 0;
    }

    .modal-left {
        width: 280px;
        flex-shrink: 0;
        position: relative;
        background: linear-gradient(160deg, #1a5c46 0%, #2a8a6a 100%);
        display: flex;
        flex-direction: column;
    }

    .modal-left img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        flex-shrink: 0;
    }

    .modal-left-placeholder {
        width: 100%;
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 72px;
        flex-shrink: 0;
    }

    .modal-left-info { padding: 20px; flex: 1; }

    .modal-left-name {
        font-size: 17px;
        font-weight: 800;
        color: var(--white);
        line-height: 1.3;
        margin-bottom: 6px;
    }

    .modal-left-seller {
        font-size: 12px;
        color: rgba(255,255,255,0.65);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .modal-left-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-bottom: 20px;
    }

    .modal-price-box {
        background: rgba(255,255,255,0.12);
        border-radius: 12px;
        padding: 12px 14px;
        border: 1px solid rgba(255,255,255,0.15);
    }

    .modal-price-label {
        font-size: 10px;
        font-weight: 700;
        color: rgba(255,255,255,0.55);
        text-transform: uppercase;
        letter-spacing: 0.07em;
        margin-bottom: 3px;
    }

    .modal-price-value {
        font-size: 18px;
        font-weight: 800;
        color: var(--yellow);
    }

    .modal-right {
        flex: 1;
        overflow-y: auto;
        padding: 24px 26px;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .modal-right::-webkit-scrollbar { width: 4px; }
    .modal-right::-webkit-scrollbar-track { background: transparent; }
    .modal-right::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 4px; }

    .modal-close-btn {
        position: absolute;
        top: 14px; right: 14px;
        width: 32px; height: 32px;
        border-radius: 50%;
        background: rgba(255,255,255,0.18);
        border: none;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.18s;
        z-index: 10;
        color: var(--white);
    }

    .modal-close-btn:hover { background: rgba(255,255,255,0.32); }

    .detail-section { margin-bottom: 18px; }

    .detail-section-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--gray-400);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 6px;
    }

    .detail-section-value {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-800);
        line-height: 1.6;
    }

    .detail-section-value.accent {
        font-size: 16px;
        font-weight: 800;
        color: var(--green-dark);
    }

    .detail-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 18px;
    }

    .detail-box {
        background: var(--gray-50);
        border-radius: 12px;
        padding: 12px 14px;
        border: 1px solid var(--gray-100);
    }

    .chip-row { display: flex; flex-wrap: wrap; gap: 6px; }

    .chip {
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
    }

    .chip-country {
        background: var(--green-xlight);
        border: 1px solid rgba(42,138,106,0.2);
        color: var(--green-dark);
    }

    .chip-cert {
        background: var(--yellow-soft);
        border: 1px solid rgba(244,196,48,0.3);
        color: #7a5c00;
    }

    .modal-photos { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 18px; }

    .modal-photo-thumb {
        width: 70px; height: 70px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid var(--gray-100);
        cursor: pointer;
        transition: border-color 0.18s;
    }

    .modal-photo-thumb:hover { border-color: var(--green); }

    .modal-action-bar {
        padding: 16px 26px;
        border-top: 1px solid var(--gray-100);
        display: flex;
        gap: 10px;
        background: var(--white);
        flex-shrink: 0;
    }

    .btn-hubungi {
        flex: 1;
        padding: 13px;
        background: var(--green-dark);
        color: var(--white);
        font-family: inherit;
        font-size: 14px;
        font-weight: 700;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: background 0.18s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        text-decoration: none;
    }

    .btn-hubungi:hover { background: var(--green); }

    .btn-close-modal {
        padding: 13px 20px;
        background: var(--gray-100);
        color: var(--gray-500);
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: background 0.18s;
    }

    .btn-close-modal:hover { background: var(--gray-200); }

    /* ===================================================
       PAGINATION
    =================================================== */
    .pagination-wrap {
        margin-top: 36px;
        display: flex;
        justify-content: center;
    }

    /* ===================================================
       RESPONSIVE
    =================================================== */
    @media (max-width: 900px) {
        .sidebar { width: 200px; }
        :root { --sidebar-w: 200px; }
        .main-content { padding: 24px 20px; }
        .catalog-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
    }

    @media (max-width: 640px) {
        .sidebar { transform: translateX(-100%); }
        :root { --sidebar-w: 0px; }
        .main-content { margin-left: 0; max-width: 100vw; }
    }
</style>
@endpush

@section('content')
<div class="sidebar">
    <div class="sidebar-brand">
        <img src="/images/logo.png" alt="UMKM SIAP">
        <span class="role-badge">UMKM</span>
    </div>
    <span class="sidebar-section-label">Menu Utama</span>
    <nav class="sidebar-nav">
        <a href="/umkm/dashboard" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="/umkm/assessment" class="nav-item active">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Assessment
        </a>
        <a href="/umkm/market" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Market Intelligence
        </a>
        <a href="/umkm/catalog" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            Katalog B2B
        </a>
        <a href="/umkm/products" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Produk Saya
        </a>
    </nav>
    <div class="sidebar-spacer"></div>
    <span class="sidebar-section-label">Akun</span>
    <nav class="sidebar-nav">
        <a href="/umkm/profile" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Profil UMKM
        </a>
        <a href="/umkm/settings" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan
        </a>
    </nav>
    <div style="height:12px"></div>
    <div class="sidebar-user">
        <div class="user-avatar" id="sidebarInitial">K</div>
        <div class="user-info">
            <div class="user-name" id="sidebarName">Loading...</div>
            <div class="user-sub" id="sidebarSub">UMKM</div>
        </div>
    </div>
    <a class="sidebar-logout" id="logoutBtn">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        Keluar
    </a>
</div>

{{-- ===== MAIN ===== --}}
<div class="main-content">

    {{-- Header --}}
    <div class="page-header">
        <h1 class="page-title">Katalog Produk B2B</h1>
        <p class="page-subtitle">Browse produk UMKM Indonesia yang siap ekspor.</p>
    </div>

    {{-- Search --}}
    <div class="toolbar">
        <div class="search-wrap">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                id="searchInput"
                class="search-input"
                placeholder="Cari produk..."
                value="{{ request('search') }}"
                autocomplete="off">
        </div>
    </div>

    {{-- Grid --}}
    <div class="catalog-grid" id="catalogGrid">

        @forelse($products as $product)
            @php
                $certs = $product->certifications ?? [];
                $images = $product->images ?? [];
                $imgSrc = count($images) ? $images[0] : null;
                $categoryEmoji = match($product->category) {
                    'kopi'      => '☕',
                    'coklat'    => '🍫',
                    'batik'     => '🎨',
                    'kerajinan' => '🪵',
                    'teh'       => '🍵',
                    'kosmetik'  => '✨',
                    'makanan'   => '🌶️',
                    default     => '📦',
                };
                $countries = $product->target_countries ?? [];
                $countryNames = [
                    'MY'=>'Malaysia','SG'=>'Singapura','JP'=>'Jepang','US'=>'Amerika',
                    'AU'=>'Australia','AE'=>'UAE','DE'=>'Jerman','CN'=>'China',
                    'KR'=>'Korea','GB'=>'Inggris',
                ];
            @endphp

            <div class="product-card" onclick="openModal('{{ $product->id }}')">

                {{-- Image --}}
                <div class="card-img-wrap">
                    @if($imgSrc)
                        <img src="{{ $imgSrc }}" alt="{{ $product->name }}" loading="lazy">
                    @else
                        <div class="card-img-placeholder">{{ $categoryEmoji }}</div>
                    @endif

                    {{-- Cert badges --}}
                    @if(count($certs))
                        <div class="card-badges">
                            @if(in_array('SNI', $certs))
                                <span class="badge badge-sni">SNI</span>
                            @endif
                            @if(in_array('Halal', $certs))
                                <span class="badge badge-halal">Halal</span>
                            @endif
                            @if(in_array('ISO', $certs))
                                <span class="badge badge-iso">ISO</span>
                            @endif
                            @if(in_array('Organic', $certs))
                                <span class="badge badge-organic">Organic</span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Body --}}
                <div class="card-body">
                    <div class="card-name">{{ $product->name }}</div>
                    <div class="card-seller">{{ $product->seller->name ?? '—' }}</div>
                    <div class="card-capacity-label">Kapasitas</div>
                    <div class="card-capacity-value">
                        {{ number_format((int) $product->production_capacity) }}
                        {{ str_contains(strtolower($product->category), 'batik') || str_contains(strtolower($product->category), 'kerajinan') ? 'pcs' : 'kg' }}/bulan
                    </div>
                </div>

                {{-- Footer --}}
                <div class="card-footer">
                    <span class="card-category-chip">
                        {{ ucfirst($product->category) }}
                    </span>
                    <a href="https://wa.me/{{ ltrim($product->seller->phone ?? '', '+') }}"
                        target="_blank"
                        class="card-contact-btn"
                        onclick="event.stopPropagation()">
                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.532 5.855L0 24l6.352-1.503A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.004-1.371l-.357-.213-3.712.877.938-3.614-.234-.371A9.796 9.796 0 012.182 12C2.182 6.574 6.574 2.182 12 2.182c5.427 0 9.818 4.392 9.818 9.818 0 5.427-4.391 9.818-9.818 9.818z"/>
                        </svg>
                        Hubungi
                    </a>
                </div>
            </div>

        @empty
            <div class="empty-state">
                <div class="empty-state-icon">🔍</div>
                <div class="empty-state-title">Produk tidak ditemukan</div>
                <div class="empty-state-desc">Coba ubah filter atau kata kunci pencarian untuk menemukan produk yang kamu cari.</div>
            </div>
        @endforelse

    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
        <div class="pagination-wrap">
            {{ $products->links() }}
        </div>
    @endif
</div>

{{-- ===== MODAL ===== --}}
<div class="modal-overlay" id="productModal" onclick="closeModal(event)">
    <div class="modal" id="modalContent">
        {{-- JS-populated --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
    const productsData = JSON.parse('@json($products->items())');

    const categoryEmoji = {
        kopi:'☕', coklat:'🍫', batik:'🎨', kerajinan:'🪵',
        teh:'🍵', kosmetik:'✨', makanan:'🌶️'
    };

    const countryNames = {
        MY:'Malaysia', SG:'Singapura', JP:'Jepang', US:'Amerika Serikat',
        AU:'Australia', AE:'UAE', DE:'Jerman', CN:'China', KR:'Korea', GB:'Inggris'
    };

    const waIcon = '<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.532 5.855L0 24l6.352-1.503A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.004-1.371l-.357-.213-3.712.877.938-3.614-.234-.371A9.796 9.796 0 012.182 12C2.182 6.574 6.574 2.182 12 2.182c5.427 0 9.818 4.392 9.818 9.818 0 5.427-4.391 9.818-9.818 9.818z"/></svg>';

    function openModal(id) {
        const p = productsData.find(x => x.id == id);
        if (!p) return;

        const certs     = p.certifications    ?? [];
        const images    = p.images            ?? [];
        const countries = p.target_countries  ?? [];
        const emoji     = categoryEmoji[p.category] ?? '📦';
        const cap       = Number(p.production_capacity).toLocaleString('id-ID');
        const priceStr  = p.price_usd > 0 ? 'USD ' + Number(p.price_usd).toFixed(2) : 'Nego';
        const unitLabel = ['batik','kerajinan'].includes(p.category) ? 'pcs' : 'kg';

        const badgesHtml = certs.map(c => {
            const cls = c === 'SNI' ? 'badge-sni' : c === 'Halal' ? 'badge-halal' : c === 'ISO' ? 'badge-iso' : 'badge-organic';
            return '<span class="badge ' + cls + '">' + c + '</span>';
        }).join('');

        const countriesHtml = countries.length
            ? countries.map(c => '<span class="chip chip-country">' + (countryNames[c] ?? c) + '</span>').join('')
            : '<span style="color:var(--gray-400);font-size:13px">—</span>';

        const certsHtml = certs.length
            ? certs.map(c => '<span class="chip chip-cert">' + c + '</span>').join('')
            : '<span style="color:var(--gray-400);font-size:13px">—</span>';

        const imgHtml = images[0]
            ? '<img id="modalMainImg" src="' + images[0] + '" alt="' + p.name + '" style="width:100%;height:220px;object-fit:cover;flex-shrink:0;">'
            : '<div class="modal-left-placeholder">' + emoji + '</div>';

        const photosHtml = images.length > 1
            ? '<div class="detail-section"><div class="detail-section-label">Foto Produk</div><div class="modal-photos">' +
              images.map(src => '<img class="modal-photo-thumb" src="' + src + '" onclick="document.getElementById(\'modalMainImg\').src=\'' + src + '\'">').join('') +
              '</div></div>'
            : '';

        const descHtml = p.description
            ? '<div class="detail-section"><div class="detail-section-label">Deskripsi Produk</div><div class="detail-section-value" style="font-weight:400;color:var(--gray-600);line-height:1.7">' + p.description + '</div></div>'
            : '';

        const waBtn = p.seller && p.seller.phone
            ? '<a href="https://wa.me/' + p.seller.phone.replace(/[^0-9]/g, '') + '" target="_blank" class="btn-hubungi">' + waIcon + ' Hubungi via WhatsApp</a>'
            : '<span class="btn-hubungi" style="opacity:.45;cursor:not-allowed">Kontak tidak tersedia</span>';

        const sellerName = (p.seller && p.seller.name) ? p.seller.name : '—';
        const catLabel   = p.category.charAt(0).toUpperCase() + p.category.slice(1);

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
                        '<div class="detail-box"><div class="detail-section-label">Kapasitas Produksi</div><div class="detail-section-value accent">' + cap + ' ' + unitLabel + '/bulan</div></div>' +
                        '<div class="detail-box"><div class="detail-section-label">Kategori</div><div class="detail-section-value">' + catLabel + '</div></div>' +
                    '</div>' +
                    descHtml +
                    '<div class="detail-section"><div class="detail-section-label">Negara Tujuan</div><div class="chip-row">' + countriesHtml + '</div></div>' +
                    '<div class="detail-section"><div class="detail-section-label">Sertifikasi</div><div class="chip-row">' + certsHtml + '</div></div>' +
                    photosHtml +
                '</div>' +
            '</div>' +
            '<div class="modal-action-bar">' +
                waBtn +
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
        if (e.key === 'Escape') {
            document.getElementById('productModal').classList.remove('open');
        }
    });

    // Search with debounce
    let debounceTimer;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(applySearch, 500);
    });

    function applySearch() {
        const params = new URLSearchParams();
        const search = document.getElementById('searchInput').value.trim();
        if (search) params.set('search', search);
        window.location.href = '?' + params.toString();
    }
</script>
@endpush
