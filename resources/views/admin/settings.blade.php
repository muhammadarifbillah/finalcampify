@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="admin-section-title">Pengaturan</h1>
            <p class="admin-section-subtitle">Konfigurasi profil admin dan preferensi dasar sistem.</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            <div class="grid gap-6 xl:grid-cols-2">
                <div class="admin-card p-6">
                    <h2 class="text-2xl font-extrabold mb-5">Profil Admin</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="admin-stat-label mb-1 d-block">Nama</label>
                            <input type="text" name="name" class="admin-form-control" value="{{ old('name', auth()->user()->name) }}" required>
                        </div>
                        <div>
                            <label class="admin-stat-label mb-1 d-block">Email</label>
                            <input type="email" name="email" class="admin-form-control" value="{{ old('email', auth()->user()->email) }}" required>
                        </div>
                        <div>
                            <label class="admin-stat-label mb-1 d-block">No. Telepon</label>
                            <input type="text" name="phone" class="admin-form-control" value="{{ old('phone', auth()->user()->phone) }}">
                        </div>
                        <div>
                            <label class="admin-stat-label mb-1 d-block">Password Baru (Opsional)</label>
                            <input type="password" name="password" class="admin-form-control" placeholder="Biarkan kosong jika tidak ingin mengubah">
                        </div>
                        <div>
                            <label class="admin-stat-label mb-1 d-block">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="admin-form-control" placeholder="Konfirmasi password baru">
                        </div>
                    </div>
                </div>

                <div class="admin-card p-6">
                    <h2 class="text-2xl font-extrabold mb-5">Informasi Rekening Bank</h2>
                    <p class="text-muted small mb-4">Informasi ini akan ditampilkan ke pembeli pada saat checkout penyewaan.</p>
                    <div class="space-y-4">
                        <div>
                            <label class="admin-stat-label mb-1 d-block">Nama Bank</label>
                            <input type="text" name="bank_name" class="admin-form-control" value="{{ old('bank_name', auth()->user()->bank_name) }}" placeholder="Contoh: BCA, BNI, Mandiri">
                        </div>
                        <div>
                            <label class="admin-stat-label mb-1 d-block">Nomor Rekening</label>
                            <input type="text" name="bank_account_number" class="admin-form-control" value="{{ old('bank_account_number', auth()->user()->bank_account_number) }}" placeholder="Contoh: 1234567890">
                        </div>
                        <div>
                            <label class="admin-stat-label mb-1 d-block">Atas Nama</label>
                            <input type="text" name="bank_account_name" class="admin-form-control" value="{{ old('bank_account_name', auth()->user()->bank_account_name) }}" placeholder="Contoh: Budi Santoso">
                        </div>
                    </div>
                    
                    <div class="mt-5 text-end">
                        <button type="submit" class="admin-button admin-button-primary">
                            <i data-lucide="save" class="me-2" style="width: 18px; height: 18px;"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
