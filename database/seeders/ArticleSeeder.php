<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil admin user atau buat jika tidak ada
        $adminUser = User::where('role', 'admin')->first() ?? User::first();

        if (!$adminUser) {
            // Jika tidak ada user, lewatkan seeder
            return;
        }

        $articles = [
            [
                'user_id' => $adminUser->id,
                'title' => 'Panduan Memilih Tenda Camping yang Tepat',
                'judul' => 'Panduan Memilih Tenda Camping yang Tepat',
                'content' => 'Memilih tenda yang tepat adalah kunci kesuksesan perjalanan camping Anda. Ada beberapa faktor yang perlu dipertimbangkan seperti ukuran, berat, material, dan ketahanan cuaca. Artikel ini akan memandu Anda memilih tenda yang sesuai dengan kebutuhan dan budget Anda. Tenda A-frame cocok untuk pemula, dome tent untuk keamanan di angin kuat, dan tunnel tent untuk kapasitas besar.',
                'konten' => 'Memilih tenda yang tepat adalah kunci kesuksesan perjalanan camping Anda. Ada beberapa faktor yang perlu dipertimbangkan seperti ukuran, berat, material, dan ketahanan cuaca. Artikel ini akan memandu Anda memilih tenda yang sesuai dengan kebutuhan dan budget Anda. Tenda A-frame cocok untuk pemula, dome tent untuk keamanan di angin kuat, dan tunnel tent untuk kapasitas besar.',
                'kategori_slug' => 'camping-tips',
                'image' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?w=800',
                'gambar' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?w=800',
                'thumbnail' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?w=300&h=200&fit=crop',
                'status' => 'publish',
                'views' => 325,
                'waktu_posting' => Carbon::now()->subDays(5),
            ],
            [
                'user_id' => $adminUser->id,
                'title' => 'Destinasi Camping Terbaik di Indonesia',
                'judul' => 'Destinasi Camping Terbaik di Indonesia',
                'content' => 'Indonesia memiliki banyak destinasi camping yang menakjubkan dengan pemandangan alam yang memukau. Dari Bromo di Jawa Timur, Rinjani di Lombok, hingga Danau Toba di Sumatera Utara, setiap tempat menawarkan pengalaman unik. Artikel ini akan membahas 10 destinasi camping terbaik yang wajib Anda kunjungi.',
                'konten' => 'Indonesia memiliki banyak destinasi camping yang menakjubkan dengan pemandangan alam yang memukau. Dari Bromo di Jawa Timur, Rinjani di Lombok, hingga Danau Toba di Sumatera Utara, setiap tempat menawarkan pengalaman unik. Artikel ini akan membahas 10 destinasi camping terbaik yang wajib Anda kunjungi.',
                'kategori_slug' => 'destinasi',
                'image' => 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800',
                'gambar' => 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800',
                'thumbnail' => 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=300&h=200&fit=crop',
                'status' => 'publish',
                'views' => 512,
                'waktu_posting' => Carbon::now()->subDays(3),
            ],
            [
                'user_id' => $adminUser->id,
                'title' => 'Peralatan Camping Wajib Dimiliki Pemula',
                'judul' => 'Peralatan Camping Wajib Dimiliki Pemula',
                'content' => 'Jika Anda baru pertama kali ingin camping, pasti bingung apa saja yang perlu dibeli. Jangan khawatir, dalam artikel ini kami akan memberikan daftar lengkap peralatan camping wajib yang perlu Anda miliki. Mulai dari sleeping bag, matras, lampu senter, hingga peralatan memasak yang portable.',
                'konten' => 'Jika Anda baru pertama kali ingin camping, pasti bingung apa saja yang perlu dibeli. Jangan khawatir, dalam artikel ini kami akan memberikan daftar lengkap peralatan camping wajib yang perlu Anda miliki. Mulai dari sleeping bag, matras, lampu senter, hingga peralatan memasak yang portable.',
                'kategori_slug' => 'camping-tips',
                'image' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=800',
                'gambar' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=800',
                'thumbnail' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=200&fit=crop',
                'status' => 'publish',
                'views' => 478,
                'waktu_posting' => Carbon::now()->subDays(7),
            ],
            [
                'user_id' => $adminUser->id,
                'title' => 'Cara Merawat Peralatan Outdoor Anda',
                'judul' => 'Cara Merawat Peralatan Outdoor Anda',
                'content' => 'Peralatan outdoor yang baik memerlukan perawatan yang tepat agar tahan lama. Dalam artikel ini, kami akan membagikan tips dan trik untuk merawat tenda, sleeping bag, dan peralatan lainnya. Dengan perawatan yang tepat, peralatan Anda bisa bertahan hingga puluhan tahun.',
                'konten' => 'Peralatan outdoor yang baik memerlukan perawatan yang tepat agar tahan lama. Dalam artikel ini, kami akan membagikan tips dan trik untuk merawat tenda, sleeping bag, dan peralatan lainnya. Dengan perawatan yang tepat, peralatan Anda bisa bertahan hingga puluhan tahun.',
                'kategori_slug' => 'perawatan',
                'image' => 'https://images.unsplash.com/photo-1471286174890-9c112ffca546?w=800',
                'gambar' => 'https://images.unsplash.com/photo-1471286174890-9c112ffca546?w=800',
                'thumbnail' => 'https://images.unsplash.com/photo-1471286174890-9c112ffca546?w=300&h=200&fit=crop',
                'status' => 'draft',
                'views' => 0,
                'waktu_posting' => Carbon::now(),
            ],
            [
                'user_id' => $adminUser->id,
                'title' => 'Budget Friendly Camping untuk Keluarga',
                'judul' => 'Budget Friendly Camping untuk Keluarga',
                'content' => 'Camping tidak harus mahal! Anda bisa menikmati pengalaman camping yang menyenangkan dengan budget terbatas. Artikel ini akan memberikan tips-tips hemat dalam camping seperti memilih tempat yang strategis, membawa makanan sendiri, dan berbagi peralatan dengan teman-teman.',
                'konten' => 'Camping tidak harus mahal! Anda bisa menikmati pengalaman camping yang menyenangkan dengan budget terbatas. Artikel ini akan memberikan tips-tips hemat dalam camping seperti memilih tempat yang strategis, membawa makanan sendiri, dan berbagi peralatan dengan teman-teman.',
                'kategori_slug' => 'budget',
                'image' => 'https://images.unsplash.com/photo-1445681696728-8ef16f8c8cfe?w=800',
                'gambar' => 'https://images.unsplash.com/photo-1445681696728-8ef16f8c8cfe?w=800',
                'thumbnail' => 'https://images.unsplash.com/photo-1445681696728-8ef16f8c8cfe?w=300&h=200&fit=crop',
                'status' => 'publish',
                'views' => 289,
                'waktu_posting' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($articles as $article) {
            Article::create($article);
        }

        $this->command->info('✅ 5 artikel contoh berhasil ditambahkan!');
    }
}
