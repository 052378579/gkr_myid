# Mesin Pencari Visual Gracia (gkr.my.id)

**Mesin Pencari Gracia** adalah platform pencarian cerdas terpadu yang memadukan arsitektur **CodeIgniter 4 (PHP 8.2+)** dan **Layanan Mikro Python (FastAPI + PyTorch MobileNetV3 + FAISS)** sebagai inti pemrosesan Vektor Kecerdasan Buatan (*Artificial Intelligence*).

Sistem hibrida ini dirancang khusus untuk mengindeks, menelusuri, dan merekomendasikan katalog furniture serta swatch bahan. Sistem ini mengotomatiskan pengolahan korpus foto dari direktori lokal (`/var/www/FOTO`) menjadi data vektor 576-dimensi untuk pencarian gambar visual (*Image-to-Image Search*), disajikan dengan antarmuka yang reaktif, cepat, intuitif, dan responsif.

---

## 🚀 Fitur Unggulan Sistem

* **Single Physical Table Database `gkr_cari` & Kolom Bahasa Indonesia:**
  Menggabungkan tabel lama menjadi satu tabel fisik murni **`gkr_cari`** dengan 11 kolom baku Bahasa Indonesia: `id`, `judul`, `alt`, `deskripsi`, `url`, `imageUrl`, `siteUrl`, `kata_kunci`, `kode_bom`, `klik`, `rusak`. Tipe entitas diidentifikasi murni dari `imageUrl IS NOT NULL`.
* **Standarisasi Absolute CAPITAL CASE `FG-` pada Kode BOM:**
  - Penulisan `fg-`, `Fg-`, `fg 12345`, `Fg 12345`, `(fg 12345)` **100% Wajib Dikonversi menjadi CAPITAL CASE `(FG-12345)`** atau `FG-12345` di seluruh proyek `/var/www/gkr_myid`.
  - Backend `Search.php` mengonversi seluruh teks judul, alt, dan deskripsi secara real-time via `/\(?\b(?:fg|Fg|FG)\s*[-_]?\s*([0-9]+)\)?/i` -> `(FG-$1)`.
  - View `search_results.php` steril dari panggilan perusak `ucwords(strtolower(...))`.
  - RESTful API (`GraciaApi.php`) dan Vue Controller (`search_results.js`, `admin_beranda.js`) mensanitasi `kode_bom` ke **CAPITAL CASE `FG-`** secara absolut.
* **Perayap Berkas Foto Lokal (`/admin/crawl` & `CrawlerLib.php`):**
  - Pemrosesan berkas foto katalog dari `/var/www/FOTO` (seperti `amaala_dining_side_chair_fg-24600.jpg`, `karlsson_dining_table_(fg-24988).jpg`):
    - `judul`, `alt`, `deskripsi`: Title Case dengan `FG-` **selalu CAPITAL CASE** dan dibungkus kurung `()`, contoh: `Amaala Dining Side Chair (FG-24600)` / `Karlsson Dining Table (FG-24988)`.
    - `kata_kunci`: Lowercase tokens dipisahkan koma dan spasi, **kecuali teks `FG-XXXXX` yang tetap CAPITAL CASE** (contoh: `amaala, dining, side, chair, FG-24600`).
    - `kode_bom`: Diekstrak murni sebagai **`FG-24600`** / **`FG-24988`**.
* **Penyempurnaan Spesifikasi Knowledge Card Panel & Interaksi Tombol Action Pills (`/cari`):**
  - `Deskripsi`: Title Case (contoh: `"Lindha Chair"` / `"Budapest Side Table (FG-42718)"`).
  - `Kode BOM`: Default `"FG-"` dengan warna huruf **Tema Gracia** (`var(--gkr-primary)` / `#2B3385`).
  - `Lihat BOM`: Default `"BOM-FG- -001"` dengan warna huruf **Tema Gracia** (`var(--gkr-primary)`).
  - `Produksi`: Default `"-"` dengan warna huruf **Tema Gracia** (`var(--gkr-primary)`).
  - `Action Pills Buttons {BOM, ERP, Foto}`:
    - **Kondisi Kode BOM Kosong:** Tombol `BOM` dan `ERP` berstatus **Disabled Button** (gaya tombol mati abu-abu `#e0e0e0` / `#303134`, teks `#9e9e9e`, `pointer-events: none; opacity: 0.65; cursor: not-allowed;`). Tombol `Foto` tetap berstatus Aktif.
    - **Kondisi Kode BOM Tersedia (misal `"FG-14540"`):** Tombol `BOM`, `ERP`, dan `Foto` berstatus **Tombol Aktif** (Default outline putih `#ffffff` + border 1px solid Biru Tema Gracia `#2B3385` -> Solid Biru Tema Gracia `#2B3385` saat Hover dengan teks/ikon warna putih `#ffffff`). Tombol Foto mengusung **Ikon Kamera** (`fa-solid fa-camera`).
* **Penataan Kartu Rapat & Presisi Aksen Garis Biru Lurus Vertikal:**
  - Kartu hasil pencarian berukuran rapat (tinggi proporsional ~127px di Desktop 1920x1082 px, padding `8px 14px !important`, margin bawah `10px !important`). Aksen garis 3px Tema Gracia di sebelah kiri kartu aktif dibuat **100% Lurus Tegak Vertikal** tanpa sudut melengkung pada ujung atas/bawah (`border-radius: 0 10px 10px 0 !important;`).
* **Pagination PNG `c-a-r-i` Horizontal Alignment:**
  - Puncak logo pagination PNG (`pageStart.png`, `pageSelected.png`, `page.png`, `pageEnd.png`) sejajar horizontal dengan garis alas terbawah Knowledge Card Panel di kolom kanan (`margin-top: 48px !important; padding-top: 12px !important;`).
* **Manajemen Engine Modal Box Ergonomis & Tabel Dinamis (`/admin/cari`):**
  * **Tabel Responsif Mobile & Pewarnaan Lencana Dinamis:** Menggunakan kelas `d-none d-md-table-cell/flex` untuk membebaskan ruang di layar seluler secara ekstrem (menyembunyikan *dropdown pagination*, ikon edit, dan 3 kolom sekunder). Kolom **Kode BOM** diwarnai dinamis di sisi klien (Vue JS) menggunakan teknik opasitas latar 10% (RGBA) agar kontras teks selalu tinggi pada *Light Mode* maupun *Dark Mode*.
  * **Modal Box 10 Field Ergonomis:** Modal Box **"Edit Data Mesin Pencari"** (`#modalEditImage`) 2-kolom memfasilitasi pengeditan field secara bersih tanpa kotak *textarea* `deskripsi`, namun nilai `deskripsi` lama tetap dilestarikan (dilindungi) di latar belakang oleh reaktivitas Vue JS saat disimpan.
* **Standardisasi Layout Google-Grade & Dark Mode Adaptif (`/cari`):**
  * **Responsivitas Header Mobile 2-Baris:** On Mobile (<768px), layout header menyusun Baris 1: Logo GRACIA (kiri) + Date/Apps/Avatar (kanan), Baris 2: Search Box 100% lebar penuh.
  * **Icon Action Buttons Presisi (`search.css`):** Tombol ikon mic, kamera, dan cari dikemas dalam kelas `.icon-action-btn` (32px x 32px).
  * **Knowledge Card Borderless & Background Transparan:** Hero image `.google-knowledge-hero-img` diset `border: none !important; border-radius: 0 !important; padding: 0 !important; background-color: transparent !important;` pada Mode TERANG dan GELAP dengan SVG fallback 100% transparan (`fill="transparent"`). Knowledge Panel 35% kanan tampil responsif di Mobile (`col-12 col-lg-4 mt-4 mt-lg-0`).
  * **Penyelarasan Garis Vertikal Pembatas (`border-left`):** Diset dengan `margin-top: 38px !important;` pada Desktop agar dimulai presisi sejajar dengan titik puncak hero image / hasil pertama, dan di-reset `border-left: none !important;` pada Mobile (<992px).
  * **Flush Left Vertical Alignment (Margin 10% Kiri):** Tab `[Semua]`, `Ditemukan {jumlah} hasil`, dan garis aksen 3px Biru Dongker `.site-result-item` berderet 100% rata lurus vertikal.
  * **Pagination Logo `c a r i` Vector Typography Baseline:** Tipografi vektor HTML/CSS Google-Grade (`Outfit`/`Product Sans`) yang 100% Rata Lurus Horizontal Baseline (`align-items: baseline`) dan Rata Tengah (*Centered*).
* **Keseragaman Dropdown Menu (Apps Grid & Kalender):**
  Dropdown Apps Grid dan Kalender menggunakan kelas native Bootstrap `dropdown-menu dropdown-menu-end shadow border-0 p-3 mt-2 rounded-4` dan `text-body` (`color: var(--bs-body-color)`), tanpa inline glassmorphism, adaptif 100% pada Mode Terang dan Gelap.
* **Halaman Catatan Versi Rilis (`/versi`):**
  * Format tanggal Indonesia seragam `01/08/2026` (`dd/mm/yyyy`).
  * Tampilan responsif Mobile 1-Kolom Stack (`@media (max-width: 767px)`) pada `public/css/admin_versi.css` agar judul dan deskripsi changelog tidak terpotong.
* **Kalender Dropdown Interaktif Navbar (`admin_layout.php` & `calendar.js`):**
  Tanggal navbar `Selasa, 04/08/2026 ▾` mengusung Bootstrap Dropdown `#calendarDropdownWrap`. Tanggal hari ini disorot dengan **lingkaran padat Biru Dongker Gracia (`#2B3385`)** dan teks putih (`#ffffff`). Layering dropdown kalender diset ke `z-index: 1060 !important;` melayang mulus di atas seluruh elemen beranda.
* **Otomatisasi Cronjob & Notifikasi Telegram Ringkas:**
  - **Auto Crawler (`0 18 * * *`)**: Dijalankan setiap pukul **18:00 WIB** via CLI `php spark crawl:run /var/www/FOTO`. Mengirimkan notifikasi Telegram ringkas dengan label `Server: DEV/PROD` dan jumlah item terbackup (`💾 X Item Baru Ditambahkan`).
  - **AI Trainer Engine (`1 0 * * *`)**: Dijalankan setiap pukul **00:01 WIB** via Python `ai_sync.py`, mengekstrak fitur foto secara diferensial dan memuat ulang `ai_scanner.service`. Mengirimkan notifikasi Telegram ringkas (`Server: DEV/PROD` & `💾 Inkremental Berhasil: X Ditambahkan, Y Dihapus.`).
* **Pembersihan Scan Direktori (`SAMPLE GRACIA` Excluded):**
  Seluruh proses pengindeksan perayap web (`CrawlerLib.php`) dan pelatih AI (`ai_sync.py` & `ai_reset.py`) hanya menyisir 4 direktori aktif (`BUYER`, `GRACIA`, `SWATCHES`, `WEB`). Direktori legacy `SAMPLE GRACIA` diblokir total.
* **Pendaftaran Otomatis (*Auto-Bind*) Telegram Chatbot (`ChatBotApi.php`):**
  Karyawan terdaftar dapat menghubungkan akun Telegram pribadi secara mandiri via `/start 08...` atau `/daftar 08...`. Sistem otomatis mencocokkan `no_hp` di `gkr_users` (Skenario 1 Keamanan terkunci untuk nomor HP terdaftar oleh Admin/HRD).
* **Penguncian Notifikasi Sistem Statis Administrator:**
  Metode `sendTelegramNotification()` di `Auth.php` (login/logout) dan `ai_sync.py` tetap dikunci pada 1 ID Statis Administrator (`8784856529` - Budi).
* **Dashboard KPI Administrasi & Visualisasi ApexCharts (`/admin/dashboard`):**
  Rute `/admin` dan `/admin/dashboard` menyajikan Dasbor KPI Utama dengan 4 Summary Cards (Termasuk **Total Users** Karyawan Terdaftar) dan **ApexCharts Bar Chart (Custom Data Labels)** yang menampilkan 10 produk paling sering dicari (`klik DESC`).
* **Optimalisasi Crawler 1 Produk = 1 Baris Utuh (`CrawlerLib.php`):**
  Setiap file foto produk diindeks murni sebagai **1 Baris Utuh** (menampung `url` dan `imageUrl` secara bersamaan), menghemat 50% kapasitas penyimpanan database.
* **Resolusi URL Dinamis Host (DEV vs PROD):**
  Penyesuaian domain foto otomatis terpusat via `getFotoUrlPrefix()`:
  * **Server DEV** (`192.168.1.4`, `10.147.17.40`, `gkr.budi.biz.id`) -> **`https://foto.budi.biz.id/`**
  * **Server PROD** (`192.168.1.17`, `10.147.17.6`, `gkr.my.id`) -> **`https://foto.gkr.my.id/`**
* **Pencarian Visual AI (Image-to-Image Search & Cropper.js):**
  Mengunggah sampel foto produk untuk mencocokkan kemiripan visual secara presisi menggunakan PyTorch *MobileNetV3-Small* (576 dimensi) dan FAISS Vector Database (`IndexIDMap`, *Cosine Similarity* $\ge 0.68$, $k=15$). Dilengkapi pemotong gambar interaktif **Cropper.js** di browser.
* **Autocomplete Pencarian Teks (Debounced RESTful API):**
  Fitur rekomendasi kata kunci pencarian *real-time* berbasis API (`/api/autocomplete`) yang menyaring data dari tabel `gkr_material` (bahan & warna) dan `gkr_cari` (judul) dengan *debounce* 300ms serta navigasi kibor.
* **Zero-Footprint Storage (Auto-Cleanup):**
  Gambar unggahan pengguna ditransfer via `multipart/form-data` ke layanan FastAPI dan seketika dimusnahkan (`unlink()`) dari storage web server, menjaga penyimpanan server tetap bersih.
* **PWA Service Worker Exception (`public/sw.js`):**
  Service Worker mengabaikan permintaan `POST` dan rute `/crawler/`, `/api/`, `/admin/` agar streaming log perayap `/admin/crawl` dan Reset DB berjalan 100% lancar.
* **Dasbor Pelatih AI & Terminal Streaming HTTP (`/admin/ai`):**
  Menyajikan dasbor operasi pelatih AI (`ai_sync.py` & `ai_reset.py`) dengan tampilan konsol terminal peretas (*hacker style* `#1e1e1e`). Mengalirkan baris log komputasi Python secara *real-time* via *HTTP ReadableStream API* tanpa risiko *PHP Timeout*, dilengkapi notifikasi Telegram Bot.
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
* **Lingkungan Server PROD:** `192.168.1.17` | ZeroTier `10.147.17.6` | `gkr.my.id` | Foto: **`https://foto.gkr.my.id/`**
* **Spesifikasi Server:** Armbian OS (Debian bookworm) Linux 6.12 pada peranti Amlogic S905x.
* **Web Backend:** CodeIgniter 4 (PHP 8.2+, MVC).
* **AI Microservice:** Python 3, FastAPI, PyTorch, FAISS (`http://127.0.0.1:5000` via daemon `ai_scanner.service`).
* **AI Trainer Engine:** Python 3 (`ai_sync.py` dipanggil via subprocess CLI mode `-u` dengan PyTorch ARM Thread Clamping `OMP_NUM_THREADS=1` dan cache home `writable/torch_cache/`).
* **Web Frontend:** Vue.js 3 (CDN), ApexCharts, Bootstrap 5.3 (Native Dark Mode), Cropper.js, FontAwesome 6, ReadableStream API.
* **Database:** MySQL / MariaDB (Tabel Utama: `gkr_cari`).

---

## 📜 Lisensi & Pengembang
Dikembangkan oleh **RND &copy; 2026** untuk ekosistem Mebel Gracia.
