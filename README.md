# Mesin Pencari Visual Gracia (gkr.my.id)

**Mesin Pencari Gracia** adalah platform pencarian cerdas terpadu yang memadukan arsitektur **CodeIgniter 4 (PHP 8.2+)** dan **Layanan Mikro Python (FastAPI + PyTorch MobileNetV3 + FAISS)** sebagai inti pemrosesan Vektor Kecerdasan Buatan (*Artificial Intelligence*).

Sistem hibrida ini dirancang khusus untuk mengindeks, menelusuri, dan merekomendasikan katalog furniture serta swatch bahan. Sistem ini mengotomatiskan pengolahan korpus foto dari direktori lokal (`/var/www/FOTO`) menjadi data vektor 576-dimensi untuk pencarian gambar visual (*Image-to-Image Search*), disajikan dengan antarmuka yang reaktif, cepat, intuitif, dan responsif.

---

## 🚀 Fitur Unggulan Sistem

* **Single Physical Table Database `gkr_cari` & Kolom Bahasa Indonesia:**
  Menggabungkan tabel lama menjadi satu tabel fisik murni **`gkr_cari`** dengan kolom baku Bahasa Indonesia (`judul`, `alt`, `deskripsi`, `url`, `imageUrl`, `siteUrl`, `kata_kunci`, `klik`, `rusak`). Tipe entitas diidentifikasi murni dari `imageUrl IS NOT NULL`.
* **Kalender Dropdown Interaktif Navbar (`admin_layout.php` & `calendar.js`):**
  Tanggal navbar `Selasa, 28/07/2026 ▾` mengusung Bootstrap Dropdown `#calendarDropdownWrap`. Tanggal hari ini disorot dengan **lingkaran padat Biru Dongker Gracia (`#2B3385`)** dan teks putih (`#ffffff`).
* **Desain Bersih Tombol Cari (`beranda.php`):**
  Tombol **Cari** menggunakan gaya murni Bootstrap `rounded-pill` (`bg-body-tertiary`, `height: 42px`, `min-width: 120px`) tanpa pendaran warna pelangi. Layering dropdown kalender diset ke `z-index: 1060 !important;` agar melayang mulus di atas seluruh elemen beranda.
* **Pembersihan Scan Direktori (`SAMPLE GRACIA` Excluded):**
  Seluruh proses pengindeksan perayap web (`CrawlerLib.php`) dan pelatih AI (`ai_index.py`) hanya menyisir 4 direktori aktif (`BUYER`, `GRACIA`, `SWATCHES`, `WEB`). Direktori legacy `SAMPLE GRACIA` diblokir total.
* **Pendaftaran Otomatis (*Auto-Bind*) Telegram Chatbot (`ChatBotApi.php`):**
  Karyawan terdaftar dapat menghubungkan akun Telegram pribadi secara mandiri via `/start 08...` atau `/daftar 08...`. Sistem otomatis mencocokkan `no_hp` di `gkr_users` (Skenario 1 Keamanan terkunci untuk nomor HP terdaftar oleh Admin/HRD).
* **Penguncian Notifikasi Sistem Statis Administrator:**
  Metode `sendTelegramNotification()` di `Auth.php` (login/logout) dan `ai_index.py` (crawler) tetap dikunci pada 1 ID Statis Administrator (`8784856529` - Budi).
* **Dashboard KPI Administrasi & Visualisasi ApexCharts (`/admin/dashboard`):**
  Rute `/admin` dan `/admin/dashboard` menyajikan Dasbor KPI Utama dengan 4 Summary Cards (Termasuk **Total Users** Karyawan Terdaftar) dan **ApexCharts Bar Chart (Custom Data Labels)** yang menampilkan 10 produk paling sering dicari (`klik DESC`).
* **Halaman Manajemen Mesin Pencari (`/admin/cari`):**
  Antarmuka pengelolaan data `gkr_cari` dengan desain Card Header bersih tanpa TAB, reposisi pemilih baris `Tampilkan: [10 v]` di sebelah kiri kotak pencarian, serta thumbnail foto kolom **Gambar** yang dapat diklik langsung untuk membuka gambar ukuran penuh pada domain server asal.
* **Optimalisasi Crawler 1 Produk = 1 Baris Utuh (`CrawlerLib.php`):**
  Setiap file foto produk diindeks murni sebagai **1 Baris Utuh** (menampung `url` dan `imageUrl` secara bersamaan), menghemat 50% kapasitas penyimpanan database.
* **Resolusi URL Dinamis Host (DEV vs PROD):**
  Penyesuaian domain foto otomatis terpusat via `getFotoUrlPrefix()`:
  * **Server DEV** (`192.168.1.4`, `10.147.17.40`, `gkr.budi.biz.id`) -> **`https://foto.budi.biz.id/`**
  * **Server PROD** (`192.168.1.17`, `10.147.17.60`, `gkr.my.id`) -> **`https://foto.gkr.my.id/`**
* **Pencarian Visual AI (Image-to-Image Search & Cropper.js):**
  Mengunggah sampel foto produk untuk mencocokkan kemiripan visual secara presisi menggunakan PyTorch *MobileNetV3-Small* (576 dimensi) dan FAISS Vector Database (`IndexFlatIP`, *Cosine Similarity* $\ge 0.68$, $k=15$). Dilengkapi pemotong gambar interaktif **Cropper.js** di browser.
* **Autocomplete Pencarian Teks (Debounced RESTful API):**
  Fitur rekomendasi kata kunci pencarian *real-time* berbasis API (`/api/autocomplete`) yang menyaring data dari tabel `gkr_material` (bahan & warna) dan `gkr_cari` (judul) dengan *debounce* 300ms serta navigasi kibor.
* **Zero-Footprint Storage (Auto-Cleanup):**
  Gambar unggahan pengguna ditransfer via `multipart/form-data` ke layanan FastAPI dan seketika dimusnahkan (`unlink()`) dari storage web server, menjaga penyimpanan server tetap bersih.
* **PWA Service Worker Exception (`public/sw.js`):**
  Service Worker mengabaikan permintaan `POST` dan rute `/crawler/`, `/api/`, `/admin/` agar streaming log perayap `/admin/crawl` dan Reset DB berjalan 100% lancar.
* **Dasbor Pelatih AI & Terminal Streaming HTTP (`/admin/ai`):**
  Menyajikan dasbor operasi pelatih AI (`ai_index.py`) dengan tampilan konsol terminal peretas (*hacker style* `#1e1e1e`). Mengalirkan baris log komputasi Python secara *real-time* via *HTTP ReadableStream API* tanpa risiko *PHP Timeout*, dilengkapi notifikasi Telegram Bot.
* **Sistem Otorisasi, Private Mode & Audit Log (RBAC):**
  Ekosistem dikunci secara absolut ke dalam mode privat (Private Mode) melalui `AuthFilter.php` (hanya membuka celah untuk halaman `/login`, `/daftar`, dan webhook Telegram). Rute `/admin/*` diisolasi lapis kedua menggunakan `SuperAdminFilter` yang khusus mengizinkan sesi `id_user = 1`.

---

## 🛠️ Stack Teknologi & Topologi Infrastruktur

* **Lingkungan Server DEV:** `192.168.1.4` | ZeroTier `10.147.17.40` | `gkr.budi.biz.id` | Foto: **`https://foto.budi.biz.id/`**
* **Lingkungan Server PROD:** `192.168.1.17` | ZeroTier `10.147.17.60` | `gkr.my.id` | Foto: **`https://foto.gkr.my.id/`**
* **Spesifikasi Server:** Armbian OS (Debian bookworm) Linux 6.12 pada peranti Amlogic S905x.
* **Web Backend:** CodeIgniter 4 (PHP 8.2+, MVC).
* **AI Microservice:** Python 3, FastAPI, PyTorch, FAISS (`http://127.0.0.1:5000` via daemon `ai_scanner.service`).
* **AI Trainer Engine:** Python 3 (`ai_index.py` dipanggil via subprocess CLI mode `-u` dengan PyTorch ARM Thread Clamping `OMP_NUM_THREADS=1` dan cache home `writable/torch_cache/`).
* **Web Frontend:** Vue.js 3 (CDN), ApexCharts, Bootstrap 5.3 (Native Dark Mode), Cropper.js, FontAwesome 6, ReadableStream API.
* **Database:** MySQL / MariaDB (Tabel Utama: `gkr_cari`).

---

## 📁 Ikhtisar Arsitektur Direktori Utama

* **`app/Controllers/Admin/`**: Mengelola pengontrol administratif (`AdminController.php`, `AiCrawler.php`, `Crawler.php`, `KaryawanController.php`, `DoodleController.php`, `VersiController.php`).
* **`app/Controllers/Api/`**: Sub-direktori steril layanan RESTful API murni (`GraciaApi.php`, `ImageSearchApi.php`, `VersiApi.php`, `ChatBotApi.php`, `CrawlerApi.php`).
* **`app/Filters/`**: Perisai keamanan middleware (`AuthFilter.php` & `SuperAdminFilter.php`).
* **`app/Libraries/`**: Library pendukung (`CrawlerLib.php` berotak Regex harmonis & Single-row product item indexing).
* **`app/Models/`**: Representasi tabel SQL (`CariModel.php`, `MaterialModel.php`, `LogCariModel.php`, `LogUserModel.php`, `UserModel.php`, `DoodleModel.php`).
* **`app/Views/layout/`**: Kerangka utama UI terpusat (`main.php` & `admin_layout.php`).
* **`public/`**: Titik masuk web (`index.php`), aset statis, `sw.js` (PWA Service Worker), dan flat-file changelog (`versi.json`).
* **`python_services/`**: Karantina kecerdasan buatan Python (`ai_index.py`, `ai_scanner.service`, `produk.index`, `mapping.json`).

---

## 🔒 Konvensi Keamanan & Pengelolaan Berkas

1. **Aturan Keamanan Database Backup:** File dump database (`.sql`) **dilarang keras** berada di direktori web (`/var/www/gkr_myid`). Semua berkas backup wajib dievakuasi ke direktori terisolasi (`/root/backups/`).
2. **Kertas Kerja Internal vs Etalase Publik:**
   * **Etalase Publik (Lacak Git):** `README.md`, `Tautan.md`, `struktur_folder.md`, `git.txt`.
   * **Dokumen Internal (Gitignore):** `PRD.md`, `Memory.md`, `Skills.md`, `StyleGuide.md`, `Task.md`.
