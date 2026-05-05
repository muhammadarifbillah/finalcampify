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
                    <input type="date" name="start_date" id="start_date" min="{{ date('Y-m-d') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-green-500" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Tanggal Pengembalian</label>
                    <input type="date" name="end_date" id="end_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-green-500" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Durasi Sewa (hari)</label>
                    <input type="number" name="duration" id="duration" min="1" value="1" class="w-full rounded-xl border border-slate-200 px-4 py-3 bg-gray-50 focus:ring-2 focus:ring-green-500" required readonly />
                    <p class="text-xs text-gray-500 mt-1">*Durasi dihitung otomatis berdasarkan tanggal penyewaan & pengembalian.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Alamat Pengiriman</label>
                    @php
                        $user = auth()->user();
                        $alamatDefault = $user->address;
                        if($user->district) $alamatDefault .= ', Kec. ' . $user->district;
                        if($user->city) $alamatDefault .= ', ' . $user->city;
                        if($user->postal_code) $alamatDefault .= ' ' . $user->postal_code;
                    @endphp
                    <textarea name="alamat" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-green-500" rows="3" required>{{ old('alamat', $alamatDefault) }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <h3 class="font-bold text-lg mb-3 uppercase tracking-wider text-slate-800">Metode Pengiriman</h3>
                    <div class="space-y-3">
                        <label class="flex items-center justify-between rounded-xl border border-slate-200 p-4 cursor-pointer hover:border-green-500 transition-colors">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="metode_pengiriman" value="kurir" checked class="w-4 h-4 text-green-600 focus:ring-green-500">
                                <span class="font-medium">Diantar kurir</span>
                            </div>
                        </label>
                        <label class="flex items-center justify-between rounded-xl border border-slate-200 p-4 cursor-pointer hover:border-green-500 transition-colors">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="metode_pengiriman" value="standar" class="w-4 h-4 text-green-600 focus:ring-green-500">
                                <span class="font-medium">Diambil</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-4">
                    <h3 class="font-bold text-lg mb-3 uppercase tracking-wider text-slate-800">Metode Pembayaran</h3>
                    <div class="space-y-3">
                        <label class="flex items-center rounded-xl border border-slate-200 p-4 cursor-pointer hover:border-green-500 transition-colors">
                            <input type="radio" name="metode_pembayaran" value="qris" checked class="w-4 h-4 text-green-600 focus:ring-green-500 mr-3">
                            <span class="font-medium">QRIS / E-Wallet</span>
                        </label>
                        <label class="flex items-center rounded-xl border border-slate-200 p-4 cursor-pointer hover:border-green-500 transition-colors">
                            <input type="radio" name="metode_pembayaran" value="va" class="w-4 h-4 text-green-600 focus:ring-green-500 mr-3">
                            <span class="font-medium">Virtual Account</span>
                        </label>
                        <label class="flex items-center rounded-xl border border-slate-200 p-4 cursor-pointer hover:border-green-500 transition-colors">
                            <input type="radio" name="metode_pembayaran" value="cod" class="w-4 h-4 text-green-600 focus:ring-green-500 mr-3">
                            <span class="font-medium">Cash on Delivery</span>
                        </label>
                    </div>
                </div>
                <div class="pt-2">
                    <label class="block text-sm font-bold mb-1 text-slate-800">Catatan Denda</label>
                    <input type="text" name="denda_info" value="Denda ditentukan oleh toko saat pengembalian" class="w-full rounded-xl border border-slate-200 px-4 py-3 bg-red-50 text-red-600 font-medium" readonly />
                    <p class="text-xs text-red-500 mt-1">*Besaran denda akan disesuaikan dengan kondisi barang atau lamanya keterlambatan.</p>
                </div>

                <div class="pt-6 border-t border-slate-200 mt-6">
                    <div class="flex justify-between items-center mb-6 bg-green-50 p-4 rounded-xl border border-green-100">
                        <span class="text-slate-600 font-bold uppercase tracking-wider text-sm">Subtotal</span>
                        <span class="text-2xl font-black text-green-700" id="subtotalDisplay">Rp {{ number_format($produk->rent_price ?? 0) }}</span>
                    </div>
                    <button type="submit" class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-lg transition-all shadow-lg shadow-green-200">
                        Ajukan Sewa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const durationInput = document.getElementById('duration');

    function calculateDuration() {
        if(startDateInput.value && endDateInput.value) {
            const start = new Date(startDateInput.value);
            const end = new Date(endDateInput.value);
            
            // Atur waktu ke midnight agar perhitungan selisih hari akurat
            start.setHours(0,0,0,0);
            end.setHours(0,0,0,0);
            
            const diffTime = end - start;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if(diffDays > 0) {
                durationInput.value = diffDays;
            } else {
                durationInput.value = 1;
            }
            
            // Hitung dan update subtotal
            const rentPrice = {{ $produk->rent_price ?? 0 }};
            const total = rentPrice * durationInput.value;
            document.getElementById('subtotalDisplay').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }
    }

    startDateInput.addEventListener('change', function() {
        if(startDateInput.value) {
            let start = new Date(startDateInput.value);
            start.setDate(start.getDate() + 1); // minimal pengembalian adalah H+1
            let minEndDate = start.toISOString().split('T')[0];
            endDateInput.min = minEndDate;
            
            if(endDateInput.value && endDateInput.value <= startDateInput.value) {
                endDateInput.value = minEndDate;
            }
        }
        calculateDuration();
    });
    
    endDateInput.addEventListener('change', calculateDuration);
});
</script>

@endsection
