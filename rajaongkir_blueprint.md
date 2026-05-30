# 📦 Cetak Biru (Blueprint) Integrasi RajaOngkir API

Dokumen ini adalah spesifikasi teknis tingkat lanjut (Blueprint) untuk mengintegrasikan sistem ongkos kirim *real-time* menggunakan API RajaOngkir. Anda dapat menyimpan dokumen ini sebagai panduan atau *Roadmap* eksekusi di masa mendatang.

---

## 1. Persiapan Infrastruktur & Konfigurasi

### Akun & Kredensial
1. Daftar akun di **RajaOngkir.com** (Tipe akun *Starter* sudah cukup untuk JNE, POS, TIKI).
2. Dapatkan **API Key**.
3. Tambahkan ke dalam file `.env` Laravel Anda:
   ```env
   RAJAONGKIR_API_KEY=kunci_rahasia_anda_disini
   RAJAONGKIR_BASE_URL=https://api.rajaongkir.com/starter
   ```

---

## 2. Rencana Modifikasi Database (Migrations)

Kita perlu membuat file *migration* baru untuk menambahkan pilar data logistik:

### A. Tabel `products` (Produk)
Logistik membutuhkan satuan berat.
*   **[ADD]** `berat` (`integer`) - Satuan dalam Gram (gr). Berikan nilai default `1000` (1 Kg) agar produk lama tidak *error*.

### B. Tabel `stores` (Toko Penjual - Origin)
Sistem harus tahu paket dikirim dari kota mana.
*   **[ADD]** `rajaongkir_province_id` (`integer`, nullable)
*   **[ADD]** `rajaongkir_city_id` (`integer`, nullable)

### C. Tabel `users` (Pembeli - Destination)
Sistem harus tahu paket dikirim ke kota mana.
*   **[ADD]** `rajaongkir_province_id` (`integer`, nullable)
*   **[ADD]** `rajaongkir_city_id` (`integer`, nullable)

---

## 3. Arsitektur Backend & API Internal

Buat Controller baru khusus untuk menjembatani komunikasi: `app/Http/Controllers/Api/RajaOngkirController.php`.

### Daftar Endpoint API Internal (AJAX):
1.  **`GET /api/rajaongkir/provinces`**
    *   *Logic:* Memanggil RajaOngkir untuk daftar provinsi.
    *   *Optimization:* Gunakan `Cache::remember` selama 30 hari karena daftar provinsi jarang berubah. Ini mencegah limitasi API.
2.  **`GET /api/rajaongkir/cities?province={id}`**
    *   *Logic:* Memanggil daftar kota berdasarkan provinsi. Gunakan *Cache*.
3.  **`POST /api/rajaongkir/cost`**
    *   *Payload:* `{ origin, destination, weight, courier }`
    *   *Logic:* Menembak endpoint `/cost` RajaOngkir. Mengembalikan data daftar layanan (REG, YES, OKE) lengkap dengan harga dan estimasi hari.

---

## 4. Modifikasi Logika Bisnis Utama (Controllers)

> [!CAUTION]
> **Arsitektur Multi-Toko (Multi-Origin)**
> Jika pembeli melakukan checkout 3 barang dari 3 Toko berbeda, sistem tidak bisa menghitungnya sebagai 1 pengiriman! RajaOngkir mengharuskan 1 Kota Asal untuk 1 kalkulasi ongkir.

### Perombakan `PembeliCheckoutController.php`
*   **Keranjang harus di-Group (Dikelompokkan):** Saat checkout, keranjang (`Keranjang_pembeli`) harus dikelompokkan berdasarkan `store_id`.
*   **Hitung Total Berat per Toko:**
    *   Toko A: 2 Baju (500gr x 2 = 1000gr).
    *   Toko B: 1 Tenda (3000gr).
*   **Multiple API Calls:** Controller harus melakukan proses perhitungan terpisah. Minta harga ongkir dari Toko A ke Pembeli (1Kg), lalu minta lagi harga dari Toko B ke Pembeli (3Kg).
*   **Total Ongkir:** Adalah akumulasi dari ongkir Toko A + Toko B.

---

## 5. Rencana Perombakan Antarmuka (Frontend UI)

### A. Halaman Pengaturan Toko (Seller)
*   **[MODIFY]** `resources/views/seller/store/edit.blade.php`
*   Tambahkan 2 *Dropdown* menggunakan `<select>`.
*   Gunakan JavaScript (`fetch` API) untuk memanggil `/api/rajaongkir/provinces`. Ketika provinsi dipilih, otomatis memunculkan kota di *dropdown* kedua.

### B. Halaman Checkout Pembeli (Buyer)
*   **[MODIFY]** `resources/views/pembeli/checkout/index_pembeli.blade.php`
*   **Dropdown Alamat:** Sama seperti seller, ganti input teks alamat kota dengan Dropdown Provinsi & Kota.
*   **UI Multi-Pengiriman:** Buat blok UI terpisah untuk masing-masing toko di keranjang.
    *   *Paket 1: Dikirim dari Toko Adventure JKT*
    *   *Pilih Kurir: [Dropdown JNE / SiCepat]* -> Memicu AJAX loading harga.
    *   *Paket 2: Dikirim dari Toko Camping BDG*
    *   *Pilih Kurir: [Dropdown JNE / POS]* -> Memicu AJAX loading harga.
*   **Dynamic Grand Total:** Total belanja (Subtotal + Ongkir 1 + Ongkir 2) harus dihitung secara *real-time* di sisi klien menggunakan JavaScript sebelum tombol "Bayar" bisa ditekan.

---

## 6. Urutan Fase Pengerjaan (Roadmap)

Jika Anda ingin mengeksekusinya nanti, kerjakan dengan urutan bertahap berikut agar aplikasi tidak *crash*:

1. **Fase 1: Database & Migrations.** Eksekusi penambahan kolom berat dan ID kota.
2. **Fase 2: Pembuatan API Bridge.** Buat `RajaOngkirController` dan pastikan data provinsi/kota bisa muncul di *browser* (tes via Postman/Browser).
3. **Fase 3: UI Input Data.** Ubah form profil Penjual dan form alamat Pembeli. Pastikan ID kota tersimpan ke database.
4. **Fase 4: Logic Keranjang (Sulit).** Rombak sistem keranjang agar mendukung tampilan Multi-Toko (*Group by Store*).
5. **Fase 5: Checkout & Pembayaran.** Gabungkan API Cost (Harga) ke halaman checkout dan pastikan *Grand Total* masuk dengan benar ke tabel `orders`.
