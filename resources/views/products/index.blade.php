@extends('layouts.app')

@section('title', 'Katalog Produk - Sibu Toy')
@section('page_title', 'Manajemen Produk')
@section('page_subtitle', 'Kelola data produk, kontrol stok minimum, dan harga jual.')

@section('top_actions')
    @if(auth()->user()->role === 'admin')
        <a href="{{ route('products.create') }}" class="btn-action-primary">
            
            <span>Tambah Produk Baru</span>
        </a>
    @endif
@endsection

@section('content')
<div class="section-card">

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <form action="{{ route('products.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; flex: 1; max-width: 820px;">
            <!-- 1. Input Pencarian Teks -->
            <div class="search-input-group" style="flex: 1.2; min-width: 220px;">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, SKU, atau kategori..." class="form-control">
            </div>

            <!-- 2. Dropdown Filter Kategori -->
            <div style="flex: 1; min-width: 170px;">
                <select name="category_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Dropdown Filter Status Stok -->
            <div style="flex: 1; min-width: 160px;">
                <select name="stock_status" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Status Stok --</option>
                    <option value="safe" {{ request('stock_status') === 'safe' ? 'selected' : '' }}>Stok Aman</option>
                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Stok Kritis (≤ Min)</option>
                    <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Habis (0 Unit)</option>
                </select>
            </div>

            <!-- Tombol Submit & Reset -->
            <div style="display: flex; gap: 6px;">
                <button type="submit" class="btn-action-primary" style="padding: 9px 18px;">
                    <i class="fa-solid fa-magnifying-glass"></i> Filter
                </button>
                @if(request('search') || request('category_id') || request('stock_status'))
                    <a href="{{ route('products.index') }}" class="btn-action-soft" style="padding: 9px 14px;" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        <span style="font-size: 13px; font-weight: 600; color: var(--text-secondary); background: #f8fafc; padding: 6px 14px; border-radius: 20px; border: 1.5px solid var(--border-soft);">
            Total: {{ $products->total() ?? count($products) }} Produk
        </span>
    </div>

    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 70px;">Foto</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>SKU</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Status Stok</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            @if($product->image)
                                <img src="{{ asset('uploads/' . basename($product->image)) }}" alt="{{ $product->name }}" style="width: 48px; height: 48px; object-fit: contain; background: #f8fafc; border-radius: 8px; border: 1px solid var(--border-soft);">
                            @else
                                <div style="width: 48px; height: 48px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 16px;">
                                    
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong style="color: var(--text-primary); font-size: 14px;">{{ $product->name }}</strong>
                        </td>
                        <td>
                            <span style="font-size: 11.5px; font-weight: 600; color: #6366f1; background: #eef2ff; padding: 3px 8px; border-radius: 6px;">
                                {{ $product->category->name ?? 'Uncategorized' }}
                            </span>
                        </td>
                        <td style="color: var(--text-muted); font-size: 12.5px;">{{ $product->sku }}</td>
                        <td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                        <td style="font-weight: 700; color: #0f172a;">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td style="font-weight: 700;">{{ $product->stock }} unit</td>
                        <td>
                            <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 600; {{ $product->stock <= $product->minimum_stock ? 'background: #fee2e2; color: #b91c1c;' : 'background: #dcfce7; color: #15803d;' }}">
                                {{ $product->stock <= $product->minimum_stock ? 'Stok Kritis' : 'Aman' }} (Min: {{ $product->minimum_stock }})
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; align-items: center; gap: 6px;">
                                <a href="{{ route('products.show', $product->id) }}" class="btn-action-soft" style="padding: 6px 12px; font-size: 12px; font-weight: 600; background: #e0f2fe; color: #0284c7; border-color: #bae6fd;" title="Lihat Detail Produk">
                                     Detail
                                </a>
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn-action-soft" style="padding: 6px 12px; font-size: 12px;" title="Edit Produk">
                                        <i class="fa-regular fa-pen-to-square"></i> Edit
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-card-delete" style="padding: 6px 10px; border-radius: 8px;" title="Hapus Produk">
                                            <i class="fa-regular fa-trash-can"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            Tidak ada produk yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($products, 'links') && $products->hasPages())
        <div style="margin-top: 24px; padding-top: 18px; border-top: 1.5px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
            <div style="font-size: 13px; color: var(--text-secondary);">
                Menampilkan <strong>{{ $products->firstItem() }}</strong> sampai <strong>{{ $products->lastItem() }}</strong> dari <strong>{{ $products->total() }}</strong> produk
            </div>
            <div>
                {{ $products->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
