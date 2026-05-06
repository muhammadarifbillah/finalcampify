@extends('layouts.app_pembeli')

@section('content')
<div class="py-12 max-w-4xl mx-auto px-4">

    <a href="{{ route('cart.index') }}" class="text-sm text-green-600 mb-4 inline-block">
        ← Kembali ke Keranjang
    </a>

    <h1 class="text-3xl font-bold mb-8">Checkout</h1>

    <form action="{{ route('checkout.process') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- LEFT: FORM -->
            <div class="space-y-6">

                {{-- ALAMAT PENGIRIMAN --}}
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-bold mb-4">Alamat Pengiriman</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ auth()->user()->name }}" 
                                   class="w-full border rounded px-3 py-2" required>
                        </div>


                        <div>
                            <label class="block text-sm font-medium mb-1">Alamat Lengkap</label>
                            <textarea name="alamat" rows="3" class="w-full border rounded px-3 py-2" placeholder="Masukkan alamat lengkap pengiriman" required>{{ old('alamat', auth()->user()->address) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                            <div>
                                <label class="block text-sm font-medium mb-1">Kota</label>
                                <select name="kota" id="city-checkout" class="w-full border rounded px-3 py-2" required>
                                    <option value="">Pilih Kota</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Kecamatan</label>
                                <select name="kecamatan" id="district-checkout" class="w-full border rounded px-3 py-2" required>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Kode Pos</label>
                                <input type="text" name="kode_pos" id="postal_code-checkout" class="w-full border rounded px-3 py-2 bg-gray-50" readonly required value="{{ old('kode_pos', auth()->user()->postal_code) }}">
                            </div>
                        </div>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                                                // Data lokal sama seperti profile
                                                const dataAlamat = [
                                                    {
                                                        kota: 'Jakarta',
                                                        kecamatan: [
                                                            { nama: 'Gambir', kode_pos: '10110' },
                                                            { nama: 'Menteng', kode_pos: '10310' }
                                                        ]
                                                    },
                                                    {
                                                        kota: 'Bandung',
                                                        kecamatan: [
                                                            { nama: 'Coblong', kode_pos: '40132' },
                                                            { nama: 'Lengkong', kode_pos: '40261' }
                                                        ]
                                                    }
                                                ];

                                                // Dropdown dinamis checkout
                                                const citySelect = document.getElementById('city-checkout');
                                                const districtSelect = document.getElementById('district-checkout');
                                                const postalInput = document.getElementById('postal_code-checkout');

                                                if(citySelect && districtSelect && postalInput) {
                                                    // Populate city
                                                    dataAlamat.forEach(item => {
                                                        const opt = document.createElement('option');
                                                        opt.value = item.kota;
                                                        opt.textContent = item.kota;
                                                        citySelect.appendChild(opt);
                                                    });

                                                    citySelect.addEventListener('change', function() {
                                                        districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
                                                        postalInput.value = '';
                                                        const kota = dataAlamat.find(k => k.kota === citySelect.value);
                                                        if (kota) {
                                                            kota.kecamatan.forEach(kec => {
                                                                const opt = document.createElement('option');
                                                                opt.value = kec.nama;
                                                                opt.textContent = kec.nama;
                                                                districtSelect.appendChild(opt);
                                                            });
                                                        }
                                                    });

                                                    districtSelect.addEventListener('change', function() {
                                                        const kota = dataAlamat.find(k => k.kota === citySelect.value);
                                                        if (kota) {
                                                            const kec = kota.kecamatan.find(d => d.nama === districtSelect.value);
                                                            postalInput.value = kec ? kec.kode_pos : '';
                                                        }
                                                    });

                                                    // Set default value if exist
                                                    const defaultCity = "{{ old('kota', auth()->user()->city) }}";
                                                    const defaultDistrict = "{{ old('kecamatan', auth()->user()->district ?? '') }}";
                                                    const defaultPostal = "{{ old('kode_pos', auth()->user()->postal_code) }}";
                                                    if(defaultCity) {
                                                        citySelect.value = defaultCity;
                                                        citySelect.dispatchEvent(new Event('change'));
                                                    }
                                                    if(defaultDistrict) {
                                                        districtSelect.value = defaultDistrict;
                                                        districtSelect.dispatchEvent(new Event('change'));
                                                    }
                                                    if(defaultPostal) {
                                                        postalInput.value = defaultPostal;
                                                    }
                                                }
                                                });
                        </script>

                        <div>
                            <label class="block text-sm font-medium mb-1">Nomor Telepon</label>
                            <input type="tel" name="telepon" value="{{ old('telepon', auth()->user()->phone) }}" class="w-full border rounded px-3 py-2" required>
                        </div>
                    </div>
                </div>

                {{-- METODE PEMBAYARAN --}}
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-bold mb-4">Metode Pembayaran</h2>

                    <div class="space-y-3">
                        <label class="flex items-center rounded-2xl border border-slate-200 px-4 py-3 cursor-pointer">
                            <input type="radio" name="metode_pembayaran" value="transfer" checked class="mr-3">
                            <span>Transfer Bank</span>
                        </label>
                        <label class="flex items-center rounded-2xl border border-slate-200 px-4 py-3 cursor-pointer">
                            <input type="radio" name="metode_pembayaran" value="cod" class="mr-3">
                            <span>Cash on Delivery</span>
                        </label>
                        <label class="flex items-center rounded-2xl border border-slate-200 px-4 py-3 cursor-pointer">
                            <input type="radio" name="metode_pembayaran" value="ewallet" class="mr-3">
                            <span>E-Wallet</span>
                        </label>
                    </div>

                    <div class="mt-6 p-4 bg-slate-50 rounded-xl border border-dashed border-slate-300 text-center">
                        <label class="block text-sm font-semibold mb-2 text-slate-600">Unggah Bukti Pembayaran</label>
                        <input type="file" name="bukti_pembayaran" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" required />
                        <p class="mt-2 text-[10px] text-slate-400 italic">Format: JPG, PNG, WEBP (Maks. 2MB)</p>
                    </div>
                </div>

                {{-- INFO REKENING DINAMIS CHECKOUT --}}
                <div id="checkout_payment_info" class="bg-white p-6 rounded-lg shadow mt-6 hidden">
                    <h2 class="text-xl font-bold mb-4">Informasi Pembayaran</h2>
                    <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100">
                        <div id="checkout_info_transfer" class="hidden space-y-4">
                            @php
                                $uniqueStores = $cart->pluck('product.store')->unique('id')->filter();
                            @endphp
                            
                            @forelse($uniqueStores as $store)
                                <div class="p-3 bg-white rounded-xl border border-emerald-100">
                                    <p class="text-[10px] font-black text-emerald-600 uppercase mb-2">TOKO: {{ $store->nama_toko }}</p>
                                    @if($store->bank_account_number)
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $store->bank_name ?? 'Transfer Bank' }}</p>
                                                <p class="font-black text-slate-800 text-lg">{{ $store->bank_account_number }}</p>
                                                <p class="text-xs text-slate-500">a/n {{ $store->bank_account_name }}</p>
                                            </div>
                                            <button type="button" onclick="copyToClipboard('{{ $store->bank_account_number }}')" class="text-emerald-600 font-bold text-xs hover:underline">Salin</button>
                                        </div>
                                    @else
                                        <p class="text-xs text-slate-500 italic">Seller belum mengatur info rekening.</p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-slate-500 italic">Tidak ada data toko.</p>
                            @endforelse
                        </div>
                        <div id="checkout_info_ewallet" class="hidden text-center space-y-3">
                            <p class="text-xs font-bold text-slate-500 uppercase">Gunakan QRIS Pembayaran Pusat</p>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=CAMPIFY_EWALLET" class="mx-auto border-2 border-white rounded-xl shadow-sm">
                            <p class="text-sm font-bold text-slate-700">Scan QRIS a/n Campify Indonesia</p>
                        </div>
                        <div id="checkout_info_cod" class="hidden">
                            <p class="text-sm text-slate-700">Bayar langsung ke kurir saat barang tiba.</p>
                        </div>
                    </div>
                </div>

                {{-- METODE PENGIRIMAN --}}
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-bold mb-4">Pilih Pengiriman</h2>

                    <div class="grid gap-3">
                        <label class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-4 cursor-pointer">
                            <div>
                                <p class="font-semibold">JNE Express</p>
                                <p class="text-sm text-slate-500">Estimasi 2-3 hari</p>
                            </div>
                            <div class="text-slate-700 font-semibold">Rp 15.000</div>
                            <input type="radio" name="shipping_method" value="jne" checked class="ml-4">
                        </label>
                        <label class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-4 cursor-pointer">
                            <div>
                                <p class="font-semibold">GoSend</p>
                                <p class="text-sm text-slate-500">Estimasi 1 hari</p>
                            </div>
                            <div class="text-slate-700 font-semibold">Rp 25.000</div>
                            <input type="radio" name="shipping_method" value="gosend" class="ml-4">
                        </label>
                    </div>
                </div>

            </div>

            <!-- RIGHT: RINGKASAN -->
            <div class="space-y-6">

                {{-- RINGKASAN PESANAN --}}
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-bold mb-4">Ringkasan Pesanan</h2>

                    <div class="space-y-3 mb-4">
                        @php $total = 0; @endphp
                        @foreach($cart as $item)
                            @php
                                $price = $item->type === 'buy' 
                                    ? ($item->product->buy_price ?? 0) 
                                    : ($item->product->rent_price ?? 0) * $item->duration;
                                $subtotal = $price * $item->qty;
                                $total += $subtotal;
                            @endphp

                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium">{{ $item->product->name ?? 'Produk Tidak Ditemukan' }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $item->type === 'buy' ? 'Beli' : 'Sewa' }} x{{ $item->qty }}
                                        @if($item->type === 'rent')
                                            ({{ $item->duration }} hari)
                                        @endif
                                    </p>
                                </div>
                                <span class="font-medium">Rp {{ number_format($subtotal) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <hr class="my-4">

                    <div class="flex justify-between text-sm text-slate-500 mb-2">
                        <span>Biaya Pengiriman</span>
                        <span id="shippingTotal">Rp 15.000</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total Pembayaran</span>
                        <span id="grandTotal">Rp {{ number_format($total) }}</span>
                    </div>
                </div>

                {{-- TOMBOL BAYAR --}}
                <button type="submit" class="w-full bg-green-600 text-white py-4 rounded-lg font-bold hover:bg-green-700">
                    Bayar Sekarang
                </button>

            </div>

        </div>

    </form>

</div>

<script>
    const shippingRadios = document.querySelectorAll('input[name="shipping_method"]');
    const shippingTotal = document.getElementById('shippingTotal');
    const grandTotal = document.getElementById('grandTotal');
    const orderSubtotal = {{ $total }};

    function updateTotal() {
        let shipping = 15000;
        const selected = document.querySelector('input[name="shipping_method"]:checked');
        if (selected) {
            shipping = selected.value === 'gosend' ? 25000 : 15000;
        }
        shippingTotal.textContent = 'Rp ' + shipping.toLocaleString('id-ID');
        grandTotal.textContent = 'Rp ' + (orderSubtotal + shipping).toLocaleString('id-ID');
    }

    shippingRadios.forEach(radio => radio.addEventListener('change', updateTotal));
    updateTotal();

    // Payment Info Toggle Checkout
    const payRadios = document.querySelectorAll('input[name="metode_pembayaran"]');
    const payInfoBox = document.getElementById('checkout_payment_info');
    const infoTransfer = document.getElementById('checkout_info_transfer');
    const infoEwallet = document.getElementById('checkout_info_ewallet');
    const infoCodCheckout = document.getElementById('checkout_info_cod');

    function updatePayInfo() {
        const selected = document.querySelector('input[name="metode_pembayaran"]:checked').value;
        payInfoBox.classList.remove('hidden');
        infoTransfer.classList.add('hidden');
        infoEwallet.classList.add('hidden');
        infoCodCheckout.classList.add('hidden');

        if(selected === 'transfer') infoTransfer.classList.remove('hidden');
        if(selected === 'ewallet') infoEwallet.classList.remove('hidden');
        if(selected === 'cod') infoCodCheckout.classList.remove('hidden');
    }

    payRadios.forEach(r => r.addEventListener('change', updatePayInfo));
    updatePayInfo();
</script>
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Nomor rekening berhasil disalin!');
    });
}
</script>
@endsection