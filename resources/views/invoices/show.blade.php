@extends('layouts.app')

@section('title', 'Nota Pesanan #' . $sale->sale_number)
@section('page_title', 'Nota Pembelian Sibu Toy')
@section('page_subtitle', 'Faktur resmi transaksi pesanan pelanggan.')

@section('top_actions')
    <a href="{{ route('dashboard') }}" class="btn-action-soft">
        
        <span>Kembali ke Dashboard</span>
    </a>
    <button onclick="window.print()" class="btn-action-primary">
        
        <span>Cetak Nota</span>
    </button>
@endsection

@section('content')
<div style="max-width: 680px; margin: 0 auto; width: 100%;">
    <div class="section-card" id="invoiceCard" style="background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-hover); padding: 36px; border: 2px solid var(--border-soft); position: relative; overflow: hidden;">
        
        <!-- Hiasan Top Border Pastel -->
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(90deg, #b197fc 0%, #f783ac 50%, #63e6be 100%);"></div>

        <!-- Header Nota -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 22px; border-bottom: 2px dashed #f1edfa; margin-bottom: 22px; margin-top: 6px;">
            <div>
                <h2 style="font-size: 20px; font-weight: 800; color: #2b2d42; letter-spacing: -0.4px; margin-bottom: 6px;">Sibu Toy</h2>
                <p style="font-size: 12.5px; color: #6c757d;">Official Store Art Toys & Collectibles</p>
                <p style="font-size: 12px; color: #adb5bd;">Jl. Operasional Inventaris No. 88</p>
            </div>
            <div style="text-align: right;">
                <span style="display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 11.5px; font-weight: 800; background: #e6fcf5; color: #0ca678; margin-bottom: 6px; border: 1px solid #c3fae8;">
                     LUNAS / SELESAI
                </span>
                <div style="font-size: 14px; font-weight: 800; color: #7048e8;">#{{ $sale->sale_number }}</div>
                <div style="font-size: 12px; color: #adb5bd;">{{ \Carbon\Carbon::parse($sale->sold_at)->format('d F Y, H:i') }} WIB</div>
            </div>
        </div>

        <!-- Info Pembeli -->
        <div style="background: #faf7fd; border-radius: var(--radius-md); padding: 16px 20px; margin-bottom: 24px; display: flex; justify-content: space-between; font-size: 13px; border: 1px solid #f1edfa;">
            <div>
                <span style="color: #6c757d; display: block; margin-bottom: 3px; font-size: 11.5px; font-weight: 700; text-transform: uppercase;">Diterbitkan Untuk:</span>
                <strong style="color: #2b2d42; font-size: 14.5px;">{{ $sale->user->name ?? 'Customer' }}</strong>
                <div style="color: #6c757d; font-size: 12px;">{{ $sale->user->email ?? '-' }}</div>
            </div>
            <div style="text-align: right;">
                <span style="color: #6c757d; display: block; margin-bottom: 3px; font-size: 11.5px; font-weight: 700; text-transform: uppercase;">Status Pesanan:</span>
                <strong style="color: #0ca678; font-size: 13.5px;">
                     Diterima Pelanggan
                </strong>
            </div>
        </div>

        <!-- Rincian Item Barang -->
        <div class="table-responsive" style="margin-bottom: 22px;">
            <table class="table-modern" style="font-size: 13.5px;">
                <thead>
                    <tr>
                        <th>Item Mainan</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Harga</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                        <tr>
                            <td>
                                <strong style="color: #2b2d42;">{{ $item->product->name ?? 'Produk' }}</strong>
                                <div style="font-size: 11.5px; color: #adb5bd;">SKU: {{ $item->product->sku ?? '-' }}</div>
                            </td>
                            <td style="text-align: center; font-weight: 800; color: #7048e8;">{{ $item->quantity }}</td>
                            <td style="text-align: right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td style="text-align: right; font-weight: 800; color: #e64980;">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total Pembayaran -->
        <div style="border-top: 2px dashed #f1edfa; padding-top: 16px; margin-bottom: 26px;">
            <div style="display: flex; justify-content: space-between; font-size: 13.5px; color: #6c757d; margin-bottom: 6px;">
                <span>Total Item:</span>
                <span style="font-weight: 700;">{{ $sale->items->sum('quantity') }} unit</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13.5px; color: #6c757d; margin-bottom: 12px;">
                <span>Pajak & Biaya Layanan:</span>
                <span style="color: #0ca678; font-weight: 700;">GRATIS</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1.5px solid var(--border-soft); padding-top: 12px;">
                <span style="font-size: 16px; font-weight: 800; color: #2b2d42;">Total Pembayaran:</span>
                <span style="font-size: 24px; font-weight: 800; color: #e64980;">
                    Rp {{ number_format($sale->total, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Footer / Catatan -->
        <div style="text-align: center; color: #adb5bd; font-size: 12.5px; border-top: 1.5px solid var(--border-soft); padding-top: 18px;">
            <p>Terima kasih telah berbelanja di <strong>Sibu Toy</strong>!</p>
            <p style="margin-top: 3px;">Simpan nota ini sebagai bukti transaksi pembelian yang sah.</p>
        </div>

    </div>
</div>

<style>
    @media print {
        body { background: white !important; }
        .sidebar, .top-navbar, .top-actions { display: none !important; }
        .main-wrapper { margin-left: 0 !important; width: 100% !important; }
        .content-container { padding: 0 !important; }
        #invoiceCard { border: none !important; box-shadow: none !important; }
    }
</style>
@endsection
