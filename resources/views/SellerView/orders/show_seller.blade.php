@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="d-flex align-items-center">
        <a href="{{ route('seller.orders.index') }}" class="btn btn-light rounded-circle p-3 me-4 shadow-sm border-0 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-arrow-left fs-4"></i>
        </a>
        <div>
            <h2 class="fw-bold m-0 text-dark">Detail Seller Order {{ $order->seller_order_number ?? '#'.$order->id }}</h2>
            <p class="text-muted">Kelola proses pengiriman dan informasi transaksi pesanan ini.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT: ORDER ITEMS & CUSTOMER INFO --}}
    <div class="col-lg-8">
        {{-- KYC & RISK WARNING --}}
        @if(!$order->buyer->ktp_verified_at)
            <div class="alert alert-danger border-0 rounded-4 p-4 mb-4 shadow-sm d-flex gap-4 align-items-start">
                <div class="icon-box bg-white bg-opacity-20 rounded-circle p-3 text-white fs-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.2) !important;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold m-0 text-white">⚠️ Verifikasi KTP Diperlukan (RISIKO TINGGI)</h5>
                        <span class="badge bg-white text-danger px-3 py-2 rounded-pill fw-bold animate-pulse">MENUNGGU ADMIN</span>
                    </div>
                    <p class="small text-white opacity-90 mb-0">Penyewa belum memverifikasi identitasnya. Anda <strong>tidak dapat memproses pesanan</strong> sebelum Admin memvalidasi KTP penyewa. Silakan hubungi Admin untuk mempercepat proses verifikasi.</p>
                </div>
            </div>
        @endif

        {{-- PANEL PENGEMBALIAN BARANG JUAL BELI (BPMN BASED) --}}
        @php
            $returnRequest = \App\Models\SellerModels\Return_seller::where('order_id', $order->order_id)
                ->where('type', 'jual_beli')
                ->first();
        @endphp

        @if($returnRequest)
            <div class="card card-modern border-0 p-5 mb-4 shadow-sm" style="border-left: 5px solid #e11d48 !important;">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold text-uppercase" style="font-size: 11px;">Komplain & Pengembalian Produk</span>
                        <h4 class="fw-bold text-dark mt-2 mb-0">Status Komplain: 
                            <span class="text-uppercase" style="color: #e11d48 !important;">
                                {{ str_replace('_', ' ', $returnRequest->status) }}
                            </span>
                        </h4>
                    </div>
                    <i class="bi bi-exclamation-octagon fs-2 text-danger opacity-75"></i>
                </div>

                {{-- Detail Masalah & Bukti dari Pembeli --}}
                <div class="p-4 bg-light rounded-4 mb-4 border border-slate-100">
                    <h6 class="fw-bold mb-3 text-secondary text-uppercase tracking-wider" style="font-size: 11px;">Rincian Komplain Pembeli</h6>
                    <div class="row g-3">
                        <div class="col-md-3 text-muted fw-semibold small">Alasan Masalah:</div>
                        <div class="col-md-9 text-dark fw-bold small">"{{ $returnRequest->renter_notes }}"</div>

                        <div class="col-md-3 text-muted fw-semibold small">Rekening Refund:</div>
                        <div class="col-md-9 text-dark small">
                            <span class="fw-bold text-dark">{{ $returnRequest->buyer_refund_bank_name }} - {{ $returnRequest->buyer_refund_bank_account }}</span><br>
                            <span class="text-muted">Atas Nama:</span> <span class="fw-bold">{{ $returnRequest->buyer_refund_bank_name_owner }}</span>
                        </div>

                        @if($returnRequest->proof_returned_image)
                            <div class="col-md-3 text-muted fw-semibold small">Bukti Masalah (Foto):</div>
                            <div class="col-md-9">
                                <a href="{{ asset($returnRequest->proof_returned_image) }}" target="_blank" class="d-block" style="max-width: 250px;">
                                    <img src="{{ asset($returnRequest->proof_returned_image) }}" class="img-fluid rounded-3 border shadow-sm">
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- DYNAMIC STAGES (SELLER ACTIONS) --}}
                @if($returnRequest->status === 'pending')
                    {{-- STAGE 1: ACTION REQUIRED - TINJAU KOMPLAIN --}}
                    <div class="p-4 rounded-4 border-dashed border-2 text-dark bg-white">
                        <h5 class="fw-bold mb-2 text-dark">Tinjau Komplain & Pilih Resolusi</h5>
                        <p class="text-muted small mb-4">Silakan tentukan apakah komplain pembeli ini valid. Jika valid, pilih resolusi yang sesuai: <strong>Refund Dana</strong> atau <strong>Replacement (Kirim Produk Pengganti)</strong>.</p>
                        
                        <form action="{{ route('seller.returns.review-complaint', $returnRequest->id) }}" method="POST" id="reviewComplaintForm">
                            @csrf
                            <input type="hidden" name="action" id="complaintAction" value="approve">

                            <div class="mb-4">
                                <label class="fw-bold text-dark mb-2">Pilihan Tindakan</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-4 cursor-pointer action-card active" onclick="selectAction('approve')" id="actionApproveCard">
                                            <div class="d-flex align-items-center gap-3">
                                                <i class="bi bi-check-circle-fill text-success fs-3"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark small">Setujui Komplain</span>
                                                    <span class="text-muted" style="font-size: 10px;">Proses solusi retur</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-4 cursor-pointer action-card" onclick="selectAction('reject')" id="actionRejectCard">
                                            <div class="d-flex align-items-center gap-3">
                                                <i class="bi bi-x-circle-fill text-danger fs-3"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark small">Tolak Komplain</span>
                                                    <span class="text-muted" style="font-size: 10px;">Komplain tidak valid</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Resolusi Section (Only visible for Approve) --}}
                            <div id="resolutionSection" class="mb-4">
                                <label class="fw-bold text-dark mb-2">Pilihan Resolusi</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="p-3 border rounded-4 w-100 cursor-pointer d-block">
                                            <div class="d-flex align-items-center gap-3">
                                                <input type="radio" name="resolution_type" value="refund" checked class="form-check-input text-rose">
                                                <div>
                                                    <span class="fw-bold d-block text-dark small">Refund Dana</span>
                                                    <span class="text-muted" style="font-size: 10px;">Kembalikan uang 100%</span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="p-3 border rounded-4 w-100 cursor-pointer d-block">
                                            <div class="d-flex align-items-center gap-3">
                                                <input type="radio" name="resolution_type" value="replacement" class="form-check-input text-rose">
                                                <div>
                                                    <span class="fw-bold d-block text-dark small">Kirim Produk Pengganti</span>
                                                    <span class="text-muted" style="font-size: 10px;">Kirim barang baru</span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold text-dark mb-2" id="notesLabel">Catatan Tambahan (Opsional)</label>
                                <textarea name="owner_notes" rows="2" class="form-control rounded-3" placeholder="Masukkan catatan atau alasan jika komplain ditolak..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-emerald w-100 py-3 rounded-4 fw-bold">
                                Proses Keputusan Komplain
                            </button>
                        </form>
                    </div>

                @elseif($returnRequest->status === 'refund_pending')
                    {{-- STAGE 2A: ACTION REQUIRED - PROSES TRANSFER REFUND --}}
                    <div class="p-4 rounded-4 border-dashed border-2 text-dark bg-white">
                        <h5 class="fw-bold mb-2 text-dark">Proses Transfer Refund Dana</h5>
                        <p class="text-muted small mb-4">Anda menyetujui resolusi <strong>Refund Dana</strong>. Silakan lakukan transfer sebesar <strong>Rp {{ number_format($returnRequest->escrow_total) }}</strong> ke rekening pembeli di bawah ini, kemudian lakukan konfirmasi.</p>
                        
                        <div class="p-4 bg-emerald-soft rounded-4 border border-emerald border-opacity-20 mb-4">
                            <div class="row g-2 text-xs">
                                <div class="col-4 text-muted">Bank Tujuan:</div>
                                <div class="col-8 fw-bold text-dark">{{ $returnRequest->buyer_refund_bank_name }}</div>
                                <div class="col-4 text-muted">Nomor Rekening:</div>
                                <div class="col-8 fw-bold text-dark">{{ $returnRequest->buyer_refund_bank_account }}</div>
                                <div class="col-4 text-muted">Pemilik Rekening:</div>
                                <div class="col-8 fw-bold text-dark">{{ $returnRequest->buyer_refund_bank_name_owner }}</div>
                                <div class="col-4 text-muted">Total Transfer:</div>
                                <div class="col-8 fw-extrabold text-danger fs-6">Rp {{ number_format($returnRequest->escrow_total) }}</div>
                            </div>
                        </div>

                        <form action="{{ route('seller.returns.confirm-refund', $returnRequest->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-emerald w-100 py-3 rounded-4 fw-bold shadow-sm">
                                <i class="bi bi-check-circle me-2"></i>Konfirmasi Telah Transfer Refund
                            </button>
                        </form>
                    </div>

                @elseif($returnRequest->status === 'approved')
                    {{-- STAGE 2B: ACTION REQUIRED - KIRIM BARANG PENGGANTI --}}
                    <div class="p-4 rounded-4 border-dashed border-2 text-dark bg-white">
                        <h5 class="fw-bold mb-2 text-dark">Kirim Produk Pengganti & Input Resi</h5>
                        <p class="text-muted small mb-4">Anda menyetujui resolusi <strong>Kirim Produk Pengganti</strong>. Silakan packing barang pengganti dan kirimkan ke alamat pembeli di bawah ini. Jika sudah, masukkan nomor resi ekspedisinya:</p>
                        
                        <div class="p-3 bg-light rounded-4 mb-4">
                            <small class="text-muted text-uppercase tracking-wider d-block mb-1" style="font-size: 9px;">Alamat Kirim Pembeli</small>
                            <p class="small text-dark m-0 fw-bold">
                                {{ $order->shipping_address ?? 'Alamat tidak tersedia' }}<br>
                                {{ $order->shipping_district ?? '' }}, {{ $order->shipping_city ?? '' }} {{ $order->shipping_postal_code ?? '' }}
                            </p>
                        </div>

                        <form action="{{ route('seller.returns.send-replacement', $returnRequest->id) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="fw-bold text-dark mb-2">Nomor Resi Barang Pengganti</label>
                                <input type="text" name="resi_pengganti" class="form-control rounded-3 py-3" placeholder="Masukkan resi ekspedisi pengganti (Contoh: JNE / J&T - JG2931)" required>
                            </div>
                            <button type="submit" class="btn btn-emerald w-100 py-3 rounded-4 fw-bold">
                                <i class="bi bi-truck me-2"></i>Kirimkan Nomor Resi Pengganti
                            </button>
                        </form>
                    </div>

                @elseif($returnRequest->status === 'replacement_shipping')
                    {{-- STAGE 3: WAITING FOR BUYER --}}
                    <div class="alert alert-warning border-0 p-4 rounded-4 d-flex gap-3 align-items-center m-0">
                        <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                        <div>
                            <h6 class="fw-bold m-0 text-dark">Menunggu Konfirmasi Penerimaan</h6>
                            <p class="small text-muted m-0 mt-1">Produk pengganti telah dikirim dengan nomor resi <strong>{{ $returnRequest->resi_pengganti }}</strong>. Menunggu pembeli melakukan konfirmasi penerimaan barang.</p>
                        </div>
                    </div>

                @elseif($returnRequest->status === 'completed')
                    {{-- STAGE 4: COMPLETED --}}
                    <div class="p-4 bg-emerald-soft border-emerald border border-opacity-20 rounded-4 text-dark m-0">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-check-circle-fill text-success fs-2"></i>
                            <div>
                                <h6 class="fw-bold m-0 text-success">Proses Retur Selesai Sukses!</h6>
                                <p class="small text-muted m-0 mt-1">
                                    @if($returnRequest->resolution_type === 'refund')
                                        Refund dana 100% sebesar <strong>Rp {{ number_format($returnRequest->escrow_total) }}</strong> telah sukses ditransfer ke rekening pembeli.
                                    @else
                                        Barang pengganti dengan resi <strong>{{ $returnRequest->resi_pengganti }}</strong> telah sukses diterima oleh pembeli.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                @elseif($returnRequest->status === 'rejected')
                    {{-- STAGE: REJECTED --}}
                    <div class="p-4 bg-danger-subtle rounded-4 text-dark m-0">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-x-circle-fill text-danger fs-2"></i>
                            <div>
                                <h6 class="fw-bold m-0 text-danger">Komplain Telah Anda Tolak</h6>
                                <p class="small text-muted m-0 mt-1">Komplain pembeli dinilai tidak valid. Catatan penolakan: <strong>"{{ $returnRequest->owner_notes }}"</strong>.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <script>
            function selectAction(action) {
                document.getElementById('complaintAction').value = action;
                const approveCard = document.getElementById('actionApproveCard');
                const rejectCard = document.getElementById('actionRejectCard');
                const resolutionSection = document.getElementById('resolutionSection');
                const notesLabel = document.getElementById('notesLabel');
                
                if (action === 'approve') {
                    approveCard.classList.add('active');
                    rejectCard.classList.remove('active');
                    resolutionSection.style.display = 'block';
                    notesLabel.innerText = 'Catatan Tambahan (Opsional)';
                } else {
                    approveCard.classList.remove('active');
                    rejectCard.classList.add('active');
                    resolutionSection.style.display = 'none';
                    notesLabel.innerText = 'Alasan Penolakan Komplain (Wajib)';
                }
            }
        </script>
        <style>
            .action-card {
                transition: all 0.2s ease-in-out;
                border: 2px solid #e2e8f0 !important;
            }
            .action-card:hover {
                border-color: #cbd5e1 !important;
                transform: translateY(-2px);
            }
            .action-card.active {
                border-color: #10B981 !important;
                background-color: #f0fdf4 !important;
            }
        </style>

        {{-- ITEM LIST --}}
        <div class="card card-modern border-0 p-5 mb-4 shadow-sm">
            <h5 class="fw-bold mb-4 text-dark">Item yang Dipesan</h5>
            @foreach($order->details as $detail)
            <div class="d-flex align-items-center gap-4 mb-4 p-4 bg-light rounded-4 border-0">
                <div class="rounded-3 overflow-hidden shadow-sm" style="width: 100px; height: 100px;">
                    @if($detail->product->image)
                        <img src="{{ asset($detail->product->image) }}" class="w-100 h-100 object-fit-cover">
                    @else
                        <div class="w-100 h-100 bg-white d-flex align-items-center justify-content-center">📦</div>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <small class="text-emerald fw-bold text-uppercase ls-1" style="font-size: 0.7rem;">{{ $detail->product->kategori ?? 'Umum' }}</small>
                    <h5 class="fw-bold m-0 text-dark">{{ $detail->product->nama_produk ?? '-' }}</h5>
                    <p class="text-muted small mb-0">{{ Str::limit($detail->product->deskripsi, 100) }}</p>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">Harga x Qty</small>
                    <span class="fw-bold text-dark">Rp {{ number_format($detail->harga, 0, ',', '.') }} x {{ $detail->qty }}</span>
                    <h5 class="fw-bold text-emerald mt-1">Rp {{ number_format($detail->harga * $detail->qty, 0, ',', '.') }}</h5>
                </div>
            </div>
            @endforeach
        </div>

        {{-- CUSTOMER & SHIPPING --}}
        <div class="card card-modern border-0 p-5 shadow-sm">
            <h5 class="fw-bold mb-4 text-dark">Informasi Pengiriman</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="p-3 bg-light rounded-circle text-emerald fs-4" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-person"></i></div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold ls-1">Nama Pembeli</small>
                            <h6 class="fw-bold m-0 text-dark">{{ $order->buyer->name ?? $order->buyer_name }}</h6>
                            <p class="text-muted small m-0">{{ $order->buyer->email ?? '-' }}</p>
                            @if($order->buyer->ktp_verified_at)
                                <span class="badge bg-emerald-soft text-emerald mt-2" style="font-size: 10px;"><i class="bi bi-patch-check-fill me-1"></i>VERIFIED KTP</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="p-3 bg-light rounded-circle text-emerald fs-4" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold ls-1">Alamat Tujuan</small>
                            <p class="text-dark small m-0 fw-semibold">
                                {{ $order->shipping_address ?? 'Alamat tidak tersedia' }}<br>
                                {{ $order->shipping_district ?? '' }}, {{ $order->shipping_city ?? '' }} {{ $order->shipping_postal_code ?? '' }}
                            </p>
                            @if($order->shipping_phone)
                                <small class="text-muted"><i class="bi bi-telephone-fill me-1"></i> {{ $order->shipping_phone }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- TRACKING INFO --}}
            @if($order->no_resi)
            <div class="mt-4 p-4 rounded-4 bg-emerald-soft border-emerald border-start border-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-emerald fw-bold text-uppercase ls-1">Nomor Resi Pengiriman</small>
                        <h4 class="fw-bold m-0 text-dark">{{ $order->no_resi }}</h4>
                    </div>
                    <i class="bi bi-truck fs-1 text-emerald opacity-25"></i>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- RIGHT: SUMMARY & ACTIONS --}}
    <div class="col-lg-4">
        {{-- STATUS CARD --}}
        <div class="card card-modern border-0 p-4 mb-4 shadow-sm">
            <h6 class="fw-bold mb-4 small text-muted text-uppercase ls-1">Status Pesanan</h6>
            <div class="text-center py-2">
                @php
                    $statusClass = match($order->status) {
                        'pending' => 'bg-warning-subtle text-warning',
                        'processing' => 'bg-info-subtle text-info',
                        'shipped' => 'bg-primary-subtle text-primary',
                        'delivered' => 'bg-emerald-soft text-emerald',
                        'cancelled' => 'bg-danger-subtle text-danger',
                        default => 'bg-light text-dark'
                    };
                @endphp
                <span class="badge rounded-pill px-5 py-3 fw-bold text-uppercase ls-1 fs-6 {{ $statusClass }} w-100 mb-4">
                    {{ $order->status_label }}
                </span>
                
                <div class="d-grid gap-2">
                    @if($order->buyer->ktp_verified_at)
                        <a href="{{ route('seller.orders.edit', $order->id) }}" class="btn btn-emerald rounded-4 py-3 fw-bold shadow-sm">
                            <i class="bi bi-pencil-square me-2"></i>Update Status & Resi
                        </a>
                    @else
                        <button class="btn btn-secondary rounded-4 py-3 fw-bold border-0 shadow-sm opacity-50 cursor-not-allowed" disabled>
                            <i class="bi bi-lock-fill me-2"></i>Status Terkunci
                        </button>
                    @endif
                    
                    <a href="/seller/chat?user={{ $order->user_id }}" class="btn btn-light rounded-4 py-3 fw-bold text-muted border">
                        <i class="bi bi-chat-dots me-2"></i>Hubungi Pembeli
                    </a>
                </div>
            </div>
        </div>

        {{-- PAYMENT SUMMARY --}}
        <div class="card card-modern border-0 p-4 mb-4 shadow-sm">
            <h6 class="fw-bold mb-4 small text-muted text-uppercase ls-1">Ringkasan Pembayaran</h6>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal Produk</span>
                <span class="fw-bold text-dark">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 pb-3 border-bottom">
                <span class="text-muted">Biaya Pengiriman</span>
                <span class="fw-bold text-dark">Rp 0</span>
            </div>
            <div class="d-flex justify-content-between mt-3 mb-4">
                <h6 class="fw-bold m-0 text-dark">Total Tagihan</h6>
                <h5 class="fw-bold m-0 text-emerald">Rp {{ number_format($order->total, 0, ',', '.') }}</h5>
            </div>

            @if($order->bukti_pembayaran)
            <div class="bg-light rounded-4 p-3 text-center border dashed">
                <small class="text-muted d-block mb-2 fw-bold text-uppercase ls-1">Bukti Pembayaran</small>
                <a href="{{ asset($order->bukti_pembayaran) }}" target="_blank">
                    <img src="{{ asset($order->bukti_pembayaran) }}" class="img-fluid rounded-3 shadow-sm" style="max-height: 200px;">
                </a>
            </div>
            @else
            <div class="alert alert-warning small rounded-4 border-0 m-0">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Bukti pembayaran belum diunggah.
            </div>
            @endif
        </div>

        {{-- TIPS --}}
        <div class="card card-modern border-0 p-4 bg-dark text-white text-opacity-75 shadow-sm">
            <h6 class="fw-bold text-white mb-2"><i class="bi bi-shield-check me-2 text-emerald"></i>Keamanan Seller</h6>
            <p class="small m-0">Pastikan KTP pembeli sudah diverifikasi Admin sebelum mengirimkan barang bernilai tinggi.</p>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .text-emerald { color: #10B981 !important; }
    .btn-emerald { background-color: #10B981; color: white; }
    .btn-emerald:hover { background-color: #059669; color: white; }
    .border-emerald { border: 1px solid #10B981 !important; }
    .bg-warning-subtle { background-color: #fffbeb; }
    .bg-info-subtle { background-color: #f0f9ff; }
    .bg-primary-subtle { background-color: #eff6ff; }
    .bg-danger-subtle { background-color: #fef2f2; }
    .object-fit-cover { object-fit: cover; }
    .dashed { border: 2px dashed #cbd5e1 !important; }
    .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
</style>
@endsection
