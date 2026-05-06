@extends('layouts.app_pembeli')

@section('content')
<div class="pt-28 pb-20 bg-slate-50">
    <div class="max-w-2xl mx-auto px-4">
        <div class="mb-8">
            <a href="{{ route('produk.detail.rent', $produk->id) }}" class="text-sm text-green-600 hover:text-green-800 font-semibold">← Kembali ke Detail Produk</a>
        </div>
        <div class="bg-white rounded-3xl shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6">Formulir Penyewaan</h2>
            <form action="{{ route('sewa.process') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
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

                {{-- INFO REKENING DINAMIS --}}
                <div id="payment_info_box" class="hidden pt-4">
                    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-5">
                        <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest mb-3">Tujuan Pembayaran</p>
                        
                        <div id="info_qris" class="hidden space-y-3 text-center">
                            <div class="bg-white p-4 rounded-xl inline-block border-2 border-emerald-200">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=CAMPIFY_PAYMENT" alt="QRIS" class="mx-auto">
                            </div>
                            <p class="text-sm font-bold text-slate-700">Scan QRIS a/n Campify Indonesia</p>
                        </div>

                        <div id="info_va" class="hidden space-y-2">
                            @if($produk->store && $produk->store->bank_account_number)
                            <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-emerald-100">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $produk->store->bank_name ?? 'Transfer Bank' }}</p>
                                    <p class="font-black text-slate-800 text-lg">{{ $produk->store->bank_account_number }}</p>
                                    <p class="text-xs text-slate-500">a/n {{ $produk->store->bank_account_name }}</p>
                                </div>
                                <button type="button" onclick="copyToClipboard('{{ $produk->store->bank_account_number }}')" class="text-emerald-600 font-bold text-xs hover:underline">Salin</button>
                            </div>
                            @else
                            <p class="text-sm text-slate-500 italic">Seller belum mengatur informasi rekening.</p>
                            @endif
                        </div>

                        <div id="info_cod" class="hidden">
                            <p class="text-sm text-slate-700 font-medium">Pembayaran dilakukan secara tunai kepada kurir saat barang sampai di lokasi Anda.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <h3 class="font-bold text-lg mb-3 uppercase tracking-wider text-slate-800">Bukti Pembayaran</h3>
                    <div class="p-4 bg-slate-50 rounded-xl border border-dashed border-slate-300 text-center">
                        <label class="block text-sm font-semibold mb-2 text-slate-600">Unggah Bukti Transfer / QRIS</label>
                        <input type="file" name="bukti_pembayaran" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" required />
                        <p class="mt-2 text-[10px] text-slate-400 italic">Format: JPG, PNG, WEBP (Maks. 2MB)</p>
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

    // Payment Info Toggle
    const paymentRadios = document.querySelectorAll('input[name="metode_pembayaran"]');
    const infoBox = document.getElementById('payment_info_box');
    const infoQris = document.getElementById('info_qris');
    const infoVa = document.getElementById('info_va');
    const infoCod = document.getElementById('info_cod');

    function updatePaymentInfo() {
        const selected = document.querySelector('input[name="metode_pembayaran"]:checked').value;
        infoBox.classList.remove('hidden');
        infoQris.classList.add('hidden');
        infoVa.classList.add('hidden');
        infoCod.classList.add('hidden');

        if(selected === 'qris') infoQris.classList.remove('hidden');
        if(selected === 'va') infoVa.classList.remove('hidden');
        if(selected === 'cod') infoCod.classList.remove('hidden');
    }

    paymentRadios.forEach(radio => radio.addEventListener('change', updatePaymentInfo));
    updatePaymentInfo(); // Initial call
});

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Nomor rekening berhasil disalin!');
    });
}
</script>

@endsection
