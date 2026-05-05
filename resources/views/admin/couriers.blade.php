@extends('layouts.admin')

@section('content')

    <h1 class="text-2xl font-bold mb-2">Data Kurir</h1>
    <p class="text-gray-600 mb-6">Kelola layanan pengiriman dan kategori kurir seperti JNE, Ninja Express, SiCepat, serta
        buat data langsung tersimpan ke database.</p>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 border border-green-200 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4 text-red-800">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-4 xl:grid-cols-3 mb-6">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500">Total Kurir</p>
            <h2 class="text-3xl font-semibold text-slate-900">{{ $couriers->count() }}</h2>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500">Kategori Layanan</p>
            <h2 class="text-3xl font-semibold text-slate-900">{{ $serviceList->count() }}</h2>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500">Filter Aktif</p>
            <h2 class="text-3xl font-semibold text-slate-900">{{ $selectedService ?: 'Semua' }}</h2>
        </div>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 mb-6">
        <h2 class="font-semibold mb-4">Menu Kategori Kurir</h2>
        <div class="flex flex-wrap gap-2">
            <a href="/admin/couriers"
                class="px-4 py-2 rounded-full text-sm font-medium {{ !$selectedService ? 'bg-green-800 text-white' : 'bg-gray-100 text-gray-700' }}">Semua</a>
            @foreach($serviceList as $service)
                <a href="/admin/couriers?service={{ urlencode($service) }}"
                    class="px-4 py-2 rounded-full text-sm font-medium {{ $selectedService === $service ? 'bg-green-800 text-white' : 'bg-gray-100 text-gray-700' }}">{{ $service }}</a>
            @endforeach
        </div>
    </div>

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Kelola Kurir</h2>
            <p class="text-slate-500">Tambah, edit, atau atur layanan pengiriman tanpa mengubah logika yang sudah ada.</p>
        </div>
        <button id="openCourierModal"
            class="bg-emerald-600 text-white px-5 py-3 rounded-3xl font-semibold hover:bg-emerald-700 transition">Tambah Kurir</button>
    </div>

    <div id="courierModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-xl rounded-[32px] bg-white p-6 shadow-2xl max-h-[calc(100vh-3rem)] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 id="courierModalTitle" class="text-2xl font-semibold">Tambah Kurir</h2>
                <button type="button" id="closeCourierModal" class="text-slate-500 hover:text-slate-900">Tutup</button>
            </div>
            <form id="courierForm" method="POST" action="/admin/couriers/store" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kurir</label>
                    <input id="courierName" type="text" name="name"
                        class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Contoh: JNE" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Layanan</label>
                    <select id="courierService" name="service"
                        class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Pilih layanan</option>
                        @foreach($serviceList as $service)
                            <option value="{{ $service }}">{{ $service }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-2">Pilih hanya dari layanan yang sudah tersedia untuk menjaga
                        konsistensi kategori.</p>
                </div>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estimasi Pengiriman</label>
                        <input id="courierEstimate" type="text" name="estimate"
                            class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Contoh: 1-2 hari" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Ongkir</label>
                        <input id="courierPrice" type="number" min="0" step="1" name="price"
                            class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Contoh: 15000" />
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="courierStatus" name="status"
                            class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                            <option value="banned">Banned</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" id="cancelCourierModal"
                        class="bg-gray-200 text-gray-700 px-5 py-3 rounded-xl">Batal</button>
                    <button id="courierSubmit" type="submit"
                        class="bg-green-700 text-white px-5 py-3 rounded-xl">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="lg:col-span-3">
        <div class="bg-white p-6 rounded-xl shadow">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-4">
                <div>
                    <h2 class="font-semibold text-2xl text-slate-900">Daftar Kurir</h2>
                    <p class="text-sm text-slate-500">Klik edit untuk memperbarui layanan kurir atau hapus bila tidak lagi aktif.</p>
                </div>
                <span class="inline-flex items-center rounded-full bg-slate-100 px-4 py-2 text-sm text-slate-600">Total {{ $couriers->count() }} layanan</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 text-sm font-semibold text-gray-600">Nama Kurir</th>
                            <th class="p-3 text-sm font-semibold text-gray-600">Layanan / Kategori</th>
                            <th class="p-3 text-sm font-semibold text-gray-600">Estimasi</th>
                            <th class="p-3 text-sm font-semibold text-gray-600">Ongkir</th>
                            <th class="p-3 text-sm font-semibold text-gray-600">Status</th>
                            <th class="p-3 text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($couriers as $courier)
                            <tr class="border-b last:border-b-0 hover:bg-slate-50">
                                <td class="p-4 align-top text-slate-800">{{ $courier->name }}</td>
                                <td class="p-4 align-top text-slate-700">{{ $courier->service }}</td>
                                <td class="p-4 align-top text-slate-700">{{ $courier->estimate }}</td>
                                <td class="p-4 align-top text-slate-700">Rp {{ number_format($courier->price, 0, ',', '.') }}</td>
                                <td class="p-4 align-top">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $courier->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : ($courier->status === 'nonaktif' ? 'bg-slate-100 text-slate-700' : 'bg-red-100 text-red-800') }}">{{ ucfirst($courier->status) }}</span>
                                </td>
                                <td class="p-4 flex flex-wrap gap-2">
                                    <button type="button" onclick='openEditCourier(@json($courier))'
                                        class="bg-slate-800 text-white px-4 py-2 rounded-2xl text-sm transition hover:bg-slate-900">Edit</button>
                                    <a href="/admin/couriers/delete/{{ $courier->id }}"
                                        onclick="return confirm('Yakin ingin hapus kurir ini?')"
                                        class="bg-red-600 text-white px-4 py-2 rounded-2xl text-sm transition hover:bg-red-700">Hapus</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-slate-500">Belum ada data kurir. Tambahkan layanan kurir terlebih dahulu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    <script>
        const courierModal = document.getElementById('courierModal');
        const courierForm = document.getElementById('courierForm');
        const courierModalTitle = document.getElementById('courierModalTitle');
        const courierSubmit = document.getElementById('courierSubmit');
        const courierName = document.getElementById('courierName');
        const courierService = document.getElementById('courierService');
        const courierEstimate = document.getElementById('courierEstimate');
        const courierPrice = document.getElementById('courierPrice');
        const courierStatus = document.getElementById('courierStatus');
        const openCourierModalButton = document.getElementById('openCourierModal');
        const closeCourierModalButton = document.getElementById('closeCourierModal');
        const cancelCourierModalButton = document.getElementById('cancelCourierModal');

        function openCourierModal(mode = 'add', courier = null) {
            courierModal.classList.remove('hidden');
            if (mode === 'add') {
                courierModalTitle.textContent = 'Tambah Kurir';
                courierForm.action = '/admin/couriers/store';
                courierSubmit.textContent = 'Tambah';
                courierName.value = '';
                courierService.value = '';
                courierEstimate.value = '';
                courierPrice.value = '';
                courierStatus.value = 'aktif';
            } else {
                courierModalTitle.textContent = 'Edit Kurir';
                courierForm.action = '/admin/couriers/update/' + courier.id;
                courierSubmit.textContent = 'Simpan';
                courierName.value = courier.name;
                courierService.value = courier.service;
                courierEstimate.value = courier.estimate || '';
                courierPrice.value = courier.price || '';
                courierStatus.value = courier.status || 'aktif';
            }
        }

        function closeCourierModal() {
            courierModal.classList.add('hidden');
        }

        function openEditCourier(courier) {
            openCourierModal('edit', courier);
        }

        openCourierModalButton.addEventListener('click', () => openCourierModal('add'));
        closeCourierModalButton.addEventListener('click', closeCourierModal);
        cancelCourierModalButton.addEventListener('click', closeCourierModal);
        window.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !courierModal.classList.contains('hidden')) {
                closeCourierModal();
            }
        });
    </script>

@endsection