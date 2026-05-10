@extends('layouts.admin')

@section('title', 'Resolusi Sengketa Pengembalian')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl text-sm font-bold flex items-center gap-3 shadow-sm">
            <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-100 text-red-700 px-6 py-4 rounded-2xl text-sm font-bold space-y-1 shadow-sm">
            <div class="flex items-center gap-3 mb-1">
                <i data-lucide="alert-circle" style="width: 18px; height: 18px;"></i>
                <span>Terjadi kesalahan:</span>
            </div>
            <ul class="list-disc list-inside ml-7 text-[11px] font-medium opacity-80">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.returns.sewa') }}" class="px-4 py-2 bg-gray-50 border border-gray-200 text-gray-500 text-xs font-bold rounded-lg hover:bg-gray-100 transition-all active:scale-95 flex items-center gap-2">
                <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Kembali
            </a>
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-bold rounded uppercase tracking-wider">RESOLUSI #RT-{{ 99200 + $return->id }}</span>
                    @if($return->order->user->ktp_verified_at)
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-600 text-[10px] font-bold rounded uppercase tracking-wider flex items-center gap-1">
                            <i data-lucide="shield-check" style="width: 10px; height: 10px;"></i> KTP TERVERIFIKASI
                        </span>
                    @else
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-600 text-[10px] font-bold rounded uppercase tracking-wider flex items-center gap-1">
                            <i data-lucide="shield-alert" style="width: 10px; height: 10px;"></i> BELUM VERIFIKASI KTP
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl font-black text-gray-900">{{ $return->order->details->first()->product->name ?? 'Produk Sewa' }}</h1>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="const f = document.getElementById('updateFeeForm'); const i = document.createElement('input'); i.type='hidden'; i.name='action'; i.value='delay'; f.appendChild(i); f.submit();" 
                class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-[13px] font-bold rounded-lg hover:bg-gray-50 transition-all active:scale-95">Tunda Keputusan</button>
            <form method="POST" action="{{ route('admin.returns.finalize', $return->id) }}">
                @csrf
                <input type="hidden" name="final_status" value="completed">
                <button type="submit" class="px-5 py-2.5 bg-[#0f6b52] text-white text-[13px] font-bold rounded-lg flex items-center gap-2 hover:bg-[#0c5843] transition-all shadow-sm active:scale-95">
                    <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Selesaikan & Cairkan Dana
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left Side: Proofs and Chat -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Proofs Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-sm font-black text-gray-800 uppercase tracking-wider">BUKTI KONDISI BARANG (SIDE-BY-SIDE)</h2>
                    <div class="flex items-center gap-4 text-[10px] font-bold uppercase tracking-widest">
                        <span class="flex items-center gap-1.5 text-emerald-600"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Terkirim</span>
                        <span class="flex items-center gap-1.5 text-red-600"><span class="w-2 h-2 rounded-full bg-red-500"></span> Kembali</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Proof Sent -->
                    <div class="space-y-3">
                        <div class="relative group aspect-video rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                            <img src="{{ $return->proof_sent_image ?? 'https://via.placeholder.com/400x250?text=Foto+Terkirim' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-3 left-3 px-2 py-1 bg-emerald-500/90 text-white text-[9px] font-black rounded uppercase tracking-widest backdrop-blur-sm">SAAT TERKIRIM</div>
                        </div>
                        <p class="text-[11px] text-gray-500 leading-relaxed italic">"{{ $return->owner_notes ?? 'Kondisi barang saat dikirim.' }}" - <span class="font-bold text-gray-700">Pemilik</span></p>
                    </div>

                    <!-- Proof Returned -->
                    <div class="space-y-3">
                        <div class="relative group aspect-video rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                            <img src="{{ $return->proof_returned_image ?? 'https://via.placeholder.com/400x250?text=Foto+Kembali' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-3 left-3 px-2 py-1 bg-red-500/90 text-white text-[9px] font-black rounded uppercase tracking-widest backdrop-blur-sm">SAAT KEMBALI</div>
                        </div>
                        <p class="text-[11px] text-gray-500 leading-relaxed italic">"{{ $return->renter_notes ?? 'Kondisi barang saat kembali.' }}" - <span class="font-bold text-gray-700">Penyewa</span></p>
                    </div>
                </div>
            </div>

            <!-- KYC / Fraud Protection -->
            <div class="bg-indigo-50 border border-indigo-100 p-6 rounded-2xl flex gap-6">
                <div class="shrink-0 w-12 h-12 rounded-xl bg-white border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                    <i data-lucide="shield-check" style="width: 24px; height: 24px;"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="text-sm font-black text-indigo-900 uppercase tracking-wider">Proteksi Keamanan Campify</h3>
                    <p class="text-xs text-indigo-700 leading-relaxed">
                        Penyewa ini <strong>{{ $return->order->user->ktp_verified_at ? 'sudah' : 'belum' }}</strong> memverifikasi identitas KTP. 
                        @if(!$return->order->user->ktp_verified_at)
                            Gunakan denda kerusakan untuk mengamankan kerugian jika barang rusak/hilang. Jika penyewa tidak kooperatif, Anda dapat menandai sebagai <strong>Fraud</strong> untuk memblokir akun secara permanen.
                        @endif
                    </p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[450px]">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                    <h2 class="text-sm font-black text-gray-800 uppercase tracking-wider">LOG PERCAKAPAN SENGKETA</h2>
                    <span class="text-[10px] font-bold text-emerald-500 animate-pulse">Terakhir aktif 10 menit lalu</span>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50/50">
                    @php
                        $messagesToShow = [];
                        if ($conversation && $conversation->messages->count() > 0) {
                            foreach ($conversation->messages as $msg) {
                                $messagesToShow[] = [
                                    'sender' => ($msg->sender_id === $return->order->user_id ? 'renter' : ($msg->sender_id === ($return->order->details->first()->product->store?->user_id ?? $return->order->details->first()->product->user_id) ? 'owner' : 'admin')),
                                    'name' => $msg->sender->name,
                                    'message' => $msg->message,
                                    'time' => $msg->created_at->format('H:i')
                                ];
                            }
                        } else {
                            $messagesToShow = $return->dispute_chat_log ?? [];
                        }
                    @endphp

                    @if(count($messagesToShow) > 0)
                        @foreach($messagesToShow as $chat)
                            <div class="flex {{ $chat['sender'] === 'renter' ? 'justify-end' : 'justify-start' }} items-start gap-3">
                                @if($chat['sender'] === 'owner')
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0 border border-indigo-200">
                                        <i data-lucide="store" style="width: 14px; height: 14px;" class="text-indigo-600"></i>
                                    </div>
                                @elseif($chat['sender'] === 'admin')
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0 border border-emerald-200">
                                        <i data-lucide="shield-check" style="width: 14px; height: 14px;" class="text-emerald-600"></i>
                                    </div>
                                @endif
                                <div class="max-w-[80%] space-y-1">
                                    <div class="flex items-center gap-2 {{ $chat['sender'] === 'renter' ? 'justify-end' : '' }}">
                                        <span class="text-[10px] font-black text-gray-700">
                                            @if($chat['sender'] === 'admin') <span class="text-emerald-600">ADMIN:</span> @endif
                                            {{ $chat['name'] }}
                                        </span>
                                        <span class="text-[9px] text-gray-400 font-medium">{{ $chat['time'] }}</span>
                                    </div>
                                    <div class="p-4 rounded-2xl shadow-sm text-[13px] leading-relaxed {{ $chat['sender'] === 'renter' ? 'bg-blue-600 text-white rounded-tr-none' : ($chat['sender'] === 'admin' ? 'bg-emerald-600 text-white rounded-tl-none' : 'bg-white text-gray-700 border border-gray-100 rounded-tl-none') }}">
                                        {{ $chat['message'] }}
                                    </div>
                                </div>
                                @if($chat['sender'] === 'renter')
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 border border-blue-200">
                                        <i data-lucide="user" style="width: 14px; height: 14px;" class="text-blue-600"></i>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="h-full flex flex-col items-center justify-center text-gray-400 space-y-2">
                            <i data-lucide="message-square" style="width: 40px; height: 40px;" class="opacity-20"></i>
                            <p class="text-sm font-medium">Belum ada percakapan mediasi.</p>
                        </div>
                    @endif
                </div>

                <div class="p-4 bg-white border-t border-gray-100">
                    @if($conversation && $return->status !== 'completed')
                        <form method="POST" action="{{ route('admin.returns.message', $return->id) }}" class="relative flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                            <input type="text" name="message" required placeholder="Ketik pesan mediasi (sebagai Admin)..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-5 py-3 text-[13px] focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                            <button type="submit" class="bg-[#0f6b52] text-white p-3 rounded-xl hover:bg-[#0c5843] transition-all active:scale-95 shrink-0">
                                <i data-lucide="send" style="width: 18px; height: 18px;"></i>
                            </button>
                        </form>
                    @else
                        <div class="text-center py-2 text-[11px] text-gray-400 italic">
                            {{ $return->status === 'completed' ? 'Percakapan telah ditutup.' : 'Fitur chat live belum tersedia untuk transaksi ini.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Side: Financial Control -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-8 sticky top-6">
                <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100 space-y-6">
                    <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest text-center">PANEL KONTROL KEUANGAN</h2>

                    <!-- Escrow Breakdown -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white p-4 rounded-xl border border-gray-100 text-center space-y-1 shadow-sm">
                            <div class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Biaya Sewa</div>
                            <div class="text-sm font-black text-gray-900">Rp {{ number_format((int)$return->rental_fee_amount, 0, ',', '.') }}</div>
                            <div class="text-[9px] font-bold text-gray-400">({{ $return->order->details->first()->duration ?? '-' }} Hari)</div>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-100 text-center space-y-1 shadow-sm">
                            <div class="text-[8px] font-black text-emerald-600 uppercase tracking-widest">Dana Jaminan (25%)</div>
                            <div class="text-sm font-black text-emerald-700">Rp {{ number_format((int)$return->deposit_amount, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="bg-[#f4f9f6] p-5 rounded-xl border border-emerald-100 text-center space-y-1">
                        <div class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Total Dana Ditahan (Escrow)</div>
                        <div class="text-2xl font-black text-[#0f6b52]">Rp {{ number_format((int)$return->escrow_total, 0, ',', '.') }}</div>
                    </div>

                    <!-- Fees Form -->
                    <form method="POST" action="{{ route('admin.returns.update', $return->id) }}" id="updateFeeForm" class="space-y-5">
                        @csrf
                        <input type="hidden" name="type" value="{{ $return->type }}">
                        <input type="hidden" name="status" value="{{ $return->status }}">
                        <input type="hidden" name="escrow_total" value="{{ (int)$return->escrow_total }}">

                        <!-- Late Fee (Read Only) -->
                        <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="p-1.5 bg-red-50 text-red-500 rounded-md">
                                        <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                                    </div>
                                    <label class="text-[10px] font-black text-gray-600 uppercase tracking-wider">Denda Terlambat</label>
                                </div>
                                <span class="text-xs font-black text-red-600">Rp {{ number_format((int)$return->late_fee, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-[9px] text-gray-400 leading-tight italic">
                                *Dihitung otomatis (diambil dari Dana Jaminan).
                            </div>
                        </div>

                        <!-- Damage Fee Input -->
                        <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm space-y-3">
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 bg-orange-50 text-orange-500 rounded-md">
                                    <i data-lucide="hammer" style="width: 12px; height: 12px;"></i>
                                </div>
                                <label class="text-[10px] font-black text-gray-600 uppercase tracking-wider">Denda Kerusakan (Manual)</label>
                            </div>
                            
                            <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg overflow-hidden focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-500/5 transition-all">
                                <span class="px-4 text-gray-400 font-bold text-xs border-r border-gray-200">Rp</span>
                                <input type="number" name="damage_fee" value="{{ (int)$return->damage_fee }}" 
                                    {{ $return->status === 'completed' ? 'disabled' : '' }}
                                    class="w-full bg-transparent px-4 py-2.5 text-sm font-bold focus:outline-none {{ $return->status === 'completed' ? 'opacity-60 cursor-not-allowed' : '' }}" placeholder="0">
                            </div>
                            <p class="text-[9px] text-gray-400 italic leading-relaxed">
                                Denda akan dipotong dari Dana Jaminan penyewa.
                            </p>
                        </div>

                        @if($return->status !== 'completed')
                            <button type="submit" class="w-full py-3.5 bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-black transition-all active:scale-[0.98] shadow-lg flex items-center justify-center gap-2">
                                <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i> Update & Hitung Ulang
                            </button>
                        @endif
                    </form>
                </div>

                @if($return->deficit > 0)
                    <div class="bg-red-50 border border-red-100 p-4 rounded-2xl flex items-start gap-3">
                        <div class="p-2 bg-red-100 text-red-600 rounded-lg">
                            <i data-lucide="alert-circle" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="space-y-1">
                            <div class="text-[11px] font-black text-red-700 uppercase tracking-wider">Peringatan: Jaminan Tidak Cukup</div>
                            <p class="text-[10px] text-red-600 leading-relaxed font-medium">
                                Total denda (Rp {{ number_format((int)$return->total_fines, 0, ',', '.') }}) melebihi Dana Jaminan (Rp {{ number_format((int)$return->deposit_amount, 0, ',', '.') }}). Sisa kekurangan harus ditagih manual.
                            </p>
                        </div>
                    </div>
                @endif

                <div class="border-t border-dashed border-gray-200"></div>

                <!-- Settlement Summary -->
                <div class="space-y-5">
                    <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">RINGKASAN PEMBAGIAN DANA</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span class="text-[13px] font-bold text-gray-700">Cair ke Pemilik</span>
                            </div>
                            <span class="text-[13px] font-black text-gray-900">Rp {{ number_format((int)$return->to_seller, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[9px] text-gray-400 ml-4">(Sewa + Denda yang Tercover)</p>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#2b59ff]"></span>
                                <span class="text-[13px] font-bold text-gray-700">Refund ke Penyewa</span>
                            </div>
                            <span class="text-[13px] font-black text-gray-900">Rp {{ number_format((int)$return->to_buyer, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[9px] text-gray-400 ml-4">(Sisa Jaminan/Deposit)</p>

                        @if($return->deficit > 0)
                            <div class="flex items-center justify-between pt-2 border-t border-dashed border-gray-100">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                                    <span class="text-[13px] font-bold text-red-600">Kekurangan Pembayaran</span>
                                </div>
                                <span class="text-[13px] font-black text-red-600">Rp {{ number_format((int)$return->deficit, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Final Action -->
                <div class="space-y-4 pt-4">
                    @if($return->status !== 'completed')
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox" required class="mt-1 w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span class="text-[11px] text-gray-500 leading-snug group-hover:text-gray-700 transition-colors">
                                Saya telah meninjau semua bukti dan keputusan ini bersifat final sesuai Syarat & Ketentuan Campify.
                            </span>
                        </label>

                        <form method="POST" action="{{ route('admin.returns.finalize', $return->id) }}">
                            @csrf
                            <input type="hidden" name="final_status" value="completed">
                            <button type="submit" class="w-full py-4 bg-[#084535] text-white text-sm font-black rounded-xl hover:bg-[#063327] transition-all active:scale-95 shadow-xl flex items-center justify-center gap-3 group">
                                <i data-lucide="gavel" style="width: 20px; height: 20px;" class="group-hover:rotate-[-20deg] transition-transform"></i>
                                SELESAIKAN SENGKETA
                            </button>
                        </form>
                    @else
                        <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center gap-3">
                            <i data-lucide="check-circle" class="text-emerald-600" style="width: 20px; height: 20px;"></i>
                            <div class="text-[11px] font-bold text-emerald-800">Sengketa telah diselesaikan dan dana telah dicairkan.</div>
                        </div>
                    @endif
                    <p class="text-[9px] text-gray-400 text-center">Email notifikasi akan dikirim ke kedua belah pihak segera setelah dana dicairkan.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
