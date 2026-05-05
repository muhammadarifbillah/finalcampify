@extends('SellerView.layouts.app_seller')

@section('content')
<h2 class="mb-4">Register</h2>

<form method="POST" action="/register">
    @csrf

   <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-control" required>
            <option value="user">User</option>
            <option value="seller">Seller</option>
        </select>
    </div>

    <button class="btn btn-success">Register</button>
</form>
@endsection
