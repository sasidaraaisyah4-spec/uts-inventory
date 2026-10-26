@extends('layouts.app')

@section('title', 'Checkout Produk - Sibu Toy')
@section('page_title', 'Checkout Pembelian Produk')
@section('page_subtitle', 'Konfirmasi pesanan dan selesaikan pembelian secara instan.')

@section('top_actions')
    <a href="{{ route('dashboard') }}" class="btn-action-soft">
        
        <span>Kembali ke Katalog</span>
    </a>
@endsection

@section('content')
<div style="max-width: 820px; margin: 0 auto; width: 100%;">
    <div class="bottom-layout-grid" style="grid-template-columns: 1fr 1.25fr; gap: 26px;">

        <!-- Ringkasan Produk -->
        <div class="section-card" style="text-align: center;">
            <div style="width: 100%; height: 210px; background: linear-gradient(135deg, #f8f0fc 0%, #e6fcf5 100%); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; overflow: hidden; margin-bottom: 16px; padding: 10px;">
                @if($product->image)
                    <img src="{{ asset('uploads/' . basename($product->image)) }}" alt="{{ $product->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                @else
                    <div style="color: #adb5bd; font-size: 13px;">
                        
                        No Image
                    </div>
                @endif
            </div>

            <span style="font-size: 11.5px; font-weight: 700; color: #7048e8; background: #f3f0ff; padding: 4px 12px; border-radius: 14px; display: inline-block; margin-bottom: 8px;">
                {{ $product->category->name ?? 'Art Toy' }}
            </span>

            <h3 style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">{{ $product->name }}</h3>
            <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 14px;">SKU: {{ $product->sku }}</p>

            <div style="font-size: 22px; font-weight: 800; color: #e64980; margin-bottom: 10px;">
                Rp {{ number_format($product->selling_price, 0, ',', '.') }} <span style="font-size: 12.5px; font-weight: 600; color: var(--text-secondary);">/ pcs</span>
            </div>

            <div style="display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; {{ $product->stock > 0 ? 'background: #e6fcf5; color: #0ca678;' : 'background: #fff0f6; color: #e64980;' }}">
                
                {{ $product->stock > 0 ? 'Tersedia: ' . $product->stock . ' unit' : 'Stok Habis' }}
            </div>
        </div>

        <!-- Form Konfirmasi Checkout -->
        <div class="section-card">
            <div style="margin-bottom: 22px; padding-bottom: 14px; border-bottom: 1.5px solid var(--border-soft);">
                <h3 style="font-size: 17px; font-weight: 800; color: var(--text-primary);">Rincian Pemesanan</h3>
                <p style="font-size: 12.5px; color: var(--text-secondary);">Atur jumlah pesanan dan konfirmasi pembayaran</p>
            </div>

            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="form-group">
                    <label class="form-label">Nama Pemesan</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->name }} ({{ auth()->user()->email }})" readonly style="background: #f8f9fa;">
                </div>

                <div class="form-group">
                    <label class="form-label">Jumlah Pembelian (Unit)</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <button type="button" onclick="adjustQty(-1)" class="btn-action-soft" style="padding: 10px 18px; font-size: 18px; font-weight: 800;">-</button>
                        <input type="number" name="quantity" id="checkoutQty" class="form-control" 
                               value="1" min="1" max="{{ $product->stock }}" 
                               style="text-align: center; font-weight: 800; font-size: 17px;" 
                               oninput="calculateTotal()" required>
                        <button type="button" onclick="adjustQty(1)" class="btn-action-soft" style="padding: 10px 18px; font-size: 18px; font-weight: 800;">+</button>
                    </div>
                    <small style="color: var(--text-muted); font-size: 11.5px; margin-top: 5px;">
                        Maksimal pemesanan sesuai stok tersedia ({{ $product->stock }} unit).
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Metode Pembayaran</label>
                    <div style="background: #faf7fd; border: 1.5px solid #ece4f9; border-radius: var(--radius-sm); padding: 12px 16px; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 34px; height: 34px; border-radius: 50%; background: #e6fcf5; color: #0ca678; display: flex; align-items: center; justify-content: center; font-size: 15px;">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <strong style="font-size: 13.5px; color: var(--text-primary); display: block;">Tunai (Cash)</strong>
                                <span style="font-size: 11.5px; color: var(--text-muted);">Pembayaran langsung di kasir toko fisik</span>
                            </div>
                        </div>
                        <span style="font-size: 11px; font-weight: 700; color: #0ca678; background: #e6fcf5; padding: 3px 10px; border-radius: 12px; border: 1px solid #c3fae8;">
                            Opsi Tunggal
                        </span>
                    </div>
                    <input type="hidden" name="payment_method" value="cash">
                </div>

                <!-- Kalkulasi Total Pembayaran -->
                <div style="background: #faf7fd; border: 1.5px dashed #e5dbff; border-radius: var(--radius-md); padding: 18px; margin: 22px 0;">
                    <div style="display: flex; justify-content: space-between; font-size: 13.5px; color: var(--text-secondary); margin-bottom: 8px;">
                        <span>Harga Satuan:</span>
                        <span>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13.5px; color: var(--text-secondary); margin-bottom: 12px;">
                        <span>Kuantitas:</span>
                        <span id="displayQty">1 pcs</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1.5px solid var(--border-soft); padding-top: 12px;">
                        <span style="font-weight: 800; color: var(--text-primary); font-size: 15px;">Total Tagihan:</span>
                        <span id="displayTotal" style="font-size: 22px; font-weight: 800; color: #e64980;">
                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                @if($product->stock > 0)
                    <button type="submit" class="btn-action-primary" style="width: 100%; justify-content: center; padding: 14px; font-size: 15px;">
                         Konfirmasi Pembelian Sekarang
                    </button>
                @else
                    <button type="button" disabled class="btn-action-soft" style="width: 100%; justify-content: center; padding: 14px; cursor: not-allowed; opacity: 0.6;">
                         Maaf, Stok Produk Habis
                    </button>
                @endif
            </form>
        </div>

    </div>
</div>

@push('scripts')
<script>
    const pricePerUnit = {{ $product->selling_price }};
    const maxStock = {{ $product->stock }};

    function calculateTotal() {
        let input = document.getElementById('checkoutQty');
        let qty = parseInt(input.value) || 1;

        if (qty < 1) qty = 1;
        if (qty > maxStock && maxStock > 0) qty = maxStock;
        input.value = qty;

        const total = pricePerUnit * qty;
        document.getElementById('displayQty').innerText = qty + ' pcs';
        document.getElementById('displayTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    function adjustQty(change) {
        let input = document.getElementById('checkoutQty');
        let current = parseInt(input.value) || 1;
        let next = current + change;
        if (next >= 1 && next <= maxStock) {
            input.value = next;
            calculateTotal();
        }
    }
</script>
@endpush
@endsection
