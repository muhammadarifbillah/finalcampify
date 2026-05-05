@extends('layouts.admin')

@section('content')

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Moderasi Chat</h1>
            <p class="text-gray-600">Kelola pesan chat yang bermasalah dari pengguna.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-xl">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if($chats->isEmpty())
        <div class="bg-white p-6 rounded-xl shadow text-gray-500">Tidak ada chat yang perlu dimoderasi saat ini.</div>
    @else
        <div class="space-y-4">
            @foreach($chats as $c)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-3 flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="inline-flex rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-xs font-semibold">
                                    💬 Pesan Chat
                                </span>
                                <span
                                    class="text-sm text-gray-500">{{ $c->created_at ? $c->created_at->diffForHumans() : 'Tidak diketahui' }}</span>
                                @if($c->is_flagged)
                                    <span class="inline-flex rounded-full bg-red-100 text-red-800 px-3 py-1 text-xs font-semibold">
                                        ⚠️ Ditandai
                                    </span>
                                @endif
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-blue-500">
                                <p class="text-gray-900">{{ $c->message }}</p>
                            </div>

                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <div>
                                    <span class="font-medium">Pengguna:</span>
                                    <span class="ml-1">{{ $c->user->name ?? 'Tidak Diketahui' }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Email:</span>
                                    <span class="ml-1">{{ $c->user->email ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 lg:min-w-fit">
                            @if(!$c->is_flagged)
                                <a href="/admin/chats/flag/{{ $c->id }}"
                                    class="inline-flex items-center justify-center rounded-lg bg-yellow-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-yellow-700">
                                    🚩 Tandai
                                </a>
                            @else
                                <span
                                    class="inline-flex items-center justify-center rounded-lg bg-red-100 text-red-800 px-4 py-2 text-sm font-semibold">
                                    ✓ Sudah Ditandai
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection