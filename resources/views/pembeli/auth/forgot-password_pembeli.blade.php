@extends('layouts.app_pembeli')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card shadow p-4" style="width: 420px; border-top: 4px solid #0a8045;">

        <h3 class="text-center mb-4" style="color: #0a8045; font-weight: 700;">
            Lupa Password
        </h3>

        @if(session('status'))
            <div class="alert alert-success text-center">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <p class="text-center mb-4 text-muted">
            Masukkan email Anda dan kami akan mengirim link untuk mereset password.
        </p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label class="fw-semibold" style="color:#0a8045;">Email</label>
                <input type="email" name="email" class="form-control" required
                       value="{{ old('email') }}">
            </div>

            <button class="btn w-100 mt-3"
                    style="background-color: #0a8045; color:white; font-weight:600;">
                Kirim Link Reset
            </button>

            <p class="text-center mt-3">
                <a href="{{ route('login') }}" style="color: #0a8045; text-decoration: none;">
                    ← Kembali ke Login
                </a>
            </p>
        </form>
    </div>
</div>
@endsection