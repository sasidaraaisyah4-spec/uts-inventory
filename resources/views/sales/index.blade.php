@extends('layouts.app')

@section('title', 'Transaksi Keluar (Penjualan) - Sibu Toy')
@section('page_title', 'Transaksi Keluar (Penjualan/Kasir)')
@section('page_subtitle', 'Pencatatan nota penjualan kasir, pengurangan stok otomatis, dan riwayat pesanan.')

@section('top_actions')
    <a href="{{ route('sales.create') }}" class="btn-action-primary">
        
        <span>Tambah Penjualan Baru</span>
    </a>
@endsection

@section('content')
<div class="section-card">

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <form action="{{ route('sales.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; flex: 1; max-width: 820px;">
            <div class="search-input-group" style="flex: 1.5; min-width: 220px;">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. faktur, nama kasir/customer, produk..." class="form-control">
            </div>

            <div style="flex: 1; min-width: 140px;">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div style="display: flex; align-items: center; gap: 6px;">
                <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()" class="form-control" style="padding: 7px 10px; font-size: 12px; width: 130px;" title="Dari Tanggal">
                <span style="color: var(--text-muted);">-</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()" class="form-control" style="padding: 7px 10px; font-size: 12px; width: 130px;" title="Sampai Tanggal">
            </div>

            <div style="display: flex; gap: 6px;">
                <button type="submit" class="btn-action-primary" style="padding: 9px 18px;">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
                @if(request('search') || request('status') || request('start_date') || request('end_date'))
                    <a href="{{ route('sales.index') }}" class="btn-action-soft" style="padding: 9px 14px;" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>

        <span style="font-size: 13px; font-weight: 600; color: var(--text-secondary); background: #f8fafc; padding: 6px 14px; border-radius: 20px; border: 1.5px solid var(--border-soft);">
            Total: {{ $sales->total() ?? count($sales) }} Transaksi
        </span>
    </div>

    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Kasir / Petugas</th>
                    <th>Waktu Transaksi</th>
                    <th>Item Terjual</th>
                    <th>Total Pembayaran</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr>
                        <td>
                            <strong style="color: #7c3aed; font-size: 13.5px;">{{ $sale->sale_number }}</strong>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: var(--text-primary);">
                                {{ $sale->user->name ?? 'Kasir' }}
                            </span>
                        </td>
                        <td style="font-size: 12.5px; color: var(--text-secondary);">
                            {{ \Carbon\Carbon::parse($sale->sold_at)->format('d M Y H:i') }}
                        </td>
                        <td>
                            @foreach($sale->items as $item)
                                <div style="font-size: 12px; margin-bottom: 2px;">
                                    • {{ $item->product->name ?? 'Produk' }} 
                                    <strong>x{{ $item->quantity }}</strong> 
                                    <span style="color: var(--text-muted);">(Rp {{ number_format($item->subtotal, 0, ',', '.') }})</span>
                                </div>
                            @endforeach
                        </td>
                        <td style="font-weight: 800; color: #0f172a; font-size: 14.5px;">
                            Rp {{ number_format($sale->total, 0, ',', '.') }}
                        </td>
                        <td>
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 600; background: #dcfce7; color: #15803d;">
                                
                                {{ ucfirst($sale->status ?? 'Completed') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            Belum ada riwayat transaksi penjualan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($sales, 'links') && $sales->hasPages())
        <div style="margin-top: 24px; padding-top: 18px; border-top: 1.5px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
            <div style="font-size: 13px; color: var(--text-secondary);">
                Menampilkan <strong>{{ $sales->firstItem() }}</strong> sampai <strong>{{ $sales->lastItem() }}</strong> dari <strong>{{ $sales->total() }}</strong> transaksi
            </div>
            <div>
                {{ $sales->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
