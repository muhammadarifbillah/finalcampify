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
                        'address' => 'Alamat Saya',
                        'security' => 'Keamanan',
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
            @elseif($tab === 'address')
                <div class="bg-white rounded-3xl p-8 shadow-sm">
                    <h2 class="text-2xl font-bold mb-4">Alamat Saya</h2>
                    <form action="{{ route('profile.address.update') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                            <textarea name="address" rows="4" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" required>{{ old('address', $user->address) }}</textarea>
                        </div>
                                                <div class="grid gap-4 md:grid-cols-3">
                                                        <div>
                                                                <label class="block text-sm font-medium text-gray-700">Kota</label>
                                                                <select name="city" id="city" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" required>
                                                                        <option value="">Pilih Kota</option>
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
                                                                <input type="text" name="postal_code" id="postal_code" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" readonly required>
                                                        </div>
                                                </div>

                                                <script>
                                                // Contoh data lokal
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
                                                    const defaultPostal = "{{ old('postal_code', $user->postal_code) }}";
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
                                                });
                                                </script>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" required>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-green-600 px-6 py-3 text-white hover:bg-green-700">Simpan Alamat</button>
                    </form>
                </div>
            @elseif($tab === 'security')
                <div class="bg-white rounded-3xl p-8 shadow-sm">
                    <h2 class="text-2xl font-bold mb-4">Keamanan</h2>
                    <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                            <input type="password" name="current_password" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password Baru</label>
                            <input type="password" name="password" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" required>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-green-600 px-6 py-3 text-white hover:bg-green-700">Perbarui Password</button>
                    </form>
                </div>
            @elseif($tab === 'edit')
                <div class="bg-white rounded-3xl p-8 shadow-sm">
                    <h2 class="text-2xl font-bold mb-4">Edit Profile</h2>
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-2 w-full rounded-2xl border-gray-200 shadow-sm" required>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-green-600 px-6 py-3 text-white hover:bg-green-700">Simpan Profile</button>
                    </form>
                </div>
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