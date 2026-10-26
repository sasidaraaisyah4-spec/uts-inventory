@extends('layouts.app')

@section('title', 'Tambah Produk Baru - Sibu Toy')
@section('page_title', 'Tambah Produk Baru')
@section('page_subtitle', 'Masukkan data spesifikasi, harga beli, harga jual, dan foto produk.')

@section('top_actions')
    <a href="{{ route('products.index') }}" class="btn-action-soft">
        
        <span>Kembali ke Produk</span>
    </a>
@endsection

@section('content')
<div style="max-width: 650px; margin: 0 auto; width: 100%;">
    <div class="section-card">
        <div style="margin-bottom: 24px; padding-bottom: 14px; border-bottom: 1px solid var(--border-soft);">
            <h3 style="font-size: 17px; font-weight: 700; color: var(--text-primary);">Form Produk Mainan</h3>
            <p style="font-size: 13px; color: var(--text-secondary);">Isi data lengkap inventaris baru</p>
        </div>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Nama Produk</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Skullpanda Everyday Wonderland" required>
            </div>

            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">SKU (Kode Unik Produk)</label>
                <input type="text" name="sku" class="form-control" placeholder="Contoh: SKP-001" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">Harga Beli (Rp)</label>
                    <input type="number" name="purchase_price" class="form-control" min="0" placeholder="150000" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Jual (Rp)</label>
                    <input type="number" name="selling_price" class="form-control" min="0" placeholder="220000" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label class="form-label">Stok Awal</label>
                    <input type="number" name="stock" class="form-control" min="0" value="10" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Minimum Stok (Peringatan)</label>
                    <input type="number" name="minimum_stock" class="form-control" min="0" value="5" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Foto / Gambar Produk</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn-action-primary" style="width: 100%; justify-content: center; padding: 12px; margin-top: 10px; font-size: 15px;">
                 Simpan Produk Baru
            </button>
        </form>
    </div>
</div>
@endsection
