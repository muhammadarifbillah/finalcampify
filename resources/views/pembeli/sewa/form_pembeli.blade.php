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
                    <input type="text" name="denda_info" value="Denda ditentukan oleh toko saat pengembalian" class="w-full rounded-xl border border-slate-200 px-4 py-3 bg-gray-100 text-gray-600 font-medium" readonly />
                    <p class="text-xs text-gray-500 mt-1">*Besaran denda akan disesuaikan dengan kondisi barang atau lamanya keterlambatan.</p>
                </div>
                <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-lg transition-colors">Ajukan Sewa</button>
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
