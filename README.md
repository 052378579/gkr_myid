# Mesin Pencari Gracia
**Mesin Pencari Gracia** adalah Search Engine yang dikembangkan dengan **CodeIgniter 4**,**Vue.js 3** dan **Bootstrap 5.3**. Fokus untuk mengindeks, menemukan, dan menampilkan tautan serta gambar dan **Katalog Furniture**. Aplikasi ini memiliki crawler yang mampu memindai direktori maupun menelusuri tautan referensi untuk menyajikan hasil pencarian secara cepat dan intuitif.


## 📁 Struktur Proyek & Panduan Repositori
Proyek ini mengimplementasikan pola arsitektur MVC (Model-View-Controller) khas CodeIgniter 4:
- **`app/Controllers/`**: Mengatur alur dan logika endpoint utama, termasuk API, Autentikasi, Profil pengguna, Manajeman Rilis, dan Crawler.
- **`app/Models/`**: Menangani interaksi database menggunakan Query Builder CI4 (mendukung fitur Soft Delete otomatis).
- **`app/Views/`**: Berisi antarmuka pengguna yang dirender dengan Bootstrap 5.3 dan diaktifkan dengan inisialisasi Vue.js.
- **`app/Libraries/`**: Memuat modul fungsional independen seperti `CrawlerLib`, `DomDocumentParser`, dan `UrlRewriter`.


## 🚀 Fitur Utama
- **Mesin Pencari & Crawler:** Crawler bot yang dapat membaca dari direktori gambar lokal (`/var/www/FOTO`) serta mengekstrak metadata dari situs eksternal secara rekursif. Menampilkan output proses log *real-time* ke browser web dengan mekanisme streaming data.
- **Antarmuka Pencarian & Galeri:** Antarmuka pencarian sentral minimalis yang mirip dengan Google dengan navigasi Tab (Semua / Gambar). Penampil hasil gambar didukung oleh tata letak dinamis *Masonry grid layout* dan pratinjau mode *lightbox* dari *Fancybox*.
- **Panel Admin:** Operasi CRUD penuh berbasis tabel yang modern dengan efek *Glassmorphism*, mendukung *soft delete* untuk tautaan dan gambar, dan pengunggahan gambar dengan *modal box*.
- **Doodle Tematik:** Memungkinkan penjadwalan tayangan logo kustom pencarian secara dinamis berdasarkan periode waktu tanggal tertentu.
- **Sistem Rilis / Changelog:** Halaman pembaruan Changelog ke publik, serta manajemen kelola datanya dapat diakses melalui antarmuka Admin dengan elegan tanpa harus mengubah file JSON manual.


## 🛠️ Stack Teknologi & Panduan Developer
- **Backend:** CodeIgniter 4 (PHP 8.2+) dengan arsitektur MVC.
- **Frontend:** Vue.js 3 (via CDN) & Bootstrap 5.3. Interaksi data asinkron antar komponen web memprioritaskan fungsi native Vanilla Javascript (*Fetch API* murni).
- **Database:** MySQL/MariaDB.
- **Baris Kode IDN**: Baris Kode menggunakan Bahasa Indonesia untuk seluruh penulisan kode internal (nama variabel, fungsi, dan komentar), dengan mematuhi konvensi PSR-12. Mengutamakan pemanfaatan standar bawaan framework CI4 seperti *Models*, *Filters*, *Responses*, dll.


## 🔗 Daftar Tautan & Endpoint
Aplikasi melayani beberapa _routing_ HTTP yang tersebar ke beberapa halaman dan API:
- `/` - Halaman Muka Beranda.
- `/cari` - Halaman Hasil Pencarian.
- `/admin` - Dasbor Utama Panel Admin.
- `/crawl` - Antarmuka Streaming Crawler interaktif.
- `/versi` - Halaman Publik Rilis (Changelog).
- Berbagai *API endpoints* RESTful

## 🚫 Direktori & Berkas yang Tidak Disertakan
Demi keamanan dan pengelolaan repositori, beberapa direktori dan berkas sengaja diabaikan (sesuai aturan `.gitignore`):
- `/vendor` - Dependensi dan pustaka pihak ketiga dari Composer.
- `/writable` - Berkas cache, log, session, dan data *upload*.
- `.env` - Konfigurasi variabel *environment* dan kredensial akses.
- **Dokumentasi Internal Developer:** `Memory.md`, `PRD.md`, `Skills.md`, `struktur_folder.md`, `StyleGuide.md`, `Tasks.md`, `Tautan.md`.
