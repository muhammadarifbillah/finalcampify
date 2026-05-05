<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Campify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --forest: #28543f;
            --forest-dark: #173629;
            --line: #d9e1dc;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            background: #fff;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .auth-shell {
            width: 100vw;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
            border-radius: 0;
            background: #fff;
        }

        .auth-visual {
            position: relative;
            display: flex;
            align-items: flex-end;
            padding: 42px;
            color: #fff;
            background:
                linear-gradient(180deg, rgba(11, 24, 17, .08), rgba(11, 24, 17, .78)),
                url("https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1400&q=90") center/cover;
        }

        .auth-copy h1 {
            font-size: 18px;
            margin: 0 0 10px;
            font-weight: 700;
        }

        .auth-copy p {
            max-width: 340px;
            margin: 0;
            line-height: 1.7;
            font-size: 14px;
            color: rgba(255, 255, 255, .82);
        }

        .auth-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 56px;
        }

        .auth-card {
            width: min(100%, 390px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--forest);
            font-weight: 800;
            margin-bottom: 34px;
        }

        .brand-mark {
            width: 26px;
            height: 26px;
            display: grid;
            place-items: center;
            border: 2px solid var(--forest);
            border-radius: 8px;
            font-size: 14px;
        }

        .auth-title {
            font-size: 18px;
            font-weight: 700;
            color: #24342c;
            margin-bottom: 8px;
        }

        .auth-subtitle {
            font-size: 13px;
            color: #6e7a73;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .form-label {
            color: #42534a;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            height: 50px;
            border-radius: 0;
            border: 1px solid var(--line);
            color: #23332b;
            font-size: 14px;
            box-shadow: none !important;
        }

        .form-control:focus {
            border-color: var(--forest);
        }

        .btn-auth {
            width: 100%;
            height: 52px;
            border: 0;
            border-radius: 4px;
            background: var(--forest);
            color: #fff;
            font-weight: 700;
        }

        .btn-auth:hover {
            background: var(--forest-dark);
            color: #fff;
        }

        .muted-link {
            color: var(--forest);
            font-weight: 700;
            text-decoration: none;
        }

        .muted-link:hover {
            text-decoration: underline;
        }

        .footer-links {
            margin-top: 90px;
            display: flex;
            justify-content: center;
            gap: 28px;
            font-size: 12px;
            color: #8a948f;
        }

        @media (max-width: 860px) {
            body {
                display: block;
            }

            .auth-shell {
                width: 100%;
                min-height: 100vh;
                grid-template-columns: 1fr;
                border-radius: 0;
            }

            .auth-visual {
                min-height: 260px;
            }

            .auth-panel {
                padding: 34px 22px;
            }
        }
    </style>
</head>

<body>
    <main class="auth-shell">
        <section class="auth-visual">
            <div class="auth-copy">
                <h1>Campify</h1>
                <p>Terhubung kembali dengan alam dalam setiap langkah perjalanan dan transaksi outdoor.</p>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="brand">
                    <div class="brand-mark">C</div>
                    <span>Campify</span>
                </div>

                <h2 class="auth-title">Selamat Datang Kembali</h2>
                <p class="auth-subtitle">Silakan masukkan detail akun Anda untuk melanjutkan akses sesuai role.</p>

                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
                        </div>
                        <span class="small text-muted">Lupa Kata Sandi?</span>
                    </div>

                    <button type="submit" class="btn btn-auth">Masuk</button>
                </form>

                <div class="text-center mt-4 small text-muted">
                    Belum punya akun? <a href="{{ route('register') }}" class="muted-link">Daftar sekarang</a>
                </div>

                <div class="footer-links">
                    <span>Privacy Policy</span>
                    <span>Terms of Service</span>
                    <span>Support</span>
                </div>
            </div>
        </section>
    </main>
</body>

</html>
