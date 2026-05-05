@extends('layouts.app_pembeli')

@section('content')
<div class="pt-28 pb-20 bg-slate-50">
    <div class="max-w-2xl mx-auto px-4">
        <div class="mb-8">
            <a href="{{ route('produk.detail.rent', $produk->id) }}" class="text-sm text-green-600 hover:text-green-800 font-semibold">← Kembali ke Detail Produk</a>
        </div>
        <div class="bg-white rounded-3xl shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6">Formulir Penyewaan</h2>
            <form action="{{ route('sewa.process') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="product_id" value="{{ $produk->id }}">
                <div>
                    <label class="block text-sm font-semibold mb-1">Tanggal Penyewaan</label>
                    <input type="date" name="start_date" min="{{ date('Y-m-d') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Tanggal Pengembalian</label>
                    <input type="date" name="end_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Durasi Sewa (hari)</label>
                    <input type="number" name="duration" min="1" value="1" class="w-full rounded-xl border border-slate-200 px-4 py-3" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Alamat Pengiriman</label>
                    <textarea name="alamat" class="w-full rounded-xl border border-slate-200 px-4 py-3" rows="3" required>{{ old('alamat', $user->address ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Metode Pembayaran</label>
                    <select name="metode_pembayaran" class="w-full rounded-xl border border-slate-200 px-4 py-3" required>
                        <option value="">Pilih Metode</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="cod">Bayar di Tempat (COD)</option>
                        <option value="ewallet">E-Wallet</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Metode Pengiriman</label>
                    <select name="metode_pengiriman" class="w-full rounded-xl border border-slate-200 px-4 py-3" required>
                        <option value="">Pilih Metode</option>
                        <option value="kurir">Kurir</option>
                        <option value="ambil">Ambil Sendiri</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Denda (Jika Telat/Hilang)</label>
                    <input type="text" name="denda" value="{{ 'Rp '.number_format($produk->denda ?? 0) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 bg-gray-100" readonly />
                </div>
                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-lg">Ajukan Sewa</button>
            </form>
        </div>
    </div>
</div>
@endsection
