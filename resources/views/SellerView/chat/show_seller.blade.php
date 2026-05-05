@extends('SellerView.layouts.app_seller')

@section('content')
<div class="d-flex" style="min-height:100vh; background:#f9fafb;">
    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('seller.chat.index') }}" class="btn btn-light rounded-pill px-3 me-3">Back</a>
                <div>
                    <h4 class="fw-bold mb-0">{{ $chatPartner->name ?? 'Chat' }}</h4>
                    <small class="text-muted">{{ $conversation?->product?->name ?? 'Chat lama' }}</small>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:16px; height: 70vh; display: flex; flex-direction: column;">
            <div class="card-body flex-grow-1 overflow-auto">
                @forelse($messages as $message)
                    <div class="mb-3 {{ $message->sender_id == \Illuminate\Support\Facades\Auth::id() ? 'text-end' : '' }}">
                        <div class="d-inline-block p-3 rounded {{ $message->sender_id == \Illuminate\Support\Facades\Auth::id() ? 'bg-success text-white' : 'bg-light' }}" style="max-width: 70%;">
                            {{ $message->message }}
                        </div>
                        <br>
                        <small class="text-muted">{{ $message->created_at->format('H:i') }}</small>
                    </div>
                @empty
                    <p class="text-center text-muted">Belum ada pesan</p>
                @endforelse
            </div>

            <div class="card-footer border-0">
                <form method="POST" action="{{ $conversation ? route('seller.chat.reply', $conversation) : route('seller.chat.store') }}" class="d-flex">
                    @csrf
                    @unless($conversation)
                        <input type="hidden" name="receiver_id" value="{{ $chatPartner->id }}">
                    @endunless
                    <input type="text" name="message" class="form-control me-2" placeholder="Ketik pesan..." required>
                    <button type="submit" class="btn text-white" style="background:#10B981;">Kirim</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
