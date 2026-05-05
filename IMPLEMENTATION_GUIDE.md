# 📋 Panduan Implementasi Fitur Validasi & Otorisasi Campify Web

Dokumen ini menjelaskan semua fitur validasi dan otorisasi yang telah diimplementasikan sesuai dengan requirement.

---

## 🎯 Requirement yang Diimplementasikan

### 1. ✅ Verifikasi Pengusulan Sama Produk (Aksi)
**Requirement**: Verifikasi produk harus sesuai dengan toko yang mengelolanya

**Implementasi**:
- **File**: `app/Http/Controllers/Admin/ProductController.php`
- **Method**: `store(StoreProductRequest $request)`
- **Perubahan Utama**:
  - `store_id` sekarang **REQUIRED** (bukan nullable)
  - Validasi menggunakan custom form request class
  - Memastikan produk hanya bisa ditambah ke toko tertentu

```php
// Sebelum (SALAH):
'store_id' => 'nullable|integer|exists:stores,id',

// Sesudah (BENAR):
'store_id' => 'required|integer|exists:stores,id',
```

**Pesan Error**:
- "Toko harus dipilih untuk menambah produk."
- "Toko yang dipilih tidak valid atau tidak ditemukan."

---

### 2. ✅ Tambah Aturan Saat Registrasi
**Requirement**: Menambah aturan/validasi saat user mendaftar

**File Baru**: `app/Http/Requests/StoreUserRequest.php`

**Aturan Registrasi**:
| Field | Aturan | Pesan Error |
|-------|--------|------------|
| name | min:3, max:255, hanya huruf | "Nama hanya boleh mengandung huruf dan spasi" |
| email | format email, unique | "Email sudah terdaftar" |
| password | min:8, uppercase, lowercase, angka, simbol | "Password harus terdiri dari huruf besar, kecil, angka, dan simbol" |
| password_confirmation | confirmed | "Konfirmasi password tidak sesuai" |
| role | in:admin,user | "Role tidak valid" |

**Contoh Password yang Valid**:
- `SecurePass123!` ✅
- `MyPassword#456` ✅
- `weak123` ❌ (tidak ada uppercase/simbol)

---

### 3. ✅ Kurir Hanya Data Saja
**Requirement**: Kurir tidak bisa diedit/ditambah/dihapus, hanya bisa dilihat

**Implementasi**:
- **File**: `app/Http/Controllers/Admin/CourierController.php`
- **Methods yang dihapus**:
  - ❌ `edit($id)` - Tidak bisa buka form edit
  - ❌ `store(Request $request)` - Tidak bisa tambah kurir
  - ❌ `update(Request $request, $id)` - Tidak bisa edit kurir
  - ❌ `destroy($id)` - Tidak bisa hapus kurir

- **Methods yang tersisa**:
  - ✅ `index(Request $request)` - Lihat daftar kurir

**Routes yang Dihapus** (`routes/web.php`):
```php
// Dihapus:
Route::post('/couriers/store', [CourierController::class, 'store']);
Route::get('/couriers/edit/{id}', [CourierController::class, 'edit']);
Route::post('/couriers/update/{id}', [CourierController::class, 'update']);
Route::get('/couriers/delete/{id}', [CourierController::class, 'destroy']);

// Tetap ada:
Route::get('/couriers', [CourierController::class, 'index']); // Read-only
```

---

### 4. ✅ User & Nama Toko Harus Sesuai Login
**Requirement**: Pengguna hanya bisa akses data toko mereka sendiri

**Implementasi**:
- **File**: `app/Http/Controllers/Admin/StoreController.php`
- **Method**: `show($id)` dengan otorisasi tambahan

```php
// ✅ VERIFIKASI: User dan toko harus sesuai dengan pengguna yang login
if (auth()->check() && auth()->user()->role !== 'admin' && $store->user_id !== auth()->user()->id) {
    return back()->with('error', 'Anda tidak memiliki akses ke toko ini.');
}
```

**Logika Otorisasi**:
- **Admin**: Bisa lihat semua toko
- **User Biasa**: Hanya bisa lihat toko mereka sendiri
- **Jika akses ditolak**: Redirect dengan pesan error

---

### 5. ✅ Cek Produk di Kelola Toko
**Requirement**: Verifikasi produk dikelola toko, semua data toko bisa dilihat

**Implementasi**:
- **File**: `app/Http/Controllers/Admin/ProductController.php`
- **Methods**:
  - `store(StoreProductRequest $request)` - Validasi saat create
  - `show($id)` - Otorisasi saat view

```php
// ✅ VERIFIKASI: Produk harus dikelola oleh toko, user hanya bisa lihat produk toko mereka
if (auth()->check() && auth()->user()->role !== 'admin' && $product->store->user_id !== auth()->user()->id) {
    return back()->with('error', 'Anda tidak memiliki akses ke produk ini.');
}
```

**Logic**:
1. Saat tambah produk: Wajib pilih toko
2. Saat lihat produk: Cek kepemilikan toko
3. User hanya bisa lihat produk dari toko mereka

---

## 📁 File yang Dibuat/Dimodifikasi

### ✨ File Baru (Form Request Classes)

#### 1. `app/Http/Requests/StoreProductRequest.php`
Validasi & otorisasi untuk produk:
- Otorisasi: User hanya bisa buat produk untuk toko mereka
- Validasi nama, harga, stok, kurir
- Custom error messages

#### 2. `app/Http/Requests/StoreUserRequest.php`
Validasi registrasi user:
- Password dengan validasi ketat (8+ char, mixed case, angka, simbol)
- Email validation (format + unique)
- Nama validation (min 3, hanya huruf)
- Custom error messages dalam bahasa Indonesia

#### 3. `app/Http/Requests/StoreStoreRequest.php`
Validasi registrasi toko:
- Nama toko unique & min 5 karakter
- Otorisasi: User hanya bisa punya 1 toko
- Alamat & deskripsi dengan min/max length

### 🔧 File yang Dimodifikasi

#### 1. `app/Http/Controllers/Admin/ProductController.php`
- ✅ Import `StoreProductRequest`
- ✅ Update method `store()` untuk use form request
- ✅ Update method `show()` dengan authorization check
- ✅ Tambahan validasi store_id

#### 2. `app/Http/Controllers/Admin/StoreController.php`
- ✅ Update method `show()` dengan authorization check
- ✅ Memastikan user hanya akses toko mereka

#### 3. `app/Http/Controllers/Admin/CourierController.php`
- ✅ Dihapus methods: `edit()`, `store()`, `update()`, `destroy()`
- ✅ Hanya `index()` yang tersisa (read-only)

#### 4. `routes/web.php`
- ✅ Dihapus routes untuk courier edit/store/update/delete
- ✅ Hanya route `GET /couriers` yang tersisa

---

## 🔐 Fitur Keamanan Overview

| Fitur | Sebelum | Sesudah | Status |
|-------|---------|---------|--------|
| Store ID Wajib | ❌ Nullable | ✅ Required | ✅ Selesai |
| Otorisasi Toko | ❌ Tidak ada | ✅ Ada | ✅ Selesai |
| Otorisasi Produk | ❌ Tidak ada | ✅ Ada | ✅ Selesai |
| Validasi Registrasi | ❌ Minimal | ✅ Ketat | ✅ Selesai |
| Kurir Read-Only | ❌ Bisa edit | ✅ Hanya view | ✅ Selesai |

---

## 🧪 Testing Checklist

Untuk memverifikasi implementasi, lakukan testing berikut:

### Test 1: Validasi Produk
```
[ ] Coba tambah produk TANPA memilih toko
    └─ Harus error: "Toko harus dipilih untuk menambah produk."

[ ] Coba tambah produk dengan toko yang tidak valid
    └─ Harus error: "Toko yang dipilih tidak valid..."

[ ] Tambah produk dengan toko yang valid
    └─ Harus berhasil: "Produk berhasil ditambahkan ke toko [nama_toko]."
```

### Test 2: Otorisasi Toko
```
[ ] Login sebagai User A
    [ ] Lihat toko milik User A → ✅ Berhasil
    [ ] Lihat toko milik User B → ❌ Error "Tidak memiliki akses"

[ ] Login sebagai Admin
    [ ] Lihat toko User A → ✅ Berhasil
    [ ] Lihat toko User B → ✅ Berhasil
```

### Test 3: Otorisasi Produk
```
[ ] Login sebagai User A
    [ ] Lihat produk dari toko A → ✅ Berhasil
    [ ] Lihat produk dari toko B → ❌ Error "Tidak memiliki akses"
```

### Test 4: Kurir Read-Only
```
[ ] Coba akses /admin/couriers/edit/1 → ❌ Route tidak ada
[ ] Coba akses /admin/couriers/store (POST) → ❌ Route tidak ada
[ ] Akses /admin/couriers (GET) → ✅ Berhasil (view only)
```

### Test 5: Validasi Registrasi User
```
[ ] Register dengan password "weak123"
    └─ Harus error: "Password harus terdiri dari..."

[ ] Register dengan email yang sudah ada
    └─ Harus error: "Email sudah terdaftar"

[ ] Register dengan nama "John123"
    └─ Harus error: "Nama hanya boleh mengandung..."

[ ] Register dengan data valid
    └─ ✅ Berhasil
```

---

## 📊 Model Relationships (Sudah Ada)

Struktur relasi yang sudah ada dan digunakan:

```
User (1) ──hasOne──> Store (1) ──hasMany──> Product (Many)
                                      ├──belongsTo──> Courier (Many)
                                      └──has────────> Transaction (Many)
```

**Key Relationships**:
- `User::store()` - Satu user punya satu toko
- `Store::products()` - Satu toko punya banyak produk
- `Product::store()` - Satu produk milik satu toko
- `Product::couriers()` - Satu produk bisa pakai banyak kurir

---

## 🚀 Cara Menggunakan Form Request di Controller

Sebelum mengimplementasikan di controller lain, berikut contohnya:

```php
// ❌ LAMA (Tidak gunakan):
public function store(Request $request)
{
    $data = $request->validate([...]);
}

// ✅ BARU (Gunakan):
use App\Http\Requests\StoreProductRequest;

public function store(StoreProductRequest $request)
{
    $data = $request->validated(); // Sudah validated & authorized
    // Process...
}
```

---

## 📝 Catatan Penting

1. **Courier Hanya Read-Only**: Kurir tidak bisa ditambah/diedit melalui web. Jika perlu manage kurir, harus setup di database langsung atau buat admin panel khusus.

2. **Store Requirement**: Setiap produk HARUS memiliki store_id. Tidak ada produk yang "orphan".

3. **User Authorization**: Semua fitur yang involve toko/produk sudah ada authorization check untuk:
   - Admin: Akses semua
   - User biasa: Akses hanya data mereka

4. **Password Policy**: Password registration mengikuti best practices:
   - Min 8 karakter
   - Harus ada huruf besar & kecil
   - Harus ada angka
   - Harus ada simbol

---

## 📞 Support

Jika ada pertanyaan atau issue, silakan cek:
1. Verifikasi sudah migrate/update database schema
2. Pastikan semua form request diimport dengan benar
3. Cek error logs di `storage/logs/`

---

**Last Updated**: April 28, 2026
**Implemented By**: GitHub Copilot
**Status**: ✅ Complete
