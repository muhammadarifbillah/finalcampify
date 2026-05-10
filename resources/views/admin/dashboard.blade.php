@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="admin-section-title">Pembelian Dashboard</h1>
                <p class="admin-section-subtitle">Monitoring transaksi, user, seller, dan aktivitas marketplace harian.</p>
            </div>
            <a href="/admin/orders" class="admin-button admin-button-primary">
                <i data-lucide="download"></i>
                Export Report
            </a>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
            <div class="admin-card admin-stat-card">
                <div class="flex items-start justify-between">
                    <div class="grid h-14 w-14 place-items-center rounded-lg bg-emerald-600 text-white"><i data-lucide="users"></i></div>
                    <span class="text-emerald-700 font-bold">+{{ $buyers }} buyer</span>
                </div>
                <div>
                    <p class="admin-stat-label">Total Pengguna</p>
                    <h2 class="admin-stat-value">{{ number_format($users) }}</h2>
                    <p class="admin-stat-meta">{{ number_format($sellers) }} penjual aktif terdaftar</p>
                </div>
            </div>

            <div class="admin-card admin-stat-card">
                <div class="flex items-start justify-between">
                    <div class="grid h-14 w-14 place-items-center rounded-lg bg-red-600 text-white"><i data-lucide="package-check"></i></div>
                    <span class="text-red-700 font-bold">{{ $pendingProducts }} waiting</span>
                </div>
                <div>
                    <p class="admin-stat-label">Total Produk</p>
                    <h2 class="admin-stat-value">{{ number_format($products) }}</h2>
                    <p class="admin-stat-meta">{{ number_format($approvedProducts) }} approved, {{ number_format($rejectedProducts) }} rejected</p>
                </div>
            </div>

            <div class="admin-card admin-stat-card">
                <div class="flex items-start justify-between">
                    <div class="grid h-14 w-14 place-items-center rounded-lg bg-blue-600 text-white"><i data-lucide="shopping-bag"></i></div>
                    <span class="text-emerald-700 font-bold">Orders {{ number_format($orders) }}</span>
                </div>
                <div>
                    <p class="admin-stat-label">Total Transaksi</p>
                    <h2 class="admin-stat-value">{{ number_format($transactions) }}</h2>
                    <p class="admin-stat-meta">Pendapatan Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="admin-card admin-stat-card {{ $bannedStores ? 'bg-emerald-700 text-white' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="grid h-14 w-14 place-items-center rounded-lg {{ $bannedStores ? 'bg-white/15' : 'bg-slate-200' }}"><i data-lucide="store"></i></div>
                    <span class="{{ $bannedStores ? 'text-white' : 'text-emerald-700' }} font-bold">Banned {{ $bannedStores }}</span>
                </div>
                <div>
                    <p class="admin-stat-label {{ $bannedStores ? 'text-emerald-100' : '' }}">Ringkasan Toko</p>
                    <h2 class="admin-stat-value {{ $bannedStores ? 'text-white' : '' }}">{{ number_format($stores) }}</h2>
                    <p class="admin-stat-meta {{ $bannedStores ? 'text-emerald-100' : '' }}">{{ number_format($activeStores) }} toko aktif</p>
                </div>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-3">
            <div class="admin-alert admin-alert-danger">
                <i data-lucide="triangle-alert"></i>
                <div>
                    <p class="font-extrabold tracking-wide">PRODUK WAITING</p>
                    <p>{{ $pendingProducts }} produk seller menunggu validasi admin.</p>
                </div>
            </div>
            <div class="admin-alert {{ $bannedStores ? 'admin-alert-danger' : 'admin-alert-warning' }}">
                <i data-lucide="ban"></i>
                <div>
                    <p class="font-extrabold tracking-wide">TOKO BANNED</p>
                    <p>{{ $bannedStores }} toko sedang diblokir atau bermasalah.</p>
                </div>
            </div>
            <div class="admin-alert admin-alert-info">
                <i data-lucide="message-square-warning"></i>
                <div>
                    <p class="font-extrabold tracking-wide">CHAT BERMASALAH</p>
                    <p>{{ $flaggedChats }} chat ditandai sistem atau user.</p>
                </div>
            </div>
            @if($pendingKyc > 0)
                <div class="admin-alert admin-alert-warning">
                    <i data-lucide="user-check"></i>
                    <div>
                        <p class="font-extrabold tracking-wide">PENDING KYC</p>
                        <p>{{ $pendingKyc }} user menunggu verifikasi KTP. <a href="{{ route('admin.users.index') }}" class="underline font-bold">Verifikasi Sekarang</a></p>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="admin-card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-extrabold">Transaksi per bulan</h2>
                    <button class="admin-icon-button" type="button"><i data-lucide="ellipsis-vertical"></i></button>
                </div>
                <div class="h-72"><canvas id="transactionsChart"></canvas></div>
            </div>
            <div class="admin-card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-extrabold">Pendapatan & user activity</h2>
                    <button class="admin-icon-button" type="button"><i data-lucide="ellipsis-vertical"></i></button>
                </div>
                <div class="h-72"><canvas id="activityChart"></canvas></div>
            </div>
        </div>

        <div class="admin-card">
            <div class="flex flex-col gap-3 p-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-extrabold">Transaksi terbaru</h2>
                    <p class="text-sm text-slate-500">Data real dari order buyer/seller dan legacy transaksi.</p>
                </div>
                <a href="/admin/orders" class="admin-button admin-button-ghost">Lihat Semua <i data-lucide="arrow-right"></i></a>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Produk</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestOrders as $order)
                            <tr>
                                <td>
                                    <div class="font-extrabold">{{ $order->buyer->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $order->buyer->email ?? '' }}</div>
                                </td>
                                <td>{{ $order->details->pluck('product.name')->filter()->implode(', ') ?: '-' }}</td>
                                <td>{{ $order->created_at?->format('d M Y') ?? '-' }}</td>
                                <td><span class="admin-badge admin-badge-info">{{ $order->status }}</span></td>
                                <td class="font-extrabold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            @forelse($latestTransactions as $transaction)
                                <tr>
                                    <td>{{ optional($transaction->user)->name ?? 'Unknown' }}</td>
                                    <td>{{ optional($transaction->product)->name ?? 'Unknown' }}</td>
                                    <td>{{ $transaction->created_at?->format('d M Y') ?? '-' }}</td>
                                    <td><span class="admin-badge admin-badge-success">success</span></td>
                                    <td class="font-extrabold">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="admin-empty">Belum ada transaksi terbaru.</div>
                                    </td>
                                </tr>
                            @endforelse
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @unless($hasCreatedAt)
            <div class="admin-alert admin-alert-warning">
                <i data-lucide="info"></i>
                <div>
                    <p class="font-extrabold">Timestamp belum lengkap</p>
                    <p>Grafik akan lebih akurat jika tabel transaksi/order memiliki kolom created_at.</p>
                </div>
            </div>
        @endunless
    </div>
@endsection

@section('scripts')
    <script>
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const transactionCounts = @json($monthlyTransactionCounts);
        const revenueValues = @json($monthlyRevenue);
        const userActivity = @json($monthlyUserActivity);

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true, labels: { boxWidth: 10, usePointStyle: true } } },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: '#dbe7de', borderDash: [4, 4] }, beginAtZero: true }
            }
        };

        new Chart(document.getElementById('transactionsChart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Transaksi',
                    data: transactionCounts,
                    backgroundColor: 'rgba(0,122,82,0.12)',
                    borderColor: '#007a52',
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#007a52',
                    pointRadius: 4,
                    tension: 0.35,
                    fill: true
                }]
            },
            options: chartOptions
        });

        new Chart(document.getElementById('activityChart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: revenueValues,
                        backgroundColor: 'rgba(0,76,215,0.12)',
                        borderColor: '#0057d8',
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'User baru',
                        data: userActivity,
                        borderColor: '#b53b35',
                        tension: 0.35
                    }
                ]
            },
            options: chartOptions
        });
    </script>
@endsection
