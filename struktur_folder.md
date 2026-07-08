# Struktur Folder Mesin Pencari Gracia (Refactoring CodeIgniter 4)

Struktur berikut memvisualisasikan bagaimana aplikasi Mesin Pencari Gracia direfaktor menggunakan standar kerangka kerja **CodeIgniter 4**, dengan balutan frontend **Bootstrap 5.3** dan **Vue.js 3 (CDN)**.

📁 gkr_myid
├── 📁 app
│   ├── 📁 Config
│   │   ├── 📄 Database.php        <- Definisi pengaturan dasar koneksi (timpa dengan .env)
│   │   ├── 📄 Routes.php          <- Daftar URL aplikasi (/, /cari, /admin, dll)
│   │   └── ...
│   ├── 📁 Controllers
│   │   ├── 📁 Admin
│   │   │   └── 📄 DoodleController.php <- Mengelola data (CRUD) Doodle logo tematik
│   │   ├── 📄 Admin.php           <- Mengelola tampilan panel manajemen
│   │   ├── 📄 AdminVersi.php      <- Mengelola API CRUD untuk tabel Changelog (gkr_versi)
│   │   ├── 📄 Api.php             <- Menampung endpoint data AJAX (JSON Response)
│   │   ├── 📄 Auth.php            <- Menangani proses autentikasi (Login/Logout)
│   │   ├── 📄 Crawler.php         <- Mengelola logika terminal scraper
│   │   ├── 📄 Dokumen.php         <- Menangani akses dokumen/foto karyawan
│   │   ├── 📄 Home.php            <- Menampilkan halaman muka/landing
│   │   ├── 📄 Profile.php         <- Menangani halaman dan proses pembaruan profil pengguna
│   │   ├── 📄 Search.php          <- Memproses dan me-render hasil pencarian
│   │   └── 📄 Versi.php           <- Menampilkan halaman Changelog ke publik
│   ├── 📁 Filters
│   │   └── 📄 AuthFilter.php      <- Proteksi otentikasi halaman admin/scraper/profil
│   ├── 📁 Libraries               <- Tempat kelas-kelas utilitas independen
│   │   ├── 📄 CrawlerLib.php          
│   │   ├── 📄 DomDocumentParser.php   
│   │   └── 📄 UrlRewriter.php         
│   ├── 📁 Models
│   │   ├── 📄 DoodleModel.php     <- Model ORM `gkr_doodle` untuk logo tematik
│   │   ├── 📄 ImageModel.php      <- Model ORM `cari_images` ($useSoftDeletes = true)
│   │   ├── 📄 SiteModel.php       <- Model ORM `cari_sites` ($useSoftDeletes = true)
│   │   ├── 📄 UserModel.php       <- Model ORM `cari_users` untuk autentikasi dan profil
│   │   └── 📄 VersiModel.php      <- Model ORM `gkr_versi` untuk riwayat changelog
│   └── 📁 Views
│       ├── 📁 layout
│       │   ├── 📄 main.php        <- Master template (injeksi Bootstrap 5.3 & Vue CDN)
│       ├── 📁 versi
│       │   └── 📄 index.php       <- View tampilan Card-Based Changelog publik
│       ├── 📄 admin.php           <- View dashboard CRUD
│       ├── 📄 admin_versi.php     <- View panel manajemen rilis (Changelog CRUD)
│       ├── 📄 crawl.php           <- View interface scraper live-stream
│       ├── 📄 index.php           <- Beranda ala Google
│       ├── 📄 login.php           <- View antarmuka login
│       ├── 📄 profile.php         <- View halaman profil pengguna
│       └── 📄 search_results.php  <- Penampil grid Masonry gambar dan situs
├── 📁 public
│   ├── 📄 index.php               <- Entry Point CodeIgniter 4 Front Controller
│   ├── 📄 .htaccess
│   ├── 📁 css
│   │   ├── 📁 fancybox
│   │   │   └── 📄 jquery.fancybox.min.css
│   │   └── 📄 style.css       <- Kustomisasi minor (Glassmorphism dll)
│   ├── 📁 images
│   │   ├── 📁 favicon
│   │   ├── 📁 icons
│   │   └── 📄 doogleLogo.png
│   └── 📁 js
│       ├── 📁 fancybox
│       │   └── 📄 jquery.fancybox.min.js
│       ├── 📁 masonry
│       │   └── 📄 masonry.pkgd.min.js
│       ├── 📄 jquery-3.3.1.min.js  <- Dibiarkan hanya untuk depedensi Fancybox/Masonry
│       └── 📄 app.js          <- Skrip Vue.js murni aplikasi
├── 📄 .env                        <- Konfigurasi env, termasuk kredensial Database
├── 📄 spark                       <- CLI Utility CodeIgniter 4
├── 📄 PRD.md
├── 📄 StyleGuide.md
├── 📄 Memory.md
├── 📄 Skills.md
├── 📄 struktur_folder.md
└── 📄 README.md