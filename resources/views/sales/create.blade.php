@extends('layouts.app')

@section('title', 'Form Penjualan Baru - Sibu Toy')
@section('page_title', 'Tambah Transaksi Penjualan')
@section('page_subtitle', 'Input penjualan mainan baru, kuantitas, dan validasi stok otomatis.')

@section('top_actions')
    <a href="{{ route('sales.index') }}" class="btn-action-soft">
        
        <span>Kembali ke Riwayat</span>
    </a>
@endsection

@section('content')
<div style="max-width: 650px; margin: 0 auto; width: 100%;">
    <div class="section-card">
        <div style="margin-bottom: 24px; padding-bottom: 14px; border-bottom: 1px solid var(--border-soft);">
            <h3 style="font-size: 17px; font-weight: 700; color: var(--text-primary);">Form Kasir / Penjualan</h3>
            <p style="font-size: 13px; color: var(--text-secondary);">Pilih barang dan jumlah pesanan pelanggan</p>
        </div>

        <form action="{{ route('sales.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Pilih Produk Mainan</label>
                <select name="items[0][product_id]" class="form-control" id="productSelect" onchange="updatePricePreview()" required>
                    <option value="">-- Pilih Produk Mainan --</option>
                    @foreach(\App\Models\Product::all() as $product)
                        <option value="{{ $product->id }}" 
                                data-price="{{ $product->selling_price }}" 
                                data-stock="{{ $product->stock }}">
                            {{ $product->name }} (Sisa Stok: {{ $product->stock }} | Rp {{ number_format($product->selling_price, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Kuantitas Pembelian (Jumlah Beli)</label>
                <input type="number" name="items[0][quantity]" id="quantityInput" class="form-control" min="1" value="1" oninput="updatePricePreview()" required>
            </div>

            <!-- Estimasi Total -->
            <div style="background: #f8fafc; border: 1px dashed var(--border-soft); border-radius: var(--radius-md); padding: 16px; margin: 20px 0; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 14px; font-weight: 600; color: var(--text-secondary);">Estimasi Total Pembayaran:</span>
                <span id="totalPreview" style="font-size: 20px; font-weight: 800; color: #7c3aed;">Rp 0</span>
            </div>

            <button type="submit" class="btn-action-primary" style="width: 100%; justify-content: center; padding: 12px; font-size: 15px;">
                 Simpan Transaksi & Kurangi Stok
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function updatePricePreview() {
        const select = document.getElementById('productSelect');
        const qty = parseInt(document.getElementById('quantityInput').value) || 0;
        const selected = select.options[select.selectedIndex];
        const price = parseFloat(selected.getAttribute('data-price')) || 0;
        const total = price * qty;
        
        document.getElementById('totalPreview').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }
</script>
@endpush
@endsection
