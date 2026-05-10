@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="d-flex align-items-center">
        <a href="{{ route('seller.chat.index') }}" class="btn btn-light rounded-circle p-3 me-4 shadow-sm border-0 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-arrow-left fs-4"></i>
        </a>
        <div class="d-flex align-items-center gap-3">
            <div class="avatar bg-emerald-soft text-emerald rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 55px; height: 55px; font-size: 1.2rem;">
                {{ strtoupper(substr($chatPartner->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <h4 class="fw-bold mb-0 text-dark">{{ $chatPartner->name ?? 'Chat' }}</h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-emerald-soft text-emerald rounded-pill px-2" style="font-size: 0.7rem;">Produk: {{ $conversation?->product?->nama_produk ?? 'Umum' }}</span>
                    <span class="text-success small"><i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Online</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card card-modern border-0 overflow-hidden shadow-lg" style="height: 65vh;">
            {{-- CHAT BODY --}}
            <div class="card-body p-5 overflow-auto bg-white" id="chatContainer">
                @forelse($messages as $message)
                    @php $isMe = ($message->sender_id == \Illuminate\Support\Facades\Auth::id()); @endphp
                    <div class="mb-4 d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="message-bubble position-relative {{ $isMe ? 'msg-me' : 'msg-other' }}">
                            <div class="p-3 px-4 rounded-4 shadow-sm">
                                {{ $message->message }}
                            </div>
                            <small class="text-muted d-block mt-1 {{ $isMe ? 'text-end' : '' }}" style="font-size: 0.7rem;">
                                {{ $message->created_at->format('H:i') }}
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 opacity-25">
                        <i class="bi bi-chat-dots fs-1 d-block mb-3"></i>
                        <p>Belum ada percakapan. Mulailah menyapa pembeli!</p>
                    </div>
                @endforelse
            </div>

            {{-- CHAT FOOTER --}}
            <div class="card-footer bg-light p-4 border-0">
                <form method="POST" action="{{ $conversation ? route('seller.chat.reply', $conversation) : route('seller.chat.store') }}" class="m-0">
                    @csrf
                    @unless($conversation)
                        <input type="hidden" name="receiver_id" value="{{ $chatPartner->id }}">
                    @endunless
                    
                    <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden bg-white">
                        <input type="text" name="message" class="form-control border-0 px-4 py-3 bg-white" placeholder="Ketik pesan Anda di sini..." required style="box-shadow: none;">
                        <button type="submit" class="btn btn-emerald px-5 py-3 fw-bold">
                            <i class="bi bi-send-fill me-2"></i>Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .msg-me div {
        background: linear-gradient(135deg, var(--primary-emerald) 0%, var(--dark-emerald) 100%);
        color: white;
        border-bottom-right-radius: 4px !important;
    }
    .msg-other div {
        background: #f1f5f9;
        color: var(--text-main);
        border-bottom-left-radius: 4px !important;
    }
    .message-bubble { max-width: 75%; }
    #chatContainer::-webkit-scrollbar { width: 6px; }
    #chatContainer::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('chatContainer');
        container.scrollTop = container.scrollHeight;
    });
</script>
@endsection
