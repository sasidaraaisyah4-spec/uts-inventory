@extends('layouts.app')

@section('title', 'Kategori Produk - Sibu Toy')
@section('page_title', 'Kategori Produk')
@section('page_subtitle', 'Manajemen klasifikasi dan grup produk inventaris.')

@section('content')
<div class="bottom-layout-grid" style="grid-template-columns: 1fr 1.6fr; gap: 24px;">

    <!-- Kolom Kiri: Form Tambah/Edit Kategori (Khusus Admin) atau Panel Informasi (Untuk Staff) -->
    <div class="section-card">
        @if(auth()->user()->role === 'admin')
            <div style="margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary);" id="formTitle">Tambah Kategori Baru</h3>
                <p style="font-size: 12.5px; color: var(--text-secondary);" id="formSubtitle">Masukkan nama kelompok barang/mainan baru</p>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" id="categoryForm">
                @csrf
                <div id="methodContainer"></div>

                <div class="form-group">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" id="categoryNameInput" class="form-control" placeholder="Contoh: Blind Box, Action Figure, Plushie" required>
                </div>

                <div style="display: flex; gap: 8px; margin-top: 10px;">
                    <button type="submit" class="btn-action-primary" id="btnSubmitForm" style="flex: 1; justify-content: center;">
                         Simpan Kategori
                    </button>
                    <button type="button" class="btn-action-soft" id="btnCancelEdit" onclick="cancelEditCategory()" style="display: none; padding: 9px 14px;">
                        Batal
                    </button>
                </div>
            </form>
        @else
            <!-- Tampilan Akses Terbatas untuk Role Staff -->
            <div style="margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary);">Akses Terbatas (Staff)</h3>
                <p style="font-size: 12.5px; color: var(--text-secondary);">Hak akses pengubahan data master kategori</p>
            </div>

            <div style="background: #f8fafc; border: 1px dashed var(--border-soft); border-radius: var(--radius-md); padding: 18px; font-size: 13px; color: var(--text-secondary); line-height: 1.6;">
                <p style="margin-bottom: 10px;">
                    
                    Anda masuk sebagai <strong>{{ auth()->user()->name }} (Staff Kasir)</strong>.
                </p>
                <p>
                    Sebagai Staff, Anda hanya memiliki izin untuk <strong>melihat daftar kategori</strong>. Modifikasi data master (menambah, mengedit, atau menghapus kategori) hanya dapat dilakukan oleh akun dengan hak akses <strong>Administrator</strong>.
                </p>
            </div>
        @endif
    </div>

    <!-- Kolom Kanan: Tabel Daftar Kategori -->
    <div class="section-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary);">Daftar Kategori</h3>
                <p style="font-size: 12.5px; color: var(--text-secondary);">Total {{ count($categories) }} kategori terdaftar</p>
            </div>
            @if(auth()->user()->role === 'admin')
                <span style="font-size: 11.5px; font-weight: 600; color: #16a34a; background: #dcfce7; padding: 4px 10px; border-radius: 20px;">
                     Akses Penuh Admin
                </span>
            @else
                <span style="font-size: 11.5px; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 4px 10px; border-radius: 20px;">
                     Mode Lihat Saja
                </span>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Kategori</th>
                        <th>Jumlah Produk</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $cat)
                        <tr>
                            <td style="font-weight: 600; color: var(--text-muted);">{{ $index + 1 }}</td>
                            <td>
                                <span style="font-weight: 700; color: var(--text-primary); font-size: 14px;" id="catNameSpan-{{ $cat->id }}">
                                    {{ $cat->name }}
                                </span>
                            </td>
                            <td>
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #eff6ff; color: #2563eb;">
                                    {{ $cat->products_count ?? $cat->products()->count() }} Produk
                                </span>
                            </td>
                            <td style="text-align: right;">
                                @if(auth()->user()->role === 'admin')
                                    <div style="display: inline-flex; gap: 6px;">
                                        <!-- Tombol Edit Kategori -->
                                        <button type="button" class="btn-action-soft" style="padding: 6px 10px; font-size: 12px;" 
                                                onclick="editCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')" 
                                                title="Edit Kategori">
                                            <i class="fa-regular fa-pen-to-square"></i> Edit
                                        </button>
                                        
                                        <!-- Tombol Hapus Kategori -->
                                        <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('Hapus kategori {{ $cat->name }}? Produk terkait tidak boleh ada dalam kategori ini.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-card-delete" style="padding: 6px 10px; border-radius: 8px;" title="Hapus Kategori">
                                                <i class="fa-regular fa-trash-can"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span style="font-size: 12px; font-weight: 600; color: var(--text-muted); background: #f8fafc; padding: 4px 8px; border-radius: 6px;">
                                         Read Only
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px; color: var(--text-muted);">
                                Belum ada data kategori.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@if(auth()->user()->role === 'admin')
@push('scripts')
<script>
    const storeUrl = "{{ route('categories.store') }}";

    function editCategory(id, name) {
        document.getElementById('formTitle').innerText = 'Edit Kategori';
        document.getElementById('formSubtitle').innerText = 'Perbarui nama kategori yang dipilih';
        document.getElementById('categoryNameInput').value = name;
        document.getElementById('categoryNameInput').focus();

        const form = document.getElementById('categoryForm');
        form.action = "{{ url('/categories') }}/" + id;

        document.getElementById('methodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('btnSubmitForm').innerHTML = ' Perbarui Kategori';
        document.getElementById('btnCancelEdit').style.display = 'inline-flex';
    }

    function cancelEditCategory() {
        document.getElementById('formTitle').innerText = 'Tambah Kategori Baru';
        document.getElementById('formSubtitle').innerText = 'Masukkan nama kelompok barang/mainan baru';
        document.getElementById('categoryNameInput').value = '';

        const form = document.getElementById('categoryForm');
        form.action = storeUrl;

        document.getElementById('methodContainer').innerHTML = '';
        document.getElementById('btnSubmitForm').innerHTML = ' Simpan Kategori';
        document.getElementById('btnCancelEdit').style.display = 'none';
    }
</script>
@endpush
@endif
@endsection
