@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="d-flex align-items-center">
        <a href="{{ route('seller.rentals.index') }}" class="btn btn-light rounded-circle p-3 me-4 shadow-sm border-0 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-arrow-left fs-4"></i>
        </a>
        <div>
            <h2 class="fw-bold m-0 text-dark">Detail Penyewaan</h2>
            <p class="text-muted">Informasi lengkap transaksi sewa unit #{{ $rental->id }}</p>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT: RENTAL DETAILS --}}
    <div class="col-lg-8">
        {{-- KYC & RISK WARNING --}}
        @if(!$rental->user->ktp_verified_at)
            <div class="alert alert-danger border-0 rounded-4 p-4 mb-4 shadow-sm d-flex gap-4 align-items-start">
                <div class="icon-box bg-white bg-opacity-20 rounded-circle p-3 text-white fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.2) !important;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold m-0 text-white">⚠️ Verifikasi KTP Diperlukan (RISIKO TINGGI)</h5>
                        <span class="badge bg-white text-danger px-3 py-2 rounded-pill fw-bold animate-pulse">ACTION REQUIRED</span>
                    </div>
                    <p class="small text-white opacity-90 mb-3">Sesuai kebijakan keamanan Campify, Anda <strong>WAJIB</strong> memvalidasi identitas penyewa sebelum memproses barang. Tombol update status akan terkunci hingga identitas diverifikasi.</p>
                    
                    @if($rental->user->ktp_image)
                        <div class="bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-20">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <a href="{{ asset($rental->user->ktp_image) }}" target="_blank">
                                        <img src="{{ asset($rental->user->ktp_image) }}" class="img-fluid rounded-3 border" style="max-height: 120px;">
                                    </a>
                                </div>
                                <div class="col-md-8">
                                    <p class="small text-white mb-3">Silakan periksa apakah foto KTP di samping sesuai dengan data penyewa.</p>
                                    <form action="{{ route('seller.user.verify', $rental->user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-light text-danger fw-bold rounded-pill px-4">
                                            <i class="bi bi-check-circle-fill me-2"></i>Verifikasi & Buka Kunci
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-danger bg-opacity-25 p-3 rounded-4 border border-white border-opacity-20">
                            <p class="small text-white m-0 italic fw-bold"><i class="bi bi-x-circle-fill me-2"></i>Penyewa belum mengunggah foto KTP. Hubungi penyewa melalui chat.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- USER & SHIPPING INFO --}}
        <div class="card card-modern border-0 p-5 mb-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-5 pb-3 border-bottom">
                <h5 class="fw-bold m-0 text-dark"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Data Penyewa & Pengiriman</h5>
                @php
                    $statusClass = match($rental->status) {
                        'pending' => 'bg-warning-subtle text-warning',
                        'active' => 'bg-info-subtle text-info',
                        'completed' => 'bg-emerald-soft text-emerald',
                        'cancelled' => 'bg-danger-subtle text-danger',
                        default => 'bg-light text-dark'
                    };
                @endphp
                <span class="badge rounded-pill px-4 py-2 fw-bold text-uppercase ls-1 {{ $statusClass }}">
                    {{ $rental->status }}
                </span>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex gap-3">
                        <div class="p-3 bg-light rounded-circle text-primary fs-4" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1" style="font-size: 0.7rem;">Nama Penyewa</small>
                            <h5 class="fw-bold m-0 text-dark">{{ $rental->user->name ?? 'User' }}</h5>
                            <p class="text-muted small m-0">{{ $rental->user->email ?? '-' }}</p>
                            @if($rental->user->ktp_verified_at)
                                <span class="badge bg-emerald-soft text-emerald mt-2" style="font-size: 10px;"><i class="bi bi-patch-check-fill me-1"></i>KTP TERVERIFIKASI</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 border-start-md">
                    <div class="d-flex gap-3">
                        <div class="p-3 bg-light rounded-circle text-primary fs-4" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1" style="font-size: 0.7rem;">Alamat Pengiriman</small>
                            <p class="text-dark small leading-relaxed m-0 fw-semibold">
                                @if($rental->order)
                                    {{ $rental->order->shipping_address ?? 'Alamat tidak tersedia' }}<br>
                                    <span class="text-muted">{{ $rental->order->shipping_district ?? '' }}, {{ $rental->order->shipping_city ?? '' }} {{ $rental->order->shipping_postal_code ?? '' }}</span>
                                @else
                                    <span class="text-muted italic">Data alamat tidak tertaut ke pesanan.</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PRODUCT INFO --}}
        <div class="card card-modern border-0 p-5 mb-4 shadow-sm">
            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-box-seam me-2 text-primary"></i>Informasi Produk</h5>
            <div class="row g-4 align-items-center">
                <div class="col-md-4">
                    <div class="rounded-4 overflow-hidden border shadow-sm" style="height: 200px;">
                        @if($rental->product->image)
                            <img src="{{ asset($rental->product->image) }}" class="w-100 h-100 object-fit-cover">
                        @else
                            <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center opacity-25 fs-1">🏕️</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-8">
                    <small class="text-primary fw-bold text-uppercase ls-1" style="font-size: 0.7rem;">{{ $rental->product->kategori ?? 'Camping Gear' }}</small>
                    <h3 class="fw-bold text-dark mb-2">{{ $rental->product->nama_produk ?? '-' }}</h3>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4 text-center">
                                <small class="text-muted d-block mb-1 small text-uppercase">Mulai Sewa</small>
                                <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($rental->start_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4 text-center">
                                <small class="text-muted d-block mb-1 small text-uppercase">Kembali</small>
                                <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($rental->end_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-primary bg-opacity-10 rounded-4 text-center border border-primary border-opacity-10">
                                <span class="text-primary fw-bold fs-5"><i class="bi bi-calendar-check me-2"></i>Durasi: {{ $rental->duration }} Hari</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($rental->catatan)
                <div class="mt-5 p-4 bg-light rounded-4 border dashed">
                    <h6 class="fw-bold mb-2 small text-uppercase ls-1 text-muted">Catatan Penyewa:</h6>
                    <p class="m-0 small text-dark leading-relaxed">{{ $rental->catatan }}</p>
                </div>
            @endif
        </div>

        {{-- PANEL MANAJEMEN PENGEMBALIAN SEWA (INTERAKTIF) --}}
        @php
            $returnRequest = \App\Models\ReturnEscrow::where('order_id', $rental->order_id)->first();
        @endphp
        
        <div class="card card-modern border-0 p-5 mb-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <h5 class="fw-bold m-0 text-dark"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Manajemen Pengembalian Sewa</h5>
                @if($returnRequest)
                    @php
                        $badgeClass = match($returnRequest->status) {
                            'pending' => 'bg-warning text-dark',
                            'approved' => 'bg-info text-dark',
                            'shipping' => 'bg-primary text-white',
                            'denda_pending' => 'bg-danger text-white',
                            'denda_submitted' => 'bg-warning text-dark',
                            'completed' => 'bg-success text-white',
                            default => 'bg-secondary text-white'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill fw-bold text-uppercase">
                        STATUS: {{ $returnRequest->status }}
                    </span>
                @else
                    <span class="badge bg-secondary px-3 py-2 rounded-pill fw-bold">BELUM DIAJUKAN</span>
                @endif
            </div>

            <!-- Kondisi 1: Belum Diajukan -->
            @if(!$returnRequest)
                <div class="text-center py-4">
                    <div class="fs-1 text-muted mb-3">🕒</div>
                    <h6 class="fw-bold text-slate-800">Menunggu Pengajuan Pengembalian</h6>
                    <p class="small text-muted mb-0">Penyewa belum mengajukan formulir pengembalian sewa di aplikasi. Hubungi penyewa jika sewa sudah berakhir.</p>
                </div>

            <!-- Kondisi 2: Status Pending (Menunggu Approval Kurir) -->
            @elseif($returnRequest->status === 'pending')
                <div class="p-4 bg-warning-subtle border border-warning rounded-4 mb-4">
                    <h6 class="fw-bold text-warning-emphasis"><i class="bi bi-exclamation-triangle-fill me-2"></i>Persetujuan Pengiriman Balik</h6>
                    <p class="small text-dark opacity-90 m-0 leading-relaxed">
                        Penyewa ingin mengembalikan barang menggunakan metode <strong>Kirim via Kurir</strong>. Silakan setujui pengajuan ini agar penyewa dapat menginput nomor resi pengiriman balik dan mengunggah foto bukti fisik barang.
                    </p>
                </div>
                <div class="d-flex gap-3">
                    <form action="{{ route('seller.rentals.approve-return', $rental->id) }}" method="POST" class="flex-grow-1">
                        @csrf
                        <button type="submit" class="btn btn-emerald w-100 py-3 fw-bold text-white shadow-sm" style="background-color: #10B981;">
                            <i class="bi bi-check-circle-fill me-2"></i>Setujui Pengajuan Pengembalian
                        </button>
                    </form>
                </div>

            <!-- Kondisi 3: Status Approved (Menunggu Resi) -->
            @elseif($returnRequest->status === 'approved')
                <div class="text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h6 class="fw-bold text-slate-800">Menunggu Input Resi Pembeli</h6>
                    <p class="small text-muted mb-0">Pengajuan disetujui. Penyewa sedang mengemas barang dan akan segera memasukkan nomor resi pengiriman balik di aplikasi mereka.</p>
                </div>

            <!-- Kondisi 4: Status Shipping (Penerimaan Barang & Input Denda) -->
            @elseif($returnRequest->status === 'shipping')
                <div class="p-4 bg-light border rounded-4 mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-box-seam-fill me-2 text-primary"></i>Barang Dalam Perjalanan</h6>
                    <div class="row g-3 small text-dark leading-relaxed">
                        <div class="col-4 font-bold text-muted">Nomor Resi:</div>
                        <div class="col-8 fw-bold">{{ $returnRequest->resi_return }}</div>
                        
                        <div class="col-4 font-bold text-muted">Tanggal Kirim:</div>
                        <div class="col-8">{{ $returnRequest->tanggal_pengembalian ? \Carbon\Carbon::parse($returnRequest->tanggal_pengembalian)->format('d M Y H:i') : '-' }}</div>

                        @if($returnRequest->renter_notes)
                            <div class="col-4 font-bold text-muted">Catatan Penyewa:</div>
                            <div class="col-8 italic">"{{ $returnRequest->renter_notes }}"</div>
                        @endif
                    </div>
                </div>

                @if($returnRequest->proof_returned_image)
                    <div class="mb-4">
                        <small class="text-muted fw-bold d-block mb-2 text-uppercase" style="font-size: 0.65rem;">Bukti Foto Kondisi Barang dari Penyewa:</small>
                        <a href="{{ asset($returnRequest->proof_returned_image) }}" target="_blank">
                            <img src="{{ asset($returnRequest->proof_returned_image) }}" class="img-fluid rounded-3 border shadow-sm" style="max-height: 200px;">
                        </a>
                    </div>
                @endif

                <div class="border-top pt-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-pencil-square me-2 text-primary"></i>Konfirmasi Barang Diterima</h6>
                    
                    @php
                        $expected = \Carbon\Carbon::parse($returnRequest->expected_date)->startOfDay();
                        $actual = now()->startOfDay();
                        $daysLate = max(0, $expected->diffInDays($actual, false));
                        $dailyPrice = (float) $rental->price;
                        $lateFeePerDay = $dailyPrice * 0.3;
                        $dendaTelat = $daysLate * $lateFeePerDay;
                    @endphp

                    <form action="{{ route('seller.rentals.receive-item', $rental->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="bg-warning bg-opacity-10 border border-warning rounded-4 p-3 mb-3 text-xs text-warning-emphasis">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Batas Pengembalian:</span>
                                <span class="fw-bold">{{ $expected->format('d M Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Tanggal Diterima (Hari ini):</span>
                                <span class="fw-bold">{{ $actual->format('d M Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2 mt-2 font-bold fs-6">
                                <span>Denda Keterlambatan (Otomatis):</span>
                                <span>Rp {{ number_format($dendaTelat, 0, ',', '.') }} ({{ $daysLate }} Hari Telat)</span>
                            </div>
                            <span class="text-[10px] opacity-75 mt-1 block">*Logika: 30% dari total sewa harian per hari keterlambatan.</span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-slate-700">Denda Kerusakan Fisik (Diisi Manual)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted font-bold text-xs">Rp</span>
                                <input type="number" name="denda_kerusakan" value="0" min="0" class="form-control" placeholder="Masukkan nominal biaya kerusakan (jika ada)" required>
                            </div>
                            <span class="text-[10px] text-muted block mt-1">Masukkan 0 jika barang kembali dalam kondisi baik tanpa kerusakan.</span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-slate-700">Kondisi Fisik Barang</label>
                            <select name="kondisi_barang" class="form-select text-xs rounded-3" required>
                                <option value="baik" selected>Baik / Lengkap</option>
                                <option value="rusak_ringan">Rusak Ringan (Lecet/Kotor)</option>
                                <option value="rusak_berat">Rusak Berat (Robek/Patah)</option>
                                <option value="hilang">Barang Hilang / Rusak Total</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-4 shadow-sm border-0" style="background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);">
                            <i class="bi bi-check2-square me-2"></i>Konfirmasi Penerimaan Barang
                        </button>
                    </form>
                </div>

            <!-- Kondisi 5: Status Denda Pending (Menunggu Transfer Pembeli) -->
            @elseif($returnRequest->status === 'denda_pending')
                <div class="p-4 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-4 mb-4">
                    <h6 class="fw-bold text-danger"><i class="bi bi-clock-history me-2"></i>Menunggu Pembayaran Denda</h6>
                    <p class="small text-dark opacity-90 m-0 leading-relaxed">
                        Uang denda melebihi deposit jaminan. Deposit sebesar <strong>Rp {{ number_format($returnRequest->deposit_amount) }}</strong> telah terpotong habis. Penyewa wajib mentransfer sisa kekurangan denda sebesar:
                    </p>
                    <div class="text-center my-3 py-2 bg-white rounded-3 shadow-xs border">
                        <span class="h4 fw-bold text-rose-600">Rp {{ number_format($returnRequest->deficit, 0, ',', '.') }}</span>
                    </div>
                    <p class="small text-muted m-0 italic text-center">*Penyewa sedang mengunggah bukti pembayaran sisa kekurangan ini.*</p>
                </div>

            <!-- Kondisi 6: Status Denda Submitted (Verifikasi Bukti Transfer Denda) -->
            @elseif($returnRequest->status === 'denda_submitted')
                <div class="p-4 bg-warning bg-opacity-10 border border-warning rounded-4 mb-4 text-warning-emphasis">
                    <h6 class="fw-bold"><i class="bi bi-patch-exclamation-fill me-2"></i>Penyewa Telah Mengunggah Bukti Bayar</h6>
                    <p class="small text-dark opacity-90 m-0 leading-relaxed">
                        Penyewa melaporkan telah mentransfer kekurangan denda sebesar <strong>Rp {{ number_format($returnRequest->deficit, 0, ',', '.') }}</strong> ke rekening toko Anda. Silakan cek mutasi rekening Anda terlebih dahulu.
                    </p>
                </div>

                @if($returnRequest->bukti_denda)
                    <div class="mb-4">
                        <small class="text-muted fw-bold d-block mb-2 text-uppercase" style="font-size: 0.65rem;">Foto Bukti Transfer Denda dari Penyewa:</small>
                        <a href="{{ asset($returnRequest->bukti_denda) }}" target="_blank">
                            <img src="{{ asset($returnRequest->bukti_denda) }}" class="img-fluid rounded-3 border shadow-sm" style="max-height: 250px;">
                        </a>
                    </div>
                @endif

                <form action="{{ route('seller.rentals.verify-denda', $rental->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-emerald w-100 py-3 fw-bold text-white shadow-sm border-0" style="background-color: #10B981;">
                        <i class="bi bi-check-circle-fill me-2"></i>Konfirmasi & Verifikasi Pembayaran Denda
                    </button>
                </form>

            <!-- Kondisi 7: Status Completed (Selesai) -->
            @elseif($returnRequest->status === 'completed')
                <div class="text-center py-4">
                    <div class="fs-1 text-success mb-3">✅</div>
                    <h6 class="fw-bold text-success">Pengembalian Selesai & Sukses!</h6>
                    <p class="small text-muted mb-4">Pengembalian sewa ini telah sukses disepakati dan diverifikasi oleh kedua pihak.</p>
                </div>

                <div class="bg-slate-50 p-4 rounded-4 border text-xs">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Kondisi Akhir:</span>
                        <span class="fw-bold uppercase text-dark">{{ $returnRequest->kondisi_barang }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Denda Kerusakan:</span>
                        <span class="fw-bold text-dark">Rp {{ number_format($returnRequest->damage_fee) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Denda Keterlambatan:</span>
                        <span class="fw-bold text-dark">Rp {{ number_format($returnRequest->late_fee) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pt-2 border-top">
                        <span class="text-muted font-bold">Total Pendapatan Denda:</span>
                        <span class="fw-bold text-success">Rp {{ number_format($returnRequest->total_fines) }}</span>
                    </div>
                    
                    @if($returnRequest->refund_disbursed_at)
                        <div class="mt-3 p-3 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 text-center text-success fw-bold">
                            <i class="bi bi-cash-coin me-1"></i>Dana Hasil Escrow Telah Dicairkan Admin
                        </div>
                    @else
                        <div class="mt-3 p-3 bg-info bg-opacity-10 border border-info border-opacity-25 rounded-3 text-center text-info font-bold">
                            <i class="bi bi-clock-history me-1"></i>Menunggu Pencairan Dana Oleh Admin
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- RIGHT: SUMMARY & ACTIONS --}}
    <div class="col-lg-4">
        {{-- PAYMENT SUMMARY --}}
        <div class="card card-modern border-0 p-4 mb-4 shadow-sm">
            <h6 class="fw-bold mb-4 small text-muted text-uppercase ls-1">Ringkasan Pembayaran</h6>
            
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted small">Pendapatan Sewa</span>
                <span class="fw-bold text-dark">Rp {{ number_format($rental->price, 0, ',', '.') }}</span>
            </div>
            @php 
                $deposit = $rental->product->escrow_amount > 0 ? $rental->product->escrow_amount : ($rental->product->buy_price * 0.25);
            @endphp
            <div class="d-flex justify-content-between mb-4 pb-3 border-bottom">
                <span class="text-emerald-600 small fw-bold">Escrow (Dana Jaminan)</span>
                <span class="fw-bold text-emerald-600">Rp {{ number_format($deposit, 0, ',', '.') }}</span>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div class="d-flex flex-column">
                    <span class="h6 fw-bold m-0 text-dark">Total Transaksi</span>
                    <small class="text-muted" style="font-size: 10px;">Sudah dibayar pembeli</small>
                </div>
                <span class="h4 fw-bold m-0 text-primary">Rp {{ number_format($rental->price + $deposit, 0, ',', '.') }}</span>
            </div>

            <div class="d-grid gap-3">
                @if($rental->user->ktp_verified_at)
                    <a href="{{ route('seller.rentals.edit', $rental->id) }}" class="btn btn-primary rounded-4 py-3 fw-bold border-0 shadow-sm" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                        <i class="bi bi-pencil-square me-2"></i>Update Status Sewa
                    </a>
                @else
                    <button class="btn btn-secondary rounded-4 py-3 fw-bold border-0 shadow-sm opacity-50 cursor-not-allowed" disabled>
                        <i class="bi bi-lock-fill me-2"></i>Status Terkunci
                    </button>
                    <small class="text-danger text-center fw-bold" style="font-size: 10px;">Lakukan verifikasi KTP untuk membuka kunci.</small>
                @endif
                
                <a href="/seller/chat?user={{ $rental->user_id }}" class="btn btn-light rounded-4 py-3 fw-bold text-muted border">
                    <i class="bi bi-chat-dots me-2"></i>Hubungi Penyewa
                </a>
            </div>
        </div>

        {{-- BUKTI PEMBAYARAN --}}
        @if($rental->order && $rental->order->bukti_pembayaran)
        <div class="card card-modern border-0 p-4 mb-4 shadow-sm">
            <h6 class="fw-bold mb-3 small text-muted text-uppercase ls-1">Bukti Pembayaran</h6>
            <div class="bg-light rounded-4 overflow-hidden border dashed p-2 text-center">
                <a href="{{ asset($rental->order->bukti_pembayaran) }}" target="_blank">
                    <img src="{{ asset($rental->order->bukti_pembayaran) }}" class="img-fluid rounded-3 shadow-sm" style="max-height: 250px;">
                </a>
            </div>
        </div>
        @endif

        <div class="card card-modern border-0 p-4 bg-info bg-opacity-10 text-info border-start border-info border-4 shadow-sm">
            <h6 class="fw-bold mb-2"><i class="bi bi-info-circle-fill me-2"></i>Panduan Penjual</h6>
            <p class="small mb-0 leading-relaxed text-dark opacity-75">Gunakan dana jaminan (Escrow) sebagai jaminan keamanan barang Anda. Jangan menyerahkan barang jika identitas penyewa meragukan.</p>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .bg-warning-subtle { background-color: #fffbeb; }
    .bg-info-subtle { background-color: #f0f9ff; }
    .object-fit-cover { object-fit: cover; }
    .leading-relaxed { line-height: 1.6; }
    .dashed { border: 2px dashed #cbd5e1 !important; }
    .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
    
    @media (min-width: 768px) {
        .border-start-md { border-left: 1px solid #eef2f7 !important; padding-left: 2rem; }
    }
</style>
@endsection
