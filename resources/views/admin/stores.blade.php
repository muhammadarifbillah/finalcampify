@extends('layouts.admin')

@section('content')

<h1 class="text-xl font-bold mb-4">Manajemen Toko</h1>

@foreach($stores as $s)
<div class="bg-white p-4 rounded-xl shadow mb-3 flex justify-between items-center">

<div>
<h2 class="font-bold">{{ $s->nama_toko }}</h2>

<span class="
px-2 py-1 rounded text-white
@if($s->status=='aktif') bg-green-500
@elseif($s->status=='banned') bg-red-500
@else bg-gray-500
@endif
">
{{ $s->status }}
</span>

</div>

<div>
@if($s->status != 'banned')
<a href="/admin/stores/ban/{{ $s->id }}" 
class="bg-red-500 text-white px-3 py-1 rounded">Ban</a>
@else
<a href="/admin/stores/unban/{{ $s->id }}" 
class="bg-green-500 text-white px-3 py-1 rounded">Unban</a>
@endif
</div>

</div>
@endforeach

@endsection