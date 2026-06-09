# Penjelasan 66 Fitur Campify

## 1. Register
Fungsionalitas register digunakan untuk melakukan pendaftaran akun pengguna baru ke dalam sistem. Pada fungsionalitas register, pengguna diharuskan mengisikan data yang diperlukan seperti nama lengkap, alamat email, password, konfirmasi password, dan role akun. Sistem akan melakukan validasi terhadap kelengkapan data, format email, kekuatan password, kesesuaian password dan konfirmasi password, serta memastikan bahwa email yang digunakan belum pernah terdaftar pada sistem. Sistem akan mengembalikan pesan error jika hasil validasi dinyatakan gagal (FALSE). Sebaliknya, jika seluruh data valid dan proses registrasi berhasil (TRUE), maka akun pengguna akan tersimpan ke dalam basis data dan pengguna dapat melanjutkan proses login ke dalam sistem.

## 2. Login
Fungsionalitas login digunakan untuk melakukan proses autentikasi pengguna agar dapat masuk ke dalam sistem sesuai role yang dimiliki. Pada fungsionalitas login, pengguna diharuskan mengisikan alamat email dan password yang telah terdaftar. Sistem akan melakukan validasi terhadap kelengkapan data, kecocokan email dan password, serta status akun pengguna. Sistem akan mengembalikan pesan error jika data login tidak sesuai, akun tidak ditemukan, atau akun tidak memenuhi syarat akses (FALSE). Sebaliknya, jika data login valid dan akun aktif (TRUE), maka sistem akan membuat sesi login dan mengarahkan pengguna ke halaman yang sesuai dengan role pengguna.

## 3. Logout
Fungsionalitas logout digunakan untuk mengakhiri sesi pengguna yang sedang aktif di dalam sistem. Pada fungsionalitas logout, pengguna melakukan permintaan keluar dari aplikasi melalui tombol atau aksi logout yang tersedia. Sistem akan menghapus sesi autentikasi pengguna agar akun tidak lagi dianggap aktif pada browser tersebut. Sistem akan mengembalikan pengguna ke halaman login jika proses logout berhasil (TRUE). Sebaliknya, jika sesi tidak ditemukan atau terjadi kendala proses (FALSE), maka sistem tidak akan memberikan akses ke halaman yang membutuhkan autentikasi.

## 4. Home
Fungsionalitas home digunakan untuk menampilkan halaman utama aplikasi Campify kepada pengguna. Pada fungsionalitas home, sistem menampilkan informasi awal seperti produk, kategori, atau akses navigasi utama yang dapat digunakan pengguna untuk melanjutkan aktivitas. Sistem akan memuat data yang diperlukan dari basis data dan menampilkan halaman sesuai ketersediaan data. Sistem akan tetap menampilkan halaman dengan data terbatas jika sebagian data tidak tersedia (TRUE). Sebaliknya, jika terjadi kegagalan pemuatan halaman atau data utama tidak dapat diproses (FALSE), maka sistem dapat menampilkan pesan error atau halaman tidak dapat diakses.

## 5. Daftar Produk Beli
Fungsionalitas daftar produk beli digunakan untuk menampilkan produk yang dapat dibeli oleh pembeli. Pada fungsionalitas ini, pengguna dapat melihat daftar produk, informasi harga beli, stok, gambar produk, dan data pendukung lain yang diperlukan sebelum melakukan pembelian. Sistem akan mengambil produk dengan tipe jual beli yang memenuhi ketentuan tampilan, seperti produk yang tersedia dan layak ditampilkan. Sistem akan menampilkan daftar produk jika data berhasil ditemukan (TRUE). Sebaliknya, jika data produk tidak tersedia atau terjadi kegagalan pemuatan (FALSE), sistem akan menampilkan kondisi kosong atau pesan informasi kepada pengguna.

## 6. Daftar Produk Rental
Fungsionalitas daftar produk rental digunakan untuk menampilkan produk yang dapat disewa oleh pembeli. Pada fungsionalitas ini, pengguna dapat melihat informasi produk sewa seperti harga sewa, stok, kategori, dan detail singkat produk. Sistem akan melakukan pemisahan data antara produk rental dan produk jual beli agar pengguna dapat memilih layanan yang sesuai. Sistem akan menampilkan produk rental jika data berhasil ditemukan (TRUE). Sebaliknya, jika tidak ada produk rental yang tersedia atau proses pemuatan gagal (FALSE), sistem akan menampilkan halaman kosong atau pesan informasi.

## 7. Pencarian Produk
Fungsionalitas pencarian produk digunakan untuk membantu pengguna menemukan produk berdasarkan kata kunci tertentu. Pada fungsionalitas ini, pengguna dapat memasukkan kata kunci seperti nama produk, kategori, atau informasi lain yang relevan. Sistem akan memproses kata kunci tersebut dan mencocokkannya dengan data produk yang tersimpan di basis data. Sistem akan menampilkan hasil pencarian jika terdapat produk yang sesuai (TRUE). Sebaliknya, jika kata kunci tidak menghasilkan data atau pencarian gagal (FALSE), sistem akan menampilkan pesan bahwa produk tidak ditemukan.

## 8. Detail Produk
Fungsionalitas detail produk digunakan untuk menampilkan informasi lengkap mengenai suatu produk. Pada fungsionalitas ini, pengguna dapat melihat data seperti nama produk, gambar, deskripsi, harga, stok, kategori, rating, informasi toko, dan pilihan aksi seperti membeli, menyewa, chat, atau melaporkan produk. Sistem akan mengambil data produk berdasarkan id produk yang dipilih. Sistem akan menampilkan detail produk jika produk ditemukan dan dapat diakses (TRUE). Sebaliknya, jika produk tidak ditemukan atau tidak valid (FALSE), sistem akan menampilkan pesan error atau halaman tidak ditemukan.

## 9. Produk Berdasarkan Kategori
Fungsionalitas produk berdasarkan kategori digunakan untuk menampilkan daftar produk sesuai kategori yang dipilih pengguna. Pada fungsionalitas ini, pengguna dapat memilih kategori tertentu agar sistem hanya menampilkan produk yang memiliki kategori tersebut. Sistem akan melakukan filter data berdasarkan parameter kategori dan menampilkan hasil yang sesuai. Sistem akan menampilkan produk jika kategori valid dan data tersedia (TRUE). Sebaliknya, jika kategori tidak memiliki produk atau kategori tidak valid (FALSE), sistem akan menampilkan hasil kosong atau pesan informasi.

## 10. Artikel Publik
Fungsionalitas artikel publik digunakan untuk menampilkan informasi artikel kepada pengguna aplikasi. Pada fungsionalitas ini, pengguna dapat melihat daftar artikel yang tersedia dan membuka detail artikel tertentu. Sistem akan mengambil data artikel yang telah dipublikasikan agar informasi yang ditampilkan sesuai dengan konten yang boleh diakses publik. Sistem akan menampilkan artikel jika data tersedia dan status artikel valid (TRUE). Sebaliknya, jika artikel tidak ditemukan atau belum dipublikasikan (FALSE), sistem akan menampilkan pesan error atau halaman tidak ditemukan.

## 11. Dashboard Pembeli
Fungsionalitas dashboard pembeli digunakan untuk menampilkan ringkasan aktivitas pembeli di dalam sistem. Pada fungsionalitas ini, pembeli dapat melihat informasi umum seperti aktivitas transaksi, pesanan, atau navigasi menuju fitur utama pembeli. Sistem akan memastikan bahwa pengguna yang mengakses halaman ini telah login dan memiliki role pembeli. Sistem akan menampilkan dashboard jika autentikasi dan otorisasi berhasil (TRUE). Sebaliknya, jika pengguna belum login atau bukan pembeli (FALSE), sistem akan menolak akses dan mengarahkan pengguna ke halaman yang sesuai.

## 12. Form Penyewaan
Fungsionalitas form penyewaan digunakan untuk menampilkan formulir penyewaan produk rental. Pada fungsionalitas ini, pembeli memilih produk sewa dan mengisikan data yang diperlukan seperti tanggal mulai sewa, durasi, alamat pengiriman, dan informasi pendukung lainnya. Sistem akan memvalidasi ketersediaan produk dan memastikan data penyewa dapat diproses. Sistem akan menampilkan form jika produk rental valid dan tersedia (TRUE). Sebaliknya, jika produk tidak ditemukan, bukan produk rental, atau tidak memenuhi syarat penyewaan (FALSE), sistem akan menampilkan pesan error.

## 13. Proses Penyewaan
Fungsionalitas proses penyewaan digunakan untuk menyimpan transaksi penyewaan yang diajukan oleh pembeli. Pada fungsionalitas ini, pembeli mengirimkan data sewa dari form penyewaan, termasuk tanggal, durasi, alamat, dan data pembayaran atau identitas jika diperlukan. Sistem akan melakukan validasi terhadap kelengkapan data, stok produk, jadwal sewa, dan persyaratan KTP. Sistem akan menyimpan data penyewaan jika seluruh validasi berhasil (TRUE). Sebaliknya, jika data tidak lengkap, stok tidak mencukupi, atau persyaratan tidak terpenuhi (FALSE), sistem akan mengembalikan pesan error.

## 14. Keranjang Belanja
Fungsionalitas keranjang belanja digunakan untuk menampilkan daftar produk yang telah dipilih pembeli sebelum checkout. Pada fungsionalitas ini, pembeli dapat melihat produk, jumlah barang, harga, subtotal, dan total belanja. Sistem akan mengambil data keranjang berdasarkan akun pembeli yang sedang login. Sistem akan menampilkan keranjang jika data berhasil dimuat (TRUE). Sebaliknya, jika keranjang kosong atau terjadi kesalahan pemuatan (FALSE), sistem akan menampilkan informasi bahwa belum ada produk di keranjang.

## 15. Tambah ke Keranjang
Fungsionalitas tambah ke keranjang digunakan untuk memasukkan produk ke dalam keranjang belanja pembeli. Pada fungsionalitas ini, pembeli memilih produk dan jumlah barang yang ingin dibeli. Sistem akan melakukan validasi terhadap id produk, ketersediaan stok, jumlah produk, dan status produk. Sistem akan menambahkan produk ke keranjang jika data valid dan stok mencukupi (TRUE). Sebaliknya, jika produk tidak ditemukan, stok tidak mencukupi, atau jumlah tidak valid (FALSE), sistem akan mengembalikan pesan error kepada pembeli.

## 16. Update Keranjang
Fungsionalitas update keranjang digunakan untuk mengubah jumlah produk yang terdapat di dalam keranjang. Pada fungsionalitas ini, pembeli dapat menambah atau mengurangi jumlah barang sebelum melakukan checkout. Sistem akan melakukan validasi terhadap item keranjang, jumlah baru, dan ketersediaan stok produk. Sistem akan memperbarui data keranjang jika validasi berhasil (TRUE). Sebaliknya, jika item tidak ditemukan, jumlah tidak valid, atau stok tidak mencukupi (FALSE), sistem akan menolak perubahan dan menampilkan pesan error.

## 17. Hapus Item Keranjang
Fungsionalitas hapus item keranjang digunakan untuk menghapus produk tertentu dari keranjang belanja pembeli. Pada fungsionalitas ini, pembeli memilih item yang ingin dihapus dari daftar keranjang. Sistem akan memastikan item tersebut benar-benar milik pembeli yang sedang login sebelum proses penghapusan dilakukan. Sistem akan menghapus item jika data valid dan otorisasi sesuai (TRUE). Sebaliknya, jika item tidak ditemukan atau bukan milik pembeli tersebut (FALSE), sistem akan mengembalikan pesan error atau menolak penghapusan.

## 18. Wishlist
Fungsionalitas wishlist digunakan untuk menampilkan daftar produk yang disimpan pembeli sebagai produk favorit. Pada fungsionalitas ini, pembeli dapat melihat produk yang sebelumnya ditandai agar lebih mudah ditemukan kembali. Sistem akan mengambil data wishlist berdasarkan akun pembeli yang sedang login. Sistem akan menampilkan daftar wishlist jika data tersedia (TRUE). Sebaliknya, jika belum ada produk yang disimpan atau data tidak ditemukan (FALSE), sistem akan menampilkan kondisi kosong kepada pengguna.

## 19. Toggle Wishlist
Fungsionalitas toggle wishlist digunakan untuk menambahkan atau menghapus produk dari daftar wishlist pembeli. Pada fungsionalitas ini, pembeli menekan aksi wishlist pada produk tertentu. Sistem akan mengecek apakah produk tersebut sudah ada di wishlist pengguna. Sistem akan menambahkan produk jika belum ada, atau menghapus produk jika sudah ada (TRUE). Sebaliknya, jika produk tidak valid atau pengguna tidak memiliki akses (FALSE), sistem akan mengembalikan pesan error.

## 20. Checkout Keranjang
Fungsionalitas checkout keranjang digunakan untuk menampilkan halaman checkout dari produk yang ada di keranjang. Pada fungsionalitas ini, pembeli dapat memeriksa daftar produk, alamat pengiriman, total pembayaran, metode pembayaran, dan informasi tambahan sebelum transaksi diproses. Sistem akan memastikan keranjang tidak kosong dan seluruh produk masih tersedia. Sistem akan menampilkan halaman checkout jika data keranjang valid (TRUE). Sebaliknya, jika keranjang kosong atau terdapat produk bermasalah (FALSE), sistem akan menolak proses checkout.

## 21. Checkout Langsung
Fungsionalitas checkout langsung digunakan untuk melakukan checkout satu produk tanpa harus memasukkannya terlebih dahulu ke keranjang. Pada fungsionalitas ini, pembeli memilih produk tertentu dan langsung diarahkan ke halaman checkout produk tersebut. Sistem akan melakukan validasi terhadap produk, stok, harga, dan status produk. Sistem akan menampilkan halaman checkout langsung jika produk valid dan tersedia (TRUE). Sebaliknya, jika produk tidak ditemukan atau tidak dapat dibeli (FALSE), sistem akan mengembalikan pesan error.

## 22. Proses Checkout
Fungsionalitas proses checkout digunakan untuk menyimpan transaksi pembelian pembeli ke dalam sistem. Pada fungsionalitas ini, pembeli mengirimkan data checkout seperti produk, jumlah, alamat, metode pembayaran, dan bukti pembayaran jika diperlukan. Sistem akan melakukan validasi terhadap kelengkapan data, stok produk, total pembayaran, dan kepemilikan keranjang. Sistem akan membuat order jika seluruh data valid dan transaksi berhasil diproses (TRUE). Sebaliknya, jika data tidak lengkap, stok tidak mencukupi, atau pembayaran tidak valid (FALSE), sistem akan mengembalikan pesan error.

## 23. Profil Pembeli
Fungsionalitas profil pembeli digunakan untuk menampilkan informasi akun pembeli. Pada fungsionalitas ini, pembeli dapat melihat data pribadi, alamat, status verifikasi KTP, dan informasi akun lainnya. Sistem akan memastikan bahwa data yang ditampilkan adalah data milik pengguna yang sedang login. Sistem akan menampilkan profil jika pengguna terautentikasi sebagai pembeli (TRUE). Sebaliknya, jika pengguna belum login atau tidak memiliki hak akses (FALSE), sistem akan menolak akses ke halaman profil.

## 24. Update Biodata Pembeli
Fungsionalitas update biodata pembeli digunakan untuk memperbarui informasi pribadi pembeli. Pada fungsionalitas ini, pembeli dapat mengubah data seperti nama, nomor telepon, atau informasi lain yang tersedia pada form profil. Sistem akan melakukan validasi terhadap kelengkapan data, format data, dan batasan panjang input. Sistem akan menyimpan perubahan jika data valid (TRUE). Sebaliknya, jika data tidak lengkap atau format tidak sesuai (FALSE), sistem akan mengembalikan pesan error.

## 25. Update Alamat Pembeli
Fungsionalitas update alamat pembeli digunakan untuk memperbarui alamat pengiriman yang digunakan dalam transaksi. Pada fungsionalitas ini, pembeli mengisikan alamat, kota, kecamatan, kode pos, dan data lokasi lain yang dibutuhkan. Sistem akan memvalidasi kelengkapan alamat agar pengiriman dapat diproses dengan benar. Sistem akan menyimpan alamat baru jika data valid (TRUE). Sebaliknya, jika data alamat tidak lengkap atau tidak sesuai ketentuan (FALSE), sistem akan mengembalikan pesan error.

## 26. Ubah Password Pembeli
Fungsionalitas ubah password pembeli digunakan untuk memperbarui password akun pembeli. Pada fungsionalitas ini, pembeli diharuskan mengisikan password lama, password baru, dan konfirmasi password baru. Sistem akan memvalidasi kecocokan password lama, kekuatan password baru, dan kesesuaian konfirmasi password. Sistem akan mengganti password jika seluruh validasi berhasil (TRUE). Sebaliknya, jika password lama salah atau password baru tidak memenuhi syarat (FALSE), sistem akan mengembalikan pesan error.

## 27. Upload KTP Pembeli
Fungsionalitas upload KTP pembeli digunakan untuk mengunggah identitas pembeli sebagai syarat keamanan transaksi sewa. Pada fungsionalitas ini, pembeli memilih file foto KTP yang akan disimpan ke sistem. Sistem akan melakukan validasi terhadap keberadaan file, tipe file gambar, ukuran file, dan akun pengguna yang melakukan upload. Sistem akan menyimpan foto KTP dan menandai status menunggu verifikasi admin jika validasi berhasil (TRUE). Sebaliknya, jika file tidak valid atau proses upload gagal (FALSE), sistem akan mengembalikan pesan error.

## 28. Daftar Pesanan Pembeli
Fungsionalitas daftar pesanan pembeli digunakan untuk menampilkan seluruh transaksi yang pernah dibuat oleh pembeli. Pada fungsionalitas ini, pembeli dapat melihat ringkasan pesanan, status pesanan, tanggal transaksi, dan total pembayaran. Sistem akan mengambil data pesanan berdasarkan akun pembeli yang sedang login. Sistem akan menampilkan daftar pesanan jika data tersedia (TRUE). Sebaliknya, jika belum ada pesanan atau data tidak ditemukan (FALSE), sistem akan menampilkan kondisi kosong.

## 29. Detail Pesanan Pembeli
Fungsionalitas detail pesanan pembeli digunakan untuk menampilkan informasi lengkap dari satu pesanan tertentu. Pada fungsionalitas ini, pembeli dapat melihat item pesanan, alamat pengiriman, status pembayaran, nomor resi, status retur, dan detail transaksi lainnya. Sistem akan memastikan bahwa pesanan yang diakses adalah milik pembeli yang sedang login. Sistem akan menampilkan detail pesanan jika data ditemukan dan otorisasi sesuai (TRUE). Sebaliknya, jika pesanan tidak ditemukan atau bukan milik pembeli (FALSE), sistem akan menolak akses.

## 30. Batalkan Pesanan
Fungsionalitas batalkan pesanan digunakan untuk membatalkan transaksi yang masih memenuhi syarat pembatalan. Pada fungsionalitas ini, pembeli mengirimkan permintaan pembatalan terhadap pesanan tertentu. Sistem akan memvalidasi status pesanan, kepemilikan pesanan, dan apakah pesanan masih dapat dibatalkan. Sistem akan mengubah status pesanan menjadi dibatalkan jika seluruh syarat terpenuhi (TRUE). Sebaliknya, jika pesanan sudah diproses, tidak ditemukan, atau tidak dapat dibatalkan (FALSE), sistem akan mengembalikan pesan error.

## 31. Konfirmasi Penerimaan Pesanan
Fungsionalitas konfirmasi penerimaan pesanan digunakan untuk menandai bahwa pembeli telah menerima barang yang dipesan. Pada fungsionalitas ini, pembeli menekan aksi konfirmasi pada pesanan yang telah dikirim atau diterima. Sistem akan memvalidasi kepemilikan pesanan dan status pesanan sebelum mengubah status transaksi. Sistem akan menandai pesanan sebagai selesai jika validasi berhasil (TRUE). Sebaliknya, jika pesanan tidak valid atau status belum memenuhi syarat (FALSE), sistem akan menolak proses konfirmasi.

## 32. Pengajuan Retur
Fungsionalitas pengajuan retur digunakan untuk mengajukan pengembalian barang atau komplain terhadap transaksi tertentu. Pada fungsionalitas ini, pembeli mengisi alasan retur, catatan, dan bukti pendukung sesuai kebutuhan. Sistem akan memvalidasi detail pesanan, batas waktu retur, status transaksi, dan kelengkapan data pengajuan. Sistem akan menyimpan pengajuan retur jika seluruh syarat terpenuhi (TRUE). Sebaliknya, jika pesanan tidak valid, retur sudah pernah diajukan, atau data tidak lengkap (FALSE), sistem akan mengembalikan pesan error.

## 33. Input Resi Pengembalian
Fungsionalitas input resi pengembalian digunakan untuk menyimpan nomor resi pengiriman balik barang retur. Pada fungsionalitas ini, pembeli mengisikan nomor resi setelah pengajuan pengembalian disetujui. Sistem akan memvalidasi data retur, status retur, dan kelengkapan nomor resi. Sistem akan menyimpan nomor resi dan memperbarui status pengembalian jika data valid (TRUE). Sebaliknya, jika retur tidak ditemukan atau status belum sesuai (FALSE), sistem akan mengembalikan pesan error.

## 34. Upload Bukti Pembayaran Denda
Fungsionalitas upload bukti pembayaran denda digunakan untuk mengunggah bukti pembayaran denda ketika terdapat kekurangan pembayaran akibat keterlambatan atau kerusakan. Pada fungsionalitas ini, pembeli memilih file bukti transfer dan mengirimkannya ke sistem. Sistem akan melakukan validasi terhadap data retur, status denda, jenis file, dan ukuran file. Sistem akan menyimpan bukti pembayaran jika data valid (TRUE). Sebaliknya, jika file tidak valid atau retur tidak berada pada status yang sesuai (FALSE), sistem akan mengembalikan pesan error.

## 35. Konfirmasi Produk Pengganti
Fungsionalitas konfirmasi produk pengganti digunakan untuk menandai bahwa pembeli telah menerima produk pengganti dari seller. Pada fungsionalitas ini, pembeli melakukan konfirmasi terhadap retur yang diselesaikan dengan solusi penggantian barang. Sistem akan memvalidasi data retur, status pengiriman pengganti, dan kepemilikan transaksi. Sistem akan menandai retur sebagai selesai jika konfirmasi valid (TRUE). Sebaliknya, jika data retur tidak valid atau status belum sesuai (FALSE), sistem akan mengembalikan pesan error.

## 36. Review Produk
Fungsionalitas review produk digunakan untuk memberikan ulasan dan rating terhadap produk yang pernah dibeli atau disewa. Pada fungsionalitas ini, pembeli mengisikan nilai rating dan komentar penilaian produk. Sistem akan memvalidasi produk, pengguna, nilai rating, isi ulasan, dan syarat transaksi yang memungkinkan review diberikan. Sistem akan menyimpan review jika data valid (TRUE). Sebaliknya, jika rating tidak valid, produk tidak ditemukan, atau pengguna tidak memenuhi syarat (FALSE), sistem akan mengembalikan pesan error.

## 37. Review Toko
Fungsionalitas review toko digunakan untuk memberikan penilaian terhadap toko atau seller. Pada fungsionalitas ini, pembeli dapat mengirimkan rating dan komentar berdasarkan pengalaman transaksi dengan toko tersebut. Sistem akan memvalidasi data toko, pengguna, nilai rating, dan isi review sebelum disimpan. Sistem akan menyimpan review toko jika seluruh data valid (TRUE). Sebaliknya, jika toko tidak ditemukan atau data review tidak sesuai (FALSE), sistem akan mengembalikan pesan error.

## 38. Chat Pembeli
Fungsionalitas chat pembeli digunakan untuk melakukan komunikasi antara pembeli dan seller. Pada fungsionalitas ini, pembeli dapat membuka daftar percakapan, memulai chat dari produk tertentu, melihat detail percakapan, dan mengirim pesan. Sistem akan memvalidasi pengguna, seller, produk, percakapan, dan isi pesan sebelum menyimpan chat. Sistem akan mengirim dan menampilkan pesan jika data valid (TRUE). Sebaliknya, jika percakapan tidak ditemukan, seller tidak valid, atau pesan kosong (FALSE), sistem akan mengembalikan pesan error.

## 39. Laporan Pembeli
Fungsionalitas laporan pembeli digunakan untuk melaporkan produk, toko, atau percakapan yang bermasalah kepada admin. Pada fungsionalitas ini, pembeli mengisikan alasan laporan dan data pendukung sesuai objek yang dilaporkan. Sistem akan memvalidasi objek laporan, identitas pelapor, alasan laporan, dan memastikan laporan tersimpan dengan relasi yang benar. Sistem akan menyimpan laporan jika data valid (TRUE). Sebaliknya, jika objek laporan tidak ditemukan atau alasan tidak lengkap (FALSE), sistem akan mengembalikan pesan error.

## 40. Dashboard Seller
Fungsionalitas dashboard seller digunakan untuk menampilkan ringkasan aktivitas toko kepada seller. Pada fungsionalitas ini, seller dapat melihat informasi seperti produk, pesanan, penyewaan, rating, dan aktivitas toko. Sistem akan memastikan bahwa pengguna telah login dan memiliki role seller sebelum halaman ditampilkan. Sistem akan menampilkan dashboard jika autentikasi dan otorisasi berhasil (TRUE). Sebaliknya, jika pengguna bukan seller atau belum login (FALSE), sistem akan menolak akses.

## 41. Profil Toko Seller
Fungsionalitas profil toko seller digunakan untuk menampilkan informasi toko milik seller. Pada fungsionalitas ini, seller dapat melihat data toko seperti nama toko, deskripsi, alamat, kontak, rekening, logo, banner, status toko, dan informasi terkait. Sistem akan mengambil data toko berdasarkan seller yang sedang login. Sistem akan menampilkan profil toko jika data toko ditemukan (TRUE). Sebaliknya, jika toko belum tersedia atau pengguna tidak memiliki akses (FALSE), sistem akan menampilkan pesan informasi atau menolak akses.

## 42. Update Profil Toko
Fungsionalitas update profil toko digunakan untuk memperbarui informasi toko milik seller. Pada fungsionalitas ini, seller dapat mengubah data toko seperti nama toko, alamat, deskripsi, nomor telepon, rekening bank, logo, dan banner. Sistem akan melakukan validasi terhadap kelengkapan data, format input, file gambar, serta kepemilikan toko. Sistem akan menyimpan perubahan jika seluruh data valid (TRUE). Sebaliknya, jika data tidak lengkap, format salah, atau toko bukan milik seller (FALSE), sistem akan mengembalikan pesan error.

## 43. Klarifikasi Toko
Fungsionalitas klarifikasi toko digunakan oleh seller untuk mengirimkan penjelasan kepada admin ketika toko terkena tindakan seperti suspend atau ban. Pada fungsionalitas ini, seller mengisi catatan klarifikasi yang menjelaskan kondisi atau keberatan terhadap tindakan admin. Sistem akan memvalidasi isi klarifikasi dan memastikan toko tersebut milik seller yang sedang login. Sistem akan menyimpan klarifikasi jika data valid (TRUE). Sebaliknya, jika klarifikasi kosong atau toko tidak valid (FALSE), sistem akan mengembalikan pesan error.

## 44. Daftar Produk Seller
Fungsionalitas daftar produk seller digunakan untuk menampilkan seluruh produk yang dikelola oleh seller. Pada fungsionalitas ini, seller dapat melihat produk berdasarkan toko miliknya, termasuk status validasi produk, stok, harga, dan informasi ringkas lainnya. Sistem akan mengambil data produk yang sesuai dengan seller yang sedang login. Sistem akan menampilkan daftar produk jika data tersedia (TRUE). Sebaliknya, jika tidak ada produk atau data tidak ditemukan (FALSE), sistem akan menampilkan kondisi kosong.

## 45. Tambah Produk Seller
Fungsionalitas tambah produk seller digunakan untuk membuat produk baru yang akan dijual atau disewakan oleh seller. Pada fungsionalitas ini, seller mengisikan data produk seperti nama, kategori, deskripsi, harga, stok, jenis produk, dan gambar. Sistem akan melakukan validasi terhadap kelengkapan data, format harga dan stok, file gambar, serta kepemilikan toko. Sistem akan menyimpan produk jika seluruh data valid (TRUE). Sebaliknya, jika data tidak lengkap atau format tidak sesuai (FALSE), sistem akan mengembalikan pesan error.

## 46. Detail Produk Seller
Fungsionalitas detail produk seller digunakan untuk menampilkan informasi lengkap produk yang dimiliki seller. Pada fungsionalitas ini, seller dapat melihat detail data produk, status produk, gambar, stok, harga, dan informasi terkait. Sistem akan memastikan produk yang diakses adalah produk milik seller tersebut. Sistem akan menampilkan detail produk jika data ditemukan dan otorisasi sesuai (TRUE). Sebaliknya, jika produk tidak ditemukan atau bukan milik seller (FALSE), sistem akan menolak akses.

## 47. Update Produk Seller
Fungsionalitas update produk seller digunakan untuk memperbarui data produk yang telah dibuat oleh seller. Pada fungsionalitas ini, seller dapat mengubah nama produk, kategori, deskripsi, harga, stok, jenis produk, dan gambar. Sistem akan melakukan validasi terhadap input baru serta memastikan produk tersebut milik seller yang sedang login. Sistem akan menyimpan perubahan jika seluruh validasi berhasil (TRUE). Sebaliknya, jika data tidak valid atau produk bukan milik seller (FALSE), sistem akan mengembalikan pesan error.

## 48. Hapus Produk Seller
Fungsionalitas hapus produk seller digunakan untuk menghapus produk dari daftar produk seller. Pada fungsionalitas ini, seller memilih produk yang ingin dihapus dari sistem. Sistem akan memvalidasi kepemilikan produk dan memastikan produk dapat dihapus sesuai aturan aplikasi. Sistem akan menghapus produk jika data valid dan otorisasi sesuai (TRUE). Sebaliknya, jika produk tidak ditemukan atau bukan milik seller (FALSE), sistem akan menolak proses penghapusan.

## 49. Daftar Order Seller
Fungsionalitas daftar order seller digunakan untuk menampilkan daftar pesanan jual beli yang berkaitan dengan produk seller. Pada fungsionalitas ini, seller dapat melihat ringkasan pesanan, pembeli, status, total transaksi, dan informasi pengiriman. Sistem akan mengambil pesanan yang memiliki item produk milik seller tersebut. Sistem akan menampilkan daftar order jika data tersedia (TRUE). Sebaliknya, jika tidak ada pesanan atau data tidak valid (FALSE), sistem akan menampilkan kondisi kosong.

## 50. Detail Order Seller
Fungsionalitas detail order seller digunakan untuk menampilkan informasi lengkap suatu pesanan yang diterima seller. Pada fungsionalitas ini, seller dapat melihat data pembeli, item pesanan, alamat pengiriman, bukti pembayaran, status KTP jika terkait, dan status pengiriman. Sistem akan memastikan order yang diakses berkaitan dengan produk milik seller. Sistem akan menampilkan detail order jika data ditemukan dan otorisasi sesuai (TRUE). Sebaliknya, jika order tidak ditemukan atau bukan bagian dari seller (FALSE), sistem akan menolak akses.

## 51. Update Status dan Resi Order Seller
Fungsionalitas update status dan resi order seller digunakan untuk memperbarui progres pengiriman pesanan jual beli. Pada fungsionalitas ini, seller dapat mengubah status pesanan dan mengisi nomor resi pengiriman. Sistem akan melakukan validasi terhadap status baru, nomor resi, dan kepemilikan order oleh seller. Sistem akan memperbarui pesanan jika data valid dan status memenuhi aturan transaksi (TRUE). Sebaliknya, jika status tidak valid, resi kosong saat dibutuhkan, atau order tidak sesuai (FALSE), sistem akan mengembalikan pesan error.

## 52. Daftar Rental Seller
Fungsionalitas daftar rental seller digunakan untuk menampilkan transaksi penyewaan yang berkaitan dengan produk rental milik seller. Pada fungsionalitas ini, seller dapat melihat penyewa, produk, tanggal sewa, status sewa, dan informasi pengembalian. Sistem akan mengambil data rental berdasarkan produk milik seller yang sedang login. Sistem akan menampilkan daftar rental jika data tersedia (TRUE). Sebaliknya, jika tidak ada rental atau data tidak ditemukan (FALSE), sistem akan menampilkan kondisi kosong.

## 53. Detail Rental Seller
Fungsionalitas detail rental seller digunakan untuk menampilkan informasi lengkap transaksi penyewaan. Pada fungsionalitas ini, seller dapat melihat data penyewa, produk, durasi, tanggal mulai, tanggal akhir, alamat pengiriman, escrow, status KTP, dan status pengembalian. Sistem akan memastikan data rental yang diakses berkaitan dengan produk milik seller. Sistem akan menampilkan detail rental jika data valid dan otorisasi sesuai (TRUE). Sebaliknya, jika rental tidak ditemukan atau bukan milik seller (FALSE), sistem akan menolak akses.

## 54. Update Status Rental
Fungsionalitas update status rental digunakan untuk memperbarui status penyewaan yang sedang berjalan. Pada fungsionalitas ini, seller dapat mengubah status seperti pending, active, completed, atau cancelled sesuai proses bisnis penyewaan. Sistem akan melakukan validasi terhadap status, catatan, foto kondisi barang jika diperlukan, dan kepemilikan rental. Sistem akan memperbarui rental jika seluruh data valid (TRUE). Sebaliknya, jika status tidak valid, data pendukung tidak lengkap, atau rental tidak sesuai seller (FALSE), sistem akan mengembalikan pesan error.

## 55. Approval Pengembalian Rental
Fungsionalitas approval pengembalian rental digunakan untuk menyetujui permintaan pengembalian barang sewa dari pembeli. Pada fungsionalitas ini, seller meninjau pengajuan pengembalian dan menyetujui agar penyewa dapat mengirimkan barang kembali. Sistem akan memvalidasi data rental, data return escrow, dan status pengajuan pengembalian. Sistem akan memperbarui status menjadi approved jika data valid (TRUE). Sebaliknya, jika data pengembalian tidak ditemukan atau status tidak sesuai (FALSE), sistem akan mengembalikan pesan error.

## 56. Penerimaan Barang dan Hitung Denda
Fungsionalitas penerimaan barang dan hitung denda digunakan untuk mencatat barang sewa yang telah kembali ke seller. Pada fungsionalitas ini, seller mengisi kondisi barang dan nominal denda kerusakan jika ada. Sistem akan menghitung denda keterlambatan, denda kerusakan, deposit, defisit, dan status penyelesaian escrow berdasarkan data pengembalian. Sistem akan menyimpan hasil pemeriksaan jika data valid (TRUE). Sebaliknya, jika data pengembalian tidak ditemukan atau input denda tidak valid (FALSE), sistem akan mengembalikan pesan error.

## 57. Verifikasi Pembayaran Denda
Fungsionalitas verifikasi pembayaran denda digunakan untuk mengonfirmasi bahwa penyewa telah membayar kekurangan denda. Pada fungsionalitas ini, seller memeriksa bukti pembayaran yang telah diunggah pembeli dan melakukan konfirmasi jika pembayaran sesuai. Sistem akan memvalidasi data rental dan return escrow sebelum status diperbarui. Sistem akan mengubah status menjadi menunggu refund admin jika pembayaran dinyatakan valid (TRUE). Sebaliknya, jika data pengembalian tidak ditemukan atau status tidak sesuai (FALSE), sistem akan mengembalikan pesan error.

## 58. Tinjau Komplain Retur Jual Beli
Fungsionalitas tinjau komplain retur jual beli digunakan oleh seller untuk memutuskan tindak lanjut komplain pembeli. Pada fungsionalitas ini, seller dapat menyetujui atau menolak komplain serta memilih resolusi berupa refund atau pengiriman produk pengganti. Sistem akan memvalidasi aksi, jenis resolusi, catatan, dan data retur yang diproses. Sistem akan menyimpan keputusan seller jika seluruh data valid (TRUE). Sebaliknya, jika aksi tidak valid atau data retur tidak ditemukan (FALSE), sistem akan mengembalikan pesan error.

## 59. Kirim Produk Pengganti
Fungsionalitas kirim produk pengganti digunakan oleh seller untuk mengirimkan nomor resi produk pengganti kepada pembeli. Pada fungsionalitas ini, seller mengisi nomor resi setelah memilih resolusi replacement pada komplain retur. Sistem akan memvalidasi data retur, nomor resi, dan status retur sebelum menyimpan data pengiriman. Sistem akan memperbarui status retur menjadi replacement shipping jika data valid (TRUE). Sebaliknya, jika nomor resi kosong atau data retur tidak ditemukan (FALSE), sistem akan mengembalikan pesan error.

## 60. Konfirmasi Refund Seller
Fungsionalitas konfirmasi refund seller digunakan untuk menandai bahwa seller telah melakukan transfer refund kepada pembeli. Pada fungsionalitas ini, seller mengonfirmasi pengembalian dana setelah komplain disetujui dengan resolusi refund. Sistem akan memvalidasi data retur dan status refund sebelum transaksi ditandai selesai. Sistem akan memperbarui status retur dan pesanan jika data valid (TRUE). Sebaliknya, jika retur tidak ditemukan atau status tidak sesuai (FALSE), sistem akan mengembalikan pesan error.

## 61. Chat Seller
Fungsionalitas chat seller digunakan untuk membantu seller berkomunikasi dengan pembeli. Pada fungsionalitas ini, seller dapat membuka daftar percakapan, melihat detail chat, dan membalas pesan pembeli. Sistem akan memvalidasi identitas seller, data percakapan, serta isi pesan sebelum pesan disimpan. Sistem akan menampilkan atau mengirim pesan jika data valid (TRUE). Sebaliknya, jika percakapan tidak ditemukan atau pesan tidak valid (FALSE), sistem akan mengembalikan pesan error.

## 62. Rating dan Balasan Seller
Fungsionalitas rating dan balasan seller digunakan untuk mengelola ulasan yang diterima produk atau toko seller. Pada fungsionalitas ini, seller dapat melihat rating produk, rating toko, detail ulasan, serta memberikan balasan terhadap ulasan pembeli. Sistem akan memvalidasi rating, kepemilikan produk atau toko, dan isi balasan. Sistem akan menyimpan balasan atau menampilkan data rating jika validasi berhasil (TRUE). Sebaliknya, jika rating tidak ditemukan atau bukan milik seller (FALSE), sistem akan mengembalikan pesan error.

## 63. Laporan Seller
Fungsionalitas laporan seller digunakan untuk menampilkan laporan penjualan dan penyewaan toko. Pada fungsionalitas ini, seller dapat melihat ringkasan transaksi, laporan sales, laporan rental, dan melakukan export laporan dalam format PDF. Sistem akan mengambil data berdasarkan produk dan toko milik seller yang sedang login. Sistem akan menampilkan atau mengekspor laporan jika data valid (TRUE). Sebaliknya, jika data tidak tersedia atau tipe laporan tidak valid (FALSE), sistem akan menampilkan pesan error.

## 64. Dashboard Admin
Fungsionalitas dashboard admin digunakan untuk menampilkan ringkasan data operasional aplikasi kepada admin. Pada fungsionalitas ini, admin dapat melihat statistik pengguna, seller, produk, transaksi, laporan, pending KYC, retur, dan aktivitas sistem lainnya. Sistem akan memuat data agregat dari berbagai tabel untuk membantu admin melakukan pemantauan. Sistem akan menampilkan dashboard jika admin terautentikasi dan data berhasil dimuat (TRUE). Sebaliknya, jika pengguna bukan admin atau data gagal diproses (FALSE), sistem akan menolak akses atau menampilkan pesan error.

## 65. Manajemen User dan Verifikasi KTP Admin
Fungsionalitas manajemen user dan verifikasi KTP admin digunakan untuk mengelola akun pengguna di dalam sistem. Pada fungsionalitas ini, admin dapat melihat daftar user, melihat detail user, mengaktifkan akun, menonaktifkan akun, melakukan ban, menghapus akun, dan memverifikasi KTP pembeli. Sistem akan memvalidasi role admin, id user, status akun, serta keberadaan data KTP sebelum proses dilakukan. Sistem akan memperbarui data user jika validasi berhasil (TRUE). Sebaliknya, jika user tidak ditemukan atau admin tidak memiliki akses (FALSE), sistem akan mengembalikan pesan error.

## 66. Validasi Produk dan Toko Admin
Fungsionalitas validasi produk dan toko admin digunakan untuk mengawasi kelayakan produk serta toko seller di Campify. Pada fungsionalitas ini, admin dapat melihat daftar produk, menyetujui atau menolak produk, melihat detail toko, menyetujui toko, menolak toko, suspend toko, ban toko, mengaktifkan toko kembali, serta memvalidasi produk yang berada dalam detail toko. Sistem akan memvalidasi id produk, id toko, status pengajuan, catatan admin, dan role admin sebelum keputusan disimpan. Sistem akan memperbarui status produk atau toko jika keputusan valid (TRUE). Sebaliknya, jika produk atau toko tidak ditemukan, status tidak sesuai, atau data keputusan tidak lengkap (FALSE), sistem akan mengembalikan pesan error.
