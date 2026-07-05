Daftar tautan (URL)

Halaman Utama & Admin:
GET / → Halaman utama (Home)  
GET /cari → Halaman pencarian (Search)  
GET /admin → Halaman admin (Admin)  

Fitur Crawler:
GET /crawl → Menampilkan halaman crawler  
POST /crawler/doCrawl → Menjalankan proses crawling  

API Endpoint:
POST /api/updateLinkCount → Memperbarui jumlah tautan  
POST /api/updateImageCount → Memperbarui jumlah gambar  
POST /api/setBroken → Menandai tautan rusak (broken link)  
GET /api/getSites → Mengambil data situs  
GET /api/getImages → Mengambil data gambar  
POST /api/deleteSite/(:num) → Menghapus situs berdasarkan nomor ID  
POST /api/deleteImage/(:num) → Menghapus gambar berdasarkan nomor ID  