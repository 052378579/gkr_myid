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

**Tahap IV: Harmonisasi AI & Web Crawler**
- `[x]` **Multi-Angle Regex Harmonization:** Menyinkronkan algoritma Python (`build_index_new.py`) dan PHP (`CrawlerLib.php`) untuk mendeteksi dan merangkum seluruh varian sudut foto (seperti `-B`, `-C`, `-D`, `-E`, `_depan`, `_samping`) menjadi sebuah entitas produk identitas tunggal yang bersih.
- `[x]` **Global Folder Sensus & Integrasi /WEB:** Menganalisis puluhan ribu nama file secara rekursif menggunakan *PowerShell Automation*, menemukan pola *typo* spasi ekstra, serta secara resmi menyuntikkan dukungan direktori `/WEB` ke dalam daftar pantauan otomatis Web Crawler.

**Tahap V: Pemolesan & Manajemen Dasbor Admin**
- `[x]` **Manajemen Karyawan:** Menyelaraskan layout `admin/karyawan` ke Dasbor Admin, membuang navbar statis lama, menambahkannya ke *Sidebar*, serta merombak input Divisi menjadi *dropdown* kaku (Marketing, Produksi 1, Produksi 2, Produksi 4, RND) untuk meminimalisasi *typo*.
- `[x]` **Sistem Audit Log:** Memperbaiki sistem pencatatan alamat IP agar tidak terpaku pada `127.0.0.1` dengan mengonfigurasi struktur *array asosiatif* `$proxyIPs` pada CI4 v4.3+ sehingga sistem bisa membaca *header X-Forwarded-For* dari peladen proksi.
- `[x]` **Log UI/UX:** Mempercantik antarmuka tabel 'Log Cari' dan 'Log User' dengan penyelarasan urutan kolom, konversi format waktu (dd/mm/YYYY HH:MM:ss), penyisipan fitur *Auto-Reload* 5 Menit dengan bilah progresi (*Progress Bar*) bertema Gracia, serta menyederhanakan tampilan *User Agent*.
- `[x]` **Integrasi Navigasi & Akses Super Admin:** Menyisipkan berbagai menu admin secara dinamis (Log, Karyawan, AI Trainer) ke *Sidebar*, dan merombak *Dropdown* profil agar menampilkan tautan Rahasia 'Admin' secara eksklusif bagi pemegang kunci *Super Admin* (`id_user == 1`).
- `[x]` **Refaktor Rute AI:** Memindahkan rute mesin pelatih AI dari `/crawl/ai` menjadi `/admin/ai` untuk keseragaman arsitektur di ruang administratif.

**Tahap VI: Optimalisasi Lingkungan & Penyimpanan Meta (Zero-DB)**
- `[x]` **Migrasi Riwayat Rilis (Changelog):** Membongkar tabel `gkr_versi` dari MySQL dan memigrasikannya secara utuh ke sistem Flat-file JSON (`/public/versi.json`) demi mereduksi beban *database hit*.
- `[x]` **Pemusnahan `VersiModel` & Relikui Kode:** Menghapus `VersiModel.php` selamanya dan membersihkan sisa pemanggilannya di seluruh *Controller* (`Beranda`, `Search`, `Auth`, `AdminController`).
- `[x]` **Auto-Versioning Berbasis Kalender:** Merancang algoritma pintar yang otomatis mencetak versi dinamis (`0.{Bulan}.{Tanggal}`) ke dalam JSON setiap kali riwayat pembaruan disimpan oleh Administrator.
- `[x]` **Auto-Detect Environment (Keamanan Dinamis):** Merombak `public/index.php` untuk menangkap variabel `HTTP_HOST`. Akses via IP Lokal/ZeroTier dialihkan ke *Development*, sedangkan *bot* IP Publik dan Akses Domain terlempar/terkunci pada *Production*.
- `[x]` **Pembersihan Direktori Kosong:** Memusnahkan folder yatim piatu seperti `tests` dan `public/3d` untuk menjaga ruang kerja tetap efisien dan higienis.

## 🎯 Prioritas Tinggi (High Priority)
- `[ ]` **Keamanan Server (Pendesak):**
  - Pindahkan atau hapus file *dump* database yatim (`gkr_myid.sql`, `cari.sql`) dari *root* direktori web `/var/www/gkr_myid` ke direktori *backup* yang aman untuk mencegah risiko akses publik.
- `[ ]` **Penyempurnaan Arsitektur RESTful API (Web):**
  - Pastikan semua *Controller* API merespons dengan JSON standar yang memuat status, pesan, dan data.
- `[ ]` **Standardisasi Template Layout:**
  - Pastikan semua *View* mengimplementasikan kerangka dari `app/Views/layout`. Tidak boleh ada halaman yang menggunakan struktur HTML mandiri di luar *layout* utama.
- `[ ]` **Konsistensi Bahasa Indonesia:**
  - Lakukan *code review* menyeluruh pada Models, Views, dan Controllers untuk memastikan semua variabel, metode, dan komentar murni menggunakan Bahasa Indonesia.

## 🚀 Prioritas Menengah / Peta Jalan Masa Depan (Medium Priority & Future Roadmap)
- `[ ]` **Visual Recommendation (Produk Serupa):**
  - Mengembangkan fitur yang menampilkan 5 produk terdekat berdasarkan jarak Cosine Similarity (FAISS) di setiap halaman detail produk, memungkinkan *upselling* secara otomatis tanpa melabeli kategori secara manual.
- `[ ]` **Smart Auto-Crop (YOLOv8 Nano):**
  - Menyuntikkan model pelacak objek ringan sebelum `MobileNetV3` bekerja untuk memotong gambar ruangan pelanggan, guna mencegah latar belakang (seperti TV atau tembok) mengacaukan hasil pencarian.
- `[ ]` **Swatch Matcher (Pencocok Dekorasi):**
  - Fitur bagi pengguna untuk mengunggah lantai kayu atau gorden mereka agar dicarikan furnitur/kain bantalan (swatch) dengan warna dan tekstur visual paling serasi.
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
