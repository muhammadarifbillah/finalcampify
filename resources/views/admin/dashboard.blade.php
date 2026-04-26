@extends('layouts.admin')

@section('content')

    <h1 class="text-2xl font-bold mb-6 text-green-800">Dashboard</h1>

    <!-- CARD -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-gray-500">User</p>
            <h2 class="text-2xl font-bold">{{ $users }}</h2>
            <p class="text-sm text-gray-400">Pengguna biasa: {{ $regularUsers }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-gray-500">Produk</p>
            <h2 class="text-2xl font-bold">{{ $products }}</h2>
            <p class="text-sm text-gray-400">Pending: {{ $pendingProducts }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-gray-500">Transaksi</p>
            <h2 class="text-2xl font-bold">{{ $transactions }}</h2>
            <p class="text-sm text-gray-400">Pendapatan: Rp {{ number_format($revenue, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-gray-500">Pendapatan</p>
            <h2 class="text-2xl font-bold">Rp {{ number_format($revenue, 0, ',', '.') }}</h2>
            <p class="text-sm text-gray-400">Toko banned: {{ $bannedStores }}</p>
        </div>

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-gray-500">Toko</p>
            <h2 class="text-2xl font-bold">{{ $stores }}</h2>
            <p class="text-sm text-gray-400">Total toko aktif dan nonaktif</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-gray-500">Pengguna biasa</p>
            <h2 class="text-2xl font-bold">{{ $regularUsers }}</h2>
            <p class="text-sm text-gray-400">Jumlah pengguna non-admin</p>
        </div>
    </div>

    <!-- ALERTS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-xl shadow">
            <p class="text-sm uppercase font-semibold text-yellow-600">Produk pending</p>
            <p class="text-3xl font-bold">{{ $pendingProducts }}</p>
        </div>
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-xl shadow">
            <p class="text-sm uppercase font-semibold text-red-600">Toko banned</p>
            <p class="text-3xl font-bold">{{ $bannedStores }}</p>
        </div>
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-xl shadow">
            <p class="text-sm uppercase font-semibold text-blue-600">Chat bermasalah</p>
            <p class="text-3xl font-bold">{{ $flaggedChats }}</p>
        </div>
    </div>

    <!-- GRAFIK -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="font-bold mb-4">Transaksi per bulan</h2>
            <canvas id="transactionsChart"></canvas>
        </div>
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="font-bold mb-4">Pendapatan per bulan</h2>
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow overflow-x-auto mb-6">
        <div class="p-4 border-b">
            <h2 class="font-bold">Transaksi terbaru</h2>
        </div>
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">User</th>
                    <th>Produk</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($latestTransactions as $transaction)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ optional($transaction->user)->name ?? 'Unknown' }}</td>
                        <td>{{ optional($transaction->product)->name ?? 'Unknown' }}</td>
                        <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-3" colspan="3">Belum ada transaksi terbaru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(!$hasCreatedAt)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-xl shadow">
            <p class="font-semibold">Catatan:</p>
            <p class="text-sm text-gray-600">Untuk grafik per bulan yang akurat, tabel transaksi sebaiknya memiliki kolom
                <code>created_at</code> atau timestamp.</p>
        </div>
    @endif

    <script>
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const transactionCounts = @json($monthlyTransactionCounts);
        const revenueValues = @json($monthlyRevenue);

        new Chart(document.getElementById('transactionsChart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Transaksi',
                    data: transactionCounts,
                    backgroundColor: 'rgba(34,197,94,0.2)',
                    borderColor: 'rgba(34,197,94,1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Pendapatan',
                    data: revenueValues,
                    backgroundColor: 'rgba(37,99,235,0.2)',
                    borderColor: 'rgba(37,99,235,1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });
    </script>

@endsection