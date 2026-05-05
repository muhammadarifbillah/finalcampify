@extends('layouts.admin')

@section('content')

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Detail Seller</h1>
            <p class="text-gray-600">Kelola dan moderasi seller secara detail.</p>
        </div>
        <a href="{{ route('admin.stores.index') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
            ← Kembali ke List
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-xl">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- 🟢 A. INFORMASI UTAMA -->
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold mb-4">Informasi Utama</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Pemilik</label>
                        <p class="mt-1 text-lg">{{ $store->user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1">{{ $store->user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Toko</label>
                        <p class="mt-1 text-lg font-semibold">{{ $store->nama_toko }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status Seller</label>
                        <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold mt-1
                                @if($store->status == 'active') bg-green-100 text-green-800
                                @elseif($store->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($store->status == 'rejected') bg-red-100 text-red-800
                                @elseif($store->status == 'suspended') bg-orange-100 text-orange-800
                                @elseif($store->status == 'banned') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                            {{ ucfirst($store->status) }}
                        </span>
                    </div>
                    @if($store->deskripsi)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Deskripsi Toko</label>
                            <p class="mt-1 text-gray-600">{{ $store->deskripsi }}</p>
                        </div>
                    @endif
                    @if($store->alamat)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Alamat</label>
                            <p class="mt-1">{{ $store->alamat }}</p>
                        </div>
                    @endif
                    @if($store->logo)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Logo Toko</label>
                            <img src="{{ asset($store->logo) }}" alt="Logo {{ $store->nama_toko }}"
                                class="mt-1 w-20 h-20 object-cover rounded-lg">
                        </div>
                    @endif
                </div>
            </div>

            <!-- 🔵 D. STATISTIK -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold mb-4">Statistik Seller</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['total_products'] }}</div>
                        <div class="text-sm text-gray-600">Total Produk</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['approved_products'] }}</div>
                        <div class="text-sm text-gray-600">Produk Aktif</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_products'] }}</div>
                        <div class="text-sm text-gray-600">Pending</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $stats['total_transactions'] }}</div>
                        <div class="text-sm text-gray-600">Transaksi</div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">Rp
                            {{ number_format($stats['total_sales'], 0, ',', '.') }}</div>
                        <div class="text-sm text-gray-600">Total Penjualan</div>
                    </div>
                </div>
            </div>

            <!-- ⚫ F. RIWAYAT AKTIVITAS -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold mb-4">Riwayat Aktivitas</h2>
                <div class="space-y-3">
                    @foreach($activities as $activity)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">{{ $activity['message'] }}</p>
                                <p class="text-xs text-gray-500">{{ $activity['date']->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- 🟡 B. STATUS & 🔴 C. AKSI ADMIN -->
        <div class="space-y-6">

            <!-- 🟡 STATUS SELLER -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold mb-4">Status Seller</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Status Saat Ini:</span>
                        <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                                @if($store->status == 'active') bg-green-100 text-green-800
                                @elseif($store->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($store->status == 'rejected') bg-red-100 text-red-800
                                @elseif($store->status == 'suspended') bg-orange-100 text-orange-800
                                @elseif($store->status == 'banned') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                            {{ ucfirst($store->status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Bergabung:</span>
                        <span class="text-sm text-gray-600">{{ $store->created_at->format('d M Y') }}</span>
                    </div>
                    @if($store->last_active)
                        <div class="flex items-center justify-between">
                            <span class="text-sm">Terakhir Aktif:</span>
                            <span class="text-sm text-gray-600">{{ $store->last_active->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 🔴 C. AKSI ADMIN -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold mb-4">Aksi Admin</h3>

                @if($store->status == 'pending')
                    <form method="POST" action="{{ route('admin.stores.approve', $store->id) }}" class="mb-3">
                        @csrf
                        <button type="submit"
                            class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            ✅ Approve Seller
                        </button>
                    </form>
                @endif

                @if(in_array($store->status, ['active', 'pending']))
                    <form method="POST" action="{{ route('admin.stores.reject', $store->id) }}" class="mb-3">
                        @csrf
                        <div class="space-y-2">
                            <textarea name="reason" placeholder="Alasan reject..." required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                rows="3"></textarea>
                            <button type="submit"
                                class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                                ❌ Reject Seller
                            </button>
                        </div>
                    </form>
                @endif

                @if($store->status == 'active')
                    <form method="POST" action="{{ route('admin.stores.suspend', $store->id) }}" class="mb-3">
                        @csrf
                        <div class="space-y-2">
                            <textarea name="reason" placeholder="Alasan suspend..." required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                rows="3"></textarea>
                            <button type="submit"
                                class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
                                ⏸️ Suspend Seller
                            </button>
                        </div>
                    </form>
                @endif

                @if(!in_array($store->status, ['banned']))
                    <form method="POST" action="{{ route('admin.stores.ban', $store->id) }}" class="mb-3">
                        @csrf
                        <div class="space-y-2">
                            <textarea name="reason" placeholder="Alasan ban..." required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                rows="3"></textarea>
                            <button type="submit"
                                class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                                🚫 Ban Seller
                            </button>
                        </div>
                    </form>
                @endif

                @if(in_array($store->status, ['rejected', 'suspended', 'banned']))
                    <form method="POST" action="{{ route('admin.stores.activate', $store->id) }}" class="mb-3">
                        @csrf
                        <button type="submit"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            🔄 Aktifkan Kembali
                        </button>
                    </form>
                @endif

            </div>

            <!-- 🟣 E. CATATAN ADMIN -->
            @if($store->catatan_admin)
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Catatan Admin</h3>
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                        <p class="text-red-800">{{ $store->catatan_admin }}</p>
                    </div>
                </div>
            @endif

        </div>

    </div>

@endsection