@extends('admin.reports.layout')

@section('title', 'Laporan Ringkasan Dashboard - CAMPIFY')
@section('report_title', 'Laporan Ringkasan Dashboard')

@section('content')
    {{-- Stat Boxes --}}
    <table class="stat-boxes" cellpadding="0" cellspacing="8">
        <tr>
            <td class="stat-box" width="25%">
                <div class="stat-value">{{ $userTotal }}</div>
                <div class="stat-label">Total Pengguna</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value">{{ $productTotal }}</div>
                <div class="stat-label">Total Produk</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value">{{ $orderCount }}</div>
                <div class="stat-label">Total Transaksi</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value" style="font-size: 12pt;">Rp {{ number_format($orderRevenue, 0, ',', '.') }}</div>
                <div class="stat-label">Total Revenue</div>
            </td>
        </tr>
    </table>

    {{-- Data Pengguna --}}
    <div class="section-title">Data Pengguna</div>
    <table class="summary-table">
        <tr><td class="label">Total Pengguna</td><td class="value">{{ $userTotal }} Akun</td></tr>
        <tr><td class="label">Pembeli (Buyer)</td><td class="value">{{ $buyers }} Akun</td></tr>
        <tr><td class="label">Penjual (Seller)</td><td class="value">{{ $sellers }} Akun</td></tr>
        <tr><td class="label">Pending KYC (Menunggu Verifikasi)</td><td class="value">{{ $pendingKyc }} Akun</td></tr>
    </table>

    {{-- Data Produk --}}
    <div class="section-title">Data Produk</div>
    <table class="summary-table">
        <tr><td class="label">Total Produk</td><td class="value">{{ $productTotal }} Unit</td></tr>
        <tr><td class="label">Produk Approved</td><td class="value">{{ $productApproved }} Unit</td></tr>
        <tr><td class="label">Produk Waiting / Pending</td><td class="value">{{ $productWaiting }} Unit</td></tr>
        <tr><td class="label">Produk Rejected</td><td class="value">{{ $productRejected }} Unit</td></tr>
        <tr><td class="label">Baru Minggu Ini</td><td class="value">{{ $newProductsThisWeek }} Unit</td></tr>
    </table>

    {{-- Data Transaksi --}}
    <div class="section-title">Data Transaksi</div>
    <table class="summary-table">
        <tr><td class="label">Total Transaksi</td><td class="value">{{ $orderCount }}</td></tr>
        <tr><td class="label">Penyewaan (Rental)</td><td class="value">{{ $rentalCount }}</td></tr>
        <tr><td class="label">Pembelian</td><td class="value">{{ $buyCount }}</td></tr>
        <tr><td class="label">Total Revenue</td><td class="value">Rp {{ number_format($orderRevenue, 0, ',', '.') }}</td></tr>
    </table>

    {{-- Data Escrow & Retur --}}
    <div class="section-title">Data Escrow & Retur</div>
    <table class="summary-table">
        <tr><td class="label">Total Escrow Tertahan</td><td class="value">Rp {{ number_format($totalEscrow, 0, ',', '.') }}</td></tr>
        <tr><td class="label">Jaminan Sewa (Escrow)</td><td class="value">Rp {{ number_format($jaminanSewa, 0, ',', '.') }}</td></tr>
        <tr><td class="label">Retur Status Checking</td><td class="value">{{ $checkingReturns }}</td></tr>
        <tr><td class="label">Retur Status Pending</td><td class="value">{{ $pendingReturns }}</td></tr>
        <tr><td class="label">Overdue (Terlambat)</td><td class="value text-red">{{ $overdueReturns }}</td></tr>
        <tr><td class="label">Total Denda Keterlambatan</td><td class="value">Rp {{ number_format($totalLateFees, 0, ',', '.') }}</td></tr>
        <tr><td class="label">Pendapatan Admin (Fee 10%)</td><td class="value text-green">Rp {{ number_format($adminRevenue, 0, ',', '.') }}</td></tr>
    </table>

    {{-- Data Toko --}}
    <div class="section-title">Data Toko</div>
    <table class="summary-table">
        <tr><td class="label">Total Toko</td><td class="value">{{ $storeTotal }}</td></tr>
        <tr><td class="label">Toko Aktif</td><td class="value">{{ $storeActive }}</td></tr>
        <tr><td class="label">Toko Banned / Diblokir</td><td class="value text-red">{{ $storeBanned }}</td></tr>
    </table>

    {{-- Tren Bulanan --}}
    <div class="section-title">Tren Bulanan (Tahun {{ now()->year }})</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-right">Revenue (Rp)</th>
                <th class="text-center">Pengguna Baru</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyData as $m)
            <tr>
                <td>{{ $m['bulan'] }}</td>
                <td class="text-center">{{ $m['transaksi'] }}</td>
                <td class="text-right">Rp {{ number_format($m['revenue'], 0, ',', '.') }}</td>
                <td class="text-center">{{ $m['pengguna_baru'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
