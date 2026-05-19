@extends('layouts.app')

@push('styles')
<style>
    :root { --sidebar-w: 240px; }

    body { display: flex; min-height: 100vh; background: #f7f8fa; }

    /* ===== SIDEBAR ===== */
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
    .sidebar-logout { display: flex; align-items: center; gap: 8px; margin: 8px 12px 0; padding: 10px 14px; border-radius: 10px; font-size: 13px; color: #e54b4b; font-weight: 500; cursor: pointer; transition: background 0.15s; text-decoration: none; }
    .sidebar-logout:hover { background: #fff0f0; }

    /* ===== MAIN ===== */
    .main-content { margin-left: var(--sidebar-w); flex: 1; padding: 40px 48px; }
    .page-header { margin-bottom: 28px; }
    .page-header h1 { font-size: 26px; font-weight: 800; color: var(--text-black); margin-bottom: 6px; }
    .page-header p  { font-size: 14px; color: var(--text-muted); }

    /* ===== ASSESSMENT BADGE ===== */
    .assessment-badge {
        display: none; align-items: center; gap: 8px;
        background: var(--green-light); border: 1.5px solid #b8ddd3;
        border-radius: 10px; padding: 9px 16px;
        font-size: 13px; color: var(--green-dark); font-weight: 600;
        margin-bottom: 20px;
    }
    .assessment-badge.visible { display: inline-flex; }
    .assessment-badge svg { width: 16px; height: 16px; flex-shrink: 0; }

    /* ===== SELECTOR BAR ===== */
    .selector-bar {
        display: flex; align-items: center; gap: 12px;
        background: white; border-radius: 16px; padding: 16px 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06); margin-bottom: 28px;
        flex-wrap: wrap;
    }
    .selector-label { font-size: 13px; font-weight: 600; color: #555; white-space: nowrap; }
    .selector-input {
        flex: 1; min-width: 200px; padding: 10px 14px;
        border: 2px solid #dde1e7; border-radius: 10px;
        font-size: 14px; font-family: inherit; color: var(--text-black);
        outline: none; transition: border-color 0.2s; background: white;
    }
    .selector-input:focus { border-color: var(--green-dark); }
    .btn-analyze {
        padding: 11px 28px; border-radius: 10px;
        background: var(--green-dark); color: white;
        font-size: 14px; font-weight: 700; border: none;
        cursor: pointer; display: flex; align-items: center; gap: 8px;
        transition: background 0.2s, transform 0.1s; white-space: nowrap;
    }
    .btn-analyze:hover { background: var(--green-darkmore); }
    .btn-analyze:active { transform: scale(0.97); }
    .btn-analyze:disabled { opacity: 0.55; cursor: not-allowed; transform: none; }
    .spinner-sm {
        width: 16px; height: 16px; border: 2.5px solid rgba(255,255,255,0.35);
        border-top-color: white; border-radius: 50%;
        animation: spin 0.8s linear infinite; display: none;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ===== STATES ===== */
    #loadingArea { display: none; }
    #emptyArea   { display: block; }
    #resultArea  { display: none; }

    .loading-state {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; padding: 80px 40px; color: #aab0bb; text-align: center;
    }
    .loading-spinner-lg {
        width: 48px; height: 48px; border: 4px solid #e8ecef;
        border-top-color: var(--green-dark); border-radius: 50%;
        animation: spin 0.8s linear infinite; margin-bottom: 16px;
    }
    .loading-title { font-size: 16px; font-weight: 700; color: #777; margin-bottom: 6px; }
    .loading-sub   { font-size: 13px; color: #aab0bb; }

    .empty-state { text-align: center; padding: 80px 40px; color: #aab0bb; }
    .empty-state h3 { font-size: 20px; font-weight: 700; margin-bottom: 8px; color: #777; }
    .empty-state p  { font-size: 14px; margin-bottom: 0; }

    /* ===== MAIN 2-COL GRID (matches Figma) ===== */
    .market-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    /* ===== CARDS ===== */
    .market-card {
        background: white; border-radius: 20px; padding: 28px 32px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.07);
    }
    .card-title { font-size: 18px; font-weight: 800; color: var(--text-black); margin-bottom: 24px; }

    /* ===== COUNTRY LIST ===== */
    .country-list { display: flex; flex-direction: column; gap: 20px; }
    .country-top-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
    .country-name-wrap { display: flex; align-items: center; gap: 10px; }
    .country-flag { font-size: 22px; line-height: 1; }
    .country-name { font-size: 15px; font-weight: 700; color: var(--text-black); }
    .country-meta { display: flex; align-items: center; gap: 6px; margin-top: 3px; }
    .country-pct  { font-size: 15px; font-weight: 800; color: var(--green-dark); }
    .progress-track { height: 6px; background: #e8ecef; border-radius: 99px; overflow: hidden; }
    .progress-fill  { height: 100%; background: var(--green-dark); border-radius: 99px; width: 0%; transition: width 1s cubic-bezier(0.4,0,0.2,1); }
    .difficulty-badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; }
    .diff-easy   { background: #e6f9f0; color: #0b6e5e; }
    .diff-medium { background: #fff3cd; color: #856404; }
    .diff-hard   { background: #ffe5e5; color: #c0392b; }
    .market-size { font-size: 11px; font-weight: 600; color: #aab0bb; background: #f0f2f5; padding: 2px 8px; border-radius: 6px; }
    .req-chips { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px; }
    .req-chip  { font-size: 11px; padding: 2px 8px; background: #f7f8fa; color: #777; border-radius: 20px; border: 1px solid #e8ecef; }
    .country-reason { font-size: 12px; color: #aab0bb; margin-top: 5px; }

    /* ===== INSIGHT AI ===== */
    .insight-block { background: var(--green-light); border-radius: 14px; padding: 20px 22px; border: 1.5px solid #b8ddd3; margin-bottom: 14px; }
    .insight-block-title { font-size: 14px; font-weight: 700; color: var(--green-dark); margin-bottom: 12px; }
    .insight-list { display: flex; flex-direction: column; gap: 10px; }
    .insight-item { display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #2a5a4e; line-height: 1.55; }
    .insight-dot  { color: #c9a227; font-size: 15px; flex-shrink: 0; }

    /* ===== SECTION TITLE ===== */
    .section-title { font-size: 18px; font-weight: 800; color: var(--text-black); margin-bottom: 16px; }

    /* ===== TRENDS ===== */
    .trends-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    .trend-card  { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
    .trend-impact { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
    .impact-positive { color: #0b6e5e; }
    .impact-negative { color: #c0392b; }
    .impact-neutral  { color: #856404; }
    .trend-name { font-size: 14px; font-weight: 700; color: var(--text-black); margin-bottom: 5px; }
    .trend-desc { font-size: 12px; color: var(--text-muted); line-height: 1.5; }

    /* ===== OPPORTUNITIES ===== */
    .opp-list { display: flex; flex-direction: column; gap: 12px; }
    .opp-item { background: white; border-radius: 14px; padding: 16px 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); display: flex; gap: 14px; align-items: flex-start; }
    .urgency-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; flex-shrink: 0; margin-top: 1px; }
    .urgency-high   { background: #ffe5e5; color: #c0392b; }
    .urgency-medium { background: #fff3cd; color: #856404; }
    .urgency-low    { background: var(--green-light); color: var(--green-dark); }
    .opp-title { font-size: 14px; font-weight: 700; color: var(--text-black); margin-bottom: 4px; }
    .opp-desc  { font-size: 13px; color: var(--text-muted); line-height: 1.5; }

    /* ===== COMPETITOR ===== */
    .competitor-card { background: white; border-radius: 18px; padding: 28px 32px; box-shadow: 0 1px 6px rgba(0,0,0,0.07); margin-bottom: 20px; }
    .competitor-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 28px; margin-top: 20px; }
    .comp-col-label  { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #aab0bb; margin-bottom: 12px; }
    .comp-tag { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; background: #f0f2f5; color: #444; margin: 4px 4px 0 0; }
    .comp-advantage { font-size: 14px; font-weight: 600; color: var(--green-dark); line-height: 1.6; }
    .diff-tip { display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #444; line-height: 1.5; margin-bottom: 8px; }
    .diff-tip-arrow { color: var(--green-dark); font-weight: 700; flex-shrink: 0; }

    /* ===== NARRATIVE ===== */
    .narrative-card { background: white; border-radius: 18px; padding: 28px 32px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); margin-bottom: 20px; }
    .narrative-text { font-size: 14px; color: #444; line-height: 1.85; }
    .rec-country-wrap { margin-top: 20px; display: flex; align-items: center; gap: 14px; padding: 16px 20px; background: var(--green-light); border-radius: 12px; border: 1.5px solid #b8ddd3; }
    .rec-country-icon { width: 40px; height: 40px; border-radius: 10px; background: white; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
    .rec-country-label { font-size: 12px; font-weight: 600; color: #2a5a4e; margin-bottom: 2px; }
    .rec-country-name  { font-size: 17px; font-weight: 800; color: var(--green-dark); }

    /* ===== ERROR ===== */
    .error-card { background: #fff5f5; border: 1.5px solid #fccaca; border-radius: 16px; padding: 20px 24px; display: flex; align-items: flex-start; gap: 12px; margin-bottom: 20px; }
    .error-card svg { flex-shrink: 0; color: #e54b4b; margin-top: 1px; }
    .error-title { font-size: 14px; font-weight: 700; color: #c0392b; margin-bottom: 4px; }
    .error-msg   { font-size: 13px; color: #e54b4b; }
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
        <a href="/umkm/assessment" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Assessment
        </a>
        <a href="/umkm/market" class="nav-item active">
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
            <div class="user-sub"  id="sidebarSub">UMKM</div>
        </div>
    </div>
    <a class="sidebar-logout" id="logoutBtn">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        Keluar
    </a>
</div>

<!-- ===== MAIN CONTENT ===== -->
<div class="main-content">
    <div class="page-header">
        <h1>Market Intelligence</h1>
        <p>Riset pasar global & rekomendasi negara target untuk produk Anda.</p>
    </div>

    {{-- Badge dari assessment — muncul kalau ada data assessment di localStorage --}}
    <div class="assessment-badge" id="assessmentBadge">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span id="assessmentBadgeText">Analisis dipersonalisasi berdasarkan hasil assessment Anda</span>
    </div>

    {{-- Selector bar --}}
    <div class="selector-bar">
        <span class="selector-label">Kategori Produk:</span>
        <input type="text" class="selector-input" id="productInput"
               placeholder="Contoh: Kopi Arabika, Batik Premium, Kerajinan Rotan..."
               onkeydown="if(event.key==='Enter') runAnalysis()" />
        <button class="btn-analyze" id="analyzeBtn" onclick="runAnalysis()">
            <span class="spinner-sm" id="analyzeSpinner"></span>
            <svg id="analyzeIcon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span id="analyzeBtnText">Analisis Pasar</span>
        </button>
    </div>

    {{-- Loading --}}
    <div id="loadingArea">
        <div class="loading-state">
            <div class="loading-spinner-lg"></div>
            <div class="loading-title">AI sedang menganalisis pasar global...</div>
            <div class="loading-sub">Biasanya memerlukan 5–10 detik</div>
        </div>
    </div>

    {{-- Empty state --}}
    <div id="emptyArea">
        <div class="empty-state">
            <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 16px;display:block;color:#ccc">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3>Mulai Riset Pasar Anda</h3>
            <p>Masukkan kategori produk di atas untuk mendapatkan<br>analisis pasar ekspor berbasis AI secara real-time.</p>
        </div>
    </div>

    {{-- Result area --}}
    <div id="resultArea">

        {{-- Error --}}
        <div id="errorArea" style="display:none">
            <div class="error-card">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <div class="error-title">Gagal memuat analisis</div>
                    <div class="error-msg" id="errorMsg">Terjadi kesalahan. Silakan coba lagi.</div>
                </div>
            </div>
        </div>

        {{-- ROW 1: Target Market + Insight AI (2-col, matches Figma) --}}
        <div class="market-grid">
            <div class="market-card">
                <div class="card-title">Target Market Terbaik</div>
                <div class="country-list" id="countryList"></div>
            </div>
            <div class="market-card">
                <div class="card-title">Insight AI</div>
                <div id="insightArea"></div>
            </div>
        </div>

        {{-- Narrative + recommended country --}}
        <div class="narrative-card" id="narrativeCard" style="display:none">
            <div style="font-size:14px;font-weight:700;color:var(--text-black);margin-bottom:14px;display:flex;align-items:center;gap:8px">
                <div style="width:8px;height:8px;border-radius:50%;background:var(--green-dark);flex-shrink:0"></div>
                Ringkasan Analisis AI
            </div>
            <p class="narrative-text" id="narrativeText"></p>
            <div class="rec-country-wrap" id="recCountryWrap" style="display:none">
                <div class="rec-country-icon" id="recCountryFlag"></div>
                <div>
                    <div class="rec-country-label">✦ Rekomendasi negara terbaik untuk mulai ekspor</div>
                    <div class="rec-country-name" id="recCountryName"></div>
                </div>
            </div>
        </div>

        {{-- Global Trends --}}
        <div id="trendsSection" style="display:none;margin-bottom:20px">
            <div class="section-title">Tren Global</div>
            <div class="trends-grid" id="trendsGrid"></div>
        </div>

        {{-- Export Opportunities --}}
        <div id="oppSection" style="display:none;margin-bottom:20px">
            <div class="section-title">Peluang Ekspor</div>
            <div class="opp-list" id="oppList"></div>
        </div>

        {{-- Competitor Landscape --}}
        <div class="competitor-card" id="competitorCard" style="display:none">
            <div class="card-title">Lanskap Persaingan</div>
            <div class="competitor-grid">
                <div>
                    <div class="comp-col-label">Negara Pesaing Utama</div>
                    <div id="competitorNames"></div>
                </div>
                <div>
                    <div class="comp-col-label">Keunggulan Indonesia</div>
                    <div class="comp-advantage" id="competitorAdvantage"></div>
                </div>
                <div>
                    <div class="comp-col-label">Tips Diferensiasi</div>
                    <div id="differentiationTips"></div>
                </div>
            </div>
        </div>

    </div>{{-- /resultArea --}}
</div>
@endsection

@push('scripts')
<script>
const TOKEN = localStorage.getItem('token');
if (!TOKEN) window.location.href = '/login/umkm';

/* ── Sidebar user info ── */
const user = JSON.parse(localStorage.getItem('user') || '{}');
if (user.name) {
    document.getElementById('sidebarName').textContent    = user.name;
    document.getElementById('sidebarInitial').textContent = user.name.charAt(0).toUpperCase();
    document.getElementById('sidebarSub').textContent     = user.city ? `UMKM · ${user.city}` : 'UMKM';
}
document.getElementById('logoutBtn').onclick = () => {
    localStorage.clear();
    window.location.href = '/login/umkm';
};

/* ── Baca assessment result dari localStorage (di-set oleh assessment/result page) ── */
let assessmentData = null;
try {
    const raw = localStorage.getItem('assessment_result');
    if (raw) {
        assessmentData = JSON.parse(raw);

        /* Tampilkan badge */
        const badge = document.getElementById('assessmentBadge');
        badge.classList.add('visible');
        document.getElementById('assessmentBadgeText').textContent =
            `Analisis dipersonalisasi · Skor Anda: ${assessmentData.score}/100 (${assessmentData.level})`;

        /* Pre-fill product dari assessment jika ada */
        if (assessmentData.product_category) {
            document.getElementById('productInput').value = assessmentData.product_category;
        }
    }
} catch(e) { assessmentData = null; }

/* ── Country code → emoji flag ── */
function flagEmoji(code) {
    if (!code || code.length !== 2) return '🌍';
    return [...code.toUpperCase()].map(c =>
        String.fromCodePoint(0x1F1E6 - 65 + c.charCodeAt(0))
    ).join('');
}

/* ── Show/hide state areas ── */
function showState(state) {
    document.getElementById('emptyArea').style.display   = state === 'empty'   ? 'block' : 'none';
    document.getElementById('loadingArea').style.display = state === 'loading' ? 'block' : 'none';
    document.getElementById('resultArea').style.display  = state === 'result'  ? 'block' : 'none';
}

/* ── Main: call API & render ── */
async function runAnalysis() {
    const product = document.getElementById('productInput').value.trim();
    if (!product) {
        const inp = document.getElementById('productInput');
        inp.focus();
        inp.style.borderColor = '#e54b4b';
        setTimeout(() => inp.style.borderColor = '#dde1e7', 1500);
        return;
    }

    /* Reset sections */
    ['narrativeCard','trendsSection','oppSection','competitorCard'].forEach(id => {
        document.getElementById(id).style.display = 'none';
    });
    document.getElementById('errorArea').style.display = 'none';

    /* UI: loading */
    const btn = document.getElementById('analyzeBtn');
    btn.disabled = true;
    document.getElementById('analyzeSpinner').style.display = 'block';
    document.getElementById('analyzeIcon').style.display    = 'none';
    document.getElementById('analyzeBtnText').textContent   = 'Menganalisis...';
    showState('loading');

    try {
        /* Build payload — sertakan context assessment jika ada */
        const body = { product_category: product };
        if (assessmentData) {
            body.assessment_score = assessmentData.score;
            body.readiness_level  = assessmentData.level;
            body.strengths        = assessmentData.strengths || [];
        }

        const res = await fetch('/api/v1/market/analyze', {
            method:  'POST',
            headers: {
                'Content-Type':  'application/json',
                'Authorization': `Bearer ${TOKEN}`,
                'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
            body: JSON.stringify(body),
        });

        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message || `HTTP ${res.status}`);
        }

        const data = await res.json();
        renderResult(data, product);
        showState('result');

    } catch (err) {
        document.getElementById('errorMsg').textContent = err.message || 'Terjadi kesalahan. Coba lagi.';
        document.getElementById('errorArea').style.display = 'block';
        showState('result');
    } finally {
        btn.disabled = false;
        document.getElementById('analyzeSpinner').style.display = 'none';
        document.getElementById('analyzeIcon').style.display    = 'block';
        document.getElementById('analyzeBtnText').textContent   = 'Analisis Pasar';
    }
}

/* ── Render semua sections dari response Gemini ── */
function renderResult(data, product) {

    /* 1. TARGET COUNTRIES */
    const countries = data.recommended_countries || [];
    const maxScore  = Math.max(...countries.map(c => c.match_score || 0), 0.01);
    const diffLabel = { easy: 'Mudah Masuk', medium: 'Sedang', hard: 'Kompetitif' };
    const diffClass = { easy: 'diff-easy',   medium: 'diff-medium', hard: 'diff-hard' };

    document.getElementById('countryList').innerHTML = countries.length
        ? countries.map(c => {
            const pct    = Math.round((c.match_score / maxScore) * 100);
            const dClass = diffClass[c.entry_difficulty] || 'diff-medium';
            const dLabel = diffLabel[c.entry_difficulty] || c.entry_difficulty;
            const reqs   = (c.key_requirements || [])
                .map(r => `<span class="req-chip">${r}</span>`).join('');
            return `
            <div class="country-item">
                <div class="country-top-row">
                    <div class="country-name-wrap">
                        <span class="country-flag">${flagEmoji(c.country_code)}</span>
                        <div>
                            <div class="country-name">${c.country}</div>
                            <div class="country-meta">
                                <span class="difficulty-badge ${dClass}">${dLabel}</span>
                                ${c.estimated_market_size_usd
                                    ? `<span class="market-size">~USD ${c.estimated_market_size_usd}</span>`
                                    : ''}
                            </div>
                        </div>
                    </div>
                    <span class="country-pct">${pct}%</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" data-w="${pct}"></div>
                </div>
                ${c.reason ? `<div class="country-reason">${c.reason}</div>` : ''}
                ${reqs ? `<div class="req-chips">${reqs}</div>` : ''}
            </div>`;
        }).join('')
        : '<p style="font-size:13px;color:#aab0bb">Tidak ada data negara tersedia.</p>';

    /* Animate progress bars setelah DOM update */
    requestAnimationFrame(() => {
        document.querySelectorAll('.progress-fill[data-w]').forEach(el => {
            el.style.width = el.dataset.w + '%';
        });
    });

    /* 2. INSIGHT AI — export_opportunities sebagai bullet di green card */
    const opps = data.export_opportunities || [];
    const insightArea = document.getElementById('insightArea');

    if (opps.length) {
        const bullets = opps.slice(0, 5)
            .map(o => `
            <div class="insight-item">
                <span class="insight-dot">✦</span>
                <span><strong>${o.opportunity}</strong>: ${o.description}</span>
            </div>`).join('');
        insightArea.innerHTML = `
        <div class="insight-block">
            <div class="insight-block-title">Peluang Market untuk ${product}</div>
            <div class="insight-list">${bullets}</div>
        </div>`;
    } else if (data.narrative_summary) {
        insightArea.innerHTML = `
        <div class="insight-block">
            <div class="insight-block-title">Ringkasan Peluang</div>
            <div class="insight-list">
                <div class="insight-item">
                    <span class="insight-dot">✦</span>
                    <span>${data.narrative_summary}</span>
                </div>
            </div>
        </div>`;
    }

    /* 3. NARRATIVE + RECOMMENDED COUNTRY */
    if (data.narrative_summary) {
        document.getElementById('narrativeText').textContent = data.narrative_summary;
        document.getElementById('narrativeCard').style.display = 'block';
    }
    if (data.recommended_starting_country) {
        const recName    = data.recommended_starting_country;
        const recCountry = countries.find(c =>
            c.country.toLowerCase() === recName.toLowerCase()
        );
        document.getElementById('recCountryFlag').textContent = flagEmoji(recCountry?.country_code || '');
        document.getElementById('recCountryName').textContent = recName;
        document.getElementById('recCountryWrap').style.display = 'flex';
    }

    /* 4. GLOBAL TRENDS */
    const trends = data.global_trends || [];
    if (trends.length) {
        const impMap = {
            positive: { cls: 'impact-positive', icon: '↑ Positif' },
            negative: { cls: 'impact-negative', icon: '↓ Negatif' },
            neutral:  { cls: 'impact-neutral',  icon: '→ Netral'  },
        };
        document.getElementById('trendsGrid').innerHTML = trends.map(t => {
            const imp = impMap[t.impact] || impMap.neutral;
            return `
            <div class="trend-card">
                <div class="trend-impact ${imp.cls}">${imp.icon}</div>
                <div class="trend-name">${t.trend}</div>
                <div class="trend-desc">${t.description}</div>
            </div>`;
        }).join('');
        document.getElementById('trendsSection').style.display = 'block';
    }

    /* 5. EXPORT OPPORTUNITIES (full list) */
    if (opps.length) {
        const urgLabel = { high: 'Urgent', medium: 'Sedang', low: 'Jangka Panjang' };
        const urgClass = { high: 'urgency-high', medium: 'urgency-medium', low: 'urgency-low' };
        document.getElementById('oppList').innerHTML = opps.map(o => `
        <div class="opp-item">
            <span class="urgency-badge ${urgClass[o.urgency] || 'urgency-low'}">${urgLabel[o.urgency] || o.urgency}</span>
            <div>
                <div class="opp-title">${o.opportunity}</div>
                <div class="opp-desc">${o.description}</div>
            </div>
        </div>`).join('');
        document.getElementById('oppSection').style.display = 'block';
    }

    /* 6. COMPETITOR LANDSCAPE */
    const comp = data.competitor_landscape;
    if (comp && Object.keys(comp).length) {
        document.getElementById('competitorNames').innerHTML =
            (comp.main_competitors || []).map(c => `<span class="comp-tag">${c}</span>`).join('');
        document.getElementById('competitorAdvantage').textContent = comp.indonesia_advantage || '-';
        document.getElementById('differentiationTips').innerHTML =
            (comp.differentiation_tips || []).map(t =>
                `<div class="diff-tip"><span class="diff-tip-arrow">→</span><span>${t}</span></div>`
            ).join('');
        document.getElementById('competitorCard').style.display = 'block';
    }
}

/* ── Auto-run jika user datang dari assessment & product sudah terisi ── */
window.addEventListener('DOMContentLoaded', () => {
    const val = document.getElementById('productInput').value.trim();
    if (assessmentData && val) {
        setTimeout(runAnalysis, 400);
    }
});
</script>
@endpush
