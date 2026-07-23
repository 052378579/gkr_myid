# Mesin Pencari Visual Gracia

**Mesin Pencari Gracia** (gkr.my.id) adalah pencarian cerdas yang direkayasa memadukan dua arsitektur **CodeIgniter 4 (PHP)** dan **Python Microservice (FastAPI + PyTorch + FAISS)** sebagai inti kecerdasan buatan (*Artificial Intelligence*).

Sistem Hibrida ini difokuskan untuk mengindeks, menelusuri, dan merepresentasikan tautan situs serta mendeduksi kemiripan Galeri Visual dengan spesialisasi **Foto Furniture**. Aplikasi dilengkapi dengan *Bot Crawler* serta **Pelatih AI** untuk memindai ribuan gambar lokal, merender hasilnya dengan cepat dan intuitif dengan pendekatan *Continuous HTTP Streaming* dan **RESTful API**.

## 🚀 Fitur Unggulan
- **Agregasi Identitas Visual Multi-Sudut (AI Harmonization):**
  Sistem kebal terhadap variasi nama file gambar dari berbagai sudut kamera (seperti varian `-B`, `-C`, `-D`, `-E`, `_depan`, `_samping`, dan `_perspektif`). PHP dan Python berbagi Otak *Regex* yang harmonis untuk memotong kode teknis tersebut, menyatukan seluruh sudut menjadi satu identitas produk tunggal secara otomatis.
- **Mesin Pencari Visual (Image-to-Image Search):** 
  Kemampuan mengunggah gambar kursi atau tekstur kain (*swatches*) untuk dicocokkan secara otomatis dengan ribuan katalog perusahaan menggunakan ekstrasi dimensi vektor 576 (*MobileNetV3*) dan mesin kedekatan algoritme (*Cosine Similarity* FAISS).
- **Dasbor Pelatih AI & Crawler Real-Time:** 
  Mesin sinkronisasi cerdas yang beroperasi secara asinkron. Mampu memindai susunan direktori raksasa lokal (termasuk folder katalog `/WEB` terbaru) dan memproses *Machine Learning*, dengan progres penelusuran (log terminal hitam) yang dialirkan seketika ke penjelajah web melalui antarmuka *Fetch API ReadableStream* tanpa risiko *Timeout*.
- **Desain UI/UX Modern & Reaktif:** 
  Tampilan pencarian bersih bergaya minimalis. Hasil gambar disuguhkan melalui struktur *Masonry Grid* dinamis dan galeri "Kecocokan visual". Elemen interaktif pada dasbor dipoles menggunakan pendekatan *Glassmorphism* dan komponen **Native Modal Bootstrap 5.3** terintegrasi Vue (Bukan SweetAlert statis) untuk entri data formulir.
- **Arsitektur RESTful API & Layanan Mikro Python:** 
  Seluruh manipulasi data (CRUD) dioperasikan penuh melalui metode RESTful API yang konsisten antara *frontend* (Vue.js) dan *backend* (CodeIgniter). Sementara untuk operasi komputasi berat, PHP mendelegasikan tugas ke layanan mikro rahasia **FastAPI (Python)** di *localhost:5000*.
- **Sistem Keamanan & Otorisasi RBAC:** 
  Sistem diproteksi dengan otentikasi login serta filter keamanan presisi tinggi (*SuperAdminFilter*) di mana rute-rute sakral seperti `/admin/*` hanya dapat diterobos oleh sesi administrator tingkat puncak (`id_user = 1`).
- **Mekanisme Cache Busting & Dropdown Bertingkat:** 
  Aplikasi ini menggunakan teknik *Cache Busting* otomatis (`?v=time()`) untuk seluruh aset skrip kritikal, dan Dropdown Kaskade pintar untuk pengisian basis data.

- **Modul Audit Log & Manajemen Akses:**
  Sistem perlindungan ganda terintegrasi. Menampilkan jejak aktivitas pengguna (Log User) dan pencarian (Log Cari) yang dilengkapi detektor IP asli (menembus *Reverse Proxy*) dan *Progress Bar Auto-Reload*. Sistem manajemen karyawan juga diperkuat antarmuka modal tervalidasi *dropdown*.
- **Zero-DB Hit Changelog & Auto-Versioning:** 
  Sistem riwayat rilis (Changelog) murni beroperasi di atas *flat-file* statis JSON (`/public/versi.json`) untuk mereduksi beban *query* MySQL secara mutlak. Versi rilis otomatis dicetak oleh sistem menggunakan skema kalender dinamis (`0.{Bulan}.{Tanggal}`).
- **Auto-Detect Environment (Kunci Pengaman Mode):** 
  Sistem perisai di `public/index.php`. Sistem akan melunak dan berubah menjadi mode *Development* ketika diakses melalui jaringan LAN/ZeroTier, namun akan mengunci mati dirinya di mode *Production* bila mendeteksi ketukan lalu lintas dari IP Publik maupun domain internet.

## 📁 Struktur Inti Arsitektur MVC & Microservice
- **`app/Controllers/`**: Menangani seluruh routing HTTP UI, dan memfasilitasi titik panggil (*endpoint*) RESTful API. `Admin.php` bertindak sebagai sentral untuk seluruh kendali administratif.
- **`app/Models/`**: Berinteraksi dengan pangkalan data MySQL/MariaDB menggunakan Query Builder CI4 (dilengkapi manajemen *Soft Delete* dan Tabel Audit).
- **`app/Views/`**: Menampung komponen visual (*frontend*). Khusus area admin, seluruhnya wajib mewarisi kerangka utama di `layout/admin_layout.php`.
- **`app/Filters/`**: Tempat bernaungnya perisai lalu lintas web (`AuthFilter.php` & `SuperAdminFilter.php`).
- **`python_services/`**: Markas besar kecerdasan buatan. Berisi model infrastruktur *PyTorch* dan *FastAPI* (`main_new.py`, `build_index_new.py`).

## 🛠️ Stack Teknologi
*   **Infrastruktur Server:** Armbian OS (Debian bookworm) bertumpu pada perangkat keras Amlogic S905x (Aml.S905x).
*   **Web Backend:** PHP 8.2+ dengan framework CodeIgniter 4.
*   **AI Backend:** Python 3, FastAPI, PyTorch (MobileNetV3-Small), FAISS (Facebook AI Similarity Search).
*   **Web Frontend:** Vue.js 3 (CDN), tata ruang utilitas Bootstrap 5.3, interaksi *Continuous Streaming* via ReadableStream API.
*   **Database:** MySQL/MariaDB.
*   **Penyajian Aset Lokal:** Memetakan path logis sumber daya berat ke `https://foto.gkr.my.id`.

## 🔗 Rute Utama & Endpoint Ekosistem
*   `/` - Halaman Muka Beranda.
*   `/cari` - Penampil Hasil Pencarian (Terintegrasi Ikon Kamera AI).
*   `/admin` - Dasbor Manajemen Data Situs, Gambar, Karyawan, dan Log.
*   `/admin/crawl` - Monitor Eksekusi Mesin Penjelajah Tautan.
*   `/admin/ai` - Terminal Dasbor Pelatih AI (Live Streaming).
*   *Layanan Mikro Internal (FastAPI)* - Beroperasi eksklusif di `http://127.0.0.1:5000`. Membypass semua otorisasi Linux untuk memanipulasi direktori `/mnt/sdcard`. (Rincian teknis lengkap di `Tautan.md`).

## 🚫 Konvensi Berkas yang Diabaikan (Gitignore)
Agar keamanan dan kerahasiaan dapur proyek terjaga saat publikasi ke repositori:
*   `/vendor` - Diunduh otomatis melalui `composer install`.
*   `/writable` - Bersifat dinamis dan unik per *server* (log, cache, sesi).
*   `/python_services/__pycache__` - File binari temporer Python.
*   `.env` - Menyimpan rahasia sistem dan profil lingkungan.
*   `*.sql`, `*.psd` - Berkas cadangan mentah dan *dump* basis data berukuran gajah.
*   `*.md`, `*.txt` - Dokumen pedoman arsitektur rahasia internal tim (kecuali `README.md` muka ini) sengaja dikecualikan (*ignored*) dari panggung repositori publik.
