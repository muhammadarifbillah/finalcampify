@extends('admin.reports.layout')

@section('title', 'Laporan Data Pengguna - CAMPIFY')
@section('report_title', 'Laporan Data Pengguna')

@section('content')
    {{-- Summary Cards --}}
    <table class="stat-boxes" cellpadding="0" cellspacing="8">
        <tr>
            <td class="stat-box" width="25%">
                <div class="stat-value">{{ count($users) }}</div>
                <div class="stat-label">Total Pengguna</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value" style="color: #0284c7;">{{ $users->where('role', 'buyer')->count() }}</div>
                <div class="stat-label">Pembeli (Buyer)</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value" style="color: #059669;">{{ $users->where('role', 'seller')->count() }}</div>
                <div class="stat-label">Penjual (Seller)</div>
            </td>
            <td class="stat-box" width="25%">
                <div class="stat-value" style="color: #ea580c;">{{ $users->whereNotNull('ktp_image')->whereNull('ktp_verified_at')->count() }}</div>
                <div class="stat-label">Pending KYC</div>
            </td>
        </tr>
    </table>

    {{-- Main Data Table --}}
    <div class="section-title">Daftar Pengguna Platform</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="20%">Nama & Email</th>
                <th width="10%">Role</th>
                <th width="10%">Kota</th>
                <th width="12%">No. Telepon</th>
                <th width="13%">KYC Status</th>
                <th width="10%" class="text-center">Jml Order</th>
                <th width="20%">Terdaftar Pada</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
                <tr>
                    <td class="text-bold">#{{ $u->id }}</td>
                    <td>
                        <span class="text-bold">{{ $u->name ?? 'N/A' }}</span><br>
                        <span style="font-size: 6.5pt; color: #64748b;">{{ $u->email }}</span>
                    </td>
                    <td>
                        @if($u->role === 'seller')
                            <span class="badge badge-green">Seller</span>
                        @elseif($u->role === 'admin')
                            <span class="badge badge-red">Admin</span>
                        @else
                            <span class="badge badge-blue">Buyer</span>
                        @endif
                    </td>
                    <td>{{ $u->city ?? '-' }}</td>
                    <td>{{ $u->phone ?? '-' }}</td>
                    <td>
                        @if($u->ktp_verified_at)
                            <span class="badge badge-green" style="font-size: 6.5pt;">Verified</span>
                            <div style="font-size: 5.5pt; color: #64748b; margin-top: 2px;">
                                {{ \Carbon\Carbon::parse($u->ktp_verified_at)->format('d/m/Y') }}
                            </div>
                        @elseif($u->ktp_image)
                            <span class="badge badge-yellow" style="font-size: 6.5pt;">Pending KYC</span>
                        @else
                            <span class="badge badge-gray" style="font-size: 6.5pt;">Unverified</span>
                        @endif
                    </td>
                    <td class="text-center text-bold">{{ $u->orders_count ?? 0 }}</td>
                    <td>
                        {{ $u->created_at ? $u->created_at->format('d/m/Y H:i') : '-' }}
                        @if($u->is_fraud)
                            <div class="text-red text-bold" style="font-size: 6pt; text-transform: uppercase;">⚠️ INDIKASI FRAUD</div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #64748b;">
                        Tidak ada data pengguna.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
