@extends('layouts.app_pembeli')

@section('content')
<div class="pt-28 pb-16 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4">
        <div class="mb-8 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Pesan</h1>
                <p class="text-sm text-slate-500">Langsung chat dengan penjual atau tim support.</p>
            </div>
            <a href="{{ route('produk.index') }}" class="text-sm text-green-600 hover:text-green-800">← Kembali ke Produk</a>
        </div>

        <div class="grid gap-6 xl:grid-cols-[320px_1fr]">

            <aside class="space-y-4">
                <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Percakapan</p>
                            <p class="text-xs text-slate-500">Pilih percakapan untuk mulai chat.</p>
                        </div>
                        <span class="text-sm text-slate-400">{{ $messages->count() }} pesan</span>
                    </div>
                    <div class="space-y-3">
                        @foreach($contacts as $contact)
                            <a href="#chat-thread" class="block rounded-3xl border border-slate-200 bg-slate-50 p-4 hover:border-green-300 transition">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $contact['name'] }}</p>
                                        <p class="text-xs text-slate-500">{{ $contact['last_message'] }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs text-slate-400">{{ $contact['time'] }}</span>
                                        <div class="mt-1 text-[10px] font-semibold text-green-600">{{ $contact['online'] ? 'Online' : 'Offline' }}</div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>

            <section id="chat-thread" class="flex flex-col h-[calc(100vh-220px)] overflow-hidden rounded-3xl bg-white shadow-sm border border-slate-200">
                <div class="border-b px-6 py-5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $contacts[0]['name'] }}</p>
                        <p class="text-xs text-slate-500">Online</p>
                    </div>
                    <button class="rounded-full border border-slate-200 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Lihat Profil</button>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-6 space-y-4 bg-slate-50">
                    @if($messages->isEmpty())
                        <div class="mx-auto max-w-xl rounded-3xl border border-dashed border-slate-200 bg-white p-10 text-center">
                            <p class="font-semibold text-slate-900">Mulai percakapanmu sekarang</p>
                            <p class="mt-2 text-sm text-slate-500">Kirim pesan untuk menanyakan produk, stok, atau status pesanan.</p>
                        </div>
                    @endif

                    @foreach($messages as $msg)
                        <div class="flex {{ $msg->sender === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xl rounded-3xl p-4 text-sm leading-relaxed shadow-sm {{ $msg->sender === 'user' ? 'bg-green-600 text-white rounded-br-none' : 'bg-white border border-slate-200 rounded-bl-none' }}">
                                <p>{{ $msg->message }}</p>
                                <div class="mt-3 text-[11px] text-slate-400 text-right">{{ $msg->created_at->format('H:i') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t px-6 py-5 bg-white">
                    <form action="{{ route('chat.send') }}" method="POST" class="flex gap-3">
                        @csrf
                        <input type="text" name="message" placeholder="Tulis pesan..." class="flex-1 rounded-2xl border border-slate-200 px-4 py-3 focus:border-green-500 focus:outline-none" required>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-green-600 px-6 py-3 text-sm font-semibold text-white hover:bg-green-700">Kirim</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection