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

        {{-- ======================== STAT CARDS ======================== --}}
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-5">
            {{-- Card 1: Transaksi --}}
            <div class="premium-glass-card premium-glass-card-emerald p-6 flex flex-col justify-between min-h-[160px] group">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#059669] rounded-t-2xl"></div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Transaksi</p>
                        <div class="w-8 h-8 rounded-lg bg-[#ecfdf5] text-[#059669] flex items-center justify-center transition-colors group-hover:bg-[#059669] group-hover:text-white">
                            <i data-lucide="banknote" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($orders->count()) }}</h2>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 font-bold mt-2">Order marketplace</p>
            </div>

            {{-- Card 2: Seller --}}
            <div class="premium-glass-card premium-glass-card-forest p-6 flex flex-col justify-between min-h-[160px] group">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#064e3b] rounded-t-2xl"></div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Seller</p>
                        <div class="w-8 h-8 rounded-lg bg-[#e6f4ea] text-[#047857] flex items-center justify-center transition-colors group-hover:bg-[#064e3b] group-hover:text-white">
                            <i data-lucide="store" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($totalSellers) }}</h2>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 font-bold mt-2">Aktif memproses order</p>
            </div>

            {{-- Card 3: Buyer --}}
            <div class="premium-glass-card premium-glass-card-teal p-6 flex flex-col justify-between min-h-[160px] group">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#0f766e] rounded-t-2xl"></div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Buyer</p>
                        <div class="w-8 h-8 rounded-lg bg-[#f0fdfa] text-[#0f766e] flex items-center justify-center transition-colors group-hover:bg-[#0f766e] group-hover:text-white">
                            <i data-lucide="users" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($totalBuyers) }}</h2>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 font-bold mt-2">Aktif belanja</p>
            </div>

            {{-- Card 4: Produk --}}
            <div class="premium-glass-card premium-glass-card-mint p-6 flex flex-col justify-between min-h-[160px] group">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#10b981] rounded-t-2xl"></div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Produk</p>
                        <div class="w-8 h-8 rounded-lg bg-[#ecfdf5] text-[#10b981] flex items-center justify-center transition-colors group-hover:bg-[#10b981] group-hover:text-white">
                            <i data-lucide="package" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($totalProducts) }}</h2>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 font-bold mt-2">Produk terdaftar</p>
            </div>

            {{-- Card 5: Flagged Chat --}}
            <div class="premium-glass-card premium-glass-card-rose p-6 flex flex-col justify-between min-h-[160px] group">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#f43f5e] rounded-t-2xl"></div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Flagged Chat</p>
                        <div class="w-8 h-8 rounded-lg bg-[#fff1f2] text-[#f43f5e] flex items-center justify-center transition-colors group-hover:bg-[#f43f5e] group-hover:text-white">
                            <i data-lucide="messages-square" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($flaggedChats) }}</h2>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 font-bold mt-2">Butuh review</p>
            </div>
        </div>

        {{-- ======================== CHART + STATUS ======================== --}}
        <div class="grid gap-6 xl:grid-cols-2">
            <div class="premium-glass-card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-extrabold text-slate-800">Aktivitas 7 Hari</h2>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-bold text-emerald-700 ring-1 ring-emerald-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Realtime
                    </span>
                </div>
                <div class="h-72"><canvas id="monitorChart"></canvas></div>
            </div>
            <div class="premium-glass-card p-6">
                <h2 class="text-lg font-extrabold text-slate-800 mb-6">Status Transaksi</h2>
                <div class="space-y-4">
                    @php
                        $statusColors = [
                            'pending'    => ['bar' => 'bg-amber-500',  'text' => 'text-amber-700',  'bg' => 'bg-amber-50'],
                            'paid'       => ['bar' => 'bg-blue-500',   'text' => 'text-blue-700',   'bg' => 'bg-blue-50'],
                            'processing' => ['bar' => 'bg-indigo-500', 'text' => 'text-indigo-700', 'bg' => 'bg-indigo-50'],
                            'shipped'    => ['bar' => 'bg-cyan-500',   'text' => 'text-cyan-700',   'bg' => 'bg-cyan-50'],
                            'delivered'  => ['bar' => 'bg-teal-500',   'text' => 'text-teal-700',   'bg' => 'bg-teal-50'],
                            'completed'  => ['bar' => 'bg-emerald-600','text' => 'text-emerald-700','bg' => 'bg-emerald-50'],
                            'cancelled'  => ['bar' => 'bg-rose-500',   'text' => 'text-rose-700',   'bg' => 'bg-rose-50'],
                            'returned'   => ['bar' => 'bg-orange-500', 'text' => 'text-orange-700', 'bg' => 'bg-orange-50'],
                            'received'   => ['bar' => 'bg-green-600',  'text' => 'text-green-700',  'bg' => 'bg-green-50'],
                        ];
                    @endphp
                    @foreach($statusSummary as $status => $count)
                        @php
                            $color = $statusColors[strtolower($status)] ?? ['bar' => 'bg-slate-400', 'text' => 'text-slate-600', 'bg' => 'bg-slate-50'];
                            $pct = max(8, min(100, ($count / max(1, $orders->count())) * 100));
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1.5">
                                <span class="font-bold {{ $color['text'] }} capitalize">{{ $status }}</span>
                                <span class="inline-flex items-center gap-1 {{ $color['text'] }} {{ $color['bg'] }} rounded-full px-2.5 py-0.5 text-xs font-extrabold">{{ $count }}</span>
                            </div>
                            <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-2.5 rounded-full {{ $color['bar'] }} transition-all duration-700" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    @if($statusSummary->isEmpty())
                        <div class="admin-empty py-8">Belum ada status transaksi.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ======================== TABLES: TRANSAKSI + PRODUK ======================== --}}
        <div class="grid gap-6 xl:grid-cols-2">
            <div class="premium-glass-card overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-slate-800">Aktivitas Transaksi</h2>
                    <span class="text-[11px] font-bold text-slate-400">8 terbaru</span>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Pembeli</th>
                                <th>Produk</th>
                                <th>Status</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders->take(8) as $order)
                                @php
                                    $sc = $statusColors[strtolower($order->status)] ?? ['bar' => 'bg-slate-400', 'text' => 'text-slate-600', 'bg' => 'bg-slate-50'];
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold shrink-0">
                                                {{ strtoupper(substr($order->buyer->name ?? '-', 0, 1)) }}
                                            </div>
                                            <span class="font-semibold text-slate-700 truncate max-w-[120px]">{{ $order->buyer->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-slate-600 text-xs max-w-[150px] truncate">{{ $order->details->pluck('product.name')->filter()->implode(', ') ?: '-' }}</td>
                                    <td>
                                        <span class="inline-flex items-center gap-1 {{ $sc['text'] }} {{ $sc['bg'] }} rounded-full px-2.5 py-1 text-[11px] font-bold capitalize ring-1 ring-black/5">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="text-right font-bold text-slate-800 tabular-nums">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4"><div class="admin-empty py-8">Belum ada transaksi.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="premium-glass-card overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-slate-800">Produk Terbaru</h2>
                    <span class="text-[11px] font-bold text-slate-400">12 terbaru</span>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Seller / Toko</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                @php
                                    $pStatus = strtolower($product->status ?? 'pending');
                                    $pBadge = match($pStatus) {
                                        'approved' => 'text-emerald-700 bg-emerald-50 ring-emerald-200/60',
                                        'rejected' => 'text-rose-700 bg-rose-50 ring-rose-200/60',
                                        'flagged' => 'text-amber-700 bg-amber-50 ring-amber-200/60',
                                        default => 'text-slate-600 bg-slate-50 ring-slate-200/60',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                                                <i data-lucide="package" class="w-3.5 h-3.5 text-slate-400"></i>
                                            </div>
                                            <span class="font-semibold text-slate-700 truncate max-w-[150px]">{{ $product->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-slate-600 text-xs truncate max-w-[120px]">{{ $product->store?->nama_toko ?? $product->owner?->name ?? '-' }}</td>
                                    <td>
                                        <span class="inline-flex items-center gap-1 {{ $pBadge }} rounded-full px-2.5 py-1 text-[11px] font-bold capitalize ring-1">
                                            {{ $product->status ?? 'pending' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3"><div class="admin-empty py-8">Belum ada produk terbaru.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ======================== LAPORAN + PELANGGARAN ======================== --}}
        <div class="grid gap-6 xl:grid-cols-2">
            {{-- Laporan Sistem --}}
            <div class="premium-glass-card overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                            <i data-lucide="flag" class="w-4 h-4"></i>
                        </div>
                        <h2 class="text-lg font-extrabold text-slate-800">Laporan Sistem</h2>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-600 ring-1 ring-rose-200/60">
                        {{ $reports->count() }} laporan
                    </span>
                </div>
                <div class="divide-y divide-slate-100 max-h-[520px] overflow-y-auto">
                    @forelse($reports->take(10) as $report)
                        <div class="px-6 py-4 hover:bg-slate-50/50 transition-colors">
                            {{-- Header: Type + Status --}}
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                <div class="flex items-center gap-2">
                                    @php
                                        $typeBadge = match($report->type) {
                                            'product' => 'text-amber-700 bg-amber-50 ring-amber-200/60',
                                            'store'   => 'text-blue-700 bg-blue-50 ring-blue-200/60',
                                            'chat'    => 'text-purple-700 bg-purple-50 ring-purple-200/60',
                                            default   => 'text-rose-700 bg-rose-50 ring-rose-200/60',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 {{ $typeBadge }} rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-wider ring-1">
                                        {{ $report->type ?? 'seller' }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-500">
                                        <span class="text-slate-400">oleh</span> {{ $report->reporter->name ?? 'User' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($report->status === 'reviewed')
                                        <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 rounded-full px-2.5 py-1 text-[10px] font-bold ring-1 ring-emerald-200/60">
                                            <i data-lucide="check-circle-2" class="w-3 h-3"></i>
                                            Ditinjau
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-amber-700 bg-amber-50 rounded-full px-2.5 py-1 text-[10px] font-bold ring-1 ring-amber-200/60">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            Menunggu
                                        </span>
                                    @endif
                                    <span class="text-[10px] text-slate-400 font-medium">{{ $report->created_at?->diffForHumans() }}</span>
                                </div>
                            </div>

                            {{-- Target Info --}}
                            <div class="mb-2 px-3 py-2 bg-slate-50 rounded-lg text-xs font-medium text-slate-500 border border-slate-100">
                                @if($report->type === 'store')
                                    <i data-lucide="store" class="w-3 h-3 inline -mt-0.5 text-blue-500"></i>
                                    Toko: <span class="font-bold text-slate-800">{{ $report->store->nama_toko ?? 'Toko #'.$report->store_id }}</span>
                                @elseif($report->type === 'product')
                                    <i data-lucide="package" class="w-3 h-3 inline -mt-0.5 text-amber-500"></i>
                                    Produk: <span class="font-bold text-slate-800">{{ $report->product->name ?? 'Produk #'.$report->product_id }}</span>
                                @elseif($report->type === 'chat')
                                    <i data-lucide="message-circle" class="w-3 h-3 inline -mt-0.5 text-purple-500"></i>
                                    Chat dengan: <span class="font-bold text-slate-800">{{ $report->seller->name ?? 'Seller #'.$report->seller_id }}</span>
                                @else
                                    <i data-lucide="user" class="w-3 h-3 inline -mt-0.5 text-rose-500"></i>
                                    Seller: <span class="font-bold text-slate-800">{{ $report->seller->name ?? 'Seller #'.$report->seller_id }}</span>
                                @endif
                            </div>

                            {{-- Reason --}}
                            <p class="font-bold text-slate-800 text-sm">{{ $report->reason }}</p>
                            @if($report->description)
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $report->description }}</p>
                            @endif

                            {{-- Action Button --}}
                            @if($report->status !== 'reviewed')
                                <div class="flex justify-end mt-3 pt-3 border-t border-slate-100">
                                    <button onclick="openActionModal('{{ $report->seller_id }}', '{{ $report->id }}', '{{ $report->product_id ?? '' }}', '{{ addslashes($report->seller->name ?? 'Seller') }}')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-sm shadow-rose-500/10">
                                        <i data-lucide="shield-alert" class="w-3.5 h-3.5"></i>
                                        Ambil Tindakan
                                    </button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-100 mx-auto flex items-center justify-center mb-3">
                                <i data-lucide="inbox" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-400">Tidak ada laporan masuk</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Pelanggaran Seller --}}
            <div class="premium-glass-card overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        </div>
                        <h2 class="text-lg font-extrabold text-slate-800">Pelanggaran Seller</h2>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700 ring-1 ring-amber-200/60">
                        {{ $violations->count() }} record
                    </span>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Seller</th>
                                <th>Aksi</th>
                                <th class="text-center">Strike</th>
                                <th>Alasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($violations as $violation)
                                @php
                                    $vBadge = match($violation->action) {
                                        'warning' => 'text-amber-700 bg-amber-50 ring-amber-200/60',
                                        'suspend' => 'text-orange-700 bg-orange-50 ring-orange-200/60',
                                        'ban' => 'text-rose-700 bg-rose-50 ring-rose-200/60',
                                        default => 'text-slate-600 bg-slate-50 ring-slate-200/60',
                                    };
                                    $strikeColor = $violation->strike_count >= 5 ? 'text-rose-700 bg-rose-100' : ($violation->strike_count >= 3 ? 'text-orange-700 bg-orange-100' : 'text-amber-700 bg-amber-100');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0">
                                                {{ strtoupper(substr($violation->seller?->name ?? '-', 0, 1)) }}
                                            </div>
                                            <span class="font-semibold text-slate-700 truncate max-w-[100px]">{{ $violation->seller?->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="inline-flex items-center gap-1 {{ $vBadge }} rounded-full px-2.5 py-1 text-[11px] font-bold capitalize ring-1">
                                            {{ $violation->action }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full {{ $strikeColor }} text-xs font-extrabold">
                                            {{ $violation->strike_count }}
                                        </span>
                                    </td>
                                    <td class="text-xs text-slate-600 max-w-[180px] truncate">{{ $violation->reason }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4">
                                    <div class="py-8 text-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 mx-auto flex items-center justify-center mb-3">
                                            <i data-lucide="shield-check" class="w-5 h-5 text-emerald-400"></i>
                                        </div>
                                        <p class="text-sm font-bold text-slate-400">Belum ada pelanggaran</p>
                                    </div>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Modal -->
    <div id="actionModal" class="fixed inset-0 hidden items-center justify-center bg-slate-900/40 backdrop-blur-sm z-50 p-4 transition-all">
        <div class="bg-white rounded-2xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] w-full max-w-md border border-slate-100 overflow-hidden animate-[fadeInUp_0.3s_ease-out]">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                        <i data-lucide="shield-alert" class="w-5 h-5 text-rose-600"></i>
                        <span>Tindak Pelanggaran Seller</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Beri warning, suspend, atau ban terhadap seller ini.</p>
                </div>
                <button onclick="closeActionModal()" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition-colors">&times;</button>
            </div>
            <form id="actionForm" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="report_id" id="modalReportId">
                <input type="hidden" name="product_id" id="modalProductId">
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Seller Terlapor</label>
                    <input type="text" id="modalSellerName" readonly class="admin-form-control bg-slate-50 rounded-xl border-slate-200 text-slate-600 outline-none cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Aksi Tindakan</label>
                    <select name="action" required class="admin-form-control rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20">
                        <option value="warning">Peringatan (Warning Strike)</option>
                        <option value="suspend">Tangguhkan Toko (Suspend)</option>
                        <option value="ban">Blokir Permanen (Ban/Block)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alasan & Catatan Pelanggaran</label>
                    <textarea name="reason" required rows="3" placeholder="Tulis catatan peninjauan dan alasan hukuman..." class="admin-form-control rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 py-3" style="min-height: 80px;"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeActionModal()" class="admin-button admin-button-ghost rounded-xl px-5">Batal</button>
                    <button type="submit" class="admin-button admin-button-danger bg-rose-600 hover:bg-rose-700 border-none rounded-xl px-6 shadow-md shadow-rose-500/20">Kirim Hukuman</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
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
                        backgroundColor: '#059669',
                        borderRadius: 6,
                        barPercentage: 0.6
                    },
                    {
                        label: 'Produk',
                        data: @json($productActivity),
                        backgroundColor: '#0ea5e9',
                        borderRadius: 6,
                        barPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 16,
                            font: { size: 11, weight: 'bold' }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: '600' }, color: '#94a3b8' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 11 }, color: '#94a3b8' }
                    }
                }
            }
        });

        function openActionModal(sellerId, reportId, productId, sellerName) {
            const modal = document.getElementById('actionModal');
            const form = document.getElementById('actionForm');
            const modalReportId = document.getElementById('modalReportId');
            const modalProductId = document.getElementById('modalProductId');
            const modalSellerName = document.getElementById('modalSellerName');

            form.action = `/admin/monitoring/sellers/${sellerId}/action`;
            modalReportId.value = reportId || '';
            modalProductId.value = productId || '';
            modalSellerName.value = sellerName || '';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            lucide.createIcons();
        }

        function closeActionModal() {
            const modal = document.getElementById('actionModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
@endsection
