<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan tabel artikel terlebih dahulu agar tidak menumpuk saat di-seed ulang
        DB::table('articles')->truncate();

        // Ambil admin user atau buat jika tidak ada
        $adminUser = User::where('role', 'admin')->first() ?? User::first();

        if (!$adminUser) {
            $this->command->error('Tidak ada user Admin ditemukan. Silakan jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $articles = [
            [
                'user_id' => $adminUser->id,
                'title' => 'Panduan Lengkap Memilih Tenda Camping Sesuai Musim',
                'judul' => 'Panduan Lengkap Memilih Tenda Camping Sesuai Musim',
                'content' => "Memilih tenda yang tepat adalah investasi terbaik untuk kenyamanan Anda saat berada di alam bebas. Banyak pemula yang salah memilih tenda sehingga pengalaman camping pertama mereka berujung pada kedinginan atau tenda bocor saat hujan.\n\nFaktor pertama yang harus Anda perhatikan adalah musim atau cuaca lokasi camping. Jika Anda berencana mendaki gunung dengan cuaca ekstrem, tenda jenis '4-season' dengan material Nylon tebal dan frame aluminium adalah pilihan mutlak. Namun, jika Anda hanya camping ceria di akhir pekan bersama keluarga di dataran rendah, tenda '3-season' berbahan Polyester sudah lebih dari cukup karena sirkulasi udaranya lebih baik.\n\nJangan lupa perhatikan kapasitas tenda. Selalu kurangi 1 dari kapasitas maksimal yang tertera di label. Jika label mengatakan 'Kapasitas 4 Orang', idealnya tenda tersebut paling nyaman digunakan untuk 3 orang dewasa ditambah barang bawaan Anda.",
                'konten' => "Memilih tenda yang tepat adalah investasi terbaik untuk kenyamanan Anda saat berada di alam bebas. Banyak pemula yang salah memilih tenda sehingga pengalaman camping pertama mereka berujung pada kedinginan atau tenda bocor saat hujan.\n\nFaktor pertama yang harus Anda perhatikan adalah musim atau cuaca lokasi camping. Jika Anda berencana mendaki gunung dengan cuaca ekstrem, tenda jenis '4-season' dengan material Nylon tebal dan frame aluminium adalah pilihan mutlak. Namun, jika Anda hanya camping ceria di akhir pekan bersama keluarga di dataran rendah, tenda '3-season' berbahan Polyester sudah lebih dari cukup karena sirkulasi udaranya lebih baik.\n\nJangan lupa perhatikan kapasitas tenda. Selalu kurangi 1 dari kapasitas maksimal yang tertera di label. Jika label mengatakan 'Kapasitas 4 Orang', idealnya tenda tersebut paling nyaman digunakan untuk 3 orang dewasa ditambah barang bawaan Anda.",
                'kategori_slug' => 'gear-review',
                'image' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=800&q=80',
                'gambar' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=800&q=80',
                'thumbnail' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=300&h=200&q=80',
                'status' => 'publish',
                'views' => 0,
                'waktu_posting' => Carbon::now()->subDays(10),
            ],
            [
                'user_id' => $adminUser->id,
                'title' => '7 Peralatan Dapur Portabel Wajib untuk Glamping',
                'judul' => '7 Peralatan Dapur Portabel Wajib untuk Glamping',
                'content' => "Camping tidak melulu soal makan mi instan atau makanan kaleng. Dengan era Glamping (Glamorous Camping) yang semakin populer, pengalaman memasak di alam bebas kini bisa semewah di dapur rumah Anda sendiri.\n\nUntuk mewujudkannya, Anda membutuhkan peralatan dapur portabel yang tepat. Pertama, kompor lipat berbahan bakar gas hi-cook sangat direkomendasikan karena ringan dan apinya stabil. Kedua, set nesting (panci dan wajan yang bisa ditumpuk) berbahan titanium atau anodized aluminum. Bahan ini menghantarkan panas lebih cepat dan sangat mudah dibersihkan meski hanya dengan tisu basah.\n\nSelain itu, jangan lupakan cooler box berinsulasi tinggi untuk menjaga kesegaran daging dan sayuran Anda hingga 48 jam. Dengan persiapan ini, Anda bisa memasak steak atau sup hangat di tengah dinginnya udara pegunungan.",
                'konten' => "Camping tidak melulu soal makan mi instan atau makanan kaleng. Dengan era Glamping (Glamorous Camping) yang semakin populer, pengalaman memasak di alam bebas kini bisa semewah di dapur rumah Anda sendiri.\n\nUntuk mewujudkannya, Anda membutuhkan peralatan dapur portabel yang tepat. Pertama, kompor lipat berbahan bakar gas hi-cook sangat direkomendasikan karena ringan dan apinya stabil. Kedua, set nesting (panci dan wajan yang bisa ditumpuk) berbahan titanium atau anodized aluminum. Bahan ini menghantarkan panas lebih cepat dan sangat mudah dibersihkan meski hanya dengan tisu basah.\n\nSelain itu, jangan lupakan cooler box berinsulasi tinggi untuk menjaga kesegaran daging dan sayuran Anda hingga 48 jam. Dengan persiapan ini, Anda bisa memasak steak atau sup hangat di tengah dinginnya udara pegunungan.",
                'kategori_slug' => 'camping-tips',
                'image' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=800&q=80',
                'gambar' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=800&q=80',
                'thumbnail' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=300&h=200&q=80',
                'status' => 'publish',
                'views' => 0,
                'waktu_posting' => Carbon::now()->subDays(8),
            ],
            [
                'user_id' => $adminUser->id,
                'title' => 'Cara Aman Menyalakan dan Memadamkan Api Unggun',
                'judul' => 'Cara Aman Menyalakan dan Memadamkan Api Unggun',
                'content' => "Api unggun adalah jiwa dari setiap kegiatan camping. Menghangatkan tubuh, memanggang marshmallow, atau sekadar bercengkrama mengelilingi nyala api adalah momen yang tak tergantikan. Namun, api juga bisa menjadi bencana jika tidak ditangani dengan tanggung jawab.\n\nSelalu pastikan Anda menyalakan api di spot yang memang sudah disediakan (fire ring) atau buatlah area yang bersih dari dedaunan kering dalam radius 3 meter. Gunakan ranting kecil (tinder) sebagai pemantik awal, lalu perlahan susun kayu yang lebih besar dengan bentuk menyilang menyerupai piramida agar sirkulasi oksigen tetap terjaga.\n\nYang paling penting adalah proses memadamkannya. Jangan pernah meninggalkan api yang masih berasap. Siram dengan air sedikit demi sedikit sambil diaduk menggunakan kayu atau sekop kecil. Pastikan abu benar-benar dingin saat Anda menyentuhnya sebelum Anda meninggalkan lokasi perkemahan.",
                'konten' => "Api unggun adalah jiwa dari setiap kegiatan camping. Menghangatkan tubuh, memanggang marshmallow, atau sekadar bercengkrama mengelilingi nyala api adalah momen yang tak tergantikan. Namun, api juga bisa menjadi bencana jika tidak ditangani dengan tanggung jawab.\n\nSelalu pastikan Anda menyalakan api di spot yang memang sudah disediakan (fire ring) atau buatlah area yang bersih dari dedaunan kering dalam radius 3 meter. Gunakan ranting kecil (tinder) sebagai pemantik awal, lalu perlahan susun kayu yang lebih besar dengan bentuk menyilang menyerupai piramida agar sirkulasi oksigen tetap terjaga.\n\nYang paling penting adalah proses memadamkannya. Jangan pernah meninggalkan api yang masih berasap. Siram dengan air sedikit demi sedikit sambil diaduk menggunakan kayu atau sekop kecil. Pastikan abu benar-benar dingin saat Anda menyentuhnya sebelum Anda meninggalkan lokasi perkemahan.",
                'kategori_slug' => 'survival',
                'image' => 'https://images.unsplash.com/photo-1537225228614-56cc355cb99c?auto=format&fit=crop&w=800&q=80',
                'gambar' => 'https://images.unsplash.com/photo-1537225228614-56cc355cb99c?auto=format&fit=crop&w=800&q=80',
                'thumbnail' => 'https://images.unsplash.com/photo-1537225228614-56cc355cb99c?auto=format&fit=crop&w=300&h=200&q=80',
                'status' => 'publish',
                'views' => 0,
                'waktu_posting' => Carbon::now()->subDays(6),
            ],
            [
                'user_id' => $adminUser->id,
                'title' => 'Mengenal Tren Campervan: Liburan Tanpa Batas',
                'judul' => 'Mengenal Tren Campervan: Liburan Tanpa Batas',
                'content' => "Beberapa tahun terakhir, tren Campervan atau liburan menggunakan mobil van yang dimodifikasi menjadi 'rumah berjalan' meledak di Indonesia. Konsep ini menawarkan kebebasan tak terbatas. Anda tidak perlu pusing memikirkan jadwal check-in hotel atau repot membongkar pasang tenda saat cuaca tiba-tiba memburuk.\n\nModifikasi campervan dasar biasanya mencakup instalasi tempat tidur lipat, sistem kelistrikan mandiri (solar panel dan aki cadangan), serta mini kitchen set di bagian belakang mobil. Kendaraan jenis MPV atau minibus sering menjadi pilihan utama masyarakat kita karena ukurannya yang pas dengan kondisi jalanan lokal.\n\nSelain fleksibilitas, campervan juga mendorong gaya hidup 'slow travel'. Anda bisa berhenti di pinggir danau tersembunyi, membuka pintu belakang mobil, dan langsung menikmati kopi hangat dengan pemandangan alam terbaik tanpa perlu bersinggungan dengan hiruk-pikuk turis lainnya.",
                'konten' => "Beberapa tahun terakhir, tren Campervan atau liburan menggunakan mobil van yang dimodifikasi menjadi 'rumah berjalan' meledak di Indonesia. Konsep ini menawarkan kebebasan tak terbatas. Anda tidak perlu pusing memikirkan jadwal check-in hotel atau repot membongkar pasang tenda saat cuaca tiba-tiba memburuk.\n\nModifikasi campervan dasar biasanya mencakup instalasi tempat tidur lipat, sistem kelistrikan mandiri (solar panel dan aki cadangan), serta mini kitchen set di bagian belakang mobil. Kendaraan jenis MPV atau minibus sering menjadi pilihan utama masyarakat kita karena ukurannya yang pas dengan kondisi jalanan lokal.\n\nSelain fleksibilitas, campervan juga mendorong gaya hidup 'slow travel'. Anda bisa berhenti di pinggir danau tersembunyi, membuka pintu belakang mobil, dan langsung menikmati kopi hangat dengan pemandangan alam terbaik tanpa perlu bersinggungan dengan hiruk-pikuk turis lainnya.",
                'kategori_slug' => 'destinasi',
                'image' => 'https://images.unsplash.com/photo-1510312305653-8ed496efae84?auto=format&fit=crop&w=800&q=80',
                'gambar' => 'https://images.unsplash.com/photo-1510312305653-8ed496efae84?auto=format&fit=crop&w=800&q=80',
                'thumbnail' => 'https://images.unsplash.com/photo-1510312305653-8ed496efae84?auto=format&fit=crop&w=300&h=200&q=80',
                'status' => 'publish',
                'views' => 0,
                'waktu_posting' => Carbon::now()->subDays(4),
            ],
            [
                'user_id' => $adminUser->id,
                'title' => 'Teknik Perawatan Sleeping Bag Agar Tetap Mengembang',
                'judul' => 'Teknik Perawatan Sleeping Bag Agar Tetap Mengembang',
                'content' => "Banyak penggiat alam yang mengeluh sleeping bag mereka menjadi kempes dan tidak lagi hangat setelah beberapa kali digunakan. Masalahnya bukan pada kualitas barangnya, melainkan pada cara perawatannya yang salah, terutama saat proses penyimpanan dan pencucian.\n\nAturan emas yang pertama: Jangan pernah menyimpan sleeping bag Anda dalam keadaan tergulung rapat di dalam kantong kompresinya saat berada di rumah. Hal ini akan merusak loft (rongga udara) pada bahan insulasinya, baik itu dakron maupun bulu angsa. Selalu gantung sleeping bag Anda di lemari atau simpan dalam kantong jaring yang sangat longgar.\n\nKedua, hindari mencuci sleeping bag dengan mesin cuci bukaan atas (top load) karena agitatornya bisa merobek jahitan. Gunakan sabun khusus peralatan outdoor (tanpa deterjen keras) dan cuci secara perlahan dengan tangan di dalam bak mandi. Keringkan dengan cara dijemur mendatar, bukan digantung secara vertikal.",
                'konten' => "Banyak penggiat alam yang mengeluh sleeping bag mereka menjadi kempes dan tidak lagi hangat setelah beberapa kali digunakan. Masalahnya bukan pada kualitas barangnya, melainkan pada cara perawatannya yang salah, terutama saat proses penyimpanan dan pencucian.\n\nAturan emas yang pertama: Jangan pernah menyimpan sleeping bag Anda dalam keadaan tergulung rapat di dalam kantong kompresinya saat berada di rumah. Hal ini akan merusak loft (rongga udara) pada bahan insulasinya, baik itu dakron maupun bulu angsa. Selalu gantung sleeping bag Anda di lemari atau simpan dalam kantong jaring yang sangat longgar.\n\nKedua, hindari mencuci sleeping bag dengan mesin cuci bukaan atas (top load) karena agitatornya bisa merobek jahitan. Gunakan sabun khusus peralatan outdoor (tanpa deterjen keras) dan cuci secara perlahan dengan tangan di dalam bak mandi. Keringkan dengan cara dijemur mendatar, bukan digantung secara vertikal.",
                'kategori_slug' => 'perawatan',
                'image' => 'https://images.unsplash.com/photo-1445307396158-a24d100fac09?auto=format&fit=crop&w=800&q=80',
                'gambar' => 'https://images.unsplash.com/photo-1445307396158-a24d100fac09?auto=format&fit=crop&w=800&q=80',
                'thumbnail' => 'https://images.unsplash.com/photo-1445307396158-a24d100fac09?auto=format&fit=crop&w=300&h=200&q=80',
                'status' => 'publish',
                'views' => 0,
                'waktu_posting' => Carbon::now()->subDays(3),
            ],
            [
                'user_id' => $adminUser->id,
                'title' => 'Hiking vs Trekking: Apa Perbedaannya?',
                'judul' => 'Hiking vs Trekking: Apa Perbedaannya?',
                'content' => "Banyak orang menggunakan istilah 'Hiking' dan 'Trekking' secara bergantian, padahal di dunia aktivitas alam bebas, keduanya memiliki arti dan tingkat kesulitan yang sangat berbeda. Memahami perbedaan ini sangat penting agar Anda tidak salah membawa peralatan atau salah mempersiapkan fisik.\n\nHiking umumnya merujuk pada aktivitas berjalan kaki melintasi jalur alam yang sudah jelas dan terawat (seperti di taman nasional). Aktivitas ini biasanya memakan waktu singkat, mulai dari beberapa jam hingga maksimal satu hari penuh (day hike), dan Anda akan kembali ke titik awal atau penginapan sebelum malam tiba.\n\nSebaliknya, Trekking adalah perjalanan yang jauh lebih menantang dan memakan waktu berhari-hari. Trekking seringkali dilakukan di daerah terpencil dengan jalur yang minim rambu petunjuk. Para trekker harus mandiri, membawa tenda, perbekalan makanan berat, dan peralatan navigasi lengkap. Singkatnya, semua trekking adalah berjalan kaki, tapi tidak semua berjalan kaki adalah trekking.",
                'konten' => "Banyak orang menggunakan istilah 'Hiking' dan 'Trekking' secara bergantian, padahal di dunia aktivitas alam bebas, keduanya memiliki arti dan tingkat kesulitan yang sangat berbeda. Memahami perbedaan ini sangat penting agar Anda tidak salah membawa peralatan atau salah mempersiapkan fisik.\n\nHiking umumnya merujuk pada aktivitas berjalan kaki melintasi jalur alam yang sudah jelas dan terawat (seperti di taman nasional). Aktivitas ini biasanya memakan waktu singkat, mulai dari beberapa jam hingga maksimal satu hari penuh (day hike), dan Anda akan kembali ke titik awal atau penginapan sebelum malam tiba.\n\nSebaliknya, Trekking adalah perjalanan yang jauh lebih menantang dan memakan waktu berhari-hari. Trekking seringkali dilakukan di daerah terpencil dengan jalur yang minim rambu petunjuk. Para trekker harus mandiri, membawa tenda, perbekalan makanan berat, dan peralatan navigasi lengkap. Singkatnya, semua trekking adalah berjalan kaki, tapi tidak semua berjalan kaki adalah trekking.",
                'kategori_slug' => 'camping-tips',
                'image' => 'https://images.unsplash.com/photo-1523987355523-c7b5b0dd90a7?auto=format&fit=crop&w=800&q=80',
                'gambar' => 'https://images.unsplash.com/photo-1523987355523-c7b5b0dd90a7?auto=format&fit=crop&w=800&q=80',
                'thumbnail' => 'https://images.unsplash.com/photo-1523987355523-c7b5b0dd90a7?auto=format&fit=crop&w=300&h=200&q=80',
                'status' => 'publish',
                'views' => 0,
                'waktu_posting' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => $adminUser->id,
                'title' => '5 Hutan Pinus Paling Estetik untuk Camping Akhir Pekan',
                'judul' => '5 Hutan Pinus Paling Estetik untuk Camping Akhir Pekan',
                'content' => "Camping di tengah rimbunnya hutan pinus selalu memberikan sensasi magis. Suara angin yang menyapu dedaunan jarum, aroma khas pinus yang menenangkan, serta kabut tipis di pagi hari adalah obat stres terbaik bagi warga kota. Jika Anda mencari referensi, kami telah merangkumnya untuk Anda.\n\nDaerah Lembang dan Pangalengan di Jawa Barat masih menjadi primadona. Pineus Tilu di Pangalengan misalnya, menawarkan pengalaman unik mendirikan tenda tepat di atas panggung kayu pinggir sungai yang dikelilingi hutan pinus lebat. Fasilitasnya sangat lengkap sehingga cocok untuk membawa anak-anak.\n\nBergeser ke Jawa Tengah, area lereng Gunung Merbabu seperti Kopeng juga memiliki kawasan camping pinus yang udaranya sangat sejuk. Pastikan Anda membawa hammock saat berkunjung ke destinasi ini, karena tidur menggantung di antara dua pohon pinus adalah pengalaman relaksasi yang wajib dicoba.",
                'konten' => "Camping di tengah rimbunnya hutan pinus selalu memberikan sensasi magis. Suara angin yang menyapu dedaunan jarum, aroma khas pinus yang menenangkan, serta kabut tipis di pagi hari adalah obat stres terbaik bagi warga kota. Jika Anda mencari referensi, kami telah merangkumnya untuk Anda.\n\nDaerah Lembang dan Pangalengan di Jawa Barat masih menjadi primadona. Pineus Tilu di Pangalengan misalnya, menawarkan pengalaman unik mendirikan tenda tepat di atas panggung kayu pinggir sungai yang dikelilingi hutan pinus lebat. Fasilitasnya sangat lengkap sehingga cocok untuk membawa anak-anak.\n\nBergeser ke Jawa Tengah, area lereng Gunung Merbabu seperti Kopeng juga memiliki kawasan camping pinus yang udaranya sangat sejuk. Pastikan Anda membawa hammock saat berkunjung ke destinasi ini, karena tidur menggantung di antara dua pohon pinus adalah pengalaman relaksasi yang wajib dicoba.",
                'kategori_slug' => 'destinasi',
                'image' => 'https://images.unsplash.com/photo-1468818461465-1f4be911d120?auto=format&fit=crop&w=800&q=80',
                'gambar' => 'https://images.unsplash.com/photo-1468818461465-1f4be911d120?auto=format&fit=crop&w=800&q=80',
                'thumbnail' => 'https://images.unsplash.com/photo-1468818461465-1f4be911d120?auto=format&fit=crop&w=300&h=200&q=80',
                'status' => 'publish',
                'views' => 0,
                'waktu_posting' => Carbon::now()->subDays(1),
            ],
            [
                'user_id' => $adminUser->id,
                'title' => 'Cara Meracik Kopi Manual Brew Saat Camping',
                'judul' => 'Cara Meracik Kopi Manual Brew Saat Camping',
                'content' => "Menyesap kopi panas sambil menatap matahari terbit di depan tenda adalah sebuah ritual suci bagi sebagian besar pejalan. Anda tidak perlu mengorbankan kualitas kopi Anda dengan hanya membawa kopi instan sachet. Teknik manual brew bisa dilakukan di mana saja dengan peralatan yang minimalis.\n\nPeralatan tempur yang Anda butuhkan hanyalah: Grinder tangan (manual grinder), timbangan digital mini, dan alat seduh portabel seperti Aeropress atau V60 plastik. Aeropress sangat kami rekomendasikan untuk camping karena rangkanya terbuat dari plastik tahan banting, mudah dibersihkan, dan hasil seduhannya sangat konsisten meski Anda menyeduhnya di atas tanah yang miring.\n\nUntuk airnya, pastikan Anda merebusnya hingga mendidih lalu diamkan sekitar satu menit agar suhunya turun ke angka 90-93 derajat Celcius sebelum dituang ke bubuk kopi. Suhu yang terlalu panas dari air mendidih langsung akan mengekstraksi rasa pahit yang berlebihan (burnt taste).",
                'konten' => "Menyesap kopi panas sambil menatap matahari terbit di depan tenda adalah sebuah ritual suci bagi sebagian besar pejalan. Anda tidak perlu mengorbankan kualitas kopi Anda dengan hanya membawa kopi instan sachet. Teknik manual brew bisa dilakukan di mana saja dengan peralatan yang minimalis.\n\nPeralatan tempur yang Anda butuhkan hanyalah: Grinder tangan (manual grinder), timbangan digital mini, dan alat seduh portabel seperti Aeropress atau V60 plastik. Aeropress sangat kami rekomendasikan untuk camping karena rangkanya terbuat dari plastik tahan banting, mudah dibersihkan, dan hasil seduhannya sangat konsisten meski Anda menyeduhnya di atas tanah yang miring.\n\nUntuk airnya, pastikan Anda merebusnya hingga mendidih lalu diamkan sekitar satu menit agar suhunya turun ke angka 90-93 derajat Celcius sebelum dituang ke bubuk kopi. Suhu yang terlalu panas dari air mendidih langsung akan mengekstraksi rasa pahit yang berlebihan (burnt taste).",
                'kategori_slug' => 'camping-tips',
                'image' => 'https://images.unsplash.com/photo-1517824806704-9040b037703b?auto=format&fit=crop&w=800&q=80',
                'gambar' => 'https://images.unsplash.com/photo-1517824806704-9040b037703b?auto=format&fit=crop&w=800&q=80',
                'thumbnail' => 'https://images.unsplash.com/photo-1517824806704-9040b037703b?auto=format&fit=crop&w=300&h=200&q=80',
                'status' => 'publish',
                'views' => 0,
                'waktu_posting' => Carbon::now()->subHours(12),
            ],
            [
                'user_id' => $adminUser->id,
                'title' => 'Pertolongan Pertama (P3K) Saat Mengalami Hipotermia',
                'judul' => 'Pertolongan Pertama (P3K) Saat Mengalami Hipotermia',
                'content' => "Hipotermia adalah ancaman mematikan yang tidak bisa dianggap remeh saat beraktivitas di alam liar. Kondisi ini terjadi ketika tubuh kehilangan panas lebih cepat daripada kemampuannya untuk menghasilkan panas, menyebabkan suhu inti tubuh turun drastis. Mengenali gejala awalnya adalah kunci keselamatan.\n\nGejala awal hipotermia meliputi menggigil hebat yang tidak bisa dikontrol, bicara melantur, kebingungan, dan hilangnya koordinasi gerak tubuh. Jika teman Anda menunjukkan tanda-tanda ini, segera hentikan pendakian atau aktivitas. Bawa korban ke tempat yang terlindung dari angin dan hujan (seperti ke dalam tenda darurat).\n\nLangkah pertolongannya: Ganti semua pakaian basah dengan pakaian kering sesegera mungkin. Bungkus korban dengan sleeping bag dan berikan minuman manis hangat secara perlahan (jangan beri alkohol atau kafein). Jika kondisi parah, terapkan teknik skin-to-skin contact (kontak kulit ke kulit) di dalam sleeping bag untuk mentransfer panas tubuh orang sehat ke korban.",
                'konten' => "Hipotermia adalah ancaman mematikan yang tidak bisa dianggap remeh saat beraktivitas di alam liar. Kondisi ini terjadi ketika tubuh kehilangan panas lebih cepat daripada kemampuannya untuk menghasilkan panas, menyebabkan suhu inti tubuh turun drastis. Mengenali gejala awalnya adalah kunci keselamatan.\n\nGejala awal hipotermia meliputi menggigil hebat yang tidak bisa dikontrol, bicara melantur, kebingungan, dan hilangnya koordinasi gerak tubuh. Jika teman Anda menunjukkan tanda-tanda ini, segera hentikan pendakian atau aktivitas. Bawa korban ke tempat yang terlindung dari angin dan hujan (seperti ke dalam tenda darurat).\n\nLangkah pertolongannya: Ganti semua pakaian basah dengan pakaian kering sesegera mungkin. Bungkus korban dengan sleeping bag dan berikan minuman manis hangat secara perlahan (jangan beri alkohol atau kafein). Jika kondisi parah, terapkan teknik skin-to-skin contact (kontak kulit ke kulit) di dalam sleeping bag untuk mentransfer panas tubuh orang sehat ke korban.",
                'kategori_slug' => 'survival',
                'image' => 'https://images.unsplash.com/photo-1533240319280-cebf9a06cba6?auto=format&fit=crop&w=800&q=80',
                'gambar' => 'https://images.unsplash.com/photo-1533240319280-cebf9a06cba6?auto=format&fit=crop&w=800&q=80',
                'thumbnail' => 'https://images.unsplash.com/photo-1533240319280-cebf9a06cba6?auto=format&fit=crop&w=300&h=200&q=80',
                'status' => 'draft',
                'views' => 0,
                'waktu_posting' => Carbon::now(),
            ],
            [
                'user_id' => $adminUser->id,
                'title' => 'Cara Membaca Peta Topografi dan Kompas Dasar',
                'judul' => 'Cara Membaca Peta Topografi dan Kompas Dasar',
                'content' => "Di era modern ini, kita sangat bergantung pada GPS atau peta digital di smartphone. Namun, perangkat elektronik bisa kehabisan baterai atau kehilangan sinyal di hutan lebat. Oleh karena itu, kemampuan membaca peta topografi cetak dan kompas tetap menjadi skill survival fundamental.\n\nPeta topografi berbeda dengan peta biasa karena dilengkapi dengan 'garis kontur'. Garis-garis ini menggambarkan bentuk permukaan bumi dan ketinggian (elevasi). Semakin rapat garis konturnya, berarti medan tersebut semakin curam. Sebaliknya, garis kontur yang renggang menandakan tanah yang landai. Puncak gunung biasanya ditandai dengan lingkaran kontur terkecil.\n\nUntuk menggunakan kompas bersanding dengan peta, langkah pertama adalah melakukan orientasi peta. Letakkan kompas rata di atas peta, putar badan dan peta Anda perlahan sampai jarum merah kompas sejajar dengan arah Utara pada peta. Setelah peta terorientasi, Anda baru bisa mulai mencocokkan lanskap nyata (seperti gunung atau sungai di kejauhan) dengan simbol yang ada di kertas.",
                'konten' => "Di era modern ini, kita sangat bergantung pada GPS atau peta digital di smartphone. Namun, perangkat elektronik bisa kehabisan baterai atau kehilangan sinyal di hutan lebat. Oleh karena itu, kemampuan membaca peta topografi cetak dan kompas tetap menjadi skill survival fundamental.\n\nPeta topografi berbeda dengan peta biasa karena dilengkapi dengan 'garis kontur'. Garis-garis ini menggambarkan bentuk permukaan bumi dan ketinggian (elevasi). Semakin rapat garis konturnya, berarti medan tersebut semakin curam. Sebaliknya, garis kontur yang renggang menandakan tanah yang landai. Puncak gunung biasanya ditandai dengan lingkaran kontur terkecil.\n\nUntuk menggunakan kompas bersanding dengan peta, langkah pertama adalah melakukan orientasi peta. Letakkan kompas rata di atas peta, putar badan dan peta Anda perlahan sampai jarum merah kompas sejajar dengan arah Utara pada peta. Setelah peta terorientasi, Anda baru bisa mulai mencocokkan lanskap nyata (seperti gunung atau sungai di kejauhan) dengan simbol yang ada di kertas.",
                'kategori_slug' => 'survival',
                'image' => 'https://images.unsplash.com/photo-1496080174650-6385f469f783?auto=format&fit=crop&w=800&q=80',
                'gambar' => 'https://images.unsplash.com/photo-1496080174650-6385f469f783?auto=format&fit=crop&w=800&q=80',
                'thumbnail' => 'https://images.unsplash.com/photo-1496080174650-6385f469f783?auto=format&fit=crop&w=300&h=200&q=80',
                'status' => 'publish',
                'views' => 0,
                'waktu_posting' => Carbon::now()->subHours(2),
            ]
        ];

        foreach ($articles as $article) {
            Article::create($article);
        }

        $this->command->info('✅ 10 Artikel camping realistis berhasil ditambahkan!');
    }
}
