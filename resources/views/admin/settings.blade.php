@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="admin-section-title">Pengaturan</h1>
        <p class="admin-section-subtitle">Konfigurasi profil admin dan informasi rekening pembayaran platform.</p>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-sm font-medium">
        <i data-lucide="check-circle" style="width:18px;height:18px;flex-shrink:0"></i>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm">
        <i data-lucide="alert-circle" style="width:18px;height:18px;flex-shrink:0;margin-top:2px"></i>
        <ul class="list-disc ml-1 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <div class="grid gap-6 xl:grid-cols-2">

            {{-- ====== PROFIL ADMIN ====== --}}
            <div class="admin-card p-6 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <i data-lucide="user" style="width:20px;height:20px;color:#6366f1"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Profil Admin</h2>
                        <p class="text-xs text-slate-400">Informasi akun dan keamanan</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" class="admin-form-control" value="{{ old('name', auth()->user()->name) }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Email</label>
                        <input type="email" name="email" class="admin-form-control" value="{{ old('email', auth()->user()->email) }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">No. Telepon</label>
                        <input type="text" name="phone" class="admin-form-control" value="{{ old('phone', auth()->user()->phone) }}" placeholder="Contoh: 081234567890">
                    </div>
                    <div class="pt-2 border-t border-slate-100">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Password Baru <span class="text-slate-300 normal-case font-normal">(opsional)</span></label>
                        <input type="password" name="password" class="admin-form-control" placeholder="Biarkan kosong jika tidak ingin mengubah">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="admin-form-control" placeholder="Ulangi password baru">
                    </div>
                </div>

                <div class="pt-2 text-right">
                    <button type="submit" class="admin-button admin-button-primary">
                        <i data-lucide="save" class="me-1.5" style="width:16px;height:16px"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>

            {{-- ====== INFORMASI REKENING ====== --}}
            <div class="space-y-5">
                <div class="admin-card p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <i data-lucide="credit-card" style="width:20px;height:20px;color:#059669"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Rekening Platform</h2>
                            <p class="text-xs text-slate-400">Ditampilkan ke pembeli saat pembayaran checkout</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Bank</label>
                            <input type="text" name="bank_name" class="admin-form-control" value="{{ old('bank_name', auth()->user()->bank_name) }}" placeholder="Contoh: BCA, BNI, Mandiri, BRI">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nomor Rekening</label>
                            <input type="text" name="bank_account_number" class="admin-form-control" value="{{ old('bank_account_number', auth()->user()->bank_account_number) }}" placeholder="Contoh: 1234567890">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Atas Nama</label>
                            <input type="text" name="bank_account_name" class="admin-form-control" value="{{ old('bank_account_name', auth()->user()->bank_account_name) }}" placeholder="Nama pemilik rekening">
                        </div>
                    </div>
                </div>

                {{-- Pratinjau Kartu Rekening --}}
                @if(auth()->user()->bank_account_number)
                <div class="admin-card p-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i data-lucide="eye" style="width:14px;height:14px"></i>
                        Pratinjau Tampilan ke Pembeli
                    </h3>

                    {{-- Virtual Bank Card --}}
                    <div class="relative overflow-hidden rounded-2xl p-5 text-white"
                        style="background: linear-gradient(135deg, #1e3a5f 0%, #0f766e 100%); min-height: 140px;">
                        {{-- Decorative circles --}}
                        <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full opacity-10 bg-white"></div>
                        <div class="absolute top-8 -right-2 w-20 h-20 rounded-full opacity-10 bg-white"></div>

                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-bold uppercase tracking-widest opacity-70">Campify Platform</span>
                                <span class="text-xs font-bold bg-white/20 px-2 py-0.5 rounded-full">{{ strtoupper(auth()->user()->bank_name ?? 'BANK') }}</span>
                            </div>
                            <p class="text-2xl font-black tracking-widest mb-3 font-mono">
                                {{ auth()->user()->bank_account_number }}
                            </p>
                            <p class="text-xs opacity-80">Atas Nama</p>
                            <p class="text-sm font-bold uppercase tracking-wide">{{ auth()->user()->bank_account_name }}</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-400 mt-3 text-center">
                        <i data-lucide="info" class="inline" style="width:12px;height:12px"></i>
                        Tampilan ini yang dilihat pembeli saat melakukan transfer
                    </p>
                </div>
                @else
                <div class="admin-card p-6 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="credit-card" style="width:24px;height:24px;color:#94a3b8"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-500">Belum Ada Rekening</p>
                    <p class="text-xs text-slate-400 mt-1">Isi form di atas lalu simpan untuk menampilkan kartu rekening virtual di sini.</p>
                </div>
                @endif
            </div>

        </div>
    </form>
</div>
@endsection
