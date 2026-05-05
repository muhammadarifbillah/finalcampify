<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Campify</title>
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
            padding: 44px 56px;
        }

        .auth-card {
            width: min(100%, 410px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--forest);
            font-weight: 800;
            margin-bottom: 26px;
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
            margin-bottom: 24px;
        }

        .form-label {
            color: #42534a;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .form-control,
        .form-select {
            height: 48px;
            border-radius: 0;
            border: 1px solid var(--line);
            color: #23332b;
            font-size: 14px;
            box-shadow: none !important;
        }

        .form-control:focus,
        .form-select:focus {
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

        .muted-link,
        .terms-link {
            color: var(--forest);
            font-weight: 700;
            text-decoration: none;
            border: 0;
            background: transparent;
            padding: 0;
        }

        .muted-link:hover,
        .terms-link:hover {
            text-decoration: underline;
        }

        .seller-terms {
            display: none;
            border: 1px solid var(--line);
            background: #f7faf8;
            padding: 14px;
            margin-bottom: 16px;
        }

        .seller-terms.active {
            display: block;
        }

        .modal-content {
            border-radius: 8px;
            border: 0;
        }

        .terms-body {
            max-height: 62vh;
            overflow-y: auto;
            line-height: 1.7;
            color: #33443b;
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
                min-height: 220px;
            }

            .auth-panel {
                padding: 30px 22px;
            }
        }
    </style>
</head>

<body>
    <main class="auth-shell">
        <section class="auth-visual">
            <div class="auth-copy">
                <h1>Campify</h1>
                <p>Bangun toko outdoor, kelola produk, dan layani pembeli dalam satu sistem marketplace.</p>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="brand">
                    <div class="brand-mark">C</div>
                    <span>Campify</span>
                </div>

                <h2 class="auth-title">Buat Akun Baru</h2>
                <p class="auth-subtitle">Daftar sebagai pembeli atau seller menggunakan satu sistem autentikasi.</p>

                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Nama lengkap" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Daftar Sebagai</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="buyer" {{ old('role') == 'buyer' ? 'selected' : '' }}>Pembeli</option>
                            <option value="seller" {{ old('role') == 'seller' ? 'selected' : '' }}>Seller</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 8 karakter" required>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi" required>
                    </div>

                    <div id="sellerTermsBox" class="seller-terms {{ old('role') == 'seller' ? 'active' : '' }}">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="seller_terms" name="seller_terms" value="1" {{ old('seller_terms') ? 'checked' : '' }}>
                            <label class="form-check-label small text-muted" for="seller_terms">
                                Dengan ini, saya menyetujui seluruh
                                <button type="button" class="terms-link" data-bs-toggle="modal" data-bs-target="#sellerTermsModal">
                                    <strong>syarat dan ketentuan</strong>
                                </button>
                                yang berlaku sebagai Seller di platform ini.
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-auth">Daftar</button>
                </form>

                <div class="text-center mt-4 small text-muted">
                    Sudah punya akun? <a href="{{ route('login') }}" class="muted-link">Masuk sekarang</a>
                </div>
            </div>
        </section>
    </main>

    <div class="modal fade" id="sellerTermsModal" tabindex="-1" aria-labelledby="sellerTermsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="sellerTermsModalLabel">SYARAT DAN KETENTUAN PENDAFTARAN SELLER</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body terms-body">
                    <p>Dengan mendaftar sebagai Seller di platform ini, Anda menyatakan telah membaca, memahami, dan menyetujui seluruh syarat dan ketentuan berikut:</p>

                    <h6 class="fw-bold mt-4">1. Kewajiban Seller</h6>
                    <p>Seller wajib memberikan informasi yang benar, akurat, dan terbaru terkait identitas, produk, dan toko.</p>
                    <p>Seller bertanggung jawab penuh atas seluruh aktivitas yang dilakukan melalui akun miliknya.</p>
                    <p>Seller wajib menjaga kualitas produk dan pelayanan kepada pembeli.</p>

                    <h6 class="fw-bold mt-4">2. Ketentuan Produk</h6>
                    <p>Produk yang dijual harus legal, tidak melanggar hukum, dan tidak termasuk barang terlarang.</p>
                    <p>Dilarang menjual produk palsu, ilegal, berbahaya, atau melanggar hak kekayaan intelektual.</p>
                    <p>Informasi produk (deskripsi, harga, gambar) harus sesuai dengan kondisi sebenarnya.</p>

                    <h6 class="fw-bold mt-4">3. Larangan</h6>
                    <p>Seller dilarang untuk:</p>
                    <p>Melakukan penipuan, manipulasi transaksi, atau aktivitas yang merugikan pengguna lain.</p>
                    <p>Menggunakan kata-kata terlarang, menyesatkan, atau tidak pantas pada produk maupun komunikasi.</p>
                    <p>Melakukan spam, duplikasi produk secara berlebihan, atau praktik tidak sehat lainnya.</p>
                    <p>Menentukan harga yang tidak wajar atau menyesatkan.</p>
                    <p>Menyalahgunakan sistem platform untuk keuntungan pribadi yang melanggar aturan.</p>

                    <h6 class="fw-bold mt-4">4. Sistem Pelaporan dan Pengawasan</h6>
                    <p>Platform memiliki hak untuk menerima laporan dari pengguna lain terkait dugaan pelanggaran.</p>
                    <p>Sistem dapat melakukan deteksi otomatis terhadap aktivitas mencurigakan.</p>
                    <p>Semua aktivitas Seller dapat dipantau melalui sistem untuk menjaga keamanan dan kualitas platform.</p>

                    <h6 class="fw-bold mt-4">5. Sanksi dan Tindakan</h6>
                    <p>Jika Seller terbukti melanggar ketentuan, maka platform berhak memberikan:</p>
                    <p>Peringatan (warning)</p>
                    <p>Pembatasan fitur</p>
                    <p>Suspend akun sementara</p>
                    <p>Pemblokiran (ban) akun secara permanen</p>
                    <p>Sistem sanksi dapat menggunakan mekanisme strike (peringatan bertahap).</p>
                    <p>Dalam kasus pelanggaran berat, akun dapat langsung diblokir tanpa peringatan.</p>

                    <h6 class="fw-bold mt-4">6. Persetujuan</h6>
                    <p>Dengan mendaftar sebagai Seller, Anda menyatakan:</p>
                    <p>Bersedia mematuhi seluruh aturan yang berlaku.</p>
                    <p>Bersedia menerima sanksi termasuk pemblokiran akun (ban) jika melanggar ketentuan.</p>
                    <p>Memahami bahwa keputusan platform bersifat final dalam menjaga keamanan dan kenyamanan pengguna.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Saya Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const roleSelect = document.getElementById('role');
        const sellerTermsBox = document.getElementById('sellerTermsBox');
        const sellerTerms = document.getElementById('seller_terms');

        function syncSellerTerms() {
            const isSeller = roleSelect.value === 'seller';
            sellerTermsBox.classList.toggle('active', isSeller);
            sellerTerms.required = isSeller;
            sellerTerms.disabled = !isSeller;
            if (!isSeller) {
                sellerTerms.checked = false;
            }
        }

        roleSelect.addEventListener('change', syncSellerTerms);
        syncSellerTerms();
    </script>
</body>

</html>
