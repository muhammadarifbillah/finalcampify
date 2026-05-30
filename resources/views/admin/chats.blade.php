@extends('layouts.admin')

@section('title', 'Chats Admin')

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="admin-section-title">Review Chat</h1>
            <p class="admin-section-subtitle">Chat yang dilaporkan user atau ditandai sistem.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-1 max-w-sm">
            {{-- Card 1: Reports --}}
            <div class="bg-white border border-slate-200 hover:border-[#e11d48] hover:shadow-[0_12px_30px_-10px_rgba(225,29,72,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden flex flex-col justify-between group min-h-[140px]">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#e11d48]"></div>
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Chat Dilaporkan</span>
                    <div class="w-8 h-8 rounded-lg bg-[#fff1f2] text-[#e11d48] flex items-center justify-center transition-colors group-hover:bg-[#e11d48] group-hover:text-white">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($reports->count()) }}</h2>
                    <p class="text-[11px] text-slate-400 font-bold mt-1">Laporan Manual dari Pengguna</p>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($reports as $report)
                <div class="bg-white border border-red-100 rounded-2xl p-6 shadow-sm relative overflow-hidden group hover:border-red-300 transition-colors">
                    <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                                <i data-lucide="user-x" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-800">Laporan Pengguna</h3>
                                <p class="text-[10px] text-slate-500 font-bold">{{ $report->created_at?->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-block px-3 py-1 text-[10px] font-bold tracking-wider rounded-full bg-red-100 text-red-700 uppercase">
                                Dilaporkan
                            </span>
                            <span class="inline-block px-3 py-1 text-[10px] font-bold tracking-wider rounded-full bg-amber-100 text-amber-700 uppercase">
                                {{ $report->status }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 mb-4">
                        <div class="text-xs font-bold text-slate-400 mb-1 uppercase tracking-wider">Isi Pesan / Alasan</div>
                        <p class="text-sm font-medium text-slate-700">"{{ $report->message?->message ?? $report->description ?? '-' }}"</p>
                    </div>

                    <div class="flex items-center gap-6 text-xs font-medium text-slate-600">
                        <div class="flex items-center gap-2">
                            <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                            Pelapor: <span class="font-bold text-slate-800">{{ $report->reporter?->name ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="store" class="w-4 h-4 text-slate-400"></i>
                            Seller: <span class="font-bold text-slate-800">{{ $report->seller?->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-slate-100 rounded-2xl py-12 text-center shadow-sm">
                    <i data-lucide="shield-check" class="w-12 h-12 text-emerald-400 mx-auto mb-3 opacity-50"></i>
                    <h3 class="text-lg font-bold text-slate-700">Tidak ada chat bermasalah</h3>
                    <p class="text-sm text-slate-500 mt-1">Belum ada satupun laporan dari pengguna. Komunitas masih aman!</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
