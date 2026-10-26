@extends('layouts.app')

@section('title', 'Detail Produk: ' . $product->name . ' - Sibu Toy')
@section('page_title', 'Detail Produk')
@section('page_subtitle', 'Informasi spesifikasi lengkap stok, rincian harga, serta jejak riwayat transaksi.')

@section('top_actions')
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('products.index') }}" class="btn-action-soft">
            
            <span>Kembali ke Katalog</span>
        </a>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('products.edit', $product->id) }}" class="btn-action-primary">
                
                <span>Edit Produk</span>
            </a>
        @endif
    </div>
@endsection

@section('content')
<!-- Ringkasan Kartu Atas -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 24px;">
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <span class="stat-card-title">Sisa Stok Fisik</span>
                <h3 class="stat-card-value" style="color: {{ $product->stock <= $product->minimum_stock ? '#e11d48' : '#059669' }};">
                    {{ $product->stock }} <small style="font-size: 14px; font-weight: 500; color: var(--text-secondary);">unit</small>
                </h3>
            </div>
        </div>
        <div style="font-size: 12px; margin-top: 6px; font-weight: 600; color: {{ $product->stock <= $product->minimum_stock ? '#e11d48' : 'var(--text-secondary)' }};">
            Batas Minimum: {{ $product->minimum_stock }} unit ({{ $product->stock <= $product->minimum_stock ? 'Perlu Restock!' : 'Stok Aman' }})
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <span class="stat-card-title">Harga Jual Satuan</span>
                <h3 class="stat-card-value" style="color: #7c3aed;">
                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                </h3>
            </div>
        </div>
        <div style="font-size: 12px; margin-top: 6px; color: var(--text-secondary);">
            Harga Beli Modal: <strong>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</strong>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <span class="stat-card-title">Total Terjual (Sales)</span>
                <h3 class="stat-card-value" style="color: #0284c7;">
                    {{ $totalSold }} <small style="font-size: 14px; font-weight: 500; color: var(--text-secondary);">unit</small>
                </h3>
            </div>
        </div>
        <div style="font-size: 12px; margin-top: 6px; color: var(--text-secondary);">
            Omzet Penjualan: <strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <span class="stat-card-title">Total Pembelian (Restock)</span>
                <h3 class="stat-card-value" style="color: #ea580c;">
                    {{ $totalPurchased }} <small style="font-size: 14px; font-weight: 500; color: var(--text-secondary);">unit</small>
                </h3>
            </div>
        </div>
        <div style="font-size: 12px; margin-top: 6px; color: var(--text-secondary);">
            Margin Profit: <strong>Rp {{ number_format(max(0, $product->selling_price - $product->purchase_price), 0, ',', '.') }} / unit</strong>
        </div>
    </div>

</div>

<!-- Info Rinci Produk -->
<div class="section-card" style="margin-bottom: 24px;">
    <div style="display: flex; flex-wrap: wrap; gap: 24px; align-items: flex-start;">
        
        <!-- Foto Produk -->
        <div style="flex: 0 0 160px; max-width: 160px; width: 100%;">
            @if($product->image)
                <img src="{{ asset('uploads/' . basename($product->image)) }}" alt="{{ $product->name }}" style="width: 100%; height: 160px; object-fit: contain; background: #fdf2f8; border-radius: 16px; border: 2px dashed #fbcfe8; padding: 6px;">
            @else
                <div style="width: 100%; height: 160px; background: #fdf2f8; border-radius: 16px; border: 2px dashed #fbcfe8; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #f472b6; font-size: 32px;">
                    
                    <span style="font-size: 11px; font-weight: 600; margin-top: 6px; color: #db2777;">Tanpa Foto</span>
                </div>
            @endif
        </div>

        <!-- Detail Meta Produk -->
        <div style="flex: 1; min-width: 260px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 700; color: #7c3aed; background: #ede9fe; padding: 4px 10px; border-radius: 8px;">
                    {{ $product->category->name ?? 'Uncategorized' }}
                </span>
                <span style="font-size: 12px; font-weight: 600; color: var(--text-muted);">
                    SKU: <strong>{{ $product->sku }}</strong>
                </span>
            </div>
            
            <h2 style="font-size: 22px; font-weight: 800; color: var(--text-primary); margin-bottom: 12px;">
                {{ $product->name }}
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-top: 14px; background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid var(--border-soft);">
                <div>
                    <div style="font-size: 11.5px; color: var(--text-muted); font-weight: 600;">HARGA BELI (HPP)</div>
                    <div style="font-size: 15px; font-weight: 700; color: var(--text-primary);">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</div>
                </div>
                <div>
                    <div style="font-size: 11.5px; color: var(--text-muted); font-weight: 600;">HARGA JUAL KONSUMEN</div>
                    <div style="font-size: 15px; font-weight: 700; color: #7c3aed;">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                </div>
                <div>
                    <div style="font-size: 11.5px; color: var(--text-muted); font-weight: 600;">STATUS INVENTARIS</div>
                    <div>
                        <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 700; {{ $product->stock <= $product->minimum_stock ? 'background: #fee2e2; color: #b91c1c;' : 'background: #dcfce7; color: #15803d;' }}">
                            {{ $product->stock <= $product->minimum_stock ? 'Perlu Restock' : 'Tersedia Normal' }}
                        </span>
                    </div>
                </div>
                <div>
                    <div style="font-size: 11.5px; color: var(--text-muted); font-weight: 600;">TANGGAL TERDAFTAR</div>
                    <div style="font-size: 13px; font-weight: 600; color: var(--text-secondary);">{{ $product->created_at ? $product->created_at->format('d M Y, H:i') : '-' }}</div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Grid Riwayat Penjualan & Pembelian -->
<div class="bottom-layout-grid" style="grid-template-columns: 1fr 1fr; gap: 24px;">

    <!-- 1. Riwayat Penjualan (Sales) -->
    <div class="section-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary);">Riwayat Penjualan</h3>
                <p style="font-size: 12px; color: var(--text-secondary);">Transaksi keluar untuk produk ini</p>
            </div>
            <span style="font-size: 12px; font-weight: 700; color: #7c3aed; background: #ede9fe; padding: 3px 8px; border-radius: 6px;">
                {{ $saleItems->total() ?? count($saleItems) }} Trx
            </span>
        </div>

        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>No. Transaksi</th>
                        <th>User / Kasir</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($saleItems as $sItem)
                        <tr>
                            <td>
                                <strong style="color: #7c3aed; font-size: 12.5px;">{{ $sItem->sale->sale_number ?? '-' }}</strong>
                            </td>
                            <td>
                                <span style="font-size: 12px; font-weight: 600; color: var(--text-primary);">
                                    {{ $sItem->sale->user->name ?? 'Kasir' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: #0284c7;">{{ $sItem->quantity }}x</span>
                            </td>
                            <td style="font-weight: 700; font-size: 12.5px;">
                                Rp {{ number_format($sItem->subtotal, 0, ',', '.') }}
                            </td>
                            <td style="font-size: 11.5px; color: var(--text-secondary);">
                                {{ $sItem->sale && $sItem->sale->sold_at ? \Carbon\Carbon::parse($sItem->sale->sold_at)->format('d/m/y H:i') : ($sItem->created_at ? $sItem->created_at->format('d/m/y H:i') : '-') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted); font-size: 12.5px;">
                                Belum ada riwayat transaksi penjualan untuk mainan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($saleItems, 'links') && $saleItems->hasPages())
            <div style="margin-top: 14px;">
                {{ $saleItems->links() }}
            </div>
        @endif
    </div>

    <!-- 2. Riwayat Pembelian (Purchases/Restock) -->
    <div class="section-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary);">Riwayat Pembelian</h3>
                <p style="font-size: 12px; color: var(--text-secondary);">Faktur pasokan masuk dari supplier</p>
            </div>
            <span style="font-size: 12px; font-weight: 700; color: #0284c7; background: #e0f2fe; padding: 3px 8px; border-radius: 6px;">
                {{ $purchaseItems->total() ?? count($purchaseItems) }} Faktur
            </span>
        </div>

        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>No. Faktur</th>
                        <th>Supplier</th>
                        <th>Qty</th>
                        <th>Biaya Satuan</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseItems as $pItem)
                        <tr>
                            <td>
                                <strong style="color: #0284c7; font-size: 12.5px;">{{ $pItem->purchase->purchase_number ?? '-' }}</strong>
                            </td>
                            <td>
                                <strong style="color: var(--text-primary); font-size: 12px;">
                                    {{ $pItem->purchase->supplier->name ?? '-' }}
                                </strong>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: #059669;">+{{ $pItem->quantity }}x</span>
                            </td>
                            <td style="font-weight: 700; font-size: 12.5px;">
                                Rp {{ number_format($pItem->unit_cost, 0, ',', '.') }}
                            </td>
                            <td style="font-size: 11.5px; color: var(--text-secondary);">
                                {{ $pItem->purchase && $pItem->purchase->purchased_at ? \Carbon\Carbon::parse($pItem->purchase->purchased_at)->format('d/m/y H:i') : ($pItem->created_at ? $pItem->created_at->format('d/m/y H:i') : '-') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted); font-size: 12.5px;">
                                Belum ada riwayat transaksi restock/pembelian untuk mainan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($purchaseItems, 'links') && $purchaseItems->hasPages())
            <div style="margin-top: 14px;">
                {{ $purchaseItems->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
