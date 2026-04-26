@extends('layouts.admin')

@section('content')

<h1 class="text-xl font-bold mb-4">Manajemen User</h1>

<div class="bg-white rounded-xl shadow overflow-x-auto">

<table class="w-full text-left">
<thead class="bg-gray-100">
<tr>
<th class="p-3">Nama</th>
<th>Email</th>
</tr>
</thead>

<tbody>
@foreach($users as $u)
<tr class="border-b hover:bg-gray-50">
<td class="p-3">{{ $u->name }}</td>
<td>{{ $u->email }}</td>
</tr>
@endforeach
</tbody>

</table>

</div>

@endsection