# Tasks & Backlog: Mesin Pencari Gracia (gkr.my.id)

Dokumen ini berisi daftar tugas (*tasks*), perbaikan (*bug fixes*), dan peningkatan fitur (*enhancements*) berbasis **CodeIgniter 4**, **Vue.js 3**, **ApexCharts**, **FastAPI (Python)**, dan **PyTorch AI**.

## ✅ Tugas Terselesaikan (Completed)

**Tahap I: Penguatan Ekosistem Web (CodeIgniter)**
- `[x]` **Manajemen Situs (Admin):** Memigrasikan fungsi Edit Situs dari *SweetAlert* murni ke UI Native Bootstrap Modal 2-Kolom dengan reaktivitas penuh.
- `[x]` **Database Setup (Kata Kunci):** Mengimplementasikan entitas baru tabel `gkr_material` (Kata kunci material & warna) beserta model dan proses prapengisian (*seeding*) mandiri.
- `[x]` **Role-Based Access Control:** Merancang `SuperAdminFilter` di `app/Filters` yang mencegat rute `/admin/*` hanya kepada pengguna dengan tingkatan *id_user = 1*.
- `[x]` **Perbaikan Bug Cache:** Menyertakan versi *timestamp* `?v=<?= time() ?>` pada pemanggilan aset JS di *views* publik dan admin.

**Tahap II: Evolusi Kecerdasan Buatan (Pencarian Gambar)**
- `[x]` **Database Migration:** Menambahkan struktur migrasi untuk menunjang kebutuhan pelacakan rekam jejak gambar-gambar visual produk AI.
- `[x]` **Layanan Mikro AI (Python Backend):** Infrastruktur FastAPI, MobileNetV3-Small, FAISS `IndexFlatIP` ($k=15$), dan endpoint `/scan`.
- `[x]` **CodeIgniter 4 API Proxy:** Mengintegrasikan `app/Controllers/Search.php` dan `search_results.php` untuk Masonry Grid AI.
- `[x]` **Zero-Footprint Image Search:** Auto-cleanup gambar unggahan pengguna (`unlink()`).

**Tahap III: Refaktorisasi Database Tunggal `gkr_cari` & Kolom Bahasa Indonesia**
- `[x]` **Physical Single Table Migration:** Menggabungkan tabel lama `cari_sites` dan `cari_images` menjadi satu tabel murni **`gkr_cari`**.
- `[x]` **Penerjemahan Kolom Bahasa Indonesia:** Mengubah nama kolom menjadi `judul`, `alt`, `deskripsi`, `url`, `imageUrl`, `siteUrl`, `kata_kunci`, `klik`, `rusak`.
- `[x]` **Penghapusan Kolom `tipe`:** Menentukan entitas murni dari `imageUrl IS NOT NULL` tanpa kolom discriminator `tipe`.
- `[x]` **Optimalisasi Perayap `CrawlerLib.php`:** Mengubah pengindeksan lokal `/var/www/FOTO` agar 1 produk foto lokal tersimpan murni sebagai **1 Baris Utuh** (menampung `url` dan `imageUrl` sekaligus), menghemat 50% kapasitas database.

**Tahap IV: Dashboard KPI Admin ApexCharts & Reposisi UI `/admin/cari`**
- `[x]` **Rute Utama Admin (`/admin` & `/admin/dashboard`):** Menjadikan `/admin/dashboard` landing page admin dengan 4 Card Summary KPI (Termasuk KPI Total Users Karyawan Terdaftar) dan **ApexCharts Bar Chart (Custom Data Labels)** menampilkan Top 10 barang sering dicari (`klik DESC`).
- `[x]` **Pembaruan Halaman Engine (`/admin/cari`):** Menyajikan tabel `gkr_cari` tanpa TAB navigasi terpisah, judul "Manajemen Mesin Pencari", reposisi Select Box `Tampilkan: [10 v]` di Card Header sebelah kiri search box, serta thumbnail foto kolom Gambar dibungkus tautan aktif.
- `[x]` **Perbaikan PWA Service Worker (`public/sw.js`):** Mengecualikan request non-GET (`POST`) dan rute `/crawler/`, `/api/`, `/admin/` agar streaming log perayap `/admin/crawl` dan Reset DB berjalan 100% lancar.

**Tahap V: Sentralisasi Resolusi Dynamic Foto URL Host (DEV vs PROD)**
- `[x]` **Dynamic Host Resolver (`getFotoUrlPrefix()`):** Mengimplementasikan pembantu terpusat di `Search.php` dan `GraciaApi.php`:
  - DEV (`192.168.1.4`, `10.147.17.40`, `gkr.budi.biz.id`) -> **`https://foto.budi.biz.id/`**
  - PROD (`192.168.1.17`, `10.147.17.60`, `gkr.my.id`) -> **`https://foto.gkr.my.id/`**

**Tahap VI: Deployment DEV & Production (PROD)**
- `[x]` **Deploy GitHub DEV:** Berhasil melakukan commit dan push cabang `dev` (`253891b`) ke GitHub.
- `[x]` **Deploy Server PROD (`root@gracia`):** Berhasil melakukan `git pull origin dev` dan eksekusi skrip migrasi bersih DDL Opsi B pada server PROD (`gkr.my.id`).

**Tahap VII: Integrasi Kalender Navbar, Telegram Auto-Bind, & Format Cronjob Notifikasi**
- `[x]` **Kalender Dropdown Interaktif Navbar (`admin_layout.php` & `calendar.js`):** Mengganti teks tanggal statis menjadi widget kalender melayang interaktif dengan sorotan **lingkaran padat Biru Dongker Gracia (`#2B3385`)** dan teks putih pada tanggal aktif.
- `[x]` **Penyesuaian Stacking Context (`beranda.php`):** Menetapkan `z-index: 1060 !important;` pada kalender dropdown agar melayang sempurna di atas seluruh elemen beranda tanpa bentrokan tombol kamera (`z-index: 3`).
- `[x]` **Clean Search UI (`beranda.php` & `index.css`):** Menghapus pendaran garis warna pelangi pada tombol **Cari**, mengembalikannya ke gaya tombol bersih Bootstrap `rounded-pill` (`bg-body-tertiary`, `height: 42px`, `min-width: 120px`).
- `[x]` **Pembersihan Direktori Scan `SAMPLE GRACIA`:** Menghapus `SAMPLE GRACIA` dari `ai_index.py` dan menambahkan *Exclusion Guard* di `CrawlerLib.php` (hanya memindai `BUYER`, `GRACIA`, `SWATCHES`, `WEB`).
- `[x]` **Pendaftaran Otomatis (*Auto-Bind*) Telegram Chatbot (`ChatBotApi.php`):** Mengimplementasikan fitur auto-bind mandiri karyawan via `/start 08...` atau `/daftar 08...` yang otomatis memperbarui `telegram_chat_id` di `gkr_users` (Skenario 1 Keamanan terkunci untuk nomor HP terdaftar oleh Admin/HRD).
- `[x]` **Penyelarasan Cronjob & Format Notifikasi Telegram Ringkas:**
  - Auto Crawler: `0 18 * * * cd /var/www/gkr_myid && php spark crawl:run /var/www/FOTO` -> Notifikasi Telegram `Server: DEV/PROD`, `💾 X Item Baru Ditambahkan`.
  - AI Trainer: `1 0 * * * cd /var/www/gkr_myid/python_services && /mnt/sdcard/ai-scanner/env-ai/bin/python3 ai_index.py && cp produk.index /mnt/sdcard/ai-scanner/ && cp mapping.json /mnt/sdcard/ai-scanner/ && systemctl restart ai_scanner.service` -> Notifikasi Telegram `Server: DEV/PROD`, `💾 Berkas produk.index & mapping.json Berhasil Diperbarui`.

**Tahap VIII: Evolusi Algoritma Pencarian Presisi & Pencarian Suara Bahasa Indonesia**
- `[x]` **Modul Voice Search Native (`voice_search.js`):** Integrasi Web Speech API `id-ID` dengan modal animasi pendaran gelombang suara reaktif & redirect otomatis ke Tab Gambar (`/cari?q=...&type=images`).
- `[x]` **Normalisasi Simbol URL:** Pembersihan otomatis simbol pemisah (`+`, `-`, `_`, `,`) pada `Search.php` menjadi spasi netral.
- `[x]` **Primary Brand/Series Anchor Search:** Deteksi token merk spesifik (`bonanza`) yang mewajibkan produk memuat kata merk utama, 100% mengeliminasi *Riazor Rectangular Coffee Table*, *Tamika Coffee Table*, *Alandra Coffee Table*, dan *Side Table Type 02*.
- `[x]` **Comprehensive Category Antonym Exclusion:** Matriks konflik tiga arah (Table vs Chair vs Lamp) yang 100% mengeliminasi *Leora Dinning Chair* (kursi) dan *Mida Table Lamp Organic Motif* (lampu meja) pada pencarian `dinning table`.
- `[x]` **Multi-Tier Relevance Scoring:** Perhitungan skor SQL `ORDER BY` bertingkat (Skor 100 untuk exact phrase, Skor 80 untuk all-tokens match, dan pengurutan popularitas `klik DESC`).
- `[x]` **Proteksi Frontend UI/UX (`beranda.php` & `index.js`):** Integrasi `v-cloak` dan `@focus="handleFocus"` untuk mengeliminasi bayangan sintaks mentah Vue 3 `{{ item }}` dan terbukanya autocomplete kosong saat fokus.

**Tahap IX: Refaktorisasi Ekstraksi Assets View CSS & JS ke `public/css` & `public/js`**
- `[x]` **Sentralisasi `window.AppConfig`:** Pembuatan objek konfigurasi global terpusat pada header layout utama `main.php` dan `admin_layout.php` (`baseUrl`, `csrfToken`, `versiUrl`, `swUrl`), menghapus duplikasi `<script>` variabel di 12 berkas View.
- `[x]` **Ekstraksi CSS Modul Fisik (`public/css/`):** Pembuatan `admin.css` (Panel Admin), `awan_kata.css` (Animations WordCloud), `auth.css` (Halaman Login & Daftar), dan `index.css` (Aturan global `v-cloak`).
- `[x]` **Ekstraksi JS Modul Fisik (`public/js/`):** Pembuatan `daftar.js` (Formulir Pendaftaran) dan `awan_kata.js` (WordCloud2 & Auto-Refresh Timer).
- `[x]` **Clean Views Sterilization:** Mengeliminasi seluruh tag `<style>` dan `<script>` inline di `app/Views` serta menstandardisasi pemanggilan aset dengan versioning `?v=<?= time() ?>`.

**Tahap X: Redesain Presisi & Penyelarasan Layout Halaman `/cari` (Desktop Responsive & Tema Gracia `#2B3385`)**
- `[x]` **Header Alignment:** Logo GRACIA di tepi paling kiri (`24px`), menu header kanan (`Sabtu, 01/08/2026`, tombol bulat App Launcher Grid `[:::]`, dan Profile Avatar `[👤]`) di tepi paling kanan (`24px`).
- `[x]` **Flush Left Vertical Alignment (10% Margin Kiri):** Tab `[Semua]`, `Ditemukan {jumlah} hasil`, dan garis aksen 3px Biru Dongker `.site-result-item` berderet 100% rata lurus vertikal.
- `[x]` **Harmonisasi Adaptif Dark/Light Mode:** CSS Override `[data-bs-theme="dark"]` pada `index.css` (Latar item aktif `rgba(138,180,248,0.12)`, judul `#8ab4f8`, URL `#81c995`, garis pemisah `#3c4043`, action pills `#303134`).
- `[x]` **Knowledge Card Borderless & Garis Pemisah Abu-Abu:** Kartu 35% kanan berlatar transparan tanpa border/shadow luar, dipisahkan oleh garis abu-abu vertikal (`border-left: 1px solid #ebebeb; padding-left: 24px;`) dari kolom 65% hasil kiri.
- `[x]` **Default Fallback Foto Putih Polos (`#ffffff`):** Mengganti gambar default fallback (Logo 'G' / `favicon.ico`) menjadi SVG Putih Polos murni (`#ffffff`) jika produk tidak memiliki foto.
- `[x]` **Pagination Logo `c a r i` Vector Typography Baseline:** Pagination `c a r i` menggunakan tipografi vektor HTML/CSS Google-Grade (`Outfit`/`Product Sans`) yang 100% Rata Lurus Horizontal Baseline (`align-items: baseline`) dan Rata Tengah (*Centered*) di Tab Semua (`type=sites`) dan Tab Gambar (`type=images`).
- `[x]` **Pemetaan Produksi Strict:** `formatResultUrls()` menghitung `$row['produksi']` secara ketat berdasarkan awalan `kode_bom` (`FG-1...` -> `UNIT 1`, `FG-2...` -> `UNIT 2`, `FG-4...` -> `UNIT 4`).