@extends('layouts.admin')

@section('title', 'Chatbot Admin')

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="admin-section-title">Chatbot</h1>
            <p class="admin-section-subtitle">Keyword dan response otomatis.</p>
        </div>

        <div class="admin-card p-6">
            <h2 class="text-2xl font-extrabold mb-5">Tambah Respons</h2>
            <form method="POST" action="/admin/chatbot/store" class="grid gap-4 md:grid-cols-[1fr_1fr_auto]">
                @csrf
                <input type="text" name="keyword" class="admin-form-control" placeholder="Keyword" required>
                <input type="text" name="response" class="admin-form-control" placeholder="Response" required>
                <button type="submit" class="admin-button admin-button-primary">Tambah</button>
            </form>
        </div>

        <div class="admin-card">
            <div class="p-6">
                <h2 class="text-2xl font-extrabold">Daftar Respons</h2>
            </div>
            <div class="space-y-3 px-6 pb-6">
                @forelse($data as $d)
                    <div class="rounded-lg border border-slate-200 p-4">
                        <div class="admin-stat-label">Keyword</div>
                        <p class="mt-1 font-bold text-emerald-700">{{ $d->keyword }}</p>
                        <div class="admin-stat-label mt-3">Response</div>
                        <p class="mt-1">{{ $d->response }}</p>
                    </div>
                @empty
                    <div class="admin-empty">Belum ada respons chatbot.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
