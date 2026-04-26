@extends('layouts.admin')

@section('content')

<h1 class="text-xl font-bold mb-4">Moderasi Chat</h1>

@foreach($chats as $c)
<div class="bg-white p-4 rounded-xl shadow mb-3 flex justify-between">

<div>
<p>{{ $c->message }}</p>
<small class="text-gray-500">User ID: {{ $c->user_id }}</small>
</div>

<a href="/admin/chats/flag/{{ $c->id }}" 
class="bg-yellow-500 text-white px-3 py-1 rounded">Flag</a>

</div>
@endforeach

@endsection