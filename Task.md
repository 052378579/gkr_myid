# Tasks & Backlog: Mesin Pencari Gracia (gkr.my.id)

Dokumen ini berisi daftar tugas (*tasks*), perbaikan (*bug fixes*), dan peningkatan fitur (*enhancements*) berdasarkan standar pengembangan menggunakan ekosistem campuran: **CodeIgniter 4**, **Vue.js 3**, **FastAPI (Python)**, dan **PyTorch AI**.

## ✅ Tugas Terselesaikan (Completed)

**Tahap I: Penguatan Ekosistem Web (CodeIgniter)**
- `[x]` **Manajemen Situs (Admin):** Memigrasikan fungsi Edit Situs dari *SweetAlert* murni ke UI Native Bootstrap Modal 2-Kolom dengan reaktivitas penuh.
- `[x]` **Database Setup (Kata Kunci):** Mengimplementasikan entitas baru tabel `gkr_material` (Kata kunci material & warna) beserta model dan proses prapengisian (*seeding*) mandiri.
- `[x]` **Role-Based Access Control:** Merancang `SuperAdminFilter` di `app/Filters` yang mencegat rute `/admin/*` hanya kepada pengguna dengan tingkatan *id_user = 1*.
- `[x]` **Perbaikan Bug Cache:** Menyertakan versi *timestamp* `?v=<?= time() ?>` pada pemanggilan aset JS di *views* publik dan admin agar peramban selalu mengambil versi termutakhir secara instan.
- `[x]` **Penyempurnaan UI Frontend:** Menyuntikkan fungsionalitas UI seperti ikon profil berbentuk *Dropdown Menu* interaktif, dan widget kalender beranda yang dinamis.

**Tahap II: Evolusi Kecerdasan Buatan (Pencarian Gambar)**
- `[x]` **Database Migration:** Menambahkan struktur migrasi untuk menunjang kebutuhan pelacakan rekam jejak gambar-gambar visual produk AI.
- `[x]` **Layanan Mikro AI (Python Backend):** Menulis infrastruktur FastAPI, mengimpor model *MobileNetV3-Small*, meramu skrip pelatih indeks matriks dimensi *FAISS* `build_index_new.py`, serta merekayasa REST API pencarian gambar kemiripan (`/scan`).
- `[x]` **CodeIgniter 4 API Proxy:** Mengintegrasikan `app/Controllers/Search.php` agar dapat memparsing hasil vektor ke dalam format galeri yang dikenali UI.
- `[x]` **UI Galeri Pencarian Visual:** Menyematkan ikon unggah kamera interaktif, membuat menu tab **New** (Pencarian Gambar AI), dan mendesain ubin bata presentasi visual (*Masonry Grid*) yang membuang teks analitis panjang menjadi teks elegan ("Kecocokan visual").
- `[x]` **Zero-Footprint Image Search:** Mengimplementasikan penghapusan otomatis (*auto-cleanup*) gambar unggahan pengguna segera setelah dikirim ke FastAPI, memastikan penyimpanan *server* selalu bersih dan efisien.

**Tahap III: Otomatisasi Jaringan Neural**
- `[x]` **AI Trainer Engine (Web UI):** Merombak total skema sinkronisasi memori pelatih AI. Mengubah terminal primitif SSH (*root@budi*) menjadi panel web ber-UI layar terminal hitam (`/crawl/ai`) yang modern dan interaktif.
- `[x]` **Bypass Timeout & Asynchronous Streaming:** Menghancurkan batas *PHP Timeout 30s* dan batasan *Cloudflare/Nginx*, menyetel `set_time_limit(0)`, mengeksekusi parameter Python *unbuffered* (`-u`), dan menangkap HTTP *ReadableStream* untuk menampilkan proses log ke layar secara *real-time*.
- `[x]` **No-FG Indexing (AI Indexing Bebas Hambatan):** Mengubah *regular expression* pada pelatih AI agar sanggup mengindeks semua gambar tak peduli apakah failnya memiliki nama berformat khusus (`FG-XXXX`) atau format mentah biasa (`IMG_1234`). Total sinkronisasi melonjak 20x lipat (Rasio 100%).

## 🎯 Prioritas Tinggi (High Priority)
- `[ ]` **Keamanan Server (Pendesak):**
  - Pindahkan atau hapus file *dump* database yatim (`gkr_myid.sql`, `cari.sql`) dari *root* direktori web `/var/www/gkr_myid` ke direktori *backup* yang aman untuk mencegah risiko akses publik.
- `[ ]` **Penyempurnaan Arsitektur RESTful API (Web):**
  - Pastikan semua *Controller* API merespons dengan JSON standar yang memuat status, pesan, dan data.
- `[ ]` **Standardisasi Template Layout:**
  - Pastikan semua *View* mengimplementasikan kerangka dari `app/Views/layout`. Tidak boleh ada halaman yang menggunakan struktur HTML mandiri di luar *layout* utama.
- `[ ]` **Konfigurasi Environment (Dev/Prod):**
  - Buat pengaturan base URL yang dinamis pada file `.env` CodeIgniter 4 agar otomatis menyesuaikan dengan environment.
- `[ ]` **Konsistensi Bahasa Indonesia:**
  - Lakukan *code review* menyeluruh pada Models, Views, dan Controllers untuk memastikan semua variabel, metode, dan komentar murni menggunakan Bahasa Indonesia.

## 🚀 Prioritas Menengah (Medium Priority)
- `[ ]` **Manajemen Kata Kunci (Tabel Material):**
  - Buat halaman CRUD tersendiri di panel Admin untuk mengatur ketersediaan opsi warna dan material pada tabel `gkr_material`. Saat ini tabel tersebut dimigrasi namun belum bisa di-edit dari panel Admin.
- `[ ]` **Manajemen Error Gambar (Broken Links):**
  - Implementasikan skrip otomatis untuk memverifikasi tautan gambar dan mengubah statusnya jika terdeteksi rusak (404/500).
- `[ ]` **Fitur Recycle Bin (Trash) di Panel Admin:**
  - Manfaatkan *Soft Delete* (Model CI4) untuk menampilkan daftar item terhapus dan fitur pemulihan (*Restore*).

## 🛠️ Prioritas Rendah / Kosmetik (Low Priority)
- `[ ]` **Dark Mode (Tema Gelap):**
  - Implementasikan tema gelap memanfaatkan variabel CSS dari Bootstrap 5.3.
- `[ ]` **Autocomplete Pencarian Teks:**
  - Integrasikan sistem saran pencarian *real-time* yang terhubung ke endpoint RESTful API.
