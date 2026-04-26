@extends('layouts.admin')

@section('content')

<h1 class="text-xl font-bold mb-4">Monitoring Transaksi</h1>

<div class="bg-white rounded-xl shadow overflow-x-auto">

<table class="w-full text-left">
<thead class="bg-gray-100">
<tr>
<th class="p-3">User</th>
<th>Produk</th>
<th>Total</th>
</tr>
</thead>

<tbody>
@foreach($transactions as $t)
<tr class="border-b hover:bg-gray-50">
<td class="p-3">{{ $t->user_id }}</td>
<td>{{ $t->product_id }}</td>
<td>Rp {{ number_format($t->total) }}</td>
</tr>
@endforeach
</tbody>

</table>

</div>

@endsection