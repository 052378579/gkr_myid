Daftar tautan (URL)

Halaman Utama & Admin:
GET / → Halaman utama (Home)  
GET /cari → Halaman pencarian (Search)  
GET /admin → Halaman admin (Admin)  

Fitur Crawler:
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