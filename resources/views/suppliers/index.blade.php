@extends('layouts.app')

@section('title', 'Daftar Pemasok - Sibu Toy')
@section('page_title', 'Pemasok & Rekanan')
@section('page_subtitle', 'Data kontak supplier untuk kebutuhan supply chain dan restock barang.')

@section('content')
<div class="bottom-layout-grid" style="grid-template-columns: 1fr 1.6fr; gap: 24px;">

    <!-- Form Tambah / Edit Supplier -->
    <div class="section-card">
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary);" id="supplierFormTitle">Tambah Pemasok Baru</h3>
            <p style="font-size: 12.5px; color: var(--text-secondary);" id="supplierFormSubtitle">Input informasi vendor / distributor</p>
        </div>

        <form action="{{ route('suppliers.store') }}" method="POST" id="supplierForm">
            @csrf
            <div id="supplierMethodContainer"></div>

            <div class="form-group">
                <label class="form-label">Nama Pemasok / Distributor</label>
                <input type="text" name="name" id="supplierName" class="form-control" placeholder="Contoh: PT PopMart Indonesia" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nomor Telepon / WhatsApp</label>
                <input type="text" name="phone" id="supplierPhone" class="form-control" placeholder="Contoh: 081234567890">
            </div>

            <div class="form-group">
                <label class="form-label">Email Pemasok</label>
                <input type="email" name="email" id="supplierEmail" class="form-control" placeholder="Contoh: supplier@domain.com">
            </div>

            <div style="display: flex; gap: 8px; margin-top: 10px;">
                <button type="submit" class="btn-action-primary" id="btnSubmitSupplier" style="flex: 1; justify-content: center;">
                     Simpan Data Pemasok
                </button>
                <button type="button" class="btn-action-soft" id="btnCancelSupplierEdit" onclick="cancelEditSupplier()" style="display: none; padding: 9px 14px;">
                    Batal
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Supplier -->
    <div class="section-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary);">Daftar Pemasok Aktif</h3>
                <p style="font-size: 12.5px; color: var(--text-secondary);">Total {{ count($suppliers) }} rekanan terdaftar</p>
            </div>
            <span style="font-size: 11.5px; font-weight: 600; color: #16a34a; background: #dcfce7; padding: 4px 10px; border-radius: 20px;">
                 Akses Admin
            </span>
        </div>

        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Vendor</th>
                        <th>Kontak Telepon</th>
                        <th>Email</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $index => $sup)
                        <tr>
                            <td style="font-weight: 600; color: var(--text-muted);">{{ $index + 1 }}</td>
                            <td>
                                <strong style="color: var(--text-primary); font-size: 14px;">{{ $sup->name }}</strong>
                            </td>
                            <td>
                                @if($sup->phone)
                                    <span style="display: inline-flex; align-items: center; gap: 6px; color: #0369a1; font-weight: 600;">
                                         {{ $sup->phone }}
                                    </span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 12px;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($sup->email)
                                    <span style="color: var(--text-secondary); font-size: 12.5px;">{{ $sup->email }}</span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 12px;">-</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <!-- Tombol Edit Pemasok -->
                                    <button type="button" class="btn-action-soft" style="padding: 6px 10px; font-size: 12px;" 
                                            onclick="editSupplier({{ $sup->id }}, '{{ addslashes($sup->name) }}', '{{ addslashes($sup->phone ?? '') }}', '{{ addslashes($sup->email ?? '') }}')" 
                                            title="Edit Data Pemasok">
                                         Edit
                                    </button>

                                    <!-- Tombol Hapus Pemasok -->
                                    <form action="{{ route('suppliers.destroy', $sup->id) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('Hapus pemasok {{ $sup->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-card-delete" style="padding: 6px 10px; border-radius: 8px;" title="Hapus Pemasok">
                                            <i class="fa-regular fa-trash-can"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted);">
                                Belum ada rekanan pemasok yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    const storeSupplierUrl = "{{ route('suppliers.store') }}";

    function editSupplier(id, name, phone, email) {
        // Ubah Judul Form
        document.getElementById('supplierFormTitle').innerText = 'Edit Data Pemasok';
        document.getElementById('supplierFormSubtitle').innerText = 'Perbarui profil kontak ' + name;

        // Isi form dengan data lama
        document.getElementById('supplierName').value = name;
        document.getElementById('supplierPhone').value = phone;
        document.getElementById('supplierEmail').value = email;
        document.getElementById('supplierName').focus();

        // Arahkan action form ke rute update dengan method PUT
        const form = document.getElementById('supplierForm');
        form.action = "{{ url('/suppliers') }}/" + id;

        document.getElementById('supplierMethodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('btnSubmitSupplier').innerHTML = ' Perbarui Pemasok';
        document.getElementById('btnCancelSupplierEdit').style.display = 'inline-flex';
    }

    function cancelEditSupplier() {
        // Reset Judul Form ke kondisi Tambah
        document.getElementById('supplierFormTitle').innerText = 'Tambah Pemasok Baru';
        document.getElementById('supplierFormSubtitle').innerText = 'Input informasi vendor / distributor';

        // Kosongkan form
        document.getElementById('supplierName').value = '';
        document.getElementById('supplierPhone').value = '';
        document.getElementById('supplierEmail').value = '';

        // Kembalikan action form ke store
        const form = document.getElementById('supplierForm');
        form.action = storeSupplierUrl;

        document.getElementById('supplierMethodContainer').innerHTML = '';
        document.getElementById('btnSubmitSupplier').innerHTML = ' Simpan Data Pemasok';
        document.getElementById('btnCancelSupplierEdit').style.display = 'none';
    }
</script>
@endpush
@endsection
