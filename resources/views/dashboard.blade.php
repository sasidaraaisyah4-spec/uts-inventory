@extends('layouts.app')

@section('title', auth()->user()->role === 'customer' ? 'Customer Hub - Sibu Toy' : 'Dashboard Operasional - Sibu Toy')
@section('page_title', auth()->user()->role === 'customer' ? 'Customer Dashboard' : 'Dashboard Operasional')
@section('page_subtitle', auth()->user()->role === 'customer' ? 'Selamat datang kembali! Pantau riwayat belanja dan pesan koleksi Art Toys & Collectibles favorit Anda.' : 'Ringkasan performa inventaris, status stok, dan log transaksi real-time.')

@section('top_actions')
    @if(auth()->user()->role === 'admin')
        <a href="{{ route('sales.create') }}" class="btn-action-soft">
            
            <span>Catat Penjualan</span>
        </a>
        <a href="{{ route('products.create') }}" class="btn-action-primary">
            
            <span>Tambah Produk</span>
        </a>
    @elseif(auth()->user()->role === 'staff')
        <a href="{{ route('sales.create') }}" class="btn-action-primary">
            
            <span>Buka Kasir / Penjualan</span>
        </a>
    @elseif(auth()->user()->role === 'customer')
        <a href="#katalog-customer" class="btn-action-primary">
            
            <span>Belanja Sekarang</span>
        </a>
    @endif
@endsection

@push('styles')
<style>
    /* KARTU STATISTIK */
    .stats-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 22px;
    }

    .stats-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
    }

    .stat-card {
        background: var(--surface-card);
        border-radius: var(--radius-lg);
        padding: 24px 22px;
        border: 1.5px solid var(--border-soft);
        box-shadow: var(--shadow-subtle);
        display: flex;
        align-items: flex-start;
        gap: 18px;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .stat-card.blue::after { background: linear-gradient(90deg, #74c0fc, #4dabf7); }
    .stat-card.purple::after { background: linear-gradient(90deg, #b197fc, #9775fa); }
    .stat-card.green::after { background: linear-gradient(90deg, #63e6be, #38d9a9); }
    .stat-card.rose::after { background: linear-gradient(90deg, #f783ac, #f06595); }

    .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        box-shadow: 0 6px 16px rgba(0,0,0,0.04);
    }

    .stat-icon.blue { background: #e7f5ff; color: #1c7ed6; }
    .stat-icon.purple { background: #f3f0ff; color: #7048e8; }
    .stat-icon.green { background: #e6fcf5; color: #0ca678; }
    .stat-icon.rose { background: #fff0f6; color: #e64980; }

    .stat-details {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .stat-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-secondary);
    }

    .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.6px;
    }

    .stat-badge {
        font-size: 11.5px;
        font-weight: 700;
        margin-top: 4px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .badge-positive { color: #0ca678; }
    .badge-neutral { color: #7048e8; }
    .badge-warning { color: #e64980; }

    /* TWO COLUMN LAYOUT */
    .bottom-layout-grid {
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: 26px;
    }

    .section-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
        padding-bottom: 16px;
        border-bottom: 1.5px solid var(--border-soft);
    }

    .section-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title-icon {
        width: 38px;
        height: 38px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .section-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--text-primary);
    }

    /* Informasi & Pengumuman Internal */
    .announcement-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .announcement-item {
        padding: 18px;
        border-radius: var(--radius-md);
        border: 1.5px solid transparent;
        transition: all 0.25s;
    }

    .announcement-item.type-info { background: #f3f0ff; border-color: #e5dbff; }
    .announcement-item.type-warning { background: #fff0f6; border-color: #ffdeeb; }
    .announcement-item.type-notice { background: #e6fcf5; border-color: #c3fae8; }

    .announcement-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .announcement-tag {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        padding: 3px 10px;
        border-radius: 12px;
        letter-spacing: 0.5px;
    }

    .tag-info { background: #d0bfff; color: #5f3dc4; }
    .tag-warning { background: #fcc2d7; color: #a61e4d; }
    .tag-notice { background: #96f2d7; color: #087f5b; }

    .announcement-time { font-size: 11.5px; color: var(--text-muted); }
    .announcement-title { font-size: 14.5px; font-weight: 800; color: var(--text-primary); margin-bottom: 5px; }
    .announcement-desc { font-size: 13px; color: var(--text-secondary); line-height: 1.6; }

    /* Kategori Terlaris */
    .category-list { display: flex; flex-direction: column; gap: 16px; }
    .category-item { display: flex; flex-direction: column; gap: 8px; }
    .category-row { display: flex; justify-content: space-between; align-items: center; }
    .category-info-wrap { display: flex; align-items: center; gap: 10px; }

    .category-rank {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #f8f0fc;
        color: #7048e8;
        font-size: 12px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .category-rank.top-1 { background: #fff3bf; color: #f59f00; }
    .category-rank.top-2 { background: #f1f3f5; color: #495057; }
    .category-rank.top-3 { background: #ffe8cc; color: #d9480f; }

    .category-name { font-size: 14px; font-weight: 700; color: var(--text-primary); }
    .category-sales-count { font-size: 13px; font-weight: 800; color: #7048e8; }

    .progress-bar-bg { width: 100%; height: 9px; background: #f1edfa; border-radius: 10px; overflow: hidden; }
    .progress-bar-fill {
        height: 100%;
        border-radius: 10px;
        background: linear-gradient(90deg, #b197fc 0%, #f783ac 100%);
        transition: width 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* SEARCH FILTER & CATALOG */
    .catalog-search-form { display: flex; gap: 10px; width: 100%; }
    .search-input-group { position: relative; flex-grow: 1; }
    .search-input-group i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #adb5bd;
        font-size: 15px;
    }
    .modern-search-input {
        width: 100%;
        padding: 12px 18px 12px 42px;
        background: #faf7fd;
        border: 1.5px solid #ece4f9;
        border-radius: var(--radius-sm);
        font-size: 13.5px;
        color: var(--text-primary);
        outline: none;
        transition: all 0.25s ease;
    }
    .modern-search-input:focus {
        background: white;
        border-color: #b197fc;
        box-shadow: 0 0 0 4px rgba(177, 151, 252, 0.22);
    }
    .btn-modern-search {
        padding: 12px 22px;
        background: linear-gradient(135deg, #b197fc 0%, #9775fa 100%);
        color: white;
        border: none;
        border-radius: var(--radius-sm);
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 4px 14px rgba(151, 117, 250, 0.25);
    }
    .btn-modern-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(151, 117, 250, 0.35);
    }
    .btn-modern-reset {
        padding: 12px 16px;
        background: #f1f3f5;
        color: #495057;
        text-decoration: none;
        border-radius: var(--radius-sm);
        font-size: 13.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .btn-modern-reset:hover {
        background: #e9ecef;
    }

    /* PRODUK GRID CUSTOMER (2 KOLOM KOMPAK & LUCU) */
    .customer-product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 20px;
    }

    .customer-product-card {
        background: white;
        border-radius: var(--radius-lg);
        border: 1.5px solid var(--border-soft);
        padding: 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 8px 22px -6px rgba(177, 151, 252, 0.1);
    }

    .customer-product-card:hover {
        box-shadow: var(--shadow-hover);
        transform: translateY(-6px);
        border-color: #e5dbff;
    }

    .customer-product-img {
        width: 100%;
        height: 150px;
        background: linear-gradient(135deg, #f8f0fc 0%, #e6fcf5 100%);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-bottom: 12px;
        padding: 8px;
    }

    .customer-product-img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .customer-product-card:hover .customer-product-img img { transform: scale(1.1); }

    .product-category-chip {
        font-size: 11px;
        font-weight: 700;
        color: #7048e8;
        background: #f3f0ff;
        padding: 3px 10px;
        border-radius: 12px;
        display: inline-block;
        margin-bottom: 6px;
    }

    /* TOMBOL PESAN (PINK MINT PASTEL THEME) */
    .btn-card-order {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, #f783ac 0%, #da77f2 100%);
        color: white;
        text-decoration: none;
        padding: 10px 14px;
        border-radius: 18px;
        font-size: 13.5px;
        font-weight: 800;
        box-shadow: 0 6px 18px rgba(247, 131, 172, 0.35);
        transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .btn-card-order:hover {
        background: linear-gradient(135deg, #f06595 0%, #cc5de8 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(247, 131, 172, 0.45);
    }

    .btn-card-order.disabled {
        background: #f1f3f5;
        color: #adb5bd;
        box-shadow: none;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* TOMBOL NOTA */
    .btn-nota {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f3f0ff;
        color: #7048e8;
        padding: 6px 12px;
        border-radius: 12px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 800;
        border: 1px solid #e5dbff;
        transition: all 0.25s ease;
    }

    .btn-nota:hover {
        background: #e5dbff;
        color: #5f3dc4;
        transform: translateY(-1px);
    }

    /* ADMIN CATALOG GRID */
    .product-modern-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 22px;
    }

    .product-modern-card {
        background: white;
        border-radius: var(--radius-lg);
        border: 1.5px solid var(--border-soft);
        padding: 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 8px 20px -6px rgba(177, 151, 252, 0.1);
    }

    .product-modern-card:hover {
        box-shadow: var(--shadow-hover);
        transform: translateY(-5px);
        border-color: #e5dbff;
    }

    .product-img-box {
        width: 100%;
        height: 160px;
        background: linear-gradient(135deg, #f8f0fc 0%, #e6fcf5 100%);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-bottom: 14px;
        padding: 8px;
    }

    .product-img-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .product-card-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.35;
        margin-bottom: 4px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 40px;
    }

    .product-sku { font-size: 11.5px; color: var(--text-muted); margin-bottom: 10px; }
    .product-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        padding-top: 10px;
        border-top: 1.5px dashed var(--border-soft);
    }
    .product-price { font-size: 17px; font-weight: 800; color: #e64980; }
    .product-stock-tag { font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 10px; }
    .stock-safe { background: #e6fcf5; color: #0ca678; }
    .stock-low { background: #fff0f6; color: #e64980; }

    .card-actions-wrapper { display: flex; gap: 8px; margin-top: auto; }
    .btn-card-edit {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        background: #f3f0ff;
        color: #7048e8;
        text-decoration: none;
        padding: 9px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 700;
        border: 1px solid #e5dbff;
        transition: all 0.2s;
    }
    .btn-card-edit:hover { background: #e5dbff; transform: translateY(-1px); }

    @media (max-width: 1200px) {
        .stats-grid-4 { grid-template-columns: repeat(2, 1fr); }
        .stats-grid-3 { grid-template-columns: repeat(2, 1fr); }
        .bottom-layout-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .stats-grid-4, .stats-grid-3 { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

@if(auth()->user()->role === 'customer')
    {{-- ======================================================== --}}
    {{-- TAMPILAN DASHBOARD KHUSUS ROLE CUSTOMER                  --}}
    {{-- ======================================================== --}}

    <!-- 1. TIGA KARTU STATISTIK RINGKASAN CUSTOMER -->
    <section class="stats-grid-3">
        <!-- Kartu 1: Total Pesanan -->
        <div class="stat-card blue">
            <div class="stat-details">
                <span class="stat-title">Total Pesanan</span>
                <span class="stat-value">{{ number_format($custTotalOrders ?? 0) }} Pesanan</span>
            </div>
        </div>

        <!-- Kartu 2: Total Belanja -->
        <div class="stat-card purple">
            <div class="stat-details">
                <span class="stat-title">Total Belanja</span>
                <span class="stat-value">Rp {{ number_format($custTotalSpent ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Kartu 3: Barang Diterima -->
        <div class="stat-card green">
            <div class="stat-details">
                <span class="stat-title">Barang Diterima</span>
                <span class="stat-value">{{ number_format($custReceivedCount ?? 0) }} Pesanan</span>
            </div>
        </div>
    </section>

    <!-- 2. LAYOUT DUA KOLOM CUSTOMER -->
    <section class="bottom-layout-grid" style="grid-template-columns: 1.15fr 0.85fr;" id="katalog-customer">
        
        <!-- KOLOM KIRI: KATALOG PRODUK INTERAKTIF -->
        <div class="section-card">
            <div class="section-card-header">
                <div>
                    <h3 class="section-title">Katalog Art Toys & Collectibles</h3>
                    <p style="font-size: 12.5px; color: var(--text-secondary); margin-top: 2px;">Pilih koleksi favoritmu dan klik 'Pesan' untuk checkout langsung</p>
                </div>
            </div>

            <!-- Fitur Pencarian & Filter Produk Customer -->
            <form action="{{ url('/') }}" method="GET" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 22px;">
                <div style="display: flex; gap: 8px;">
                    <div class="search-input-group" style="flex: 1;">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari mainan atau koleksi..." 
                               class="modern-search-input">
                    </div>
                    <button type="submit" class="btn-modern-search">
                        <i class="fa-solid fa-magnifying-glass"></i> Cari
                    </button>
                    @if(request('search') || request('category_id'))
                        <a href="{{ url('/') }}" class="btn-modern-reset" title="Reset">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>

                <!-- Pilihan Filter Kategori Cepat (Tombol Interaktif Kategori) -->
                <div style="display: flex; gap: 8px; align-items: center; overflow-x: auto; padding-bottom: 6px;">
                    <a href="{{ url('/?' . http_build_query(request()->except(['category_id', 'products_page']))) }}#katalog-customer" 
                       style="padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; text-decoration: none; white-space: nowrap; transition: all 0.2s; display: inline-flex; align-items: center; {{ !request('category_id') ? 'background: #7048e8; color: white; box-shadow: 0 4px 10px rgba(112, 72, 232, 0.25);' : 'background: #f1edfa; color: #5c5f66;' }}">
                        Semua
                    </a>
                    @foreach($categories ?? [] as $c)
                        <a href="{{ url('/?' . http_build_query(array_merge(request()->except('products_page'), ['category_id' => $c->id]))) }}#katalog-customer" 
                           style="padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; text-decoration: none; white-space: nowrap; transition: all 0.2s; display: inline-flex; align-items: center; {{ request('category_id') == $c->id ? 'background: #7048e8; color: white; box-shadow: 0 4px 10px rgba(112, 72, 232, 0.25);' : 'background: #f1edfa; color: #5c5f66;' }}">
                            {{ $c->name }}
                        </a>
                    @endforeach
                </div>
            </form>

            <!-- Grid Produk Kompak -->
            <div class="customer-product-grid">
                @forelse($products as $product)
                    <div class="customer-product-card">
                        <div>
                            <div class="customer-product-img">
                                @if($product->image)
                                    <img src="{{ asset('uploads/' . basename($product->image)) }}" alt="{{ $product->name }}">
                                @else
                                    <div style="color: #adb5bd; font-size: 12px; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                        
                                        <span>No Image</span>
                                    </div>
                                @endif
                            </div>

                            <span class="product-category-chip">
                                {{ $product->category->name ?? 'Art Toy' }}
                            </span>

                            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 38px;" title="{{ $product->name }}">
                                {{ $product->name }}
                            </h4>
                        </div>

                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin: 10px 0 12px; padding-top: 8px; border-top: 1.5px dashed var(--border-soft);">
                                <span style="font-size: 15.5px; font-weight: 800; color: #e64980;">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </span>
                                <span style="font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 8px; {{ $product->stock > 0 ? 'background: #e6fcf5; color: #0ca678;' : 'background: #fff0f6; color: #e64980;' }}">
                                    Stok: {{ $product->stock }}
                                </span>
                            </div>

                            {{-- Tombol Pesan Interaktif --}}
                            @if($product->stock > 0)
                                <a href="{{ route('checkout.show', $product->id) }}" class="btn-card-order">
                                     Pesan Sekarang
                                </a>
                            @else
                                <span class="btn-card-order disabled">
                                     Stok Habis
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 48px; color: var(--text-muted);">
                        
                        <p style="font-weight: 700;">Mainan tidak ditemukan</p>
                    </div>
                @endforelse
            </div>

            @if(method_exists($products, 'links') && $products->hasPages())
                <div style="margin-top: 20px; padding-top: 14px; border-top: 1.5px solid var(--border-soft); display: flex; justify-content: center;">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

        <!-- KOLOM KANAN: TABEL RIWAYAT PESANAN SAYA -->
        <div class="section-card" id="pesanan-saya">
            <div class="section-card-header" style="flex-wrap: wrap; gap: 10px;">
                <div>
                    <h3 class="section-title">Pesanan Saya</h3>
                    <p style="font-size: 12.5px; color: var(--text-secondary); margin-top: 2px;">Daftar transaksi pembelian Anda</p>
                </div>
                <span style="font-size: 12px; font-weight: 700; color: #7048e8; background: #f3f0ff; padding: 4px 12px; border-radius: 20px;">
                    {{ $custTotalOrders ?? (count($customerOrders ?? [])) }} Pesanan
                </span>
            </div>

            <!-- Form Pencarian & Filter Pesanan Customer -->
            <form action="{{ url('/') }}" method="GET" style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('category_id'))
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                @endif
                @if(request('products_page'))
                    <input type="hidden" name="products_page" value="{{ request('products_page') }}">
                @endif

                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <div class="search-input-group" style="flex: 1.4; min-width: 170px;">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" name="order_search" value="{{ request('order_search') }}" 
                               placeholder="Cari no. faktur / item..." 
                               class="modern-search-input" style="font-size: 12.5px;">
                    </div>

                    <div style="flex: 1; min-width: 125px;">
                        <select name="order_status" class="form-control" onchange="this.form.submit()" style="padding: 7px 10px; font-size: 12.5px;">
                            <option value="">Semua Status</option>
                            <option value="completed" {{ request('order_status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                            <option value="pending" {{ request('order_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-modern-search" style="padding: 7px 14px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-magnifying-glass"></i> Filter
                    </button>
                    @if(request('order_search') || request('order_status') || request('start_date') || request('end_date'))
                        <a href="{{ url('/?' . http_build_query(request()->except('order_search', 'order_status', 'start_date', 'end_date', 'orders_page'))) }}" class="btn-modern-reset" title="Reset Filter Pesanan">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>

                <!-- Filter Rentang Tanggal Transaksi (Opsional / Fleksibel) -->
                <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                    <div style="font-size: 11.5px; font-weight: 600; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
                         Tanggal:
                    </div>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()" class="form-control" style="flex: 1; min-width: 110px; padding: 4px 8px; font-size: 11.5px; height: 32px;" title="Dari Tanggal">
                    <span style="font-size: 11.5px; color: var(--text-muted);">-</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()" class="form-control" style="flex: 1; min-width: 110px; padding: 4px 8px; font-size: 11.5px; height: 32px;" title="Sampai Tanggal">
                </div>
            </form>

            <div class="table-responsive">
                <table class="table-modern" style="font-size: 13px;">
                    <thead>
                        <tr>
                            <th>Faktur</th>
                            <th>Item Mainan</th>
                            <th>Total</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customerOrders ?? [] as $order)
                            <tr>
                                <td>
                                    <strong style="color: #7048e8; font-size: 12.5px;">{{ $order->sale_number }}</strong>
                                    <div style="font-size: 11px; color: var(--text-muted);">
                                        {{ \Carbon\Carbon::parse($order->sold_at)->format('d M Y') }}
                                    </div>
                                </td>
                                <td>
                                    @foreach($order->items as $item)
                                        <div style="font-size: 12px; line-height: 1.4;">
                                            • {{ $item->product->name ?? 'Produk' }} 
                                            <strong style="color: #0ca678;">x{{ $item->quantity }}</strong>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    <strong style="color: #e64980; font-size: 13.5px;">
                                        Rp {{ number_format($order->total, 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('invoice.show', $order->id) }}" class="btn-nota" title="Buka & Cetak Nota">
                                        <i class="fa-solid fa-receipt"></i> Nota
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 40px 14px; color: var(--text-muted);">
                                    
                                    Belum ada pesanan yang sesuai kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($customerOrders, 'links') && $customerOrders->hasPages())
                <div style="margin-top: 18px; padding-top: 12px; border-top: 1.5px solid var(--border-soft); display: flex; justify-content: center;">
                    {{ $customerOrders->links() }}
                </div>
            @endif
        </div>

    </section>

@else
    {{-- ======================================================== --}}
    {{-- TAMPILAN DASHBOARD OPERASIONAL (ADMIN & STAFF)           --}}
    {{-- ======================================================== --}}

    <!-- 1. EMPAT KARTU STATISTIK (TOP ROW) -->
    <section class="stats-grid-4">
        <div class="stat-card blue">
            <div class="stat-details">
                <span class="stat-title">Total Penjualan</span>
                <span class="stat-value">Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}</span>
                <span class="stat-badge badge-positive">Akumulasi Transaksi</span>
            </div>
        </div>

        <div class="stat-card purple">
            <div class="stat-details">
                <span class="stat-title">Transaksi Baru</span>
                <span class="stat-value">{{ number_format($newSalesCount ?? 0) }} Transaksi</span>
                <span class="stat-badge badge-positive">Status Selesai</span>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-details">
                <span class="stat-title">Total Produk & Supplier</span>
                <span class="stat-value">{{ $totalProducts ?? 0 }} / {{ $totalSuppliers ?? 0 }}</span>
                <span class="stat-badge badge-neutral">{{ $totalProducts ?? 0 }} Item | {{ $totalSuppliers ?? 0 }} Pemasok</span>
            </div>
        </div>

        <div class="stat-card rose">
            <div class="stat-details">
                <span class="stat-title">Peringatan Stok Menipis</span>
                <span class="stat-value">{{ $lowStockCount ?? 0 }} Item</span>
                <span class="stat-badge {{ ($lowStockCount ?? 0) > 0 ? 'badge-warning' : 'badge-positive' }}">
                    {{ ($lowStockCount ?? 0) > 0 ? 'Perlu Restock Segera' : 'Stok Aman Terkendali' }}
                </span>
            </div>
        </div>
    </section>

    <!-- 2. LAYOUT DUA KOLOM OPERASIONAL -->
    <section class="bottom-layout-grid">
        <div class="section-card">
            <div class="section-card-header">
                <h3 class="section-title">Informasi & Pengumuman Internal</h3>
                <span style="font-size: 12px; font-weight: 700; color: #7048e8; background: #f3f0ff; padding: 4px 12px; border-radius: 20px;">Pemberitahuan</span>
            </div>

            <div class="announcement-list">
                @if(($lowStockCount ?? 0) > 0)
                    <div class="announcement-item type-warning">
                        <div class="announcement-top">
                            <span class="announcement-tag tag-warning">Penting • Restock</span>
                            <span class="announcement-time">Hari ini</span>
                        </div>
                        <h4 class="announcement-title">Peringatan Stok di Bawah Batas Minimum</h4>
                        <p class="announcement-desc">
                            Terdapat <strong>{{ $lowStockCount }} produk</strong> dengan jumlah stok mendekati limit minimum: 
                            @foreach($lowStockProducts->take(3) as $lp)
                                <span style="text-decoration: underline;">{{ $lp->name }}</span> (sisa {{ $lp->stock }}), 
                            @endforeach
                            segera lakukan pemesanan di <a href="{{ route('purchases.index') }}" style="color: #c92a2a; font-weight: 800;">Transaksi Masuk</a>.
                        </p>
                    </div>
                @else
                    <div class="announcement-item type-info">
                        <div class="announcement-top">
                            <span class="announcement-tag tag-info">Status Gudang</span>
                            <span class="announcement-time">Terbaru</span>
                        </div>
                        <h4 class="announcement-title">Seluruh Stok Barang dalam Kondisi Stabil</h4>
                        <p class="announcement-desc">Saat ini tidak ada peringatan kekurangan inventaris. Seluruh stok aman melampaui ambang batas minimum.</p>
                    </div>
                @endif

                <div class="announcement-item type-notice">
                    <div class="announcement-top">
                        <span class="announcement-tag tag-notice">Operasional Toko</span>
                        <span class="announcement-time">Shift Aktif</span>
                    </div>
                    <h4 class="announcement-title">Jadwal Rekonsiliasi & Stock Opname Mingguan</h4>
                    <p class="announcement-desc">Pastikan seluruh transaksi kasir telah tervalidasi di menu <a href="{{ route('sales.index') }}" style="color: #087f5b; font-weight: 800;">Transaksi Keluar</a> sebelum penutupan operasional.</p>
                </div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-header">
                <h3 class="section-title">Kategori Terlaris</h3>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('categories.index') }}" style="font-size: 12px; font-weight: 700; color: #7048e8; background: #f3f0ff; padding: 5px 12px; border-radius: 20px; text-decoration: none; border: 1px solid #e5dbff;">
                        Kelola Kategori 
                    </a>
                @endif
            </div>

            <div class="category-list">
                @php
                    $maxSold = isset($topCategories) && count($topCategories) > 0 ? max(collect($topCategories)->pluck('sold_count')->max(), 1) : 1;
                @endphp

                @forelse($topCategories ?? [] as $index => $cat)
                    @php
                        $percent = min(100, round(($cat['sold_count'] / $maxSold) * 100));
                        if ($percent < 15 && $cat['sold_count'] > 0) $percent = 15;
                        if ($cat['sold_count'] == 0) $percent = 8;
                    @endphp
                    <div class="category-item">
                        <div class="category-row">
                            <div class="category-info-wrap">
                                <span class="category-rank {{ $index === 0 ? 'top-1' : ($index === 1 ? 'top-2' : ($index === 2 ? 'top-3' : '')) }}">{{ $index + 1 }}</span>
                                <span class="category-name">{{ $cat['name'] }}</span>
                            </div>
                            <span class="category-sales-count">{{ $cat['sold_count'] }} Terjual <small style="color: #adb5bd; font-weight: 600;">({{ $cat['products_count'] }} SKU)</small></span>
                        </div>
                        <div class="progress-bar-bg"><div class="progress-bar-fill" style="width: {{ $percent }}%;"></div></div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 30px 10px; color: var(--text-muted);">
                        <p style="font-size: 13px;">Belum ada kategori yang tercatat.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- 3. KATALOG PRODUK ADMIN & STAFF -->
    <section class="section-card" id="katalog-produk" style="margin-top: 10px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <div>
                <h3 style="font-size: 18px; font-weight: 800; color: var(--text-primary);">Daftar Inventaris Produk Sibu Toy</h3>
                <p style="font-size: 13px; color: var(--text-secondary);">Kelola data barang, harga jual, dan ketersediaan stok fisik gudang.</p>
            </div>

            <!-- Form Pencarian & Filter Cepat Admin / Staff -->
            <form action="{{ url('/') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; max-width: 780px; width: 100%;">
                <div class="search-input-group" style="flex: 1.5; min-width: 200px;">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk, SKU, kategori..." class="modern-search-input">
                </div>

                <div style="flex: 1; min-width: 140px;">
                    <select name="category_id" class="form-control" onchange="this.form.submit()" style="padding: 9px 12px; font-size: 13px;">
                        <option value="">Semua Kategori</option>
                        @foreach($categories ?? [] as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="flex: 1; min-width: 140px;">
                    <select name="stock_status" class="form-control" onchange="this.form.submit()" style="padding: 9px 12px; font-size: 13px;">
                        <option value="">Semua Status Stok</option>
                        <option value="safe" {{ request('stock_status') === 'safe' ? 'selected' : '' }}>Stok Aman</option>
                        <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Stok Kritis (≤ Min)</option>
                        <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Habis (0 Unit)</option>
                    </select>
                </div>

                <button type="submit" class="btn-modern-search">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
                @if(request('search') || request('stock_status') || request('category_id'))
                    <a href="{{ url('/') }}" class="btn-modern-reset" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="product-modern-grid">
            @forelse($products as $product)
                <div class="product-modern-card">
                    <div>
                        <div class="product-img-box">
                            @if($product->image)
                                <img src="{{ asset('uploads/' . basename($product->image)) }}" alt="{{ $product->name }}">
                            @else
                                <div style="color: #adb5bd; font-size: 12px; display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                    
                                    <span>No Image</span>
                                </div>
                            @endif
                        </div>

                        <span class="product-category-chip">{{ $product->category->name ?? 'Uncategorized' }}</span>
                        <h4 class="product-card-title" title="{{ $product->name }}">{{ $product->name }}</h4>
                        <div class="product-sku">SKU: {{ $product->sku }}</div>
                    </div>

                    <div>
                        <div class="product-meta-row">
                            <span class="product-price">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                            <span class="product-stock-tag {{ $product->stock <= $product->minimum_stock ? 'stock-low' : 'stock-safe' }}">Stok: {{ $product->stock }}</span>
                        </div>

                        <div class="card-actions-wrapper">
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('products.edit', $product->id) }}" class="btn-card-edit">
                                    <i class="fa-regular fa-pen-to-square"></i> Edit
                                </a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-card-delete" title="Hapus Produk">
                                        <i class="fa-regular fa-trash-can"></i> Hapus
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('products.show', $product->id) }}" class="btn-action-soft" style="width: 100%; justify-content: center; font-size: 12px; font-weight: 700; padding: 7px;">
                                     Lihat Detail
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 48px 20px; color: var(--text-muted);">
                    
                    <p style="font-weight: 700;">Tidak ada produk ditemukan</p>
                </div>
            @endforelse
        </div>

        @if(method_exists($products, 'links') && $products->hasPages())
            <div style="margin-top: 24px; padding-top: 18px; border-top: 1.5px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                <div style="font-size: 13px; color: var(--text-secondary);">
                    Menampilkan <strong>{{ $products->firstItem() }}</strong> - <strong>{{ $products->lastItem() }}</strong> dari <strong>{{ $products->total() }}</strong> produk
                </div>
                <div>
                    {{ $products->links() }}
                </div>
            </div>
        @endif
    </section>

@endif

@endsection
