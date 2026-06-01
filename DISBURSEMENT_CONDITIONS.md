# Kondisi Pencairan Dana

Halaman Admin > Pencairan Dana hanya memproses transaksi pembelian murni, yaitu order yang memiliki detail `type = buy` dan tidak memiliki detail `type = rent`.

## Syarat Siap Dicairkan

Order ditampilkan sebagai `Siap Dicairkan` jika semua kondisi berikut terpenuhi:

1. Order adalah transaksi pembelian murni.
2. `orders.status = selesai`.
3. `orders.is_disbursed` kosong atau `false`.
4. `orders.received_at` sudah terisi.
5. `orders.received_at` sudah lewat minimal 7 hari.
6. Tidak ada retur aktif. Status retur yang dianggap selesai hanya `rejected` dan `completed`.

Aturan ini dipusatkan di `App\Services\OrderDisbursementService`.

## Pencairan Manual

Saat admin klik `Cairkan`:

1. Sistem mengunci row order dengan database transaction.
2. Sistem mengecek ulang eligibility dari database terbaru.
3. Jika masih valid, order diupdate:
   - `is_disbursed = true`
   - `disbursed_at = now()`
4. Sistem mencatat audit per toko/seller ke tabel `order_disbursements`.

Audit log mencatat:

- order
- toko/seller
- nominal item subtotal per toko/seller
- admin yang mencatat pencairan
- sumber pencairan (`manual` atau `auto`)
- waktu pencairan

## Pencairan Otomatis

Command:

```bash
php artisan orders:auto-disburse
```

Scheduler:

```php
Schedule::command('orders:auto-disburse')->dailyAt('00:30')->withoutOverlapping();
```

Server tetap harus menjalankan Laravel scheduler, misalnya via cron atau Windows Task Scheduler:

```bash
php artisan schedule:run
```

## Data Lama

Migration `2026_06_01_000100_backfill_received_at_for_completed_orders` mengisi `received_at` untuk order lama yang sudah `selesai` tetapi belum memiliki tanggal penerimaan. Nilainya diambil dari `updated_at`, lalu fallback ke `created_at`, lalu `NOW()`.
