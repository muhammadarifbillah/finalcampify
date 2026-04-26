@extends('layouts.admin')

@section('content')

<h1 class="text-xl font-bold mb-4">Manajemen Chatbot</h1>

<form method="POST" action="/admin/chatbot/store" class="mb-4 flex gap-2">
@csrf
<input type="text" name="keyword" placeholder="Keyword" class="border p-2">
<input type="text" name="response" placeholder="Response" class="border p-2">
<button class="bg-green-600 text-white px-4 rounded">Tambah</button>
</form>

@foreach($data as $d)
<div class="bg-white p-4 rounded-xl shadow mb-3">

<b>{{ $d->keyword }}</b>
<p>{{ $d->response }}</p>

</div>
@endforeach

@endsection