@extends('layouts.app')

@push('styles')
<style>
    body { min-height: 100vh; display: flex; }

    .left-panel {
        width: 50%;
        background: var(--yellow-light); /* ← kuning */
        padding: 60px 64px;
        display: flex; flex-direction: column;
        justify-content: center;
        position: relative; overflow: hidden;
    }

    .left-panel::before {
        content: '';
        position: absolute; inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Ccircle cx='40' cy='40' r='35' fill='rgba(0,0,0,0.04)'/%3E%3Ccircle cx='140' cy='80' r='50' fill='rgba(0,0,0,0.04)'/%3E%3Ccircle cx='80' cy='160' r='40' fill='rgba(0,0,0,0.04)'/%3E%3Ccircle cx='170' cy='170' r='30' fill='rgba(0,0,0,0.04)'/%3E%3C/svg%3E");
        background-size: 200px 200px;
    }

    .left-content { position: relative; z-index: 1; }

    .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 36px; }
    .brand img { height: 68px; width: auto; filter: brightness(0); /* ← hitam */ }

    .left-panel h2 {
        font-size: clamp(28px, 3vw, 40px); font-weight: 800;
        color: var(--text-black); /* ← hitam */ line-height: 1.2; margin-bottom: 20px;
    }

    .left-panel p {
        font-size: 15px; color: rgba(0,0,0,0.65);
        line-height: 1.7; margin-bottom: 36px; max-width: 360px;
    }

    .benefit-list { list-style: none; display: flex; flex-direction: column; gap: 16px; }
    .benefit-list li { display: flex; align-items: center; gap: 12px; font-size: 14px; color: rgba(0,0,0,0.75); }
    .benefit-list li .check-icon {
        width: 24px; height: 24px; border-radius: 50%;
        background: rgba(0,0,0,0.12);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 12px; color: var(--text-black);
    }

    .right-panel {
        width: 50%;
        display: flex; align-items: center; justify-content: center;
        padding: 60px 80px; background: var(--white);
    }

    .form-wrapper { width: 100%; max-width: 500px; }
    .form-wrapper h1 { font-size: 32px; font-weight: 800; color: var(--text-black); margin-bottom: 8px; }
    .form-wrapper .subtitle { font-size: 14px; color: var(--text-muted); margin-bottom: 32px; }

    .role-selector { display: flex; gap: 16px; margin-bottom: 24px; }
    .role-card {
        flex: 1; display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        gap: 4px; padding: 18.5px 20px;
        border-radius: 14px; border: 2px solid #BABABA;
        background-color: var(--white);
        text-decoration: none; cursor: pointer;
        transition: border-color 0.25s ease, background-color 0.25s ease, box-shadow 0.25s ease;
    }
    .role-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .role-title { font-size: 18px; font-weight: 700; color: var(--text-black); }
    .role-sub   { font-size: 13px; color: #6b7280; }

    .role-card--umkm.active  { border-color: #2DA44E; background-color: var(--green-light); }
    .role-card--buyer.active { border-color: #F5A623; background-color: var(--yellow-soft); }

    .form-group { margin-bottom: 20px; }
    .form-group label {
        display: block; font-size: 11px; font-weight: 700;
        letter-spacing: 1.5px; text-transform: uppercase;
        color: var(--text-black); margin-bottom: 8px;
    }
    .form-group input {
        width: 100%; padding: 14px 16px;
        border: 1.5px solid #e0e0e0; border-radius: 8px;
        font-size: 14px; font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--text-black); background: var(--white);
        outline: none; transition: border-color 0.2s;
    }
    .form-group input::placeholder { color: #bbb; }
    .form-group input:focus  { border-color: var(--yellow-dark); /* ← kuning saat focus */ }
    .form-group input.error  { border-color: #e74c3c; }
    .form-group input.success{ border-color: #27ae60; }

    .error-msg { font-size: 12px; color: #e74c3c; margin-top: 6px; display: none; }
    .error-msg.show { display: block; }

    .btn-submit {
        width: 100%; padding: 16px;
        background: var(--yellow-light); /* ← kuning */ color: var(--text-black);
        font-size: 15px; font-weight: 700;
        font-family: 'Plus Jakarta Sans', sans-serif;
        border: none; border-radius: 10px;
        cursor: pointer; transition: all 0.25s;
        margin-top: 8px; margin-bottom: 20px;
    }
    .btn-submit:hover { background: var(--yellow-dark); transform: translateY(-1px); }

    .login-link { text-align: center; font-size: 14px; color: var(--text-muted); }
    .login-link a { color: var(--text-black); font-weight: 700; text-decoration: underline; }
    .login-link a:hover { color: var(--yellow-dark); }

    @media (max-width: 900px) {
        body { flex-direction: column; }
        .left-panel, .right-panel { width: 100%; padding: 48px 32px; }
    }
</style>
@endpush

@section('content')

<div class="left-panel">
    <div class="left-content">
        <div class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="UMKM SIAP">
        </div>
        <h2>Akses Produk Berkualitas</h2>
        <p>Bergabunglah dengan ratusan buyer yang sudah menemukan produk terbaik.</p>
        <ul class="benefit-list">
            <li><span class="check-icon">✓</span> Gratis browsing & kontak eksportir</li>
            <li><span class="check-icon">✓</span> Verifikasi produk & sertifikasi</li>
            <li><span class="check-icon">✓</span> Support untuk B2B transaction</li>
        </ul>
    </div>
</div>

<div class="right-panel">
    <div class="form-wrapper">
        <h1>Daftar Akun Gratis</h1>
        <p class="subtitle">Mulai ekspor atau temukan produk Indonesia hari ini.</p>

        <div class="role-selector">
            <a href="{{ route('register.umkm') }}" id="btn-umkm"
                class="role-card role-card--umkm {{ request()->routeIs('register.umkm*') ? 'active' : '' }}">
                <span class="role-title">UMKM</span>
                <span class="role-sub">Eksportir</span>
            </a>
            <a href="{{ route('register.buyer') }}" id="btn-buyer"
                class="role-card role-card--buyer {{ request()->routeIs('register.buyer*') ? 'active' : '' }}">
                <span class="role-title">Buyer</span>
                <span class="role-sub">Pembeli</span>
            </a>
        </div>

        <form id="registerForm" method="POST" action="{{ route('register.buyer.post') }}" novalidate>
            @csrf
            <input type="hidden" name="role" value="buyer">

            <div class="form-group">
                <label for="nama_pengusaha">Nama Pengusaha</label>
                <input type="text" id="nama_pengusaha" name="nama_pengusaha"
                       placeholder="ABC Mulia" autocomplete="off">
                <span class="error-msg" id="err-nama">Nama pengusaha wajib diisi.</span>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       placeholder="nama@email.com" autocomplete="off">
                <span class="error-msg" id="err-email">Format email tidak valid.</span>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Min. 8 karakter">
                <span class="error-msg" id="err-password">Password min. 8 karakter, harus ada huruf besar, huruf kecil, dan angka.</span>
            </div>

            <button type="submit" class="btn-submit">Daftar Sekarang</button>
        </form>

        <div class="login-link">
            Sudah punya akun? <a href="{{ route('login.buyer') }}">Masuk disini</a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let valid = true;

        const nama     = document.getElementById('nama_pengusaha');
        const email    = document.getElementById('email');
        const password = document.getElementById('password');

        [nama, email, password].forEach(el => el.classList.remove('error', 'success'));
        document.querySelectorAll('.error-msg').forEach(el => el.classList.remove('show'));

        if (nama.value.trim() === '') {
            nama.classList.add('error');
            document.getElementById('err-nama').classList.add('show');
            valid = false;
        } else { nama.classList.add('success'); }

        const emailVal = email.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailVal)) {
            email.classList.add('error');
            document.getElementById('err-email').classList.add('show');
            valid = false;
        } else { email.classList.add('success'); }

        const passVal = password.value;
        const passValid = passVal.length >= 8
            && /[A-Z]/.test(passVal)
            && /[a-z]/.test(passVal)
            && /[0-9]/.test(passVal);

        if (!passValid) {
            password.classList.add('error');
            document.getElementById('err-password').classList.add('show');
            valid = false;
        } else { password.classList.add('success'); }

        if (valid) this.submit();
    });
</script>
@endpush
