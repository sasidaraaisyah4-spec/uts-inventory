@extends('layouts.app')

@section('title', 'Edit Produk - Sibu Toy')
@section('page_title', 'Edit Data Produk')
@section('page_subtitle', 'Perbarui informasi spesifikasi, harga, atau stok minimum produk.')

@section('top_actions')
    <a href="{{ route('products.index') }}" class="btn-action-soft">
        
        <span>Kembali ke Produk</span>
    </a>
@endsection

@section('content')
<div style="max-width: 650px; margin: 0 auto; width: 100%;">
    <div class="section-card">
        <div style="margin-bottom: 24px; padding-bottom: 14px; border-bottom: 1px solid var(--border-soft);">
            <h3 style="font-size: 17px; font-weight: 700; color: var(--text-primary);">Edit {{ $product->name }}</h3>
            <p style="font-size: 13px; color: var(--text-secondary);">Perbarui data produk inventaris</p>
        </div>

        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Nama Produk</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-control" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">SKU (Kode Unik Produk)</label>
                <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">Harga Beli (Rp)</label>
                    <input type="number" name="purchase_price" class="form-control" min="0" value="{{ old('purchase_price', $product->purchase_price) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Jual (Rp)</label>
                    <input type="number" name="selling_price" class="form-control" min="0" value="{{ old('selling_price', $product->selling_price) }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">Stok Fisik</label>
                    <input type="number" name="stock" class="form-control" min="0" value="{{ old('stock', $product->stock) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Minimum Stok</label>
                    <input type="number" name="minimum_stock" class="form-control" min="0" value="{{ old('minimum_stock', $product->minimum_stock) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Ganti Foto / Gambar Produk (Opsional)</label>
                @if($product->image)
                    <div style="margin-bottom: 8px;">
                        <img src="{{ asset('uploads/' . basename($product->image)) }}" alt="{{ $product->name }}" style="width: 80px; height: 80px; object-fit: contain; border-radius: 8px; border: 1px solid var(--border-soft); padding: 4px;">
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn-action-primary" style="width: 100%; justify-content: center; padding: 12px; margin-top: 10px; font-size: 15px;">
                 Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
