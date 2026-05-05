@extends('layouts.admin')

@section('content')

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Monitoring Transaksi</h1>
                <p class="text-gray-600">Pantau performa transaksi dan pendapatan sistem Campify.</p>
            </div>
        </div>

        <!-- 🔥 SUMMARY CARD -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div class="bg-white p-5 rounded-2xl shadow">
                <p class="text-gray-500 text-sm">Total Transaksi</p>
                <h2 class="text-2xl font-bold">{{ $transactions->count() }}</h2>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow">
                <p class="text-gray-500 text-sm">Total Pendapatan</p>
                <h2 class="text-2xl font-bold">
                    Rp {{ number_format($transactions->sum('total'), 0, ',', '.') }}
                </h2>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow">
                <p class="text-gray-500 text-sm">Fee Admin (3%)</p>
                <h2 class="text-2xl font-bold text-green-600">
                    Rp {{ number_format($transactions->sum('total') * 0.03, 0, ',', '.') }}
                </h2>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow">
                <p class="text-gray-500 text-sm">Transaksi Hari Ini</p>
                <h2 class="text-2xl font-bold text-blue-600">
                    {{ $transactions->where('created_at', '>=', now()->startOfDay())->count() }}
                </h2>
            </div>

        </div>

        <!-- 🔥 TABEL TRANSAKSI -->
        <div class="bg-white p-6 rounded-3xl shadow">
            <h2 class="text-lg font-semibold mb-4">Riwayat Transaksi</h2>

            @if($transactions->isEmpty())
                <p class="text-gray-500">Belum ada transaksi yang tercatat.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3">User</th>
                                <th>Produk</th>
                                <th>Total</th>
                                <th>Fee Admin</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $t)
                                <tr class="border-b hover:bg-gray-50">

                                    <td class="p-3">
                                        {{ optional($t->user)->name ?? 'User #' . $t->user_id }}
                                    </td>

                                    <td>
                                        {{ optional($t->product)->name ?? 'Product #' . $t->product_id }}
                                    </td>

                                    <td>
                                        Rp {{ number_format($t->total, 0, ',', '.') }}
                                    </td>

                                    <!-- 🔥 FEE 3% -->
                                    <td class="text-green-600 font-semibold">
                                        Rp {{ number_format($t->total * 0.03, 0, ',', '.') }}
                                    </td>

                                    <!-- 🔥 STATUS (kalau ada field) -->
                                    <td>
                                        <span
                                            class="px-2 py-1 rounded text-xs 
                                                    {{ $t->status == 'success' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $t->status ?? 'success' }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $t->created_at ? $t->created_at->format('d M Y H:i') : '-' }}
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-white p-6 rounded-3xl shadow">
            <h2 class="text-lg font-semibold mb-4">Laporan Buyer</h2>

            @if(session('success'))
                <div class="mb-4 rounded-xl bg-green-50 border border-green-200 p-4 text-green-800">{{ session('success') }}</div>
            @endif

            @if($reports->isEmpty())
                <p class="text-gray-500">Belum ada laporan seller.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3">Buyer</th>
                                <th>Seller</th>
                                <th>Produk</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr class="border-b align-top">
                                    <td class="p-3">{{ $report->reporter?->name ?? '-' }}</td>
                                    <td>{{ $report->seller?->name ?? '-' }}</td>
                                    <td>{{ $report->product?->name ?? '-' }}</td>
                                    <td>
                                        <div class="font-semibold">{{ $report->reason }}</div>
                                        <div class="text-sm text-gray-500">{{ $report->description }}</div>
                                    </td>
                                    <td>{{ $report->status }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.monitoring.seller.action', $report->seller_id) }}" class="flex flex-col gap-2 min-w-48">
                                            @csrf
                                            <input type="hidden" name="report_id" value="{{ $report->id }}">
                                            <input type="hidden" name="product_id" value="{{ $report->product_id }}">
                                            <select name="action" class="border rounded-lg p-2 text-sm">
                                                <option value="warning">Warning</option>
                                                <option value="suspend">Suspend</option>
                                                <option value="ban">Ban</option>
                                            </select>
                                            <input name="reason" value="{{ $report->reason }}" class="border rounded-lg p-2 text-sm" required>
                                            <button class="bg-slate-900 text-white rounded-lg px-3 py-2 text-sm">Simpan</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-white p-6 rounded-3xl shadow">
            <h2 class="text-lg font-semibold mb-4">Riwayat Pelanggaran Seller</h2>

            @if($violations->isEmpty())
                <p class="text-gray-500">Belum ada pelanggaran.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3">Seller</th>
                                <th>Aksi</th>
                                <th>Strike</th>
                                <th>Sumber</th>
                                <th>Alasan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($violations as $violation)
                                <tr class="border-b">
                                    <td class="p-3">{{ $violation->seller?->name ?? '-' }}</td>
                                    <td>{{ $violation->action }}</td>
                                    <td>{{ $violation->strike_count }}</td>
                                    <td>{{ $violation->source }}</td>
                                    <td>{{ $violation->reason }}</td>
                                    <td>{{ $violation->created_at?->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

@endsection
