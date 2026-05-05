@extends('layouts.app_pembeli')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold">Profil Saya</h1>
            <p class="text-gray-600 mt-2">Kelola data akun, alamat, pesanan, dan keamanan di satu halaman.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mt-6 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mt-6 p-4 bg-red-100 text-red-800 rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="grid gap-8 lg:grid-cols-[280px_1fr] mt-8">

        <aside class="space-y-6">
            <div class="bg-white rounded-3xl p-6 shadow-sm text-center">
                <div class="mx-auto mb-4 h-24 w-24 rounded-full bg-green-100 flex items-center justify-center text-4xl">👤</div>
                <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                <p class="text-sm text-gray-500 mt-2">{{ $user->address ? $user->address : 'Alamat belum diisi' }}</p>
            </div>

            <div class="bg-white rounded-3xl p-4 shadow-sm space-y-2">
                @php
                    $tabs = [
                        'profile' => 'Profile Saya',
                        'orders' => 'Pesanan Saya',
                        'favorites' => 'Favorit',
                        'edit' => 'Edit Profile',
                    ];
                @endphp

                @foreach($tabs as $key => $label)
                    <a href="{{ route('profile', ['tab' => $key]) }}"
                       class="block rounded-2xl px-4 py-3 text-sm font-medium {{ $tab === $key ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </aside>

        <section>
            @if($tab === 'profile')
                <div class="bg-white rounded-3xl p-8 shadow-sm">
                    <h2 class="text-2xl font-bold mb-4">Informasi Akun</h2>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Nama</p>
                            <p class="font-medium">{{ $user->name }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium">{{ $user->email }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Alamat</p>
                            <p class="font-medium">{{ $user->address ?? '-' }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Kota</p>
                            <p class="font-medium">{{ $user->city ?? '-' }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Kode Pos</p>
                            <p class="font-medium">{{ $user->postal_code ?? '-' }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-500">Telepon</p>
                            <p class="font-medium">{{ $user->phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @elseif($tab === 'orders')
                <div class="bg-white rounded-3xl p-8 shadow-sm">
                    <h2 class="text-2xl font-bold mb-4">Pesanan Saya</h2>
                    @if($orders->isEmpty())
                        <div class="rounded-3xl border border-dashed border-gray-300 p-8 text-center text-gray-500">
                            Belum ada pesanan.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($orders as $order)
                                <div class="rounded-3xl border p-6 shadow-sm">
                                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                        <div>
                                            <p class="text-sm text-gray-500">No. Pesanan</p>
                                            <p class="font-bold">#{{ $order->id }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Status</p>
                                            <p class="font-medium capitalize">{{ $order->status }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Total</p>
                                            <p class="font-medium">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <a href="{{ route('orders.detail', $order->id) }}" class="text-green-600 hover:underline">Lihat Detail</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @elseif($tab === 'edit')
                <div class="bg-white rounded-3xl p-8 shadow-sm">
                    <h2 class="text-2xl font-bold mb-4">Edit Profile & Alamat</h2>
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" required>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                            <textarea name="address" rows="3" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" required>{{ old('address', $user->address) }}</textarea>
                        </div>
                        
                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kota / Kabupaten</label>
                                <select name="city" id="city" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" required>
                                    <option value="">Pilih Kota/Kabupaten</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
                                <select name="district" id="district" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" required>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kode Pos</label>
                                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" readonly required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" required>
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-green-600 px-6 py-3 text-white hover:bg-green-700">Simpan Perubahan</button>
                    </form>
                </div>
                
                <script>
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
                    }
                ];

                document.addEventListener('DOMContentLoaded', function() {
                    const citySelect = document.getElementById('city');
                    const districtSelect = document.getElementById('district');
                    const postalInput = document.getElementById('postal_code');

                    // Populate city
                    dataAlamat.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.kota;
                        opt.textContent = item.kota;
                        citySelect.appendChild(opt);
                    });

                    // On city change
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

                    // On district change
                    districtSelect.addEventListener('change', function() {
                        const kota = dataAlamat.find(k => k.kota === citySelect.value);
                        if (kota) {
                            const kec = kota.kecamatan.find(d => d.nama === districtSelect.value);
                            postalInput.value = kec ? kec.kode_pos : '';
                        }
                    });

                    // Set default value if exist
                    const defaultCity = "{{ old('city', $user->city) }}";
                    const defaultDistrict = "{{ old('district', $user->district ?? '') }}";
                    
                    if(defaultCity) {
                        citySelect.value = defaultCity;
                        citySelect.dispatchEvent(new Event('change'));
                        
                        setTimeout(() => {
                            if(defaultDistrict) {
                                districtSelect.value = defaultDistrict;
                                districtSelect.dispatchEvent(new Event('change'));
                            }
                        }, 50);
                    }
                });
                </script>
            @elseif($tab === 'favorites')
                <div class="bg-white rounded-3xl p-8 shadow-sm">
                    <h2 class="text-2xl font-bold mb-4">Favorit Saya</h2>
                    @if(empty($wishlists) || $wishlists->isEmpty())
                        <div class="rounded-3xl border border-dashed border-gray-300 p-8 text-center text-gray-500">
                            Belum ada produk favorit. Tambahkan favorit untuk melihatnya di sini.
                        </div>
                    @else
                        <div class="grid gap-6 md:grid-cols-2">
                            @foreach($wishlists as $wishlist)
                                <div class="rounded-3xl border p-5 shadow-sm flex gap-4">
                                    <img src="{{ $wishlist->produk->image }}" alt="{{ $wishlist->produk->name }}" class="h-24 w-24 rounded-2xl object-cover">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-lg">{{ $wishlist->produk->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $wishlist->produk->category }}</p>
                                        <p class="mt-3 text-sm font-semibold text-green-600">Rp {{ number_format($wishlist->produk->buy_price, 0, ',', '.') }}</p>
                                        <a href="{{ route('produk.detail', $wishlist->produk->id) }}" class="inline-block mt-3 text-sm text-green-600 hover:underline">Lihat Produk</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </section>
    </div>
</div>
@endsection