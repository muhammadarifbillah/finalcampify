@extends('layouts.admin')

@section('title', 'Chatbot Admin')

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="admin-section-title">Chatbot</h1>
            <p class="admin-section-subtitle">Keyword dan response otomatis.</p>
        </div>

        {{-- Form Section --}}
        <div class="bg-white border border-slate-200 hover:border-[#10b981] hover:shadow-[0_12px_30px_-10px_rgba(16,185,129,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-full h-[4px] bg-[#10b981]"></div>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="message-square-plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-800">Tambah Respons Baru</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Sistem akan otomatis membalas jika chat mengandung keyword ini.</p>
                </div>
            </div>
            
            <form method="POST" action="/admin/chatbot/store" class="grid gap-4 md:grid-cols-[1fr_2fr_auto] items-start">
                @csrf
                <div>
                    <input type="text" name="keyword" class="admin-form-control rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm" placeholder="Contoh: halo, ready, harga" required>
                    <p class="text-[10px] text-slate-400 mt-1.5 px-1 font-medium">Gunakan koma untuk banyak keyword</p>
                </div>
                <div>
                    <input type="text" name="response" class="admin-form-control rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm" placeholder="Contoh: Halo! Ada yang bisa dibantu?" required>
                </div>
                <button type="submit" class="admin-button admin-button-primary bg-emerald-600 hover:bg-emerald-700 border-emerald-600 rounded-xl px-6 h-[42px] shadow-md shadow-emerald-500/20">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Simpan
                </button>
            </form>
        </div>

        {{-- List Section --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                <i data-lucide="list-tree" class="w-5 h-5 text-slate-400"></i>
                <h2 class="text-lg font-black text-slate-800">Daftar Respons Aktif</h2>
            </div>
            <div class="p-6 space-y-4">
                @forelse($data as $d)
                    <div class="flex gap-4 p-5 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-white hover:border-emerald-200 hover:shadow-md transition-all group">
                        <div class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 shrink-0 group-hover:border-emerald-500 group-hover:text-emerald-600 transition-colors">
                            <i data-lucide="bot" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">KEYWORDS:</span>
                                <div class="flex flex-wrap gap-1">
                                    @foreach(explode(',', $d->keyword) as $kw)
                                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-md bg-emerald-100 text-emerald-700 border border-emerald-200">
                                            {{ trim($kw) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="bg-white border border-slate-200 rounded-xl p-3 text-sm font-medium text-slate-700 relative before:content-[''] before:absolute before:-left-1.5 before:top-3 before:w-3 before:h-3 before:bg-white before:border-l before:border-b before:border-slate-200 before:rotate-45">
                                {{ $d->response }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400">
                        <i data-lucide="message-square-dashed" class="w-12 h-12 mx-auto mb-3 opacity-30"></i>
                        <p class="text-sm font-medium">Belum ada respons chatbot yang ditambahkan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
