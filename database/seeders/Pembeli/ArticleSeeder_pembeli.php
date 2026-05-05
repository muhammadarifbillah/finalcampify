<?php

namespace Database\Seeders\Pembeli;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pembeli\Article_pembeli;

class ArticleSeeder_pembeli extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Article_pembeli::create([
            'title' => 'Panduan Memilih Tenda Camping yang Tepat',
            'excerpt' => 'Pelajari cara memilih tenda camping yang sesuai dengan kebutuhan Anda.',
            'content' => 'Memilih tenda camping yang tepat sangat penting untuk kenyamanan dan keselamatan saat berkemah. Pertimbangkan ukuran, bahan, dan fitur-fitur yang dibutuhkan.',
            'author' => 'Admin Campify',
            'date' => now()->toDateString(),
            'category' => 'Tips Camping',
            'image' => 'storage/uploads/tenda-camping-1-artikel.jpeg',
        ]);

        Article_pembeli::create([
            'title' => '10 Alat Camping Wajib untuk Pemula',
            'excerpt' => 'Daftar alat-alat camping yang harus dimiliki oleh pemula.',
            'content' => 'Sebagai pemula, ada beberapa alat camping yang wajib Anda miliki. Mulai dari sleeping bag, kompor portabel, hingga first aid kit.',
            'author' => 'Admin Campify',
            'date' => now()->toDateString(),
            'category' => 'Pemula',
            'image' => 'storage/uploads/Backpacker-Package.jpg',
        ]);

        Article_pembeli::create([
            'title' => 'Lokasi Camping Terbaik di Indonesia',
            'excerpt' => 'Jelajahi lokasi-lokasi camping terbaik di Indonesia.',
            'content' => 'Indonesia memiliki banyak lokasi camping yang indah. Dari gunung hingga pantai, temukan tempat yang cocok untuk petualangan Anda.',
            'author' => 'Admin Campify',
            'date' => now()->toDateString(),
            'category' => 'Destinasi',
            'image' => 'storage/uploads/Landscape Wallpapers 6.jpg',
        ]);

        Article_pembeli::create([
            'title' => 'Tips Fotografi Outdoor yang Menakjubkan',
            'excerpt' => 'Dapatkan tips fotografi outdoor untuk mengabadikan momen liburan Anda.',
            'content' => 'Fotografi outdoor memerlukan teknik khusus untuk menghasilkan foto yang menakjubkan. Pelajari cara memanfaatkan cahaya alami, komposisi, dan peralatan yang tepat untuk hasil maksimal.',
            'author' => 'Admin Campify',
            'date' => now()->toDateString(),
            'category' => 'Tips Fotografi',
            'image' => 'storage/uploads/fotografi.jpg',
        ]);
    }
}