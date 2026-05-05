@extends('layouts.admin')

@section('content')

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Manajemen Chatbot</h1>
            <p class="text-gray-600">Kelola respons otomatis chatbot berdasarkan keyword.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 border border-green-200 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-3xl shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">Tambah Respons Chatbot</h2>
        <form method="POST" action="/admin/chatbot/store" class="space-y-4">
            @csrf
            <div class="grid gap-4 lg:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keyword</label>
                    <input type="text" name="keyword" placeholder="Contoh: halo, hai"
                        class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                        required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Respons</label>
                    <input type="text" name="response" placeholder="Contoh: Halo! Ada yang bisa saya bantu?"
                        class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                        required />
                </div>
            </div>
            <button type="submit"
                class="bg-green-700 text-white px-5 py-3 rounded-xl font-semibold hover:bg-green-800">Tambah
                Respons</button>
        </form>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow">
        <h2 class="text-lg font-semibold mb-4">Daftar Respons Chatbot</h2>
        @if($data->isEmpty())
            <p class="text-gray-500">Belum ada respons chatbot yang ditambahkan.</p>
        @else
            <div class="space-y-3">
                @foreach($data as $d)
                    <div class="bg-gray-50 p-4 rounded-xl border">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold text-gray-800">Keyword: <span
                                        class="text-green-600">{{ $d->keyword }}</span></p>
                                <p class="text-gray-600 mt-1">Respons: {{ $d->response }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

@endsection