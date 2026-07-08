# Memory Document: Mesin Pencari Gracia (gkr.my.id) - CodeIgniter 4 Version

Dokumen ini berfungsi sebagai "ingatan" teknis atau basis pengetahuan (*knowledge base*) terkait arsitektur dan struktur proyek Mesin Pencari Gracia pasca-refactoring ke CodeIgniter 4.

## 1. Ikhtisar Proyek
* **Nama Proyek:** Mesin Pencari Gracia
* **Tujuan:** Mesin pencari khusus katalog furniture dan gambar terkait.
* **Stack Utama:** CodeIgniter 4 (PHP), Vue.js 3 (CDN), Bootstrap 5.3.
* **Database:** `gkr_myid` (MySQL/MariaDB). Konfigurasi database diatur secara eksklusif lewat file `.env`.

## 2. Arsitektur Jaringan & Topologi Server
Sistem beroperasi dalam dua environment utama:
* **Lingkungan Development (Dev):** 
  * Akses via IP Lokal (LAN): `192.168.1.4`
  * Akses via VPN (ZeroTier): `10.147.17.40`
* **Lingkungan Production (Prod):**
  * Akses via IP Lokal (LAN): Dinamis (saat ini `192.168.1.17`)
  * Akses via VPN (ZeroTier): `10.147.17.60`
* **Penyajian Gambar Statis:** Gambar dan foto dari direktori lokal server (`/var/www/FOTO`) dilayani secara statis melalui subdomain `https://foto.gkr.my.id`.

## 3. Arsitektur Database
Sistem menggunakan tabel utama yang kini direpresentasikan penuh oleh CI4 Models:
1. **`cari_sites` (Model: `SiteModel`)**:
   * Kolom: `id`, `url`, `title`, `description`, `keywords`, `clicks`, `deleted_at`.
   * Data situs dan tautan halaman.
2. **`cari_images` (Model: `ImageModel`)**:
   * Kolom: `id`, `siteUrl`, `imageUrl`, `alt`, `title`, `clicks`, `broken`, `deleted_at`.
   * Data direktori dan galeri gambar.
3. **Model Pengguna (`UserModel`)**:
   * Mengatur data autentikasi dan profil pengguna.
4. **Tabel `gkr_doodle` (Model: `DoodleModel`)**:
   * Mengatur data logo tematik harian pada antarmuka pencarian.
5. **Tabel `gkr_versi` (Model: `VersiModel`)**:
   * Menampung rekam jejak (*Changelog*) pembaruan rilis. Menyimpan fitur, perbaikan bug, dan *patch* dalam format array JSON (`improvements`, `fixes`, `patches`).
*Catatan:* Konsep *Soft Delete* otomatis ditangani framework CI4 melalui konfigurasi parameter model.

## 4. Struktur Direktori CI4 Utama
Migrasi dari *flat PHP files* ke arsitektur *MVC*:
* `app/Controllers/`: 
  * `Home.php`, `Search.php`: Menangani halaman muka dan pencarian.
  * `Admin.php`, `Admin/DoodleController.php`: Menangani layout panel dasbor backend dan CRUD Doodle.
  * `Versi.php`, `AdminVersi.php`: Menangani rute publik Changelog dan rute manajemen data versi rilis.
  * `Crawler.php`: Menangani antarmuka dan mesin scraper.
  * `Api.php`: Endpoint terpusat untuk Vue.js `fetch()`.
  * `Auth.php`, `Profile.php`, `Dokumen.php`: Menangani proses autentikasi dan manajemen profil pengguna serta akses dokumen/gambar.
* `app/Models/`: 
  * `SiteModel.php`, `ImageModel.php`, `UserModel.php`, `DoodleModel.php`, `VersiModel.php` (Interaksi aktif database).
* `app/Filters/`:
  * `AuthFilter.php`: Middleware pelindung endpoint dan rute privat.
* `app/Views/`: 
  * Tempat komponen antarmuka pengguna bersarang (`login.php`, `profile.php`, `admin.php`, `admin_versi.php`, dll).
  * Area spesifik rute publik (contoh: `versi/index.php`).
  * Struktur Layout Template mengikuti *User Rule*: `app/Views/layout`.
* `app/Libraries/`: 
  * `CrawlerLib.php`, `DomDocumentParser.php`, dan `UrlRewriter.php`.
* `public/`:
  * Dokumen root web server (`index.php`).

## 5. Pola Implementasi Saat Ini
* **Frontend:** Tampilan visual UI sepenuhnya ditenagai oleh **Bootstrap 5.3**. Sedangkan state, interaktivitas, dan reaktivitas diurus oleh **Vue.js 3** melalui CDN, yang dimuat di dalam layout view CodeIgniter.
* **Routing:** Seluruh akses URL dipusatkan pada Controller melalui routing terstruktur (dideklarasikan di `app/Config/Routes.php`), menggantikan file `.php` murni lama.
* **Metode Crawling:**
  * Mendukung pembacaan direktori file lokal (`/var/www/FOTO`) dan ekstraksi DOM situs eksternal yang di-package sebagai *CI4 Library*.
  * Menggunakan *flush streaming mechanism* di dalam Controller khusus yang melepas buffer (bypass view renderer CI4) untuk melayani *Fetch API ReadableStream* pada front-end crawler secara real-time.

## 6. Resolusi Utang Teknis (Technical Debt)
* **Keamanan Endpoint:** Akses halaman administratif (`/admin`, `/crawl`, `/crawler/resetDb`) dilindungi secara elegan dengan memanfaatkan fitur **CI4 Filters** (`AuthFilter`) tanpa mengotori logika bisnis.
* **Isolasi Logika:** Pemisahan fungsional sangat kentara di mana endpoint pengambilan/perubahan data dipusatkan di `Api` Controller terpisah, sedangkan presentasi dikunci di dalam Views.
