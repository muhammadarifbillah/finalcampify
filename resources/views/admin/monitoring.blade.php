@extends('layouts.admin')

@section('title', 'Monitoring Admin')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="admin-section-title">Monitoring</h1>
                <p class="admin-section-subtitle">Realtime activity marketplace: transaksi, seller, buyer, produk, dan laporan.</p>
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Transaksi</p>
                <h2 class="admin-stat-value">{{ number_format($orders->count()) }}</h2>
                <p class="admin-stat-meta">Order marketplace</p>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Seller</p>
                <h2 class="admin-stat-value">{{ number_format($sellers->count()) }}</h2>
                <p class="admin-stat-meta">Aktif memproses order</p>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Buyer</p>
                <h2 class="admin-stat-value">{{ number_format($buyers->count()) }}</h2>
                <p class="admin-stat-meta">Aktif belanja</p>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Flagged Chat</p>
                <h2 class="admin-stat-value">{{ number_format($flaggedChats) }}</h2>
                <p class="admin-stat-meta">Butuh review</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="admin-card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-extrabold">Aktivitas 7 Hari</h2>
                    <span class="admin-badge admin-badge-muted">Realtime</span>
                </div>
                <div class="h-72"><canvas id="monitorChart"></canvas></div>
            </div>
            <div class="admin-card p-6">
                <h2 class="text-2xl font-extrabold mb-6">Status Transaksi</h2>
                <div class="space-y-4">
                    @foreach($statusSummary as $status => $count)
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-bold">{{ $status }}</span>
                                <span>{{ $count }}</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-emerald-700" style="width: {{ max(10, min(100, ($count / max(1, $orders->count())) * 100)) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    @if($statusSummary->isEmpty())
                        <div class="admin-empty">Belum ada status transaksi.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="admin-card">
                <div class="p-6">
                    <h2 class="text-2xl font-extrabold">Aktivitas Transaksi</h2>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Pembeli</th>
                                <th>Produk</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders->take(8) as $order)
                                <tr>
                                    <td>{{ $order->buyer->name ?? '-' }}</td>
                                    <td>{{ $order->details->pluck('product.name')->filter()->implode(', ') ?: '-' }}</td>
                                    <td><span class="admin-badge admin-badge-info">{{ $order->status }}</span></td>
                                    <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4"><div class="admin-empty">Belum ada transaksi.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-card">
                <div class="p-6">
                    <h2 class="text-2xl font-extrabold">Produk Terbaru</h2>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Seller</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->store?->nama_toko ?? $product->owner?->name ?? '-' }}</td>
                                    <td><span class="admin-badge {{ $product->status === 'approved' ? 'admin-badge-success' : ($product->status === 'rejected' ? 'admin-badge-danger' : 'admin-badge-warning') }}">{{ $product->status }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3"><div class="admin-empty">Belum ada produk terbaru.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="admin-card">
                <div class="p-6">
                    <h2 class="text-2xl font-extrabold">Laporan Sistem</h2>
                </div>
                <div class="space-y-3 px-6 pb-6">
                    @forelse($reports as $report)
                        <div class="rounded-lg border border-slate-200 p-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="admin-badge admin-badge-danger">{{ $report->type }}</span>
                                <span class="text-xs text-slate-500">{{ $report->created_at?->diffForHumans() }}</span>
                            </div>
                            <p class="mt-2 font-bold">{{ $report->reason }}</p>
                            <p class="text-sm text-slate-600">{{ $report->description }}</p>
                        </div>
                    @empty
                        <div class="admin-empty">Tidak ada laporan.</div>
                    @endforelse
                </div>
            </div>

            <div class="admin-card">
                <div class="p-6">
                    <h2 class="text-2xl font-extrabold">Pelanggaran Seller</h2>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Seller</th>
                                <th>Aksi</th>
                                <th>Strike</th>
                                <th>Alasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($violations as $violation)
                                <tr>
                                    <td>{{ $violation->seller?->name ?? '-' }}</td>
                                    <td><span class="admin-badge admin-badge-muted">{{ $violation->action }}</span></td>
                                    <td>{{ $violation->strike_count }}</td>
                                    <td>{{ $violation->reason }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4"><div class="admin-empty">Belum ada pelanggaran.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        new Chart(document.getElementById('monitorChart'), {
            type: 'bar',
            data: {
                labels: @json($activityLabels),
                datasets: [
                    {
                        label: 'Order',
                        data: @json($orderActivity),
                        backgroundColor: '#007a52'
                    },
                    {
                        label: 'Produk',
                        data: @json($productActivity),
                        backgroundColor: '#0057d8'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#dbe7de' } }
                }
            }
        });
    </script>
@endsection
