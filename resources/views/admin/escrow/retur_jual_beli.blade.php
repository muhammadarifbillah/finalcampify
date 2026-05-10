<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "tertiary-container": "#ba5551",
                        "surface-variant": "#dee4de",
                        "on-tertiary-fixed": "#410004",
                        "secondary": "#0051d5",
                        "inverse-surface": "#2c322e",
                        "primary-fixed-dim": "#68dba9",
                        "surface-dim": "#d5dcd6",
                        "primary": "#006948",
                        "on-tertiary-fixed-variant": "#7f2928",
                        "on-surface": "#171d19",
                        "outline-variant": "#bccac0",
                        "on-secondary-fixed-variant": "#003ea8",
                        "inverse-on-surface": "#ecf2ec",
                        "tertiary-fixed": "#ffdad7",
                        "surface-container-high": "#e4eae4",
                        "surface": "#f5fbf5",
                        "error-container": "#ffdad6",
                        "primary-container": "#00855d",
                        "on-primary": "#ffffff",
                        "tertiary": "#9b3e3b",
                        "outline": "#6d7a72",
                        "secondary-fixed-dim": "#b4c5ff",
                        "on-secondary": "#ffffff",
                        "on-primary-fixed-variant": "#005137",
                        "secondary-fixed": "#dbe1ff",
                        "surface-container": "#e9efe9",
                        "on-primary-fixed": "#002114",
                        "tertiary-fixed-dim": "#ffb3ae",
                        "on-error-container": "#93000a",
                        "surface-container-lowest": "#ffffff",
                        "on-secondary-fixed": "#00174b",
                        "primary-fixed": "#85f8c4",
                        "on-background": "#171d19",
                        "surface-container-highest": "#dee4de",
                        "on-tertiary-container": "#fffbff",
                        "on-primary-container": "#f5fff7",
                        "background": "#f5fbf5",
                        "surface-container-low": "#eff5ef",
                        "on-error": "#ffffff",
                        "surface-tint": "#006c4a",
                        "on-secondary-container": "#fefcff",
                        "secondary-container": "#316bf3",
                        "on-tertiary": "#ffffff",
                        "surface-bright": "#f5fbf5",
                        "inverse-primary": "#68dba9",
                        "error": "#ba1a1a",
                        "on-surface-variant": "#3d4a42"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "xs": "4px",
                        "xl": "32px",
                        "md": "16px",
                        "base": "8px",
                        "sm": "12px",
                        "lg": "24px",
                        "margin": "32px",
                        "gutter": "24px"
                    },
                    "fontFamily": {
                        "stats-number": ["Inter"],
                        "h3": ["Inter"],
                        "body-lg": ["Inter"],
                        "h2": ["Inter"],
                        "body-md": ["Inter"],
                        "label-bold": ["Inter"],
                        "h1": ["Inter"],
                        "body-sm": ["Inter"]
                    },
                    "fontSize": {
                        "stats-number": ["36px", { "lineHeight": "44px", "fontWeight": "700" }],
                        "h3": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
                        "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }],
                        "h2": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "label-bold": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600" }],
                        "h1": ["30px", { "lineHeight": "38px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "body-sm": ["14px", { "lineHeight": "20px", "fontWeight": "400" }]
                    }
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

@php
    $adminName = auth()->user()->name ?? 'Campify Admin';

    $monthMap = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    $formatDateTime = function ($date) use ($monthMap) {
        if (!$date) return '-';
        $d = \Illuminate\Support\Carbon::parse($date);
        $m = $monthMap[(int) $d->format('n')] ?? $d->format('M');
        return $d->format('d') . ' ' . $m . ' ' . $d->format('Y') . ', ' . $d->format('H:i');
    };

    $formatReturnId = fn ($id) => '#RET-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT);
    $formatOrderId = fn ($id) => 'ORD-' . str_pad((string) $id, 8, '0', STR_PAD_LEFT);

    $statusLabel = function ($status) {
        return match ($status) {
            'dispute' => 'MEDIATION',
            'pending' => 'PENDING',
            'checking' => 'APPROVED',
            'completed' => 'COMPLETED',
            'rejected' => 'REJECTED',
            default => strtoupper((string) $status),
        };
    };
@endphp

<body class="bg-background text-on-background min-h-screen">
    <!-- SideNavBar -->
    <aside class="h-screen w-64 fixed left-0 top-0 flex flex-col bg-surface border-r border-outline-variant shadow-sm z-50">
        <div class="px-lg py-xl">
            <h1 class="font-h2 text-h2 font-bold text-primary">Campify Admin</h1>
            <p class="text-body-sm text-on-surface-variant">Marketplace Escrow</p>
        </div>

        <nav class="flex-1 px-md space-y-xs overflow-y-auto">
            <a class="flex items-center gap-md px-lg py-md text-on-surface-variant font-body-md hover:bg-surface-container-high transition-colors rounded-lg active:scale-95 duration-150 ease-in-out"
                href="{{ route('admin.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span>Beranda</span>
            </a>
            <a class="flex items-center gap-md px-lg py-md text-on-surface-variant font-body-md hover:bg-surface-container-high transition-colors rounded-lg active:scale-95 duration-150 ease-in-out"
                href="{{ route('admin.orders.index') }}">
                <span class="material-symbols-outlined">shopping_cart</span>
                <span>Pembelian</span>
            </a>
            <a class="flex items-center gap-md px-lg py-md text-on-surface-variant font-body-md hover:bg-surface-container-high transition-colors rounded-lg active:scale-95 duration-150 ease-in-out"
                href="#">
                <span class="material-symbols-outlined">calendar_month</span>
                <span>Penyewaan</span>
            </a>
            <a class="flex items-center gap-md px-lg py-md bg-primary-container text-on-primary-container rounded-lg font-label-bold active:scale-95 duration-150 ease-in-out"
                href="#">
                <span class="material-symbols-outlined">gavel</span>
                <span>Pusat Resolusi</span>
            </a>

            <!-- Sub-menu focus -->
            <div class="ml-xl mt-xs flex flex-col gap-xs">
                <a class="px-lg py-sm text-primary font-label-bold bg-surface-container-high rounded-lg"
                    href="{{ route('admin.escrow.returns.jual_beli.index') }}">Retur Jual-Beli</a>
                <a class="px-lg py-sm text-on-surface-variant font-body-sm hover:bg-surface-container-high rounded-lg transition-colors"
                    href="{{ route('admin.escrow.returns.sewa.index') }}">Retur Sewa</a>
            </div>
        </nav>

        <div class="mt-auto px-md py-lg border-t border-outline-variant">
            <div class="flex items-center gap-md px-lg py-md mb-md">
                <div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center text-on-secondary-container">
                    <span class="material-symbols-outlined">person</span>
                </div>
                <div>
                    <p class="font-label-bold text-on-surface">Admin Utama</p>
                    <p class="text-xs text-on-surface-variant">Mode Hakim Aktif</p>
                </div>
            </div>
            <a class="flex items-center gap-md px-lg py-md text-on-surface-variant font-body-md hover:bg-surface-container-high transition-colors rounded-lg"
                href="{{ route('admin.settings') }}">
                <span class="material-symbols-outlined">settings</span>
                <span>Pengaturan</span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button
                    class="w-full flex items-center gap-md px-lg py-md text-on-surface-variant font-body-md hover:bg-surface-container-high transition-colors rounded-lg text-left"
                    type="submit">
                    <span class="material-symbols-outlined">logout</span>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- TopNavBar -->
    <header class="ml-64 flex justify-between items-center h-16 px-xl w-[calc(100%-16rem)] bg-surface shadow-sm sticky top-0 z-40">
        <div class="flex items-center gap-lg flex-1">
            <div class="relative w-full max-w-md">
                <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                <input class="w-full bg-surface-container border-none rounded-full py-sm pl-xl pr-md text-body-sm focus:ring-2 focus:ring-primary"
                    placeholder="Cari ID Retur atau Pesanan..." type="text" />
            </div>
        </div>
        <div class="flex items-center gap-md">
            <button class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container-high transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">notifications</span>
            </button>
            <button class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container-high transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">help</span>
            </button>
            <div class="w-px h-6 bg-outline-variant mx-sm"></div>
            <div class="flex items-center gap-sm">
                <span class="text-body-sm font-semibold text-on-surface">{{ $adminName }}</span>
                <span class="material-symbols-outlined text-on-surface-variant">account_circle</span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="ml-64 p-margin">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="mb-xl flex justify-between items-end">
                <div>
                    <h2 class="font-h1 text-h1 text-on-surface mb-xs">Retur Jual-Beli</h2>
                    <p class="text-body-md text-on-surface-variant">Kelola permintaan pengembalian dana dan barang dari transaksi marketplace.</p>
                </div>
                <div class="flex gap-sm">
                    <a href="#"
                        class="bg-primary text-on-primary px-lg py-sm rounded-lg font-label-bold flex items-center gap-xs hover:opacity-90 active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        Export Laporan
                    </a>
                </div>
            </div>

            <!-- Dashboard Stats Bar -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-gutter mb-xl">
                <div class="bg-surface p-lg rounded-xl shadow-sm border border-outline-variant">
                    <p class="text-label-bold text-on-surface-variant uppercase tracking-wider mb-sm">Total Permintaan</p>
                    <div class="flex items-end justify-between">
                        <span class="font-stats-number text-stats-number text-on-surface">{{ number_format($summary['total'] ?? 0) }}</span>
                        <span class="text-primary flex items-center font-semibold text-sm">
                            <span class="material-symbols-outlined">trending_up</span> {{ $summary['growth'] ?? '0' }}%
                        </span>
                    </div>
                </div>
                <div class="bg-surface p-lg rounded-xl shadow-sm border border-outline-variant">
                    <p class="text-label-bold text-on-surface-variant uppercase tracking-wider mb-sm">Butuh Mediasi</p>
                    <div class="flex items-end justify-between">
                        <span class="font-stats-number text-stats-number text-tertiary">{{ number_format($summary['mediation'] ?? 0) }}</span>
                        <span class="bg-tertiary-fixed text-on-tertiary-fixed-variant px-sm py-xs rounded-full text-[10px] font-bold">URGENT</span>
                    </div>
                </div>
                <div class="bg-surface p-lg rounded-xl shadow-sm border border-outline-variant">
                    <p class="text-label-bold text-on-surface-variant uppercase tracking-wider mb-sm">Escrow Tertahan</p>
                    <div class="flex items-end justify-between">
                        <span class="font-stats-number text-stats-number text-on-surface">Rp{{ number_format((int) ($summary['escrow_held'] ?? 0)) }}</span>
                        <span class="text-on-surface-variant text-sm">Active Pool</span>
                    </div>
                </div>
                <div class="bg-surface p-lg rounded-xl shadow-sm border border-outline-variant">
                    <p class="text-label-bold text-on-surface-variant uppercase tracking-wider mb-sm">Avg. Resolusi</p>
                    <div class="flex items-end justify-between">
                        <span class="font-stats-number text-stats-number text-on-surface">{{ $summary['avg_resolution_days'] ?? '0' }}d</span>
                        <span class="text-primary-container font-semibold text-sm">Efficient</span>
                    </div>
                </div>
            </div>

            <!-- Filters Area -->
            <div class="bg-surface-container-low p-lg rounded-xl mb-gutter border border-outline-variant">
                <div class="grid grid-cols-4 items-end gap-lg">
                    <div class="flex flex-col gap-xs">
                        <label class="text-label-bold text-on-surface-variant">Filter Status</label>
                        <select class="bg-surface border-outline-variant rounded-lg py-sm px-md text-body-sm focus:ring-primary focus:border-primary w-full">
                            <option>Semua Status</option>
                            <option>Pending Approval</option>
                            <option>Dalam Mediasi</option>
                            <option>Disetujui</option>
                            <option>Ditolak</option>
                            <option>Selesai</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-xs">
                        <label class="text-label-bold text-on-surface-variant">Rentang Tanggal</label>
                        <div class="flex items-center gap-xs">
                            <input class="bg-surface border-outline-variant rounded-lg py-sm px-md text-body-sm w-full" type="date" />
                            <span class="text-on-surface-variant shrink-0">-</span>
                            <input class="bg-surface border-outline-variant rounded-lg py-sm px-md text-body-sm w-full" type="date" />
                        </div>
                    </div>
                    <div class="flex flex-col gap-xs">
                        <label class="text-label-bold text-on-surface-variant">Tipe Marketplace</label>
                        <div class="flex bg-surface rounded-lg border border-outline-variant p-xs">
                            <button class="flex-1 py-xs px-md rounded-md bg-primary text-on-primary text-xs font-bold">Produk Fisik</button>
                            <button class="flex-1 py-xs px-md rounded-md text-on-surface-variant text-xs hover:bg-surface-container transition-colors">Layanan</button>
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button class="w-full h-[42px] border border-outline text-on-surface font-label-bold rounded-lg hover:bg-surface-variant transition-colors">
                            Reset Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Interactive Table -->
            <div class="bg-surface rounded-xl shadow-sm border border-outline-variant overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container-high border-b border-outline-variant">
                                <th class="px-lg py-md text-label-bold text-on-surface-variant uppercase">ID Retur</th>
                                <th class="px-lg py-md text-label-bold text-on-surface-variant uppercase">ID Pesanan</th>
                                <th class="px-lg py-md text-label-bold text-on-surface-variant uppercase">Penjual / Pembeli</th>
                                <th class="px-lg py-md text-label-bold text-on-surface-variant uppercase text-right">Total Escrow</th>
                                <th class="px-lg py-md text-label-bold text-on-surface-variant uppercase text-center">Status</th>
                                <th class="px-lg py-md text-label-bold text-on-surface-variant uppercase text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/50">
                            @forelse($returns as $item)
                                @php
                                    $order = $item->order;
                                    $buyerName = $order?->user?->name ?? '-';
                                    $storeName = $order?->details?->first()?->product?->store?->nama_toko ?? '-';

                                    $badgeClass = match ($item->status) {
                                        'dispute' => 'bg-tertiary-container/10 text-tertiary border-tertiary/20',
                                        'pending' => 'bg-secondary-container/10 text-secondary border-secondary/20',
                                        'checking' => 'bg-primary-container/10 text-primary border-primary/20',
                                        'rejected' => 'bg-error-container text-error border-error/20',
                                        'completed' => 'bg-surface-variant text-on-surface-variant border-outline-variant',
                                        default => 'bg-surface-variant text-on-surface-variant border-outline-variant',
                                    };
                                @endphp
                                <tr class="hover:bg-surface-container-low transition-colors group">
                                    <td class="px-lg py-lg">
                                        <span class="font-semibold text-primary">{{ $formatReturnId($item->id) }}</span>
                                        <p class="text-[10px] text-on-surface-variant mt-1">{{ $formatDateTime($item->created_at) }}</p>
                                    </td>
                                    <td class="px-lg py-lg text-body-sm">{{ $formatOrderId($item->order_id) }}</td>
                                    <td class="px-lg py-lg">
                                        <div class="flex flex-col">
                                            <span class="text-body-sm font-semibold text-on-surface">{{ $storeName }}</span>
                                            <span class="text-xs text-on-surface-variant">{{ $buyerName }}</span>
                                        </div>
                                    </td>
                                    <td class="px-lg py-lg text-right font-stats-number text-lg text-on-surface">Rp{{ number_format((int) $item->escrow_total) }}</td>
                                    <td class="px-lg py-lg text-center">
                                        <span class="px-sm py-xs rounded-full text-[11px] font-bold border {{ $badgeClass }}">
                                            {{ $statusLabel($item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-lg py-lg text-center">
                                        <a class="bg-primary-container text-on-primary-container px-md py-sm rounded-lg text-xs font-bold hover:opacity-80 transition-all"
                                            href="{{ route('admin.escrow.returns.jual_beli.show', $item->id) }}">
                                            Kelola
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-lg py-lg text-center text-on-surface-variant" colspan="6">Tidak ada data retur.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-lg py-md bg-surface-container-low border-t border-outline-variant flex items-center justify-between">
                    <p class="text-body-sm text-on-surface-variant">
                        Menampilkan {{ $returns->firstItem() ?? 0 }}-{{ $returns->lastItem() ?? 0 }} dari {{ number_format($returns->total()) }} permintaan
                    </p>
                    <div class="flex gap-xs">
                        {{ $returns->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>

            <!-- Contextual Help / Insights Card -->
            <div class="mt-xl grid grid-cols-1 md:grid-cols-2 gap-gutter">
                <div class="bg-primary/5 border border-primary/20 p-lg rounded-xl flex gap-lg">
                    <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center text-on-primary shrink-0">
                        <span class="material-symbols-outlined">gavel</span>
                    </div>
                    <div>
                        <h4 class="font-h3 text-on-primary-fixed-variant mb-xs">Panduan Mediasi Hakim</h4>
                        <p class="text-body-sm text-on-primary-fixed-variant/80 mb-md">
                            Pastikan untuk meninjau bukti foto dan video dari kedua belah pihak sebelum memberikan keputusan akhir pengembalian dana.
                        </p>
                        <a class="text-primary font-label-bold flex items-center gap-xs" href="#">
                            Buka Knowledge Base
                            <span class="material-symbols-outlined text-sm">open_in_new</span>
                        </a>
                    </div>
                </div>
                <div class="relative overflow-hidden group rounded-xl">
                    <img class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAXCJ7E_VAbxUpENyxgakXTPrEfZAogFGsSdkv1Ke3mYhGZZPGm39NHOKNSxU0MumW-pJPyPmfI0JoLk1BLPUWgqvaA3xPfW7qW13_s71r3K1AWMhzrbJ6rH--cWtG357b8XfVM4y17Y3scMWbeV3Cm23J-96BSE2xsKN0zNgkJM_JxszX4RMsiX5YFqwt49dg7XaPLGKxOB0VtHJ_vaPI1Bui_UeNgn33TYmcU4aGlj_VnU9MXqBLfL1FSOKVT9BbCnOO-xq5WqhQa" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end p-lg">
                        <div>
                            <p class="text-white font-h3 mb-xs">Statistik Logistik</p>
                            <p class="text-white/80 text-body-sm">94% retur berhasil diselesaikan dalam waktu kurang dari 3 hari bulan ini.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>

