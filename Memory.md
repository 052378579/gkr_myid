# Memory Document: Doogle (gkr_my.id) - CodeIgniter 4 Version

Dokumen ini berfungsi sebagai "ingatan" teknis atau basis pengetahuan (*knowledge base*) terkait arsitektur dan struktur proyek Doogle pasca-refactoring ke CodeIgniter 4.

## 1. Ikhtisar Proyek
* **Nama Proyek:** Doogle (Gracia)
* **Tujuan:** Mesin pencari khusus katalog furniture dan gambar terkait.
* **Stack Utama:** CodeIgniter 4 (PHP), Vue.js 3 (CDN), Bootstrap 5.3.
* **Database:** `gkr_myid` (MySQL/MariaDB). Konfigurasi database diatur secara eksklusif lewat file `.env`.

## 2. Arsitektur Database
Sistem menggunakan dua tabel utama yang kini direpresentasikan penuh oleh CI4 Models:
1. **`cari_sites` (Model: `SiteModel`)**:
   * Kolom: `id`, `url`, `title`, `description`, `keywords`, `clicks`, `deleted_at`.
   * Data situs dan tautan halaman.
2. **`cari_images` (Model: `ImageModel`)**:
   * Kolom: `id`, `siteUrl`, `imageUrl`, `alt`, `title`, `clicks`, `broken`, `deleted_at`.
   * Data direktori dan galeri gambar.
*Catatan:* Konsep *Soft Delete* otomatis ditangani framework CI4 melalui konfigurasi parameter model.

## 3. Struktur Direktori CI4 Utama
Migrasi dari *flat PHP files* ke arsitektur *MVC*:
* `app/Controllers/`: 
  * `Home.php`: Menangani halaman muka.
  * `Search.php`: Menangani logika pencarian dan paginasi.
  * `Admin.php`: Menangani layout panel dasbor backend.
  * `Crawler.php`: Menangani antarmuka dan mesin scraper.
  * `Api.php`: Endpoint terpusat untuk Vue.js `fetch()` (Update clicks, set broken, CRUD ajax).
* `app/Models/`: 
  * `SiteModel.php`, `ImageModel.php` (Interaksi aktif database).
* `app/Views/`: 
  * Direktori tempat komponen antarmuka pengguna bersarang, di mana Vue.js 3 diinjeksi via CDN pada masing-masing layout CI4.
* `app/Libraries/`: 
  * Lokasi baru untuk logika kelas `CrawlerLib.php`, `DomDocumentParser.php`, dan `UrlRewriter.php`.
* `public/`:
  * Dokumen root web server (`index.php`). Semua *assets* (CSS Bootstrap/kustom, script Vue/JS, library jQuery/Fancybox/Masonry, images) dipindahkan ke `public/assets/`.

## 4. Pola Implementasi Saat Ini
* **Frontend:** Tampilan visual UI sepenuhnya ditenagai oleh **Bootstrap 5.3**. Sedangkan state, interaktivitas, dan reaktivitas diurus oleh **Vue.js 3** melalui CDN, yang dimuat di dalam layout view CodeIgniter.
* **Routing:** Seluruh akses URL dipusatkan pada Controller melalui routing terstruktur (dideklarasikan di `app/Config/Routes.php`), menggantikan file `.php` murni lama.
* **Metode Crawling:**
  * Mendukung pembacaan direktori file lokal dan ekstraksi DOM situs eksternal yang di-package sebagai *CI4 Library*.
  * Menggunakan *flush streaming mechanism* di dalam Controller khusus yang melepas buffer (bypass view renderer CI4) untuk melayani *Fetch API ReadableStream* pada front-end crawler secara real-time.

## 5. Resolusi Utang Teknis (Technical Debt)
* **Keamanan Endpoint:** Akses halaman administratif (`/admin`, `/crawl`, `/reset-db`) dapat dilindungi secara elegan dengan memanfaatkan fitur **CI4 Filters** (Middleware) tanpa mengotori logika bisnis.
* **Isolasi Logika:** Pemisahan fungsional sangat kentara di mana endpoint pengambilan/perubahan data dipusatkan di `Api` Controller terpisah, sedangkan presentasi dikunci di dalam Views.
