@extends('layouts.app')

@section('title', 'Transaksi Masuk (Pembelian) - Sibu Toy')
@section('page_title', 'Transaksi Masuk (Pembelian/Restock)')
@section('page_subtitle', 'Pencatatan faktur barang masuk dari pemasok dan penambahan stok inventaris otomatis.')

@section('content')
<div class="bottom-layout-grid" style="grid-template-columns: 1fr 1.6fr; gap: 24px;">

    <!-- Form Transaksi Masuk -->
    <div class="section-card">
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary);">Form Restock Barang</h3>
            <p style="font-size: 12.5px; color: var(--text-secondary);">Catat purchase order baru</p>
        </div>

        <form action="{{ route('purchases.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Pilih Pemasok / Supplier</label>
                <select name="supplier_id" class="form-control" required>
                    <option value="">-- Pilih Rekanan Pemasok --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
                @if(count($suppliers) === 0)
                    <small style="color: #e11d48; margin-top: 4px; font-size: 11.5px;">
                        * Belum ada supplier. Tambahkan di <a href="{{ route('suppliers.index') }}" style="color: #2563eb; font-weight: 600;">Menu Pemasok</a> terlebih dahulu.
                    </small>
                @endif
            </div>

            <div class="form-group">
                <label class="form-label">Pilih Produk</label>
                <select name="items[0][product_id]" class="form-control" id="productSelect" onchange="autoFillUnitCost()" required>
                    <option value="">-- Pilih Barang yang Masuk --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" data-cost="{{ $p->purchase_price }}">
                            {{ $p->name }} (Stok Saat Ini: {{ $p->stock }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label class="form-label">Kuantitas Masuk</label>
                    <input type="number" name="items[0][quantity]" class="form-control" min="1" value="10" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Beli Satuan (Rp)</label>
                    <input type="number" name="items[0][unit_cost]" id="unitCostInput" class="form-control" min="0" placeholder="0" required>
                </div>
            </div>

            <button type="submit" class="btn-action-primary" style="width: 100%; justify-content: center; margin-top: 10px;" {{ count($suppliers) === 0 || count($products) === 0 ? 'disabled' : '' }}>
                 Simpan & Tambahkan ke Stok
            </button>
        </form>
    </div>

    <!-- Tabel Riwayat Pembelian -->
    <div class="section-card">
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary);">Riwayat Transaksi Masuk</h3>
            <p style="font-size: 12.5px; color: var(--text-secondary);">Log faktur barang masuk</p>
        </div>

        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>No. Faktur</th>
                        <th>Pemasok</th>
                        <th>Waktu Masuk</th>
                        <th>Detail Item</th>
                        <th>Total Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $pur)
                        <tr>
                            <td>
                                <strong style="color: #2563eb; font-size: 13.5px;">{{ $pur->purchase_number }}</strong>
                                <div style="font-size: 11px; color: var(--text-muted);">Operator: {{ $pur->user->name ?? 'Admin' }}</div>
                            </td>
                            <td>
                                <strong style="color: var(--text-primary); font-size: 13px;">{{ $pur->supplier->name ?? '-' }}</strong>
                            </td>
                            <td style="font-size: 12.5px; color: var(--text-secondary);">
                                {{ \Carbon\Carbon::parse($pur->purchased_at)->format('d M Y H:i') }}
                            </td>
                            <td>
                                @foreach($pur->items as $item)
                                    <div style="font-size: 12px; margin-bottom: 2px;">
                                        • {{ $item->product->name ?? 'Barang' }} 
                                        <strong>x{{ $item->quantity }}</strong>
                                    </div>
                                @endforeach
                            </td>
                            <td style="font-weight: 800; color: #0f172a;">
                                Rp {{ number_format($pur->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                Belum ada riwayat transaksi masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($purchases, 'links') && $purchases->hasPages())
            <div style="margin-top: 24px; padding-top: 18px; border-top: 1.5px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                <div style="font-size: 13px; color: var(--text-secondary);">
                    Menampilkan <strong>{{ $purchases->firstItem() }}</strong> - <strong>{{ $purchases->lastItem() }}</strong> dari <strong>{{ $purchases->total() }}</strong> faktur
                </div>
                <div>
                    {{ $purchases->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    function autoFillUnitCost() {
        const select = document.getElementById('productSelect');
        const selected = select.options[select.selectedIndex];
        const cost = selected.getAttribute('data-cost');
        if (cost) {
            document.getElementById('unitCostInput').value = Math.round(cost);
        }
    }
</script>
@endpush
@endsection
