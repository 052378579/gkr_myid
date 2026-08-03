# Mesin Pencari Visual Gracia (gkr.my.id)

**Mesin Pencari Gracia** adalah platform pencarian cerdas terpadu yang memadukan arsitektur **CodeIgniter 4 (PHP 8.2+)** dan **Layanan Mikro Python (FastAPI + PyTorch MobileNetV3 + FAISS)** sebagai inti pemrosesan Vektor Kecerdasan Buatan (*Artificial Intelligence*).

Sistem hibrida ini dirancang khusus untuk mengindeks, menelusuri, dan merekomendasikan katalog furniture serta swatch bahan. Sistem ini mengotomatiskan pengolahan korpus foto dari direktori lokal (`/var/www/FOTO`) menjadi data vektor 576-dimensi untuk pencarian gambar visual (*Image-to-Image Search*), disajikan dengan antarmuka yang reaktif, cepat, intuitif, dan responsif.

---

## 🚀 Fitur Unggulan Sistem

* **Single Physical Table Database `gkr_cari` & Kolom Bahasa Indonesia:**
  Menggabungkan tabel lama menjadi satu tabel fisik murni **`gkr_cari`** dengan 11 kolom baku Bahasa Indonesia: `id`, `judul`, `alt`, `deskripsi`, `url`, `imageUrl`, `siteUrl`, `kata_kunci`, `kode_bom`, `klik`, `rusak`. Tipe entitas diidentifikasi murni dari `imageUrl IS NOT NULL`.
* **Manajemen Engine Modal Box Ergonomis & Tabel Dinamis (`/admin/cari`):**
  * **Tabel Responsif Mobile & Pewarnaan Lencana Dinamis:** Menggunakan kelas `d-none d-md-table-cell/flex` untuk membebaskan ruang di layar seluler secara ekstrem (menyembunyikan *dropdown pagination*, ikon edit, dan 3 kolom sekunder). Kolom **Kode BOM** diwarnai dinamis di sisi klien (Vue JS) menggunakan teknik opasitas latar 10% (RGBA) agar kontras teks selalu tinggi pada *Light Mode* maupun *Dark Mode*.
  * **Modal Box 10 Field Ergonomis:** Modal Box **"Edit Data Mesin Pencari"** (`#modalEditImage`) 2-kolom memfasilitasi pengeditan field secara bersih tanpa kotak *textarea* `deskripsi`, namun nilai `deskripsi` lama tetap dilestarikan (dilindungi) di latar belakang oleh reaktivitas Vue JS saat disimpan.
* **Standardisasi Layout Google-Grade & Dark Mode Adaptif (`/cari`):**
  * **Responsivitas Header Mobile 2-Baris:** On Mobile (<768px), layout header menyusun Baris 1: Logo GRACIA (kiri) + Date/Apps/Avatar (kanan), Baris 2: Search Box 100% lebar penuh dengan penyembunyian ikon Kaca Pembesar (`.search-button`) dan `gap: 5px !important`.
  * **Icon Action Buttons Presisi (`search.css`):** Tombol ikon mic, kamera, dan cari dikemas dalam kelas `.icon-action-btn` (32px x 32px).
  * **Knowledge Card Borderless & Background Transparan:** Hero image `.google-knowledge-hero-img` diset `border: none !important; border-radius: 0 !important; padding: 0 !important; background-color: transparent !important;` pada Mode TERANG dan GELAP dengan SVG fallback 100% transparan (`fill="transparent"`). Knowledge Panel 35% kanan tampil responsif di Mobile (`col-12 col-lg-4 mt-4 mt-lg-0`).
  * **Penyelarasan Garis Vertikal Pembatas (`border-left`):** Diset dengan `margin-top: 38px !important;` pada Desktop agar dimulai presisi sejajar dengan titik puncak hero image / hasil pertama, dan di-reset `border-left: none !important;` pada Mobile (<992px).
  * **Default Fallback Knowledge Panel:** Merender bersih nilai asali `Deskripsi: -`, `Kode BOM: FG-`, `Lihat BOM: BOM-FG-`, dan `Produksi: UNIT -` saat data tidak tersedia dari basis data. Inisialisasi perenderan diamankan menggunakan `onMounted` di Vue JS untuk menghindari *race condition*.
  * **Flush Left Vertical Alignment (Margin 10% Kiri):** Tab `[Semua]`, `Ditemukan {jumlah} hasil`, dan garis aksen 3px Biru Dongker `.site-result-item` berderet 100% rata lurus vertikal.
  * **Pagination Logo `c a r i` Vector Typography Baseline:** Tipografi vektor HTML/CSS Google-Grade (`Outfit`/`Product Sans`) yang 100% Rata Lurus Horizontal Baseline (`align-items: baseline`) dan Rata Tengah (*Centered*).
* **Keseragaman Dropdown Menu (Apps Grid & Kalender):**
  Dropdown Apps Grid dan Kalender menggunakan kelas native Bootstrap `dropdown-menu dropdown-menu-end shadow border-0 p-3 mt-2 rounded-4` dan `text-body` (`color: var(--bs-body-color)`), tanpa inline glassmorphism, adaptif 100% pada Mode Terang dan Gelap.
* **Halaman Catatan Versi Rilis (`/versi`):**
  * Format tanggal Indonesia seragam `01/08/2026` (`dd/mm/yyyy`).
  * Tampilan responsif Mobile 1-Kolom Stack (`@media (max-width: 767px)`) pada `public/css/admin_versi.css` agar judul dan deskripsi changelog tidak terpotong.
* **Kalender Dropdown Interaktif Navbar (`admin_layout.php` & `calendar.js`):**
  Tanggal navbar `Selasa, 28/07/2026 ▾` mengusung Bootstrap Dropdown `#calendarDropdownWrap`. Tanggal hari ini disorot dengan **lingkaran padat Biru Dongker Gracia (`#2B3385`)** dan teks putih (`#ffffff`). Layering dropdown kalender diset ke `z-index: 1060 !important;` melayang mulus di atas seluruh elemen beranda.
* **Otomatisasi Cronjob & Notifikasi Telegram Ringkas:**
  - **Auto Crawler (`0 18 * * *`)**: Dijalankan setiap pukul **18:00 WIB** via CLI `php spark crawl:run /var/www/FOTO`. Mengirimkan notifikasi Telegram ringkas dengan label `Server: DEV/PROD` dan jumlah item terbackup (`💾 X Item Baru Ditambahkan`).
  - **AI Trainer Engine (`1 0 * * *`)**: Dijalankan setiap pukul **00:01 WIB** via Python `ai_index.py`, otomatis menyalin berkas `produk.index` & `mapping.json` ke `/mnt/sdcard/ai-scanner/` serta memuat ulang `ai_scanner.service`. Mengirimkan notifikasi Telegram ringkas (`Server: DEV/PROD` & `💾 Berkas produk.index & mapping.json Berhasil Diperbarui`).
* **Pembersihan Scan Direktori (`SAMPLE GRACIA` Excluded):**
  Seluruh proses pengindeksan perayap web (`CrawlerLib.php`) dan pelatih AI (`ai_index.py`) hanya menyisir 4 direktori aktif (`BUYER`, `GRACIA`, `SWATCHES`, `WEB`). Direktori legacy `SAMPLE GRACIA` diblokir total.
* **Pendaftaran Otomatis (*Auto-Bind*) Telegram Chatbot (`ChatBotApi.php`):**
  Karyawan terdaftar dapat menghubungkan akun Telegram pribadi secara mandiri via `/start 08...` atau `/daftar 08...`. Sistem otomatis mencocokkan `no_hp` di `gkr_users` (Skenario 1 Keamanan terkunci untuk nomor HP terdaftar oleh Admin/HRD).
* **Penguncian Notifikasi Sistem Statis Administrator:**
  Metode `sendTelegramNotification()` di `Auth.php` (login/logout) dan `ai_index.py` (crawler) tetap dikunci pada 1 ID Statis Administrator (`8784856529` - Budi).
* **Dashboard KPI Administrasi & Visualisasi ApexCharts (`/admin/dashboard`):**
  Rute `/admin` dan `/admin/dashboard` menyajikan Dasbor KPI Utama dengan 4 Summary Cards (Termasuk **Total Users** Karyawan Terdaftar) dan **ApexCharts Bar Chart (Custom Data Labels)** yang menampilkan 10 produk paling sering dicari (`klik DESC`).
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
* **Pencarian Suara Bahasa Indonesia Native (`id-ID`) & Tab Gambar Default:**
  Pencarian suara berbasis Web Speech API `id-ID` (`public/js/voice_search.js`) dengan animasi pendaran gelombang suara reaktif. Pencarian suara dari beranda secara otomatis mengarahkan ke **Tab Gambar Katalog** (`/cari?q=...&type=images`).
* **Mesin Pencari Presisi Presisi Tinggi (`Search.php`):**
  * **Primary Brand/Series Anchor Search:** Deteksi otomatis token merk/seri spesifik (`$specificBrandAnchor`, misal `"bonanza"` pada `bonanza+table` atau `bonanza+coffee`) yang mewajibkan produk memuat kata merk utama. Mengeliminasi 100% produk pengotor seperti *Riazor Rectangular Coffee Table*, *Tamika Coffee Table*, *Alandra Coffee Table*, dan *Side Table Type 02*.
  * **Comprehensive Category Antonym Exclusion:** Matriks konflik tiga arah (Table vs Chair vs Lamp) yang 100% mengeliminasi *Leora Dinning Chair* (kursi) dan *Mida Table Lamp Organic Motif* (lampu meja) pada pencarian `dinning table`.
  * **Multi-Tier Relevance Scoring:** Perhitungan skor SQL `ORDER BY` bertingkat (Skor 100 untuk exact phrase, Skor 80 untuk all-tokens match, dan pengurutan popularitas `klik DESC`).
* **Sistem Otorisasi, Private Mode & Audit Log (RBAC):**
  Ekosistem dikunci secara absolut ke dalam mode privat (Private Mode) melalui `AuthFilter.php` (hanya membuka celah untuk halaman `/login`, `/daftar`, dan webhook Telegram). Rute `/admin/*` diisolasi lapis kedua menggunakan `SuperAdminFilter` yang khusus mengizinkan sesi `id_user = 1`. Seluruh aktivitas (Log Cari & Log User) direkam dengan pelacakan *Real IP* menembus jaringan *Reverse Proxy* / VPN ZeroTier (membaca `X-Forwarded-For`), yang dibersihkan secara otomatis ke format **IPv4 murni** untuk keperluan Audit Forensik presisi.

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
* **`public/css/`**: Modul CSS fisik (`index.css`, `search.css`, `admin.css`, `admin_versi.css`, `auth.css`, `awan_kata.css`).
* **`public/js/`**: Skrip JS fisik modular (`admin_beranda.js`, `admin_dashboard.js`, `calendar.js`, `voice_search.js`, `awan_kata.js`, `daftar.js`).
* **`python_services/`**: Karantina kecerdasan buatan Python (`ai_index.py`, `ai_scanner.service`, `produk.index`, `mapping.json`).

---

## 🔒 Konvensi Keamanan & Pengelolaan Berkas

1. **Aturan Keamanan Database Backup:** File dump database (`.sql`) **dilarang keras** berada di direktori web (`/var/www/gkr_myid`). Semua berkas backup wajib dievakuasi ke direktori terisolasi (`/root/backups/`).
2. **Kertas Kerja Internal vs Etalase Publik:**
   * **Etalase Publik (Lacak Git):** `README.md`, `Tautan.md`, `struktur_folder.md`, `git.txt`.
   * **Dokumen Internal (Gitignore):** `PRD.md`, `Memory.md`, `Skills.md`, `StyleGuide.md`, `Task.md`.
