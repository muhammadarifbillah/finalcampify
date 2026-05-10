<?php

namespace Database\Seeders;

use App\Models\ChatbotResponse;
use Illuminate\Database\Seeder;

class ChatbotResponseSeeder extends Seeder
{
    public function run(): void
    {
        $responses = [
            [
                'keyword' => 'halo',
                'response' => 'Halo! Selamat datang di Campify. Ada yang bisa saya bantu terkait produk, sewa, atau pesanan?',
            ],
            [
                'keyword' => 'cek pesanan',
                'response' => 'Untuk cek pesanan, buka menu Pesanan lalu pilih detail order. Kamu bisa lihat status, resi, dan rincian item di sana.',
            ],
            [
                'keyword' => 'status pesanan',
                'response' => 'Status pesanan biasanya: menunggu → diproses → dikirim → selesai. Jika dibatalkan, status akan berubah menjadi dibatalkan.',
            ],
            [
                'keyword' => 'pembayaran',
                'response' => 'Metode pembayaran yang tersedia: transfer bank, e-wallet/QRIS, dan COD (jika tersedia). Pastikan unggah bukti pembayaran untuk transfer/ewallet.',
            ],
            [
                'keyword' => 'ongkir',
                'response' => 'Ongkir mengikuti kurir/jenis pengiriman yang dipilih saat checkout. Biaya akan otomatis ditambahkan ke total pembayaran.',
            ],
            [
                'keyword' => 'jne',
                'response' => 'JNE Express: estimasi 2-3 hari (tergantung wilayah). Pilih JNE di bagian pengiriman saat checkout.',
            ],
            [
                'keyword' => 'gosend',
                'response' => 'GoSend: estimasi 1 hari untuk area yang terjangkau. Pilih GoSend di bagian pengiriman saat checkout.',
            ],
            [
                'keyword' => 'retur',
                'response' => 'Untuk retur/komplain, masuk ke detail pesanan lalu ikuti instruksi retur (jika tersedia). Sertakan alasan dan bukti foto agar proses lebih cepat.',
            ],
            [
                'keyword' => 'sewa',
                'response' => 'Untuk sewa, pilih produk yang bertanda sewa lalu tentukan tanggal mulai dan durasi. Pastikan baca syarat sewa dan kondisi barang.',
            ],
            [
                'keyword' => 'stok',
                'response' => 'Ketersediaan stok tertera di halaman produk. Jika stok habis, kamu bisa pantau lagi atau chat seller untuk info restock.',
            ],
            [
                'keyword' => 'hubungi penjual',
                'response' => 'Kamu bisa klik tombol chat di halaman detail produk untuk menghubungi seller terkait stok, pengiriman, atau detail barang.',
            ],
            [
                'keyword' => 'lapor',
                'response' => 'Jika ada masalah dengan produk/toko/chat, kamu bisa laporkan lewat fitur laporan. Admin akan meninjau laporan tersebut.',
            ],
            [
                'keyword' => 'produk ditolak',
                'response' => 'Jika produk ditolak admin, cek alasan (flag). Perbaiki deskripsi/harga/foto lalu ajukan ulang agar bisa direview kembali.',
            ],
            [
                'keyword' => 'estimasi',
                'response' => 'Estimasi pengiriman bisa berbeda per wilayah. Kamu bisa lihat estimasi pada opsi pengiriman saat checkout.',
            ],
        ];

        foreach ($responses as $row) {
            ChatbotResponse::updateOrCreate(
                ['keyword' => $row['keyword']],
                $row
            );
        }
    }
}

