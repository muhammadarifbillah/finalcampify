@extends('SellerView.layouts.app_seller')

@section('content')
<div class="d-flex" style="min-height:100vh; background:#f9fafb;">

    {{-- SIDEBAR --}}
    <div style="width:260px; background:#ffffff; border-right:1px solid #e5e7eb; display:flex; flex-direction:column; justify-content:space-between;">

        {{-- TOP --}}
        <div>

            {{-- BRAND --}}
            <div class="p-4 border-bottom">
                <h4 style="color:#10B981; font-weight:800; letter-spacing:1px;">CAMPIFY.</h4>
                <small class="text-muted">SELLER HUB</small>
            </div>

            {{-- MENU --}}
            <ul class="nav flex-column px-3 mt-3">

                {{-- DASHBOARD --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}"
                    href="{{ route('seller.dashboard') }}">
                        📊 Dashboard
                    </a>
                </li>

                {{-- PRODUK --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('products*') ? 'active' : '' }}"
                    href="{{ route('seller.products.index') }}">
                        📦 Kelola Produk
                    </a>
                </li>

                {{-- RATING --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('seller.ratings.index') ? 'active' : '' }}"
                    href="/seller/ratings">
                        ⭐ Kelola Rating
                    </a>
                </li>

                {{-- TRANSAKSI (DROPDOWN) --}}
                <li class="nav-item mb-1">

                    <a class="nav-link sidebar-link d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse"
                    href="#transaksiMenu"
                    role="button"
                    aria-expanded="false"
                    aria-controls="transaksiMenu">

                        💰 Transaksi
                        <span class="text-muted">▾</span>

                    </a>

                    <div class="collapse {{ request()->is('seller/orders*') || request()->is('seller/rentals*') ? 'show' : '' }}"
                        id="transaksiMenu">

                        <ul class="nav flex-column ms-3 mt-1">

                            <li class="nav-item">
                                <a class="nav-link sidebar-sub {{ request()->is('seller/orders*') ? 'active' : '' }}"
                                href="/seller/orders">
                                    🧾 Pesanan Baru
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link sidebar-sub {{ request()->is('seller/rentals*') ? 'active' : '' }}"
                                href="/seller/rentals">
                                    🏕️ Penyewaan Alat
                                </a>
                            </li>

                        </ul>

                    </div>
                </li>

                {{-- CHAT --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('chat.index') ? 'active' : '' }}"
                    href="/seller/chat">
                        💬 Chat Pembeli
                    </a>
                </li>

            </ul>
        </div>

        {{-- BOTTOM --}}
        <div class="px-3 pb-4">
            <hr>
            <a class="nav-link sidebar-link {{ request()->routeIs('seller.store-profile*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}" href="{{ route('seller.store-profile.index') }}"">
                👤 Profil Toko
            </a>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="flex-grow-1 p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">UBAH STATUS PENYEWAAAN</h4>
            <a href="/seller/rentals" class="btn btn-light rounded-pill px-3">← Kembali</a>
        </div>

        <div class="card border-0 shadow-sm p-4" style="border-radius:16px;">

            <h5 class="fw-bold mb-1">{{ $rental->user->name ?? '-' }}</h5>
            <p class="text-muted small mb-3">Alat: <strong>{{ optional($rental->product)->nama_produk ?? '-' }}</strong></p>

            <form method="POST" action="/seller/rentals/{{ $rental->id }}">
                @csrf
                @method('PUT')

                <label class="form-label fw-bold">Status Penyewaan</label>
                <select name="status" id="statusSelect" class="form-control mb-3">
                    <option value="pending" {{ $rental->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="confirmed" {{ $rental->status == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="active" {{ $rental->status == 'active' ? 'selected' : '' }}>Aktif (Sedang Disewa)</option>
                    <option value="returned" {{ $rental->status == 'returned' ? 'selected' : '' }}>Dikembalikan Pembeli (Cek Barang)</option>
                    <option value="denda_pending" {{ $rental->status == 'denda_pending' ? 'selected' : '' }}>Menunggu Pembayaran Denda</option>
                    <option value="denda_dibayar" {{ $rental->status == 'denda_dibayar' ? 'selected' : '' }}>Denda Sudah Dibayar (Verifikasi)</option>
                    <option value="completed" {{ $rental->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $rental->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>

                {{-- FORM INPUT PENGEMBALIAN (Hanya muncul jika status tertentu) --}}
                <div id="returnFields" style="{{ in_array($rental->status, ['returned', 'denda_pending', 'denda_dibayar', 'completed']) ? '' : 'display:none;' }}">
                    <div class="p-3 bg-light rounded border mb-3">
                        <p class="small fw-bold text-muted mb-2 uppercase">Informasi Pengembalian</p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Kondisi Barang</label>
                            <select name="kondisi_barang" class="form-control">
                                <option value="baik" {{ optional($rental->returnRequest)->kondisi_barang == 'baik' ? 'selected' : '' }}>Baik / Normal</option>
                                <option value="rusak" {{ optional($rental->returnRequest)->kondisi_barang == 'rusak' ? 'selected' : '' }}>Rusak (Perlu Perbaikan)</option>
                                <option value="hilang" {{ optional($rental->returnRequest)->kondisi_barang == 'hilang' ? 'selected' : '' }}>Hilang / Ganti Rugi</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Denda Keterlambatan (Otomatis)</label>
                            <div class="p-2 bg-white border rounded">
                                <span class="text-danger fw-bold">Rp {{ number_format($dendaTelat) }}</span>
                                <small class="text-muted ms-2">({{ $daysLate }} Hari Terlambat)</small>
                            </div>
                            <input type="hidden" name="denda_telat" value="{{ $dendaTelat }}">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small">Denda Kerusakan / Hilang (Rp)</label>
                            <input type="number" name="denda_kerusakan" id="dendaKerusakan" class="form-control" value="{{ max(0, (optional($rental->returnRequest)->denda ?? 0) - $dendaTelat) }}" placeholder="0">
                            <small class="text-muted italic">Isi jika ada kerusakan barang.</small>
                        </div>

                        <div class="mt-3 pt-2 border-top">
                            <label class="form-label fw-bold small d-block">Total Denda Keseluruhan</label>
                            <h5 class="fw-bold text-danger mb-0" id="totalDendaText">Rp {{ number_format($dendaTelat + max(0, (optional($rental->returnRequest)->denda ?? 0) - $dendaTelat)) }}</h5>
                        </div>
                    </div>
                </div>

                <label class="form-label fw-bold">Catatan Internal</label>
                <textarea name="catatan" class="form-control mb-3" rows="3" placeholder="Tambahkan catatan jika diperlukan">{{ $rental->catatan ?? '' }}</textarea>

                <script>
                    const statusSelect = document.getElementById('statusSelect');
                    const returnFields = document.getElementById('returnFields');
                    const dendaTelat = {{ $dendaTelat }};
                    const dendaKerusakanInput = document.getElementById('dendaKerusakan');
                    const totalDendaText = document.getElementById('totalDendaText');

                    // Handle visibility
                    statusSelect.addEventListener('change', function() {
                        const val = this.value;
                        if(['returned', 'denda_pending', 'denda_dibayar', 'completed'].includes(val)) {
                            returnFields.style.display = 'block';
                        } else {
                            returnFields.style.display = 'none';
                        }
                    });

                    // Handle total calculation
                    dendaKerusakanInput.addEventListener('input', function() {
                        const kerusakan = parseInt(this.value) || 0;
                        const total = dendaTelat + kerusakan;
                        totalDendaText.innerText = 'Rp ' + total.toLocaleString('id-ID');
                    });
                </script>

                <button class="btn text-white rounded-pill px-4" style="background:#10B981;">
                    Simpan Perubahan
                </button>
            </form>

        </div>

    </div>

</div>
@endsection

