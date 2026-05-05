@extends('layouts.admin')

@section('content')

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Review Chat Dilaporkan</h1>
            <p class="text-gray-600">Hanya chat yang dilaporkan user atau ditandai sistem yang tampil di sini.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-xl">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if($reports->isEmpty() && $chats->isEmpty())
        <div class="bg-white p-6 rounded-xl shadow text-gray-500">Tidak ada chat yang dilaporkan.</div>
    @else
        <div class="space-y-4">
            @foreach($reports as $report)
                <div class="bg-white border border-red-100 rounded-xl shadow-sm p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-3 flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="inline-flex rounded-full bg-red-100 text-red-800 px-3 py-1 text-xs font-semibold">Chat Dilaporkan</span>
                                <span class="text-sm text-gray-500">{{ $report->created_at?->diffForHumans() }}</span>
                                <span class="inline-flex rounded-full bg-yellow-100 text-yellow-800 px-3 py-1 text-xs font-semibold">{{ $report->status }}</span>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-red-500">
                                <p class="text-sm text-gray-500 mb-1">Pesan:</p>
                                <p class="text-gray-900">{{ $report->message?->message ?? $report->description ?? '-' }}</p>
                            </div>

                            <div class="grid gap-3 md:grid-cols-2 text-sm text-gray-600">
                                <div><span class="font-medium">Pelapor:</span> {{ $report->reporter?->name ?? '-' }}</div>
                                <div><span class="font-medium">Seller:</span> {{ $report->seller?->name ?? '-' }}</div>
                                <div><span class="font-medium">Produk:</span> {{ $report->product?->name ?? '-' }}</div>
                                <div><span class="font-medium">Alasan:</span> {{ $report->reason }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @foreach($chats as $c)
                <div class="bg-white border border-yellow-100 rounded-xl shadow-sm p-6">
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="inline-flex rounded-full bg-yellow-100 text-yellow-800 px-3 py-1 text-xs font-semibold">Chat Ditandai</span>
                            <span class="text-sm text-gray-500">{{ $c->created_at?->diffForHumans() }}</span>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-yellow-500">
                            <p class="text-gray-900">{{ $c->message }}</p>
                        </div>

                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                            <div><span class="font-medium">Pengirim:</span> {{ $c->senderUser?->name ?? $c->user?->name ?? '-' }}</div>
                            <div><span class="font-medium">Penerima:</span> {{ $c->receiver?->name ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
