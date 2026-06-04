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

    .main-content {
        margin-left: var(--sidebar-w);
        flex: 1;
        padding: 40px 48px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 28px;
    }
    .page-header h1 { font-size: 24px; font-weight: 800; color: #1A3530; margin-bottom: 4px; }
    .page-header p  { font-size: 14px; color: #7FA09A; }

    .btn-upload {
        background: #0B6E5E; color: white;
        font-size: 14px; font-weight: 700;
        padding: 11px 24px; border-radius: 10px; border: none;
        cursor: pointer; transition: opacity 0.15s;
        font-family: 'Plus Jakarta Sans', sans-serif; white-space: nowrap;
    }
    .btn-upload:hover { opacity: 0.88; }

    .alert-success {
        background: #E6F5F2; color: #0B6E5E;
        border: 1px solid #B2DDD5; border-radius: 10px;
        padding: 12px 18px; margin-bottom: 20px;
        font-size: 13px; font-weight: 600;
    }
    .alert-error {
        background: #fff0f0; color: #e54b4b;
        border: 1px solid #f5b8b8; border-radius: 10px;
        padding: 12px 18px; margin-bottom: 20px;
        font-size: 13px; font-weight: 600;
    }
    .alert-error ul { margin: 6px 0 0 16px; font-weight: 400; }

    .empty-state {
        background: white; border-radius: 14px;
        border: 1px solid #D8E5E2; padding: 60px 40px;
        display: flex; flex-direction: column;
        align-items: center; text-align: center;
    }
    .empty-title { color: #1A3530; font-size: 16px; font-weight: bold; margin-bottom: 8px; }
    .empty-sub   { color: #7FA09A; font-size: 13px; margin-bottom: 24px; max-width: 340px; line-height: 1.6; }

    .products-card {
        background: white; border-radius: 14px;
        border: 1px solid #D8E5E2; padding: 20px 22px; margin-bottom: 24px;
    }
    .products-card-title { color: #1A3530; font-size: 15px; font-weight: bold; margin-bottom: 20px; }

    .product-item {
        display: flex; align-items: center; gap: 16px;
        padding: 14px 0; border-bottom: 1px solid #F0F4F3;
    }
    .product-item:last-child { border-bottom: none; padding-bottom: 0; }
    .product-item:first-child { padding-top: 0; }

    .product-thumb {
        width: 52px; height: 52px; border-radius: 10px;
        object-fit: cover; border: 1px solid #E8F0EE;
        flex-shrink: 0; background: #F0F4F3;
    }
    .product-thumb-placeholder {
        width: 52px; height: 52px; border-radius: 10px;
        background: #F0F4F3; border: 1px solid #E8F0EE;
        flex-shrink: 0; display: flex; align-items: center;
        justify-content: center; color: #B2CCC8;
    }

    .product-info { flex: 1; min-width: 0; }
    .product-name { color: #1A3530; font-size: 14px; font-weight: bold; margin-bottom: 3px; }
    .product-meta { color: #7FA09A; font-size: 12px; }

    .product-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }

    .btn-edit {
        background: none; border: 1px solid #D8E5E2; border-radius: 10px;
        padding: 8px 16px; font-size: 12px; font-weight: 600; color: #3D6B63;
        cursor: pointer; transition: background 0.15s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .btn-edit:hover { background: #F0FAF7; }

    .btn-hapus {
        background: none; border: 1px solid #D8E5E2; border-radius: 10px;
        padding: 8px 16px; font-size: 12px; font-weight: 600; color: #3D6B63;
        cursor: pointer; transition: background 0.15s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .btn-hapus:hover { background: #FFF0F0; color: #e54b4b; border-color: #e54b4b; }

    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.4); z-index: 200;
        align-items: center; justify-content: center;
    }
    .modal-overlay.open { display: flex; }

    .modal {
        background: white; border-radius: 18px; padding: 32px;
        width: 100%; max-width: 480px; max-height: 90vh;
        overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }

    .modal-title { color: #1A3530; font-size: 18px; font-weight: bold; margin-bottom: 24px; }

    .form-group { margin-bottom: 16px; }
    .form-label { display: block; color: #1A3530; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
    .form-hint  { color: #7FA09A; font-size: 11px; margin-top: 4px; }

    .form-input {
        width: 100%; border: 1px solid #D8E5E2; border-radius: 10px;
        padding: 10px 14px; font-size: 13px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #1A3530; outline: none; transition: border 0.15s; background: #fff;
    }
    .form-input:focus { border-color: #0B6E5E; }
    textarea.form-input { resize: vertical; min-height: 80px; }

    .modal-actions { display: flex; gap: 12px; margin-top: 24px; justify-content: flex-end; }
    .btn-cancel {
        background: none; border: 1px solid #D8E5E2; border-radius: 10px;
        padding: 10px 20px; font-size: 13px; font-weight: 600; color: #7FA09A;
        cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .btn-cancel:hover { background: #F7FAF9; }
    .btn-submit {
        background: #0B6E5E; color: white; border: none; border-radius: 10px;
        padding: 10px 24px; font-size: 13px; font-weight: 700;
        cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: opacity 0.15s;
    }
    .btn-submit:hover { opacity: 0.88; }

    .modal-confirm { max-width: 380px; text-align: center; padding: 36px 32px; }
    .confirm-icon {
        width: 56px; height: 56px; background: #FFF0F0; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;
    }
    .confirm-title { color: #1A3530; font-size: 17px; font-weight: 700; margin-bottom: 8px; }
    .confirm-sub   { color: #7FA09A; font-size: 13px; line-height: 1.6; margin-bottom: 24px; }
    .confirm-actions { display: flex; gap: 10px; justify-content: center; }
    .btn-confirm-cancel {
        background: none; border: 1px solid #D8E5E2; border-radius: 10px;
        padding: 10px 24px; font-size: 13px; font-weight: 600; color: #7FA09A;
        cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: background 0.15s;
    }
    .btn-confirm-cancel:hover { background: #F7FAF9; }
    .btn-confirm-delete {
        background: #e54b4b; color: white; border: none; border-radius: 10px;
        padding: 10px 24px; font-size: 13px; font-weight: 700;
        cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: opacity 0.15s;
    }
    .btn-confirm-delete:hover { opacity: 0.88; }
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
        <a href="{{ route('dashboard.umkm') }}" class="nav-item">
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
        <a href="{{ route('umkm.produk_saya') }}" class="nav-item active">
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
        <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
        <div class="user-info">
            <div class="user-name">{{ Auth::user()->name }}</div>
            <div class="user-sub">UMKM</div>
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
        <div>
            <h1>Produk Saya</h1>
            <p>Kelola produk yang sudah Anda upload ke katalog.</p>
        </div>
        <button class="btn-upload" onclick="openModal('modal-upload')">+ Upload Produk Baru</button>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tampilkan error validasi --}}
    @if($errors->any())
        <div class="alert-error">
            <b>Gagal upload:</b>
            <ul>
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        <script>
            // Buka modal upload otomatis kalau ada error
            document.addEventListener('DOMContentLoaded', function() {
                openModal('modal-upload');
            });
        </script>
    @endif

    @if($products->isEmpty())
        <div class="empty-state">
            <div class="empty-title">Belum ada produk</div>
            <div class="empty-sub">Upload produk pertama Anda agar bisa ditemukan oleh buyer internasional.</div>
            <button class="btn-upload" onclick="openModal('modal-upload')">+ Upload Produk Pertama</button>
        </div>
    @else
        <div class="products-card">
            <div class="products-card-title">Produk ({{ $products->count() }})</div>

            @foreach($products as $product)
            <div class="product-item">
                @if(!empty($product->images) && isset($product->images[0]))
                    <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" class="product-thumb">
                @else
                    <div class="product-thumb-placeholder">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif

                <div class="product-info">
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="product-meta">
                        {{ $product->production_capacity ?? '-' }}
                        @if($product->category) · {{ $product->category }} @endif
                        @if(!empty($product->certifications)) · {{ implode(', ', $product->certifications) }} @endif
                    </div>
                </div>
                <div class="product-actions">
                    <button class="btn-edit" onclick="openEditModal(
                        '{{ $product->id }}',
                        '{{ addslashes($product->name) }}',
                        '{{ addslashes($product->category) }}',
                        '{{ addslashes($product->description) }}',
                        '{{ addslashes($product->production_capacity) }}',
                        '{{ implode(', ', $product->target_countries ?? []) }}',
                        '{{ implode(', ', $product->certifications ?? []) }}'
                    )">Edit</button>
                    <button class="btn-hapus" onclick="openDeleteModal('{{ $product->id }}', '{{ addslashes($product->name) }}')">Hapus</button>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>

{{-- MODAL UPLOAD --}}
<div class="modal-overlay" id="modal-upload">
    <div class="modal">
        <div class="modal-title">Upload Produk Baru</div>
        <form method="POST" action="{{ route('umkm.produk.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Nama Produk <span style="color:#e54b4b">*</span></label>
                <input type="text" name="name" class="form-input" placeholder="Contoh: Kopi Arabika Gayo" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Kategori</label>
                <input type="text" name="category" class="form-input" placeholder="Contoh: Makanan & Minuman" value="{{ old('category') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-input" placeholder="Deskripsi singkat produk...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Kapasitas Produksi</label>
                <div style="display:flex; gap:8px; align-items:center;">
                    <input type="number" name="capacity_amount" id="capacity_amount"
                           class="form-input" placeholder="Contoh: 300" min="1"
                           style="width:120px; flex-shrink:0;"
                           oninput="updateCapacity()">
                    <select name="capacity_unit" id="capacity_unit"
                            class="form-input" style="width:130px; flex-shrink:0;"
                            onchange="updateCapacity()">
                        <option value="kg">kg</option>
                        <option value="pcs">pcs</option>
                        <option value="ton">ton</option>
                        <option value="liter">liter</option>
                        <option value="lusin">lusin</option>
                        <option value="karton">karton</option>
                        <option value="jar">jar</option>
                    </select>
                    <span style="color:#7FA09A; font-size:13px; white-space:nowrap;">/ bulan</span>
                </div>
                <input type="hidden" name="production_capacity" id="production_capacity_hidden" value="{{ old('production_capacity') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Negara Tujuan</label>
                <input type="text" name="target_countries" class="form-input" placeholder="Contoh: Jepang, UAE, Amerika" value="{{ old('target_countries') }}">
                <div class="form-hint">Pisahkan dengan koma</div>
            </div>

            <div class="form-group">
                <label class="form-label">Sertifikasi</label>
                <input type="text" name="certifications" class="form-input" placeholder="Contoh: Halal, SNI, Organic" value="{{ old('certifications') }}">
                <div class="form-hint">Pisahkan dengan koma</div>
            </div>

            <div class="form-group">
                <label class="form-label">Foto Produk</label>
                {{-- accept diperluas: jpeg,jpg,png,webp --}}
                <input type="file" name="images" class="form-input" accept="image/jpeg,image/jpg,image/png,image/webp,.jpg,.jpeg,.png,.webp">
                <div class="form-hint">Format: JPG, PNG, WEBP. Maks 5MB.</div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-upload')">Batal</button>
                <button type="submit" class="btn-submit">Upload</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal-overlay" id="modal-edit">
    <div class="modal">
        <div class="modal-title">Edit Produk</div>
        <form method="POST" id="form-edit" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Nama Produk <span style="color:#e54b4b">*</span></label>
                <input type="text" name="name" id="edit-name" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Kategori</label>
                <input type="text" name="category" id="edit-category" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" id="edit-description" class="form-input"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Kapasitas Produksi</label>
                <div style="display:flex; gap:8px; align-items:center;">
                    <input type="number" name="capacity_amount" id="edit_capacity_amount"
                           class="form-input" placeholder="Contoh: 300" min="1"
                           style="width:120px; flex-shrink:0;"
                           oninput="updateEditCapacity()">
                    <select name="capacity_unit" id="edit_capacity_unit"
                            class="form-input" style="width:130px; flex-shrink:0;"
                            onchange="updateEditCapacity()">
                        <option value="kg">kg</option>
                        <option value="pcs">pcs</option>
                        <option value="ton">ton</option>
                        <option value="liter">liter</option>
                        <option value="lusin">lusin</option>
                        <option value="karton">karton</option>
                        <option value="jar">jar</option>
                    </select>
                    <span style="color:#7FA09A; font-size:13px; white-space:nowrap;">/ bulan</span>
                </div>
                <input type="hidden" name="production_capacity" id="edit_production_capacity_hidden">
            </div>

            <div class="form-group">
                <label class="form-label">Negara Tujuan</label>
                <input type="text" name="target_countries" id="edit-countries" class="form-input">
                <div class="form-hint">Pisahkan dengan koma</div>
            </div>

            <div class="form-group">
                <label class="form-label">Sertifikasi</label>
                <input type="text" name="certifications" id="edit-certifications" class="form-input">
                <div class="form-hint">Pisahkan dengan koma</div>
            </div>

            <div class="form-group">
                <label class="form-label">Foto Produk <span style="color:#7FA09A;font-weight:400">(kosongkan jika tidak diubah)</span></label>
                <input type="file" name="images" class="form-input" accept="image/jpeg,image/jpg,image/png,image/webp,.jpg,.jpeg,.png,.webp">
                <div class="form-hint">Format: JPG, PNG, WEBP. Maks 5MB.</div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-edit')">Batal</button>
                <button type="submit" class="btn-submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL KONFIRMASI HAPUS --}}
<div class="modal-overlay" id="modal-hapus">
    <div class="modal modal-confirm">
        <div class="confirm-icon">
            <svg width="24" height="24" fill="none" stroke="#e54b4b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <div class="confirm-title">Hapus Produk?</div>
        <div class="confirm-sub" id="confirm-sub-text">Produk ini akan dihapus permanen dan tidak bisa dikembalikan.</div>
        <div class="confirm-actions">
            <button class="btn-confirm-cancel" onclick="closeModal('modal-hapus')">Batal</button>
            <button class="btn-confirm-delete" id="btn-confirm-delete-action">Ya, Hapus</button>
        </div>
        <form id="form-hapus" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    function openDeleteModal(id, name) {
        document.getElementById('confirm-sub-text').textContent =
            '"' + name + '" akan dihapus permanen dan tidak bisa dikembalikan.';
        document.getElementById('form-hapus').action = '/umkm/products/' + id;
        document.getElementById('btn-confirm-delete-action').onclick = function() {
            document.getElementById('form-hapus').submit();
        };
        openModal('modal-hapus');
    }

    function updateCapacity() {
        const amount = document.getElementById('capacity_amount').value;
        const unit = document.getElementById('capacity_unit').value;
        document.getElementById('production_capacity_hidden').value =
            amount ? amount + ' ' + unit + '/bulan' : '';
    }

    function updateEditCapacity() {
        const amount = document.getElementById('edit_capacity_amount').value;
        const unit = document.getElementById('edit_capacity_unit').value;
        document.getElementById('edit_production_capacity_hidden').value =
            amount ? amount + ' ' + unit + '/bulan' : '';
    }

    function openEditModal(id, name, category, description, capacity, countries, certifications) {
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-category').value = category;
        document.getElementById('edit-description').value = description;
        document.getElementById('edit-countries').value = countries;
        document.getElementById('edit-certifications').value = certifications;

        if (capacity) {
            const parts = capacity.split(' ');
            const amount = parts[0] || '';
            const unit = parts[1] ? parts[1].replace('/bulan', '') : 'kg';
            document.getElementById('edit_capacity_amount').value = amount;
            const selectEl = document.getElementById('edit_capacity_unit');
            for (let i = 0; i < selectEl.options.length; i++) {
                if (selectEl.options[i].value === unit) { selectEl.selectedIndex = i; break; }
            }
            document.getElementById('edit_production_capacity_hidden').value = capacity;
        } else {
            document.getElementById('edit_capacity_amount').value = '';
            document.getElementById('edit_capacity_unit').selectedIndex = 0;
            document.getElementById('edit_production_capacity_hidden').value = '';
        }

        document.getElementById('form-edit').action = '/umkm/products/' + id;
        openModal('modal-edit');
    }

    document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) overlay.classList.remove('open');
        });
    });
</script>
@endpush

@endsection