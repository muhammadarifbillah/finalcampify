@extends('layouts.app_pembeli')

@section('content')
<div class="pt-28 pb-20 bg-slate-50">
    <div class="max-w-2xl mx-auto px-4">
        <div class="mb-8">
            <a href="{{ route('produk.detail.rent', $produk->id) }}" class="text-sm text-green-600 hover:text-green-800 font-semibold">← Kembali ke Detail Produk</a>
        </div>
        <div class="bg-white rounded-3xl shadow-lg p-8">
            <div class="flex items-center gap-4 mb-6 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <img src="{{ asset('storage/' . $produk->image) }}" class="w-16 h-16 object-cover rounded-xl shadow-sm">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">{{ $produk->name }}</h2>
                    <p class="text-xs text-slate-500">Harga: Rp {{ number_format($produk->rent_price) }}/hari</p>
                </div>
            </div>
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
                    <label class="block text-sm font-semibold mb-1">Alamat Lengkap</label>
                    <textarea name="alamat" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-green-500" rows="3" placeholder="Nama Jalan, No. Rumah, RT/RW" required>{{ old('alamat', auth()->user()->address) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Kota</label>
                        <select name="kota" id="city-sewa" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-green-500" required>
                            <option value="">Pilih Kota</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Kecamatan</label>
                        <select name="kecamatan" id="district-sewa" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-green-500" required>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Kode Pos</label>
                        <input type="text" name="kode_pos" id="postal_code-sewa" class="w-full rounded-xl border border-slate-200 px-4 py-3 bg-gray-50 focus:ring-2 focus:ring-green-500" readonly required value="{{ old('kode_pos', auth()->user()->postal_code) }}">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Nomor Telepon</label>
                    <input type="tel" name="telepon" value="{{ old('telepon', auth()->user()->phone) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-green-500" required>
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
                            <input type="radio" name="metode_pembayaran" value="transfer" checked class="w-4 h-4 text-green-600 focus:ring-green-500 mr-3">
                            <span class="font-medium">Transfer Bank</span>
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
                        


                        <div id="info_transfer" class="hidden space-y-2">
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

                <div class="p-6 bg-amber-50 border-2 border-amber-200 rounded-3xl space-y-4">
                    <div class="flex gap-4 items-start">
                        <div class="shrink-0 w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                            <i data-lucide="shield-check" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-black text-amber-900 uppercase tracking-widest">Verifikasi Identitas (Wajib)</h4>
                            <p class="text-[11px] text-amber-800 leading-relaxed">
                                @if(auth()->user()->ktp_verified_at)
                                    Identitas Anda telah <strong>Terverifikasi</strong>. Anda dapat melanjutkan penyewaan dengan aman.
                                @elseif(auth()->user()->ktp_image)
                                    KTP Anda sudah diunggah dan sedang dalam proses verifikasi oleh Admin.
                                @else
                                    Sesuai kebijakan keamanan Campify, Anda <strong>wajib mengunggah foto KTP</strong> untuk dapat menyewa alat camp.
                                @endif
                            </p>
                        </div>
                    </div>

                    @if(!auth()->user()->ktp_image)
                        <div class="bg-white p-4 rounded-2xl border border-amber-200">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Unggah Foto KTP Anda</label>
                            <div class="relative group">
                                <input type="file" name="ktp_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 cursor-pointer" required />
                            </div>
                            <p class="mt-2 text-[9px] text-slate-400 italic">*Pastikan foto KTP terlihat jelas dan terbaca.</p>
                        </div>
                    @endif
                </div>

                <div class="pt-6 border-t border-slate-200 mt-6 space-y-4">
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 font-medium">Biaya Sewa (<span id="durationLabel">1</span> Hari)</span>
                            <span class="font-bold text-slate-800" id="rentalFeeDisplay">Rp {{ number_format($produk->rent_price) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex flex-col">
                                <span class="text-slate-500 font-medium">Dana Jaminan (Keamanan 25%)</span>
                                <span class="text-[9px] text-slate-400 leading-tight">*Dihitung dari 25% harga barang (Rp {{ number_format($produk->buy_price) }})</span>
                            </div>
                            @php $deposit = $produk->buy_price * 0.25; @endphp
                            <span class="font-bold text-blue-600">Rp {{ number_format($deposit) }}</span>
                        </div>
                        <div class="pt-3 border-t border-dashed border-slate-200 flex justify-between items-center">
                            <span class="text-slate-800 font-black uppercase tracking-wider text-sm">Total Dibayar</span>
                            <span class="text-2xl font-black text-green-700" id="totalPriceDisplay">Rp {{ number_format($produk->rent_price + $deposit) }}</span>
                        </div>
                        <p class="text-[10px] text-slate-500 text-center italic pt-2">Dana jaminan akan dikembalikan utuh jika barang kembali tanpa kerusakan.</p>
                    </div>

                    <button type="submit" class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-lg transition-all shadow-lg shadow-green-200">
                        Ajukan Sewa & Bayar
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
            const buyPrice = {{ $produk->buy_price ?? 0 }};
            const deposit = buyPrice * 0.25;
            const rentalFee = rentPrice * durationInput.value;
            const total = rentalFee + deposit;

            document.getElementById('durationLabel').innerText = durationInput.value;
            document.getElementById('rentalFeeDisplay').innerText = 'Rp ' + rentalFee.toLocaleString('id-ID');
            document.getElementById('totalPriceDisplay').innerText = 'Rp ' + total.toLocaleString('id-ID');
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
    const infoTransfer = document.getElementById('info_transfer');
    const infoCod = document.getElementById('info_cod');

    function updatePaymentInfo() {
        const selected = document.querySelector('input[name="metode_pembayaran"]:checked').value;
        infoBox.classList.remove('hidden');
        infoTransfer.classList.add('hidden');
        infoCod.classList.add('hidden');

        if(selected === 'transfer') infoTransfer.classList.remove('hidden');
        if(selected === 'cod') infoCod.classList.remove('hidden');
    }

    // Data Alamat Dinamis (Sync with Profile)
    const dataAlamat = [
        {
            kota: 'Kota Bandung',
            kecamatan: [
                { nama: 'Andir', kode_pos: '40184' },
                { nama: 'Antapani', kode_pos: '40291' },
                { nama: 'Arcamanik', kode_pos: '40293' },
                { nama: 'Astanaanyar', kode_pos: '40242' },
                { nama: 'Babakan Ciparay', kode_pos: '40223' },
                { nama: 'Bandung Kidul', kode_pos: '40266' },
                { nama: 'Bandung Kulon', kode_pos: '40214' },
                { nama: 'Bandung Wetan', kode_pos: '40116' },
                { nama: 'Batununggal', kode_pos: '40275' },
                { nama: 'Bojongloa Kaler', kode_pos: '40232' },
                { nama: 'Bojongloa Kidul', kode_pos: '40233' },
                { nama: 'Buahbatu', kode_pos: '40286' },
                { nama: 'Cibeunying Kaler', kode_pos: '40191' },
                { nama: 'Cibeunying Kidul', kode_pos: '40125' },
                { nama: 'Cibiru', kode_pos: '40614' },
                { nama: 'Cicendo', kode_pos: '40171' },
                { nama: 'Cidadap', kode_pos: '40142' },
                { nama: 'Cinambo', kode_pos: '40294' },
                { nama: 'Coblong', kode_pos: '40132' },
                { nama: 'Gedebage', kode_pos: '40294' },
                { nama: 'Kiaracondong', kode_pos: '40284' },
                { nama: 'Lengkong', kode_pos: '40261' },
                { nama: 'Mandalajati', kode_pos: '40194' },
                { nama: 'Panyileukan', kode_pos: '40614' },
                { nama: 'Rancasari', kode_pos: '40286' },
                { nama: 'Regol', kode_pos: '40252' },
                { nama: 'Sukajadi', kode_pos: '40161' },
                { nama: 'Sukasari', kode_pos: '40151' },
                { nama: 'Sumur Bandung', kode_pos: '40111' },
                { nama: 'Ujungberung', kode_pos: '40611' }
            ]
        },
        {
            kota: 'Kabupaten Bandung',
            kecamatan: [
                { nama: 'Arjasari', kode_pos: '40379' },
                { nama: 'Baleendah', kode_pos: '40375' },
                { nama: 'Banjaran', kode_pos: '40377' },
                { nama: 'Bojongsoang', kode_pos: '40288' },
                { nama: 'Cangkuang', kode_pos: '40238' },
                { nama: 'Cicalengka', kode_pos: '40395' },
                { nama: 'Cikancung', kode_pos: '40396' },
                { nama: 'Cilengkrang', kode_pos: '40615' },
                { nama: 'Cileunyi', kode_pos: '40622' },
                { nama: 'Cimaung', kode_pos: '40374' },
                { nama: 'Cimenyan', kode_pos: '40197' },
                { nama: 'Ciparay', kode_pos: '40381' },
                { nama: 'Ciwidey', kode_pos: '40973' },
                { nama: 'Dayeuhkolot', kode_pos: '40258' },
                { nama: 'Katapang', kode_pos: '40921' },
                { nama: 'Majalaya', kode_pos: '40382' },
                { nama: 'Margaasih', kode_pos: '40215' },
                { nama: 'Margahayu', kode_pos: '40226' },
                { nama: 'Pangalengan', kode_pos: '40378' },
                { nama: 'Paseh', kode_pos: '40383' },
                { nama: 'Rancaekek', kode_pos: '40394' },
                { nama: 'Soreang', kode_pos: '40911' }
            ]
        },
        {
            kota: 'Kabupaten Bandung Barat',
            kecamatan: [
                { nama: 'Batujajar', kode_pos: '40561' },
                { nama: 'Cihampelas', kode_pos: '40562' },
                { nama: 'Cikalongwetan', kode_pos: '40556' },
                { nama: 'Cililin', kode_pos: '40562' },
                { nama: 'Cipatat', kode_pos: '40554' },
                { nama: 'Cipeundeuy', kode_pos: '40558' },
                { nama: 'Cipongkor', kode_pos: '40564' },
                { nama: 'Cisarua', kode_pos: '40551' },
                { nama: 'Gununghalu', kode_pos: '40565' },
                { nama: 'Lembang', kode_pos: '40391' },
                { nama: 'Ngamprah', kode_pos: '40552' },
                { nama: 'Padalarang', kode_pos: '40553' },
                { nama: 'Parongpong', kode_pos: '40559' },
                { nama: 'Sindangkerta', kode_pos: '40563' }
            ]
        },
        {
            kota: 'Kota Cimahi',
            kecamatan: [
                { nama: 'Cimahi Selatan', kode_pos: '40531' },
                { nama: 'Cimahi Tengah', kode_pos: '40521' },
                { nama: 'Cimahi Utara', kode_pos: '40511' }
            ]
        },
        {
            kota: 'Kota Depok',
            kecamatan: [
                { nama: 'Beji', kode_pos: '16421' },
                { nama: 'Bojongsari', kode_pos: '16516' },
                { nama: 'Cilodong', kode_pos: '16413' },
                { nama: 'Cimanggis', kode_pos: '16451' },
                { nama: 'Cinere', kode_pos: '16514' },
                { nama: 'Cipayung', kode_pos: '16437' },
                { nama: 'Limo', kode_pos: '16515' },
                { nama: 'Pancoran Mas', kode_pos: '16431' },
                { nama: 'Sawangan', kode_pos: '16511' },
                { nama: 'Sukmajaya', kode_pos: '16411' },
                { nama: 'Tapos', kode_pos: '16457' }
            ]
        },
        {
            kota: 'Kota Bogor',
            kecamatan: [
                { nama: 'Bogor Barat', kode_pos: '16111' },
                { nama: 'Bogor Selatan', kode_pos: '16131' },
                { nama: 'Bogor Tengah', kode_pos: '16121' },
                { nama: 'Bogor Timur', kode_pos: '16141' },
                { nama: 'Bogor Utara', kode_pos: '16151' },
                { nama: 'Tanah Sareal', kode_pos: '16161' }
            ]
        },
        {
            kota: 'Kabupaten Bogor',
            kecamatan: [
                { nama: 'Babakan Madang', kode_pos: '16810' },
                { nama: 'Bojonggede', kode_pos: '16920' },
                { nama: 'Caringin', kode_pos: '16730' },
                { nama: 'Cariu', kode_pos: '16840' },
                { nama: 'Ciampea', kode_pos: '16620' },
                { nama: 'Ciawi', kode_pos: '16720' },
                { nama: 'Cibinong', kode_pos: '16911' },
                { nama: 'Cibungbulang', kode_pos: '16630' },
                { nama: 'Cigombong', kode_pos: '16110' },
                { nama: 'Cigudeg', kode_pos: '16660' },
                { nama: 'Cijeruk', kode_pos: '16740' },
                { nama: 'Cileungsi', kode_pos: '16820' },
                { nama: 'Ciomas', kode_pos: '16610' },
                { nama: 'Cisarua', kode_pos: '16750' },
                { nama: 'Ciseeng', kode_pos: '16120' },
                { nama: 'Citeureup', kode_pos: '16810' },
                { nama: 'Dramaga', kode_pos: '16680' },
                { nama: 'Gunung Putri', kode_pos: '16961' },
                { nama: 'Gunung Sindur', kode_pos: '16340' },
                { nama: 'Jonggol', kode_pos: '16830' },
                { nama: 'Kemang', kode_pos: '16310' },
                { nama: 'Klapanunggal', kode_pos: '16710' },
                { nama: 'Leuwiliang', kode_pos: '16640' },
                { nama: 'Megamendung', kode_pos: '16770' },
                { nama: 'Pamijahan', kode_pos: '16810' },
                { nama: 'Parung', kode_pos: '16330' },
                { nama: 'Sukaraja', kode_pos: '16710' },
                { nama: 'Tajur Halang', kode_pos: '16320' },
                { nama: 'Tamansari', kode_pos: '16610' }
            ]
        },
        {
            kota: 'Kota Bekasi',
            kecamatan: [
                { nama: 'Bantar Gebang', kode_pos: '17151' },
                { nama: 'Bekasi Barat', kode_pos: '17131' },
                { nama: 'Bekasi Selatan', kode_pos: '17141' },
                { nama: 'Bekasi Timur', kode_pos: '17111' },
                { nama: 'Bekasi Utara', kode_pos: '17121' },
                { nama: 'Jatiasih', kode_pos: '17421' },
                { nama: 'Jatisampurna', kode_pos: '17431' },
                { nama: 'Medan Satria', kode_pos: '17131' },
                { nama: 'Mustika Jaya', kode_pos: '17158' },
                { nama: 'Pondok Gede', kode_pos: '17411' },
                { nama: 'Pondok Melati', kode_pos: '17414' },
                { nama: 'Rawalumbu', kode_pos: '17116' }
            ]
        },
        {
            kota: 'Kabupaten Bekasi',
            kecamatan: [
                { nama: 'Babelan', kode_pos: '17610' },
                { nama: 'Cibarusah', kode_pos: '17340' },
                { nama: 'Cibitung', kode_pos: '17520' },
                { nama: 'Cikarang Barat', kode_pos: '17530' },
                { nama: 'Cikarang Pusat', kode_pos: '17530' },
                { nama: 'Cikarang Selatan', kode_pos: '17550' },
                { nama: 'Cikarang Timur', kode_pos: '17530' },
                { nama: 'Cikarang Utara', kode_pos: '17530' },
                { nama: 'Setu', kode_pos: '17320' },
                { nama: 'Tambun Selatan', kode_pos: '17510' },
                { nama: 'Tambun Utara', kode_pos: '17510' },
                { nama: 'Tarumajaya', kode_pos: '17210' }
            ]
        },
        {
            kota: 'Kabupaten Karawang',
            kecamatan: [
                { nama: 'Banyusari', kode_pos: '41374' },
                { nama: 'Batujaya', kode_pos: '41353' },
                { nama: 'Ciampel', kode_pos: '41361' },
                { nama: 'Cikampek', kode_pos: '41373' },
                { nama: 'Cilamaya Kulon', kode_pos: '41384' },
                { nama: 'Cilamaya Wetan', kode_pos: '41384' },
                { nama: 'Karawang Barat', kode_pos: '41311' },
                { nama: 'Karawang Timur', kode_pos: '41314' },
                { nama: 'Klari', kode_pos: '41371' },
                { nama: 'Kotabaru', kode_pos: '41374' },
                { nama: 'Majalaya', kode_pos: '41371' },
                { nama: 'Pakisjaya', kode_pos: '41355' },
                { nama: 'Pangkalan', kode_pos: '41362' },
                { nama: 'Purwasari', kode_pos: '41373' },
                { nama: 'Rawamerta', kode_pos: '41382' },
                { nama: 'Rengasdengklok', kode_pos: '41352' },
                { nama: 'Telukjambe Barat', kode_pos: '41361' },
                { nama: 'Telukjambe Timur', kode_pos: '41361' }
            ]
        },
        {
            kota: 'Kabupaten Purwakarta',
            kecamatan: [
                { nama: 'Babakancikao', kode_pos: '41151' },
                { nama: 'Bojong', kode_pos: '41164' },
                { nama: 'Bungursari', kode_pos: '41181' },
                { nama: 'Campaka', kode_pos: '41181' },
                { nama: 'Cibatu', kode_pos: '41181' },
                { nama: 'Darangdan', kode_pos: '41163' },
                { nama: 'Jatiluhur', kode_pos: '41152' },
                { nama: 'Kiarapedes', kode_pos: '41175' },
                { nama: 'Maniis', kode_pos: '41166' },
                { nama: 'Pasawahan', kode_pos: '41172' },
                { nama: 'Plered', kode_pos: '41162' },
                { nama: 'Pondoksalam', kode_pos: '41115' },
                { nama: 'Purwakarta', kode_pos: '41111' },
                { nama: 'Sukasari', kode_pos: '41116' },
                { nama: 'Tegalwaru', kode_pos: '41165' },
                { nama: 'Wanayasa', kode_pos: '41174' }
            ]
        },
        {
            kota: 'Kabupaten Subang',
            kecamatan: [
                { nama: 'Binong', kode_pos: '41253' },
                { nama: 'Blanakan', kode_pos: '41259' },
                { nama: 'Ciasem', kode_pos: '41256' },
                { nama: 'Ciater', kode_pos: '41281' },
                { nama: 'Cibogo', kode_pos: '41285' },
                { nama: 'Cijambe', kode_pos: '41286' },
                { nama: 'Cikaum', kode_pos: '41253' },
                { nama: 'Cipeundeuy', kode_pos: '41272' },
                { nama: 'Jalancagak', kode_pos: '41281' },
                { nama: 'Kalijati', kode_pos: '41271' },
                { nama: 'Kasomalang', kode_pos: '41283' },
                { nama: 'Pabuaran', kode_pos: '41262' },
                { nama: 'Pagaden', kode_pos: '41252' },
                { nama: 'Pamanukan', kode_pos: '41254' },
                { nama: 'Patokbeusi', kode_pos: '41263' },
                { nama: 'Purwadadi', kode_pos: '41261' },
                { nama: 'Subang', kode_pos: '41211' }
            ]
        },
        {
            kota: 'Kabupaten Sumedang',
            kecamatan: [
                { nama: 'Buahdua', kode_pos: '45392' },
                { nama: 'Cibugel', kode_pos: '45375' },
                { nama: 'Cimalaka', kode_pos: '45353' },
                { nama: 'Cimanggung', kode_pos: '45364' },
                { nama: 'Conggeang', kode_pos: '45391' },
                { nama: 'Darmaraja', kode_pos: '45372' },
                { nama: 'Jatinangor', kode_pos: '45363' },
                { nama: 'Jatinunggal', kode_pos: '45376' },
                { nama: 'Pamulihan', kode_pos: '45365' },
                { nama: 'Paseh', kode_pos: '45381' },
                { nama: 'Sumedang Selatan', kode_pos: '45311' },
                { nama: 'Sumedang Utara', kode_pos: '45321' },
                { nama: 'Tanjungsari', kode_pos: '45362' },
                { nama: 'Tomo', kode_pos: '45382' },
                { nama: 'Ujungjaya', kode_pos: '45383' }
            ]
        },
        {
            kota: 'Kabupaten Garut',
            kecamatan: [
                { nama: 'Banyuresmi', kode_pos: '44191' },
                { nama: 'Bayongbong', kode_pos: '44162' },
                { nama: 'Cibatu', kode_pos: '44185' },
                { nama: 'Cikajang', kode_pos: '44171' },
                { nama: 'Cisurupan', kode_pos: '44163' },
                { nama: 'Garut Kota', kode_pos: '44111' },
                { nama: 'Kadungora', kode_pos: '44153' },
                { nama: 'Karangpawitan', kode_pos: '44182' },
                { nama: 'Leles', kode_pos: '44152' },
                { nama: 'Pameungpeuk', kode_pos: '44175' },
                { nama: 'Samarang', kode_pos: '44161' },
                { nama: 'Tarogong Kaler', kode_pos: '44151' },
                { nama: 'Tarogong Kidul', kode_pos: '44151' }
            ]
        },
        {
            kota: 'Kabupaten Tasikmalaya',
            kecamatan: [
                { nama: 'Ciawi', kode_pos: '46156' },
                { nama: 'Cikalong', kode_pos: '46195' },
                { nama: 'Cikatomas', kode_pos: '46193' },
                { nama: 'Cineam', kode_pos: '46198' },
                { nama: 'Karangnunggal', kode_pos: '46186' },
                { nama: 'Mangunreja', kode_pos: '46462' },
                { nama: 'Rajapolah', kode_pos: '46155' },
                { nama: 'Salawu', kode_pos: '46471' },
                { nama: 'Singaparna', kode_pos: '46411' },
                { nama: 'Sukaraja', kode_pos: '46183' }
            ]
        },
        {
            kota: 'Kota Tasikmalaya',
            kecamatan: [
                { nama: 'Bungursari', kode_pos: '46151' },
                { nama: 'Cibeureum', kode_pos: '46196' },
                { nama: 'Cihideung', kode_pos: '46121' },
                { nama: 'Cipedes', kode_pos: '46131' },
                { nama: 'Indihiang', kode_pos: '46151' },
                { nama: 'Kawalu', kode_pos: '46182' },
                { nama: 'Mangkubumi', kode_pos: '46181' },
                { nama: 'Purbaratu', kode_pos: '46196' },
                { nama: 'Tawang', kode_pos: '46111' }
            ]
        },
        {
            kota: 'Kota Cirebon',
            kecamatan: [
                { nama: 'Harjamukti', kode_pos: '45141' },
                { nama: 'Kejaksan', kode_pos: '45121' },
                { nama: 'Kesambi', kode_pos: '45131' },
                { nama: 'Lemahwungkuk', kode_pos: '45111' },
                { nama: 'Pekalipan', kode_pos: '45115' }
            ]
        },
        {
            kota: 'Kabupaten Cirebon',
            kecamatan: [
                { nama: 'Arjawinangun', kode_pos: '45162' },
                { nama: 'Astanajapura', kode_pos: '45181' },
                { nama: 'Babakan', kode_pos: '45191' },
                { nama: 'Ciledug', kode_pos: '45188' },
                { nama: 'Ciwaringin', kode_pos: '45167' },
                { nama: 'Depok', kode_pos: '45155' },
                { nama: 'Dukupuntang', kode_pos: '45152' },
                { nama: 'Gunung Jati', kode_pos: '45151' },
                { nama: 'Jamblang', kode_pos: '45156' },
                { nama: 'Kedawung', kode_pos: '45153' },
                { nama: 'Klangenan', kode_pos: '45156' },
                { nama: 'Lemahabang', kode_pos: '45183' },
                { nama: 'Losari', kode_pos: '45192' },
                { nama: 'Mundu', kode_pos: '45173' },
                { nama: 'Palimanan', kode_pos: '45161' },
                { nama: 'Plumbon', kode_pos: '45155' },
                { nama: 'Sumber', kode_pos: '45611' },
                { nama: 'Susukan', kode_pos: '45166' },
                { nama: 'Talun', kode_pos: '45171' },
                { nama: 'Waled', kode_pos: '45187' },
                { nama: 'Weru', kode_pos: '45154' }
            ]
        },
        {
            kota: 'Kota Sukabumi',
            kecamatan: [
                { nama: 'Baros', kode_pos: '43161' },
                { nama: 'Cibeureum', kode_pos: '43165' },
                { nama: 'Cikole', kode_pos: '43111' },
                { nama: 'Citamiang', kode_pos: '43141' },
                { nama: 'Gunung Puyuh', kode_pos: '43121' },
                { nama: 'Lembursitu', kode_pos: '43169' },
                { nama: 'Warudoyong', kode_pos: '43131' }
            ]
        },
        {
            kota: 'Kabupaten Sukabumi',
            kecamatan: [
                { nama: 'Bantargadung', kode_pos: '43363' },
                { nama: 'Cibadak', kode_pos: '43351' },
                { nama: 'Cibitung', kode_pos: '43172' },
                { nama: 'Cicurug', kode_pos: '43359' },
                { nama: 'Cidahu', kode_pos: '43358' },
                { nama: 'Cikembar', kode_pos: '43157' },
                { nama: 'Cisaat', kode_pos: '43152' },
                { nama: 'Cisolok', kode_pos: '43366' },
                { nama: 'Gunungguruh', kode_pos: '43156' },
                { nama: 'Jampang Kulon', kode_pos: '43178' },
                { nama: 'Jampang Tengah', kode_pos: '43171' },
                { nama: 'Kabandungan', kode_pos: '43368' },
                { nama: 'Kalapanunggal', kode_pos: '43354' },
                { nama: 'Nagrak', kode_pos: '43356' },
                { nama: 'Palabuhanratu', kode_pos: '43364' },
                { nama: 'Parungkuda', kode_pos: '43357' },
                { nama: 'Pelabuhanratu', kode_pos: '43364' },
                { nama: 'Sukaraja', kode_pos: '43192' },
                { nama: 'Surade', kode_pos: '43179' }
            ]
        },
        {
            kota: 'Majalengka',
            kecamatan: [
                { nama: 'Kadipaten', kode_pos: '45452' },
                { nama: 'Jatiwangi', kode_pos: '45454' },
                { nama: 'Majalengka', kode_pos: '45411' },
                { nama: 'Bantarujeg', kode_pos: '45464' }
            ]
        }
    ];

    const citySelect = document.getElementById('city-sewa');
    const districtSelect = document.getElementById('district-sewa');
    const postalInput = document.getElementById('postal_code-sewa');

    if(citySelect) {
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

        // Set default values from profile
        const defaultCity = "{{ old('kota', auth()->user()->city) }}";
        const defaultDistrict = "{{ old('kecamatan', auth()->user()->district) }}";
        if(defaultCity) {
            citySelect.value = defaultCity;
            citySelect.dispatchEvent(new Event('change'));
            if(defaultDistrict) {
                districtSelect.value = defaultDistrict;
                districtSelect.dispatchEvent(new Event('change'));
            }
        }
    }
});

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Nomor rekening berhasil disalin!');
    });
}
</script>

@endsection
