@extends('layouts.auth_pembeli')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card shadow p-4" style="width: 420px; border-top: 4px solid #0a8045;">
        
        <h3 class="text-center mb-4" style="color: #0a8045; font-weight: 700;">
            Daftar Akun Campify
        </h3>

        @if($errors->any())
            <div class="alert alert-danger text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label class="fw-semibold" style="color:#0a8045;">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required 
                       value="{{ old('name') }}">
            </div>

            <div class="mb-3">
                <label class="fw-semibold" style="color:#0a8045;">Email</label>
                <input type="email" name="email" class="form-control" required
                       value="{{ old('email') }}">
            </div>

            <div class="mb-3">
                <label class="fw-semibold" style="color:#0a8045;">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="fw-semibold" style="color:#0a8045;">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button class="btn w-100 mt-3" 
                    style="background-color: #0a8045; color:white; font-weight:600;">
                Daftar
            </button>

            <p class="text-center mt-3">
                Sudah punya akun?
                <a href="{{ route('login') }}" style="color: #0a8045; font-weight:600;">Login</a>
            </p>
        </form>
    </div>
</div>
@endsection
