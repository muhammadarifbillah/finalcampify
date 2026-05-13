@extends('layouts.app_pembeli')

@section('content')
<div class="py-12 max-w-4xl mx-auto px-4">

    <a href="{{ route('cart.index') }}" class="text-sm text-green-600 mb-4 inline-block">
        ← Kembali ke Keranjang
    </a>

    <h1 class="text-3xl font-bold mb-8">Checkout</h1>

    <form action="{{ route('checkout.process') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Hidden inputs for selected items --}}
        @foreach($cart as $item)
            <input type="hidden" name="selected_items[]" value="{{ $item->id }}">
        @endforeach

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- LEFT: FORM -->
            <div class="space-y-6">

                {{-- ALAMAT PENGIRIMAN --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <h2 class="text-lg font-bold mb-4">Alamat Pengiriman</h2>
                    
                    <div class="grid gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input type="text" name="nama" value="{{ auth()->user()->name }}" class="w-full rounded-lg border-gray-300" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                            <textarea name="alamat" rows="3" class="w-full rounded-lg border-gray-300" required>{{ old('alamat', auth()->user()->address) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                                <select name="kota" id="city-checkout" class="w-full rounded-lg border-gray-300" required>
                                    <option value="">Pilih Kota</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                                <select name="kecamatan" id="district-checkout" class="w-full rounded-lg border-gray-300" required>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                                <input type="text" name="kode_pos" id="postal_code-checkout" class="w-full rounded-lg border-gray-300 bg-gray-50" readonly required value="{{ old('kode_pos', auth()->user()->postal_code) }}">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                            <input type="tel" name="telepon" value="{{ old('telepon', auth()->user()->phone) }}" class="w-full rounded-lg border-gray-300" required>
                        </div>
                    </div>
                </div>

                {{-- METODE PEMBAYARAN --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <h2 class="text-lg font-bold mb-4">Metode Pembayaran</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="metode_pembayaran" value="transfer" checked class="mr-2 text-green-600">
                            <span class="font-medium">Transfer Bank</span>
                        </label>
                        <label class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="metode_pembayaran" value="cod" class="mr-2 text-green-600">
                            <span class="font-medium">COD</span>
                        </label>
                    </div>

                    {{-- INFO REKENING --}}
                    <div id="checkout_payment_info" class="mt-4 p-4 bg-gray-50 rounded-lg hidden">
                        <div id="checkout_info_transfer" class="hidden">
                            <p class="text-sm font-bold mb-2">Transfer ke Rekening Toko:</p>
                            @php $uniqueStores = $cart->pluck('product.store')->unique('id')->filter(); @endphp
                            <div class="space-y-3">
                                @foreach($uniqueStores as $store)
                                    <div class="p-3 bg-white border rounded">
                                        <p class="text-xs font-bold text-green-600 uppercase">{{ $store->nama_toko }}</p>
                                        <p class="text-sm font-bold">{{ $store->bank_name }}: {{ $store->bank_account_number }}</p>
                                        <p class="text-xs text-gray-500">A/N: {{ $store->bank_account_name }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div id="checkout_info_cod" class="hidden text-sm">
                            <p>Bayar tunai saat barang sampai di alamat Anda.</p>
                        </div>
                    </div>

                    {{-- UPLOAD BUKTI --}}
                    <div id="proof_upload_container" class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Bukti Pembayaran</label>
                        <input type="file" name="bukti_pembayaran" id="bukti_pembayaran_input" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:bg-green-50 file:text-green-700 hover:file:bg-green-100" required>
                    </div>
                </div>

                {{-- METODE PENGIRIMAN --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <h2 class="text-lg font-bold mb-4">Opsi Pengiriman</h2>
                    <div class="space-y-3">
                        @foreach($couriers as $index => $courier)
                        <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <div class="flex items-center">
                                <input type="radio" name="shipping_method" value="{{ $courier->service }}" data-price="{{ $courier->price }}" {{ $index === 0 ? 'checked' : '' }} class="mr-3 text-green-600">
                                <div>
                                    <p class="font-semibold text-base">{{ $courier->service }}</p>
                                    <p class="text-xs text-gray-500">Estimasi {{ $courier->estimate ?? '-' }} &bull; Kurir: {{ $courier->name }}</p>
                                </div>
                            </div>
                            <span class="font-bold text-green-700">Rp {{ number_format($courier->price, 0, ',', '.') }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- RIGHT: SUMMARY -->
            <div class="space-y-6">
                <div class="bg-gray-900 text-white p-6 rounded-xl shadow-lg sticky top-24">
                    <h2 class="text-xl font-bold mb-6">Ringkasan Pesanan</h2>

                    <div class="space-y-4 mb-6 max-h-60 overflow-y-auto pr-2">
                        @php $total = 0; @endphp
                        @foreach($cart as $item)
                            @php
                                $price = $item->type === 'buy' ? $item->product->buy_price : $item->product->rent_price * $item->duration;
                                $subtotal = $price * $item->qty;
                                $total += $subtotal;
                            @endphp
                            <div class="flex gap-3">
                                @php
                                    $produk = $item->product;
                                    $imgPath = $produk->image ?? $produk->gambar;
                                    if ($imgPath && !str_starts_with($imgPath, 'assets/images/') && !str_starts_with($imgPath, 'storage/') && !str_starts_with($imgPath, 'http')) {
                                        if (file_exists(public_path('assets/images/' . $imgPath))) {
                                            $imgPath = 'assets/images/' . $imgPath;
                                        } else {
                                            $imgPath = 'storage/' . $imgPath;
                                        }
                                    }
                                @endphp

                                @if($imgPath && (file_exists(public_path($imgPath)) || str_contains($imgPath, 'http')))
                                    <img src="{{ asset($imgPath) }}" class="w-12 h-12 object-cover rounded shadow-sm">
                                @else
                                    <div class="w-12 h-12 bg-gray-700 rounded flex items-center justify-center text-xl">📦</div>
                                @endif
                                
                                <div class="flex-1 min-w-0 text-sm">
                                    <p class="font-bold truncate">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->qty }}x | {{ $item->type === 'buy' ? 'Beli' : 'Sewa' }}</p>
                                </div>
                                <p class="text-sm font-bold">Rp {{ number_format($subtotal) }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-800 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-400">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($total) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-400">
                            <span>Ongkir</span>
                            <span id="shippingTotal">Rp 15,000</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold pt-4">
                            <span>Total</span>
                            <span id="grandTotal">Rp {{ number_format($total + 15000) }}</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-8 bg-green-500 hover:bg-green-600 text-white py-3 rounded-lg font-bold transition">
                        Bayar Sekarang
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataAlamat = [
            {
                kota: 'Kota Bandung',
                kecamatan: [
                    { nama: 'Andir', kode_pos: '40184' }, { nama: 'Antapani', kode_pos: '40291' }, { nama: 'Arcamanik', kode_pos: '40293' }, { nama: 'Astanaanyar', kode_pos: '40242' },
                    { nama: 'Babakan Ciparay', kode_pos: '40223' }, { nama: 'Bandung Kidul', kode_pos: '40266' }, { nama: 'Bandung Kulon', kode_pos: '40214' }, { nama: 'Bandung Wetan', kode_pos: '40116' },
                    { nama: 'Batununggal', kode_pos: '40275' }, { nama: 'Bojongloa Kaler', kode_pos: '40232' }, { nama: 'Bojongloa Kidul', kode_pos: '40233' }, { nama: 'Buahbatu', kode_pos: '40286' },
                    { nama: 'Cibeunying Kaler', kode_pos: '40191' }, { nama: 'Cibeunying Kidul', kode_pos: '40125' }, { nama: 'Cibiru', kode_pos: '40614' }, { nama: 'Cicendo', kode_pos: '40171' },
                    { nama: 'Cidadap', kode_pos: '40142' }, { nama: 'Cinambo', kode_pos: '40294' }, { nama: 'Coblong', kode_pos: '40132' }, { nama: 'Gedebage', kode_pos: '40294' },
                    { nama: 'Kiaracondong', kode_pos: '40284' }, { nama: 'Lengkong', kode_pos: '40261' }, { nama: 'Mandalajati', kode_pos: '40194' }, { nama: 'Panyileukan', kode_pos: '40614' },
                    { nama: 'Rancasari', kode_pos: '40286' }, { nama: 'Regol', kode_pos: '40252' }, { nama: 'Sukajadi', kode_pos: '40161' }, { nama: 'Sukasari', kode_pos: '40151' },
                    { nama: 'Sumur Bandung', kode_pos: '40111' }, { nama: 'Ujungberung', kode_pos: '40611' }
                ]
            },
            {
                kota: 'Kabupaten Bandung',
                kecamatan: [
                    { nama: 'Arjasari', kode_pos: '40379' }, { nama: 'Baleendah', kode_pos: '40375' }, { nama: 'Banjaran', kode_pos: '40377' }, { nama: 'Bojongsoang', kode_pos: '40288' },
                    { nama: 'Cangkuang', kode_pos: '40238' }, { nama: 'Cicalengka', kode_pos: '40395' }, { nama: 'Cikancung', kode_pos: '40396' }, { nama: 'Cilengkrang', kode_pos: '40615' },
                    { nama: 'Cileunyi', kode_pos: '40622' }, { nama: 'Cimaung', kode_pos: '40374' }, { nama: 'Cimenyan', kode_pos: '40197' }, { nama: 'Ciparay', kode_pos: '40381' },
                    { nama: 'Ciwidey', kode_pos: '40973' }, { nama: 'Dayeuhkolot', kode_pos: '40258' }, { nama: 'Katapang', kode_pos: '40921' }, { nama: 'Majalaya', kode_pos: '40382' },
                    { nama: 'Margaasih', kode_pos: '40215' }, { nama: 'Margahayu', kode_pos: '40226' }, { nama: 'Pangalengan', kode_pos: '40378' }, { nama: 'Paseh', kode_pos: '40383' },
                    { nama: 'Rancaekek', kode_pos: '40394' }, { nama: 'Soreang', kode_pos: '40911' }
                ]
            },
            {
                kota: 'Kabupaten Bandung Barat',
                kecamatan: [
                    { nama: 'Batujajar', kode_pos: '40561' }, { nama: 'Cihampelas', kode_pos: '40562' }, { nama: 'Cikalongwetan', kode_pos: '40556' }, { nama: 'Cililin', kode_pos: '40562' },
                    { nama: 'Cipatat', kode_pos: '40554' }, { nama: 'Cipeundeuy', kode_pos: '40558' }, { nama: 'Cipongkor', kode_pos: '40564' }, { nama: 'Cisarua', kode_pos: '40551' },
                    { nama: 'Gununghalu', kode_pos: '40565' }, { nama: 'Lembang', kode_pos: '40391' }, { nama: 'Ngamprah', kode_pos: '40552' }, { nama: 'Padalarang', kode_pos: '40553' },
                    { nama: 'Parongpong', kode_pos: '40559' }, { nama: 'Sindangkerta', kode_pos: '40563' }
                ]
            },
            {
                kota: 'Kota Cimahi',
                kecamatan: [
                    { nama: 'Cimahi Selatan', kode_pos: '40531' }, { nama: 'Cimahi Tengah', kode_pos: '40521' }, { nama: 'Cimahi Utara', kode_pos: '40511' }
                ]
            },
            {
                kota: 'Kota Depok',
                kecamatan: [
                    { nama: 'Beji', kode_pos: '16421' }, { nama: 'Bojongsari', kode_pos: '16516' }, { nama: 'Cilodong', kode_pos: '16413' }, { nama: 'Cimanggis', kode_pos: '16451' },
                    { nama: 'Cinere', kode_pos: '16514' }, { nama: 'Cipayung', kode_pos: '16437' }, { nama: 'Limo', kode_pos: '16515' }, { nama: 'Pancoran Mas', kode_pos: '16431' },
                    { nama: 'Sawangan', kode_pos: '16511' }, { nama: 'Sukmajaya', kode_pos: '16411' }, { nama: 'Tapos', kode_pos: '16457' }
                ]
            },
            {
                kota: 'Kota Bogor',
                kecamatan: [
                    { nama: 'Bogor Barat', kode_pos: '16111' }, { nama: 'Bogor Selatan', kode_pos: '16131' }, { nama: 'Bogor Tengah', kode_pos: '16121' }, { nama: 'Bogor Timur', kode_pos: '16141' },
                    { nama: 'Bogor Utara', kode_pos: '16151' }, { nama: 'Tanah Sareal', kode_pos: '16161' }
                ]
            },
            {
                kota: 'Majalengka',
                kecamatan: [
                    { nama: 'Kadipaten', kode_pos: '45452' }, { nama: 'Jatiwangi', kode_pos: '45454' }, { nama: 'Majalengka', kode_pos: '45411' }, { nama: 'Bantarujeg', kode_pos: '45464' }
                ]
            }
        ];

        const citySelect = document.getElementById('city-checkout');
        const districtSelect = document.getElementById('district-checkout');
        const postalInput = document.getElementById('postal_code-checkout');

        if(citySelect && districtSelect && postalInput) {
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

            const defaultCity = "{{ old('kota', auth()->user()->city) }}";
            const defaultDistrict = "{{ old('kecamatan', auth()->user()->district ?? '') }}";
            if(defaultCity) {
                citySelect.value = defaultCity;
                citySelect.dispatchEvent(new Event('change'));
            }
            if(defaultDistrict) {
                setTimeout(() => {
                    districtSelect.value = defaultDistrict;
                    districtSelect.dispatchEvent(new Event('change'));
                }, 100);
            }
        }

        const shippingRadios = document.querySelectorAll('input[name="shipping_method"]');
        const shippingTotal = document.getElementById('shippingTotal');
        const grandTotal = document.getElementById('grandTotal');
        const orderSubtotal = {{ $total }};

        function updateTotals() {
            let shipping = 15000;
            const selected = document.querySelector('input[name="shipping_method"]:checked');
            if (selected) {
                shipping = selected.value === 'gosend' ? 25000 : 15000;
            }
            shippingTotal.textContent = 'Rp ' + shipping.toLocaleString('id-ID');
            grandTotal.textContent = 'Rp ' + (orderSubtotal + shipping).toLocaleString('id-ID');
        }

        shippingRadios.forEach(radio => radio.addEventListener('change', updateTotals));

        const payRadios = document.querySelectorAll('input[name="metode_pembayaran"]');
        const payInfoBox = document.getElementById('checkout_payment_info');
        const infoTransfer = document.getElementById('checkout_info_transfer');
        const infoCod = document.getElementById('checkout_info_cod');
        const proofContainer = document.getElementById('proof_upload_container');
        const proofInput = document.getElementById('bukti_pembayaran_input');

        function updatePayInfo() {
            const selected = document.querySelector('input[name="metode_pembayaran"]:checked').value;
            payInfoBox.classList.remove('hidden');
            infoTransfer.classList.add('hidden');
            infoCod.classList.add('hidden');

            if(selected === 'transfer') {
                infoTransfer.classList.remove('hidden');
                proofContainer.classList.remove('hidden');
                proofInput.required = true;
            } else {
                infoCod.classList.remove('hidden');
                proofContainer.classList.add('hidden');
                proofInput.required = false;
            }
        }

        payRadios.forEach(r => r.addEventListener('change', updatePayInfo));
        updatePayInfo();
    });
</script>
@endsection
