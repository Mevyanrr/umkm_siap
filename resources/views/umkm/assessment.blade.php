@extends('layouts.app')

@push('styles')
<style>
    :root {
        --sidebar-w: 240px;
    }

    body {
        display: flex;
        min-height: 100vh;
        background: #f7f8fa;
    }

    /* ===== SIDEBAR ===== */
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
    .sidebar-brand img { height: 36px; }
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
        background: var(--green-dark); color: white;
        display: flex; align-items: center; justify-content: center;
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
    }
    .sidebar-logout:hover { background: #fff0f0; }

    /* ===== MAIN ===== */
    .main-content {
        margin-left: var(--sidebar-w);
        flex: 1;
        padding: 40px 48px;
        max-width: 860px;
    }

    .page-header { margin-bottom: 32px; }
    .page-header h1 { font-size: 26px; font-weight: 800; color: var(--text-black); margin-bottom: 6px; }
    .page-header p  { font-size: 14px; color: var(--text-muted); }

    /* ===== PROGRESS BAR ===== */
    .progress-bar-wrap {
        background: white; border-radius: 16px;
        padding: 20px 24px; margin-bottom: 28px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        display: flex; align-items: center; gap: 16px;
    }
    .progress-track {
        flex: 1; height: 8px; background: #e8ecef; border-radius: 99px; overflow: hidden;
    }
    .progress-fill {
        height: 100%; background: var(--green-dark); border-radius: 99px;
        transition: width 0.4s ease;
    }
    .progress-label { font-size: 13px; font-weight: 600; color: var(--green-dark); white-space: nowrap; }

    /* ===== QUESTION CARD ===== */
    .question-card {
        background: white; border-radius: 18px;
        padding: 32px; margin-bottom: 16px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        border: 1.5px solid transparent;
        transition: border-color 0.2s;
    }
    .question-card.answered { border-color: var(--green-light); }

    .q-label {
        font-size: 11px; font-weight: 700; color: var(--green-dark);
        text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;
    }
    .q-text {
        font-size: 17px; font-weight: 700; color: var(--text-black); margin-bottom: 20px;
    }

    /* Boolean options */
    .options-bool { display: flex; gap: 12px; }
    .opt-btn {
        flex: 1; padding: 14px 20px;
        border-radius: 12px; border: 2px solid #dde1e7;
        background: white; font-size: 14px; font-weight: 600;
        color: #555; cursor: pointer; display: flex;
        align-items: center; justify-content: center; gap: 8px;
        transition: all 0.2s;
    }
    .opt-btn:hover { border-color: var(--green-dark); color: var(--green-dark); }
    .opt-btn.selected {
        border-color: var(--green-dark); background: var(--green-light);
        color: var(--green-dark);
    }

    /* Select options */
    .options-select { display: flex; flex-direction: column; gap: 10px; }
    .opt-select {
        padding: 13px 18px; border-radius: 12px;
        border: 2px solid #dde1e7; background: white;
        font-size: 14px; font-weight: 500; color: #444;
        cursor: pointer; display: flex; align-items: center; gap: 12px;
        transition: all 0.2s;
    }
    .opt-select .radio-dot {
        width: 18px; height: 18px; border-radius: 50%;
        border: 2px solid #ccc; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .opt-select.selected { border-color: var(--green-dark); background: var(--green-light); color: var(--green-dark); }
    .opt-select.selected .radio-dot {
        border-color: var(--green-dark); background: var(--green-dark);
    }
    .opt-select.selected .radio-dot::after {
        content: ''; width: 6px; height: 6px;
        background: white; border-radius: 50%;
    }

    /* Text input */
    .q-input {
        width: 100%; padding: 13px 16px;
        border: 2px solid #dde1e7; border-radius: 12px;
        font-size: 14px; font-family: inherit; color: var(--text-black);
        transition: border-color 0.2s; outline: none;
    }
    .q-input:focus { border-color: var(--green-dark); }

    /* Number input */
    .number-wrap { position: relative; }
    .number-wrap .q-input { padding-right: 80px; }
    .number-suffix {
        position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
        font-size: 13px; color: #aab0bb; font-weight: 500;
    }

    /* Scale */
    .scale-wrap { display: flex; gap: 10px; }
    .scale-btn {
        flex: 1; padding: 12px 8px;
        border-radius: 10px; border: 2px solid #dde1e7;
        background: white; font-size: 15px; font-weight: 700;
        color: #555; cursor: pointer; text-align: center;
        transition: all 0.2s;
    }
    .scale-btn:hover { border-color: var(--green-dark); color: var(--green-dark); }
    .scale-btn.selected { border-color: var(--green-dark); background: var(--green-dark); color: white; }
    .scale-labels { display: flex; justify-content: space-between; margin-top: 6px; }
    .scale-labels span { font-size: 11px; color: #aab0bb; }

    /* Category header */
    .category-header {
        font-size: 13px; font-weight: 700; color: var(--green-dark);
        letter-spacing: 0.5px; margin: 28px 0 12px;
        padding-left: 4px;
        display: flex; align-items: center; gap: 8px;
    }
    .category-header::after {
        content: ''; flex: 1; height: 1px; background: var(--green-light);
    }

    /* ===== CTA BUTTON ===== */
    .cta-wrap { margin-top: 32px; display: flex; justify-content: flex-end; }
    .btn-primary {
        padding: 15px 36px; border-radius: 14px;
        background: var(--green-dark); color: white;
        font-size: 15px; font-weight: 700; border: none;
        cursor: pointer; display: flex; align-items: center; gap: 10px;
        transition: background 0.2s, transform 0.1s;
    }
    .btn-primary:hover { background: var(--green-darkmore); }
    .btn-primary:active { transform: scale(0.98); }
    .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

    /* Loading spinner */
    .spinner {
        width: 18px; height: 18px; border: 3px solid rgba(255,255,255,0.3);
        border-top-color: white; border-radius: 50%;
        animation: spin 0.8s linear infinite; display: none;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
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

<div class="main-content">
    <div class="page-header">
        <h1>Assessment Kesiapan Ekspor</h1>
        <p>Jawab pertanyaan untuk mengetahui tingkat kesiapan ekspor bisnis Anda.</p>
    </div>

    <div class="progress-bar-wrap">
        <div class="progress-track">
            <div class="progress-fill" id="progressFill" style="width: 0%"></div>
        </div>
        <span class="progress-label" id="progressLabel">0 / 12 dijawab</span>
    </div>

    <div id="questionsContainer">
        <div style="text-align:center; padding: 60px; color: #aab0bb;">
            <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin: 0 auto 12px; display:block; animation: spin 1s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Memuat pertanyaan...
        </div>
    </div>

    <div class="cta-wrap">
        <button class="btn-primary" id="submitBtn" disabled onclick="submitAssessment()">
            <span class="spinner" id="submitSpinner"></span>
            <span id="submitText">Lihat Hasil Analisis</span>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
const TOKEN = localStorage.getItem('token');
if (!TOKEN) window.location.href = '/login/umkm';

let questions = [];
let answers   = {};

// Sidebar user info
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

// Fetch questions
async function loadQuestions() {
    try {
        const res = await fetch('/api/v1/assessment/questions', {
            headers: { 'Authorization': `Bearer ${TOKEN}` }
        });
        const data = await res.json();
        questions = data.categories.flatMap(cat =>
            cat.questions.map(q => ({ ...q, category: cat.label }))
        );
        renderQuestions();
    } catch (e) {
        document.getElementById('questionsContainer').innerHTML =
            '<p style="color:#e54b4b;text-align:center;padding:40px">Gagal memuat pertanyaan. Refresh halaman.</p>';
    }
}

function renderQuestions() {
    const container = document.getElementById('questionsContainer');
    let html = '';
    let lastCategory = '';
    let qNum = 0;

    questions.forEach((q, idx) => {
        if (q.category !== lastCategory) {
            html += `<div class="category-header">${q.category}</div>`;
            lastCategory = q.category;
        }
        qNum++;
        html += `<div class="question-card" id="card_${q.id}">
            <div class="q-label">PERTANYAAN ${qNum} dari ${questions.length}</div>
            <div class="q-text">${q.text}</div>
            ${renderInput(q)}
        </div>`;
    });

    container.innerHTML = html;
    updateProgress();
}

function renderInput(q) {
    if (q.type === 'boolean') {
        return `<div class="options-bool">
            <button class="opt-btn" onclick="setAnswer('${q.id}', true, this)" data-val="true">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Ya
            </button>
            <button class="opt-btn" onclick="setAnswer('${q.id}', false, this)" data-val="false">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                Tidak
            </button>
        </div>`;
    }

    if (q.type === 'select') {
        return `<div class="options-select">
            ${q.options.map(opt => `
                <div class="opt-select" onclick="setAnswer('${q.id}', '${opt}', this)">
                    <div class="radio-dot"></div>
                    ${opt}
                </div>
            `).join('')}
        </div>`;
    }

    if (q.type === 'text') {
        return `<input type="text" class="q-input" placeholder="Masukkan nama atau deskripsi singkat produk"
            oninput="setAnswerText('${q.id}', this.value)" />`;
    }

    if (q.type === 'number') {
        return `<div class="number-wrap">
            <input type="number" class="q-input" placeholder="0" min="0"
                oninput="setAnswerText('${q.id}', this.value)" />
            <span class="number-suffix">unit/bulan</span>
        </div>`;
    }

    if (q.type === 'scale') {
        const btns = Array.from({length: q.max}, (_, i) => i + 1).map(n => `
            <button class="scale-btn" onclick="setAnswer('${q.id}', ${n}, this)">${n}</button>
        `).join('');
        return `<div>
            <div class="scale-wrap">${btns}</div>
            <div class="scale-labels"><span>Tidak familiar</span><span>Sangat familiar</span></div>
        </div>`;
    }

    return '';
}

function setAnswer(id, value, el) {
    answers[id] = value;
    // Deselect siblings
    const parent = el.closest('.options-bool, .options-select, .scale-wrap');
    parent.querySelectorAll('.opt-btn, .opt-select, .scale-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('card_' + id)?.classList.add('answered');
    updateProgress();
}

function setAnswerText(id, value) {
    if (value.trim()) {
        answers[id] = q.type === 'number' ? parseInt(value) : value;
        document.getElementById('card_' + id)?.classList.add('answered');
    } else {
        delete answers[id];
        document.getElementById('card_' + id)?.classList.remove('answered');
    }
    updateProgress();
}

// Fix: text/number need different handler
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('q-input')) {
        const card = e.target.closest('.question-card');
        const id   = card?.id?.replace('card_', '');
        if (!id) return;
        const q = questions.find(q => q.id === id);
        if (!q) return;
        const val = e.target.value.trim();
        if (val) {
            answers[id] = q.type === 'number' ? parseInt(val) : val;
            card.classList.add('answered');
        } else {
            delete answers[id];
            card.classList.remove('answered');
        }
        updateProgress();
    }
});

function updateProgress() {
    const total     = questions.length;
    const answered  = Object.keys(answers).length;
    const pct       = total ? Math.round((answered / total) * 100) : 0;
    document.getElementById('progressFill').style.width  = pct + '%';
    document.getElementById('progressLabel').textContent = `${answered} / ${total} dijawab`;
    document.getElementById('submitBtn').disabled = answered < total;
}

async function submitAssessment() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    document.getElementById('submitSpinner').style.display = 'block';
    document.getElementById('submitText').textContent = 'Menganalisis...';

    const payload = questions.map(q => ({ id: q.id, value: answers[q.id] ?? null }));

    try {
        const res = await fetch('/api/v1/assessment/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${TOKEN}`
            },
            body: JSON.stringify({ answers: payload })
        });

        if (!res.ok) throw new Error('Gagal submit');
        const result = await res.json();

        // Simpan ke localStorage untuk halaman hasil
        localStorage.setItem('assessment_result', JSON.stringify(result));
        window.location.href = '/umkm/assessment/result';

    } catch (e) {
        alert('Gagal mengirim assessment. Coba lagi.');
        btn.disabled = false;
        document.getElementById('submitSpinner').style.display = 'none';
        document.getElementById('submitText').textContent = 'Lihat Hasil Analisis';
    }
}

loadQuestions();
</script>
@endpush
