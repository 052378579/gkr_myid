# Struktur Folder Mesin Pencari Gracia (Refactoring CodeIgniter 4)

Struktur berikut memvisualisasikan bagaimana aplikasi Mesin Pencari Gracia direfaktor menggunakan standar kerangka kerja **CodeIgniter 4**, dengan balutan frontend **Bootstrap 5.3** dan **Vue.js 3 (CDN)**.

📁 gkr_myid
├── 📁 app
│   ├── 📁 Config
│   │   ├── 📄 Database.php        <- Definisi pengaturan dasar koneksi (timpa dengan .env)
│   │   ├── 📄 Routes.php          <- Daftar URL aplikasi (/, /cari, /admin, dll)
│   │   └── ...
│   ├── 📁 Controllers
│   │   ├── 📄 Admin.php           <- Mengelola tampilan panel manajemen
│   │   ├── 📄 Api.php             <- Menampung endpoint data AJAX (JSON Response)
│   │   ├── 📄 Crawler.php         <- Mengelola logika terminal scraper
│   │   ├── 📄 Home.php            <- Menampilkan halaman muka/landing
│   │   └── 📄 Search.php          <- Memproses dan me-render hasil pencarian
│   ├── 📁 Filters
│   │   └── 📄 AuthFilter.php      <- Proteksi otentikasi halaman admin/scraper
│   ├── 📁 Libraries               <- Tempat kelas-kelas utilitas independen
│   │   ├── 📄 CrawlerLib.php          
│   │   ├── 📄 DomDocumentParser.php   
│   │   └── 📄 UrlRewriter.php         
│   ├── 📁 Models
│   │   ├── 📄 ImageModel.php      <- Model ORM `cari_images` ($useSoftDeletes = true)
│   │   └── 📄 SiteModel.php       <- Model ORM `cari_sites` ($useSoftDeletes = true)
│   └── 📁 Views
│       ├── 📁 layouts
│       │   ├── 📄 main.php        <- Master template (injeksi Bootstrap 5.3 & Vue CDN)
│       ├── 📄 admin.php           <- View dashboard CRUD
│       ├── 📄 crawl.php           <- View interface scraper live-stream
│       ├── 📄 index.php           <- Beranda ala Google
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