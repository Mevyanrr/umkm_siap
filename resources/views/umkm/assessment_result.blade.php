@extends('layouts.app')

@push('styles')
<style>
    :root { --sidebar-w: 240px; }

    body { display: flex; min-height: 100vh; background: #f7f8fa; }

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

    /* ===== MAIN ===== */
    .main-content { margin-left: var(--sidebar-w); flex: 1; padding: 40px 48px; max-width: 900px; }
    .page-header { margin-bottom: 28px; }
    .page-header h1 { font-size: 26px; font-weight: 800; color: var(--text-black); margin-bottom: 6px; }
    .page-header p  { font-size: 14px; color: var(--text-muted); }

    /* ===== SCORE CARD ===== */
    .score-card {
        background: white; border-radius: 20px;
        padding: 40px; margin-bottom: 20px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.07);
        text-align: center;
    }
    .score-ring-wrap { margin-bottom: 20px; }
    .score-ring {
        width: 120px; height: 120px; border-radius: 50%;
        background: var(--green-dark);
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        margin: 0 auto;
        box-shadow: 0 8px 24px rgba(11,110,94,0.25);
    }
    .score-number { font-size: 40px; font-weight: 800; color: white; line-height: 1; }
    .score-sub    { font-size: 12px; color: rgba(255,255,255,0.75); font-weight: 500; margin-top: 2px; }

    .score-level { font-size: 22px; font-weight: 800; color: var(--text-black); margin-bottom: 10px; }
    .score-desc  { font-size: 14px; color: var(--text-muted); max-width: 480px; margin: 0 auto 24px; line-height: 1.6; }

    .score-actions { display: flex; gap: 12px; justify-content: center; }
    .btn-outline {
        padding: 13px 28px; border-radius: 12px;
        border: 2px solid #dde1e7; background: white;
        font-size: 14px; font-weight: 600; color: #444;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-outline:hover { border-color: var(--green-dark); color: var(--green-dark); }
    .btn-primary {
        padding: 13px 28px; border-radius: 12px;
        background: var(--green-dark); color: white;
        font-size: 14px; font-weight: 700; border: none;
        cursor: pointer; display: flex; align-items: center; gap: 8px;
        transition: background 0.2s;
    }
    .btn-primary:hover { background: var(--green-darkmore); }

    /* ===== REKOMENDASI ===== */
    .reko-card {
        background: var(--green-light); border-radius: 20px;
        padding: 28px 32px; margin-bottom: 20px;
        border: 1.5px solid #b8ddd3;
    }
    .reko-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .reko-icon {
        width: 40px; height: 40px; border-radius: 10px;
        background: var(--yellow-light); display: flex;
        align-items: center; justify-content: center; font-size: 20px;
        flex-shrink: 0;
    }
    .reko-title { font-size: 16px; font-weight: 700; color: var(--green-dark); }
    .reko-list { list-style: none; display: flex; flex-direction: column; gap: 10px; }
    .reko-list li {
        display: flex; align-items: flex-start; gap: 10px;
        font-size: 14px; color: #2a5a4e; line-height: 1.5;
    }
    .reko-arrow { color: var(--green-dark); font-weight: 700; flex-shrink: 0; margin-top: 1px; }

    /* ===== GRID 2 col ===== */
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }

    .info-card { background: white; border-radius: 18px; padding: 28px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
    .info-card-title { font-size: 14px; font-weight: 700; color: var(--text-black); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .info-card-title .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--green-dark); }

    .tag-list { display: flex; flex-wrap: wrap; gap: 8px; }
    .tag {
        padding: 6px 14px; border-radius: 20px;
        font-size: 13px; font-weight: 500;
    }
    .tag-green  { background: var(--green-light); color: var(--green-dark); }
    .tag-red    { background: #fff0f0; color: #c0392b; }
    .tag-yellow { background: var(--yellow-soft); color: var(--yellow-dark); }

    .action-list { display: flex; flex-direction: column; gap: 10px; }
    .action-item {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 12px 14px; border-radius: 12px; background: #f7f8fa;
    }
    .priority-badge {
        padding: 3px 9px; border-radius: 20px;
        font-size: 11px; font-weight: 700; text-transform: uppercase; flex-shrink: 0;
    }
    .priority-high   { background: #ffe5e5; color: #c0392b; }
    .priority-medium { background: #fff3cd; color: #856404; }
    .priority-low    { background: var(--green-light); color: var(--green-dark); }
    .action-text { font-size: 13px; color: var(--text-black); line-height: 1.5; }

    /* Narrative */
    .narrative-card {
        background: white; border-radius: 18px; padding: 28px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06); margin-bottom: 20px;
    }
    .narrative-text { font-size: 14px; color: #444; line-height: 1.8; }

    /* Loading overlay */
    #loadingOverlay {
        position: fixed; inset: 0; background: rgba(255,255,255,0.9);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        z-index: 999; gap: 16px;
    }
    .loading-spinner {
        width: 48px; height: 48px; border: 4px solid #e8ecef;
        border-top-color: var(--green-dark); border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .loading-text { font-size: 16px; font-weight: 600; color: var(--text-muted); }

    .no-result {
        text-align: center; padding: 80px 40px; color: #aab0bb;
    }
    .no-result h3 { font-size: 20px; font-weight: 700; margin-bottom: 8px; color: #777; }
</style>
@endpush

@section('content')
<!-- Loading Overlay -->
<div id="loadingOverlay">
    <div class="loading-spinner"></div>
    <div class="loading-text">Memuat hasil analisis AI...</div>
</div>

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

<div class="main-content" id="mainContent" style="display:none">
    <div class="page-header">
        <h1>Assessment Kesiapan Ekspor</h1>
        <p>Hasil penilaian AI terhadap kesiapan ekspor UMKM Anda.</p>
    </div>

    <!-- Score Card -->
    <div class="score-card">
        <div class="score-ring-wrap">
            <div class="score-ring">
                <div class="score-number" id="scoreNumber">0</div>
                <div class="score-sub">Score</div>
            </div>
        </div>
        <div class="score-level" id="scoreLevel">-</div>
        <div class="score-desc" id="scoreDesc">-</div>
        <div class="score-actions">
            <button class="btn-outline" onclick="window.location.href='/umkm/assessment'">Ulangi Assessment</button>
            <button class="btn-primary" onclick="window.location.href='/umkm/market'">
                Lanjut ke Market Intelligence
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>
        </div>
    </div>

    <!-- Rekomendasi -->
    <div class="reko-card" id="rekoCard" style="display:none">
        <div class="reko-header">
            <div class="reko-icon">✦</div>
            <div class="reko-title">Rekomendasi untuk Anda</div>
        </div>
        <p style="font-size:14px;color:#2a5a4e;margin-bottom:14px;line-height:1.6">
            Berdasarkan analisis AI kami, berikut langkah-langkah prioritas untuk meningkatkan skor Anda:
        </p>
        <ul class="reko-list" id="rekoList"></ul>
    </div>

    <!-- Narasi AI -->
    <div class="narrative-card" id="narrativeCard" style="display:none">
        <div class="info-card-title"><div class="dot"></div> Analisis AI</div>
        <p class="narrative-text" id="narrativeText"></p>
    </div>

    <!-- Grid: Kekuatan & Risiko -->
    <div class="grid-2">
        <div class="info-card" id="strengthCard" style="display:none">
            <div class="info-card-title"><div class="dot" style="background:#27ae60"></div> Kekuatan Anda</div>
            <div class="tag-list" id="strengthList"></div>
        </div>
        <div class="info-card" id="riskCard" style="display:none">
            <div class="info-card-title"><div class="dot" style="background:#e74c3c"></div> Faktor Risiko</div>
            <div class="tag-list" id="riskList"></div>
        </div>
    </div>

    <!-- Priority Actions -->
    <div class="info-card" id="actionsCard" style="display:none">
        <div class="info-card-title"><div class="dot"></div> Langkah Prioritas</div>
        <div class="action-list" id="actionList"></div>
    </div>

    <!-- Sertifikasi -->
    <div class="info-card" id="certCard" style="display:none" style="margin-top:20px">
        <div class="info-card-title"><div class="dot" style="background:var(--yellow-dark)"></div> Sertifikasi yang Direkomendasikan</div>
        <div class="tag-list" id="certList"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const TOKEN = localStorage.getItem('token');
if (!TOKEN) window.location.href = '/login/umkm';

const user = JSON.parse(localStorage.getItem('user') || '{}');
if (user.name) {
    document.getElementById('sidebarName').textContent    = user.name;
    document.getElementById('sidebarInitial').textContent = user.name.charAt(0).toUpperCase();
    document.getElementById('sidebarSub').textContent     = user.city ? `UMKM · ${user.city}` : 'UMKM';
}
document.getElementById('logoutBtn').onclick = () => { localStorage.clear(); window.location.href = '/login/umkm'; };

function getLevelDesc(level, score) {
    const descs = {
        'Siap Ekspor': 'UMKM Anda sangat siap untuk memulai ekspor! Pertahankan performa ini dan segera cari buyer internasional.',
        'Siap Ekspor dengan Beberapa Perbaikan': 'UMKM Anda memiliki potensi besar! Ada beberapa area yang perlu diperbaiki untuk mencapai kesiapan 100%.',
        'Perlu Persiapan Lebih Lanjut': 'Anda sudah punya fondasi yang baik. Fokus pada peningkatan dokumen legal dan kapasitas produksi.',
        'Belum Siap Ekspor': 'Jangan menyerah! Mulai dari melengkapi dokumen dasar seperti NIB dan NPWP terlebih dahulu.',
    };
    return descs[level] || 'Terus tingkatkan kesiapan ekspor Anda.';
}

function renderResult(result) {
    // Score
    document.getElementById('scoreNumber').textContent = result.score;
    document.getElementById('scoreLevel').textContent  = result.level;
    document.getElementById('scoreDesc').textContent   = getLevelDesc(result.level, result.score);

    // Priority actions as rekomendasi (top 3 high)
    const highActions = (result.priority_actions || [])
        .filter(a => a.priority === 'high').slice(0, 4);
    if (highActions.length) {
        document.getElementById('rekoCard').style.display = 'block';
        document.getElementById('rekoList').innerHTML = highActions
            .map(a => `<li><span class="reko-arrow">→</span> ${a.task}</li>`).join('');
    }

    // Narrative
    if (result.narrative) {
        document.getElementById('narrativeCard').style.display = 'block';
        document.getElementById('narrativeText').textContent = result.narrative;
    }

    // Strengths
    if (result.strengths?.length) {
        document.getElementById('strengthCard').style.display = 'block';
        document.getElementById('strengthList').innerHTML = result.strengths
            .map(s => `<span class="tag tag-green">${s}</span>`).join('');
    }

    // Risks
    if (result.risk_factors?.length) {
        document.getElementById('riskCard').style.display = 'block';
        document.getElementById('riskList').innerHTML = result.risk_factors
            .map(r => `<span class="tag tag-red">${r}</span>`).join('');
    }

    // All priority actions
    if (result.priority_actions?.length) {
        document.getElementById('actionsCard').style.display = 'block';
        document.getElementById('actionList').innerHTML = result.priority_actions
            .map(a => `<div class="action-item">
                <span class="priority-badge priority-${a.priority}">${a.priority}</span>
                <span class="action-text">${a.task}</span>
            </div>`).join('');
    }

    // Certifications
    if (result.recommended_certifications?.length) {
        document.getElementById('certCard').style.display = 'block';
        document.getElementById('certList').innerHTML = result.recommended_certifications
            .map(c => `<span class="tag tag-yellow">${c}</span>`).join('');
    }

    document.getElementById('loadingOverlay').style.display = 'none';
    document.getElementById('mainContent').style.display    = 'block';
}

// Load from localStorage (set by assessment page after submit)
window.addEventListener('DOMContentLoaded', () => {
    const raw = localStorage.getItem('assessment_result');
    if (raw) {
        try {
            renderResult(JSON.parse(raw));
        } catch {
            showNoResult();
        }
    } else {
        showNoResult();
    }
});

function showNoResult() {
    document.getElementById('loadingOverlay').style.display = 'none';
    document.getElementById('mainContent').style.display    = 'block';
    document.getElementById('mainContent').innerHTML = `
        <div class="no-result">
            <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 16px;display:block;color:#ccc"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <h3>Belum Ada Hasil Assessment</h3>
            <p style="font-size:14px;color:#aab0bb;margin-bottom:24px">Selesaikan assessment terlebih dahulu untuk melihat hasil analisis AI.</p>
            <a href="/umkm/assessment" style="display:inline-flex;align-items:center;gap:8px;padding:13px 28px;background:var(--green-dark);color:white;border-radius:12px;font-weight:700;font-size:14px;text-decoration:none">
                Mulai Assessment
            </a>
        </div>`;
}
</script>
@endpush