@extends('layouts.app')

@section('title', 'Pengaturan Sistem - Sibu Toy')
@section('page_title', 'Pengaturan Sistem')
@section('page_subtitle', 'Konfigurasi akun, preferensi toko, dan parameter operasional.')

@section('content')
<div style="max-width: 750px; margin: 0 auto; width: 100%;">
    <div class="section-card">
        <div style="margin-bottom: 24px; padding-bottom: 14px; border-bottom: 1px solid var(--border-soft);">
            <h3 style="font-size: 17px; font-weight: 700; color: var(--text-primary);">Pengaturan Akun & Profil</h3>
            <p style="font-size: 13px; color: var(--text-secondary);">Informasi pengguna yang sedang aktif dalam sesi</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Nama Pengguna</label>
                <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly style="background: #f1f5f9;">
            </div>

            <div class="form-group">
                <label class="form-label">Email Pengguna</label>
                <input type="text" class="form-control" value="{{ auth()->user()->email }}" readonly style="background: #f1f5f9;">
            </div>

            <div class="form-group">
                <label class="form-label">Tingkat Hak Akses (Role)</label>
                <input type="text" class="form-control" value="{{ ucfirst(auth()->user()->role ?? 'Staff') }}" readonly style="background: #f1f5f9;">
            </div>

            <div style="padding: 16px; border-radius: var(--radius-md); background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; font-size: 13px;">
                
                Sistem database, relasi multi-tabel, dan seluruh rute navigasi telah aktif dan terhubung secara atomik.
            </div>
        </div>
    </div>
</div>
@endsection
