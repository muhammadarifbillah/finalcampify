@extends('layouts.admin')

@section('title', 'Chats Admin')

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="admin-section-title">Review Chat</h1>
            <p class="admin-section-subtitle">Chat yang dilaporkan user atau ditandai sistem.</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Reports</p>
                <h2 class="admin-stat-value">{{ $reports->count() }}</h2>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Flagged</p>
                <h2 class="admin-stat-value">{{ $chats->count() }}</h2>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($reports as $report)
                <div class="admin-card p-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="admin-badge admin-badge-danger">Chat Dilaporkan</span>
                        <span class="text-xs text-slate-500">{{ $report->created_at?->diffForHumans() }}</span>
                        <span class="admin-badge admin-badge-warning">{{ $report->status }}</span>
                    </div>
                    <p class="mt-4 font-bold">{{ $report->message?->message ?? $report->description ?? '-' }}</p>
                    <p class="mt-2 text-sm text-slate-600">Pelapor: {{ $report->reporter?->name ?? '-' }} | Seller: {{ $report->seller?->name ?? '-' }}</p>
                </div>
            @endforeach

            @foreach($chats as $c)
                <div class="admin-card p-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="admin-badge admin-badge-warning">Chat Ditandai</span>
                        <span class="text-xs text-slate-500">{{ $c->created_at?->diffForHumans() }}</span>
                    </div>
                    <p class="mt-4 font-bold">{{ $c->message }}</p>
                    <p class="mt-2 text-sm text-slate-600">Pengirim: {{ $c->senderUser?->name ?? $c->user?->name ?? '-' }} | Penerima: {{ $c->receiver?->name ?? '-' }}</p>
                </div>
            @endforeach

            @if($reports->isEmpty() && $chats->isEmpty())
                <div class="admin-card"><div class="admin-empty">Tidak ada chat yang dilaporkan.</div></div>
            @endif
        </div>
    </div>
@endsection
