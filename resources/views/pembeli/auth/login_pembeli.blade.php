@extends('layouts.auth_pembeli')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card shadow p-4" style="width: 420px; border-top: 4px solid #0a8045;">
        
        <h3 class="text-center mb-4" style="color: #0a8045; font-weight: 700;">
            Masuk ke Campify
        </h3>

        @if($errors->any())
            <div class="alert alert-danger text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="fw-semibold" style="color:#0a8045;">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="fw-semibold" style="color:#0a8045;">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3 text-end">
                <a href="{{ route('password.request') }}" style="color: #0a8045; text-decoration: none;">
                    Lupa Password?
                </a>
            </div>

            <button class="btn w-100 mt-3" 
                    style="background-color: #0a8045; color:white; font-weight:600;">
                Masuk
            </button>

            <p class="text-center mt-3">
                Belum punya akun?
                <a href="{{ route('register') }}" style="color: #0a8045; font-weight:600;">Daftar</a>
            </p>
        </form>
    </div>
</div>
@endsection
