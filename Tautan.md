Daftar tautan (URL)

Halaman Utama & Admin:
GET / → Halaman utama (Home)  
GET /cari → Halaman pencarian (Search)  
GET /admin → Halaman admin (Admin)  

Fitur Crawler:
GET /login → Halaman masuk (Login)
POST /login/process → Proses masuk (Login)
GET /logout → Proses keluar (Logout)
GET /profile → Halaman profil pengguna
POST /profile/update → Proses pembaruan profil pengguna
GET /dokumen/karyawan/(:segment) → Menampilkan foto/dokumen karyawan
GET /dokumen/doodle/(:segment) → Menampilkan foto/dokumen doodle
GET /crawl → Menampilkan halaman crawler  
POST /crawler/doCrawl → Menjalankan proses crawling  
POST /crawler/resetDb → Mereset (mengosongkan) tabel database

API Endpoint:
POST /api/updateLinkCount → Memperbarui jumlah klik tautan situs  
POST /api/updateImageCount → Memperbarui jumlah klik tautan gambar  
POST /api/setBroken → Menandai tautan rusak (broken link) pada gambar  
GET /api/getSites → Mengambil data list situs  
GET /api/getImages → Mengambil data list gambar  
POST /api/deleteSite/(:num) → Menghapus situs berdasarkan nomor ID  
POST /api/deleteImage/(:num) → Menghapus gambar berdasarkan nomor ID  
POST /api/updateSite/(:num) → Memperbarui data situs berdasarkan nomor ID
POST /api/updateImage/(:num) → Memperbarui data gambar berdasarkan nomor ID

Manajemen Doodle:
GET /doodle/getAll → Mengambil daftar seluruh doodle (JSON)
POST /doodle/store → Menyimpan doodle baru
POST /doodle/update → Memperbarui data doodle
POST /doodle/delete → Menghapus doodle

Manajemen Rilis (Changelog):
GET /versi → Halaman publik riwayat rilis
GET /admin/versi → Halaman admin panel untuk kelola rilis
GET /admin/versi/getAll → Mengambil daftar seluruh rilis versi (JSON)
POST /admin/versi/store → Menyimpan versi rilis baru
POST /admin/versi/update → Memperbarui rilis versi
POST /admin/versi/delete → Menghapus rilis versi