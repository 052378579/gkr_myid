# Mesin Pencari Gracia

**Mesin Pencari Gracia** (gkr.my.id) adalah Search Engine mutakhir yang direkayasa menggunakan arsitektur **Models, Views, Controllers (MVC)** melalui kerangka kerja **CodeIgniter 4**, diperkuat dengan antarmuka reaktif **Vue.js 3 (CDN)** dan sistem tata letak modern **Bootstrap 5.3**. 

Sistem ini difokuskan untuk mengindeks, menelusuri, dan merepresentasikan tautan situs serta galeri visual dengan spesialisasi **Katalog Furniture**. Aplikasi dilengkapi dengan *bot crawler* komprehensif yang memindai baik direktori lokal (seperti `/var/www/FOTO`) maupun URL eksternal, dan merender hasilnya dengan sangat cepat dan intuitif berkat pendekatan **RESTful API**.

## 🚀 Fitur Unggulan
- **Mesin Pencari & Crawler Real-Time:** 
  Crawler cerdas yang beroperasi secara asinkron. Mampu memindai data lokal dan internet, dengan progres penelusuran (*live stream log*) yang dialirkan seketika ke penjelajah web melalui *Fetch API ReadableStream*.
- **Desain UI/UX Modern & Reaktif:** 
  Tampilan pencarian bersih bergaya minimalis. Hasil gambar disuguhkan melalui struktur *Masonry Grid* dinamis dan *lightbox* (*Fancybox*). Elemen interaktif pada dasbor dipoles menggunakan pendekatan *Glassmorphism* dan sudut-sudut antarmuka yang lembut (*rounded*).
- **Arsitektur RESTful API & Soft Delete:** 
  Seluruh manipulasi data (CRUD) antara *frontend* (Vue.js) dan *backend* (CodeIgniter) dioperasikan penuh melalui metode RESTful API yang konsisten (GET, POST, PUT, DELETE), serta diproteksi secara aman. Penghapusan data dijaga menggunakan mekanisme *Soft Delete* otomatis dari CodeIgniter 4.
- **Konsistensi Layout Tunggal:** 
  Sistem dipastikan konsisten dalam merender setiap halamannya melalui satu kerangka pusat (*Template Layout*) di direktori `app/Views/layout`.
- **Ekosistem Penamaan Berbahasa Indonesia:** 
  Basis kode yang sangat terstruktur karena seluruh penamaan kelas, fungsi, variabel, model, kontroler, hingga komentar diatur 100% menggunakan Bahasa Indonesia.

## 📁 Struktur Inti Arsitektur MVC
- **`app/Controllers/`**: Menangani seluruh routing HTTP, pengalihan otentikasi, dan memfasilitasi titik panggil (*endpoint*) RESTful API berformat JSON.
- **`app/Models/`**: Berinteraksi dengan pangkalan data MySQL/MariaDB menggunakan Query Builder CI4 (dilengkapi manajemen *Soft Delete* dan proteksi bawaan).
- **`app/Views/`**: Menampung komponen visual (*frontend*). Diinjeksi ke dalam master layout `app/Views/layout` dan direaktivasi melalui tag `<script>` Vue.js.
- **`app/Libraries/`**: Memuat modul utilitas di luar domain MVC/HTTP (seperti `CrawlerLib`, `DomDocumentParser`, dan pemoles URL).

## 🛠️ Stack Teknologi
- **Backend:** PHP 8.2+ dengan framework **CodeIgniter 4** (MVC murni & Filter Autentikasi).
- **Frontend:** **Vue.js 3** (diintegrasikan via CDN), tata ruang utilitas **Bootstrap 5.3**, interaksi asinkron via **Fetch API** (tanpa jQuery untuk AJAX).
- **Database:** MySQL/MariaDB.
- **Topologi:** Terbagi secara tegas atas lingkungan Pengembangan (*Dev* di `10.147.17.40`) dan Produksi (*Prod* di `10.147.17.60`).
- **Penyajian Aset Lokal:** Memetakan path logis sumber daya berat ke `https://foto.gkr.my.id`.

## 🔗 Rute Utama & Endpoint
Pengelolaan halaman dan rute dipetakan ketat, beberapa yang paling krusial meliputi:
- `/` - Halaman Muka Beranda.
- `/cari` - Penampil Hasil Pencarian.
- `/admin` - Dasbor Manajemen Data Situs dan Gambar.
- `/crawl` - Monitor Eksekusi Mesin Penjelajah.
- `/versi` - Penampil Riwayat Pembaruan Rilis Publik.
- *API RESTful* tersebar untuk menjembatani asinkronisasi (Lihat [Tautan.md](Tautan.md) untuk struktur penuh).

## 🚫 Konvensi Berkas yang Diabaikan (Gitignore)
Agar keamanan dan integritas proyek terjaga saat memproses rilis melalui repositori:
- `/vendor` - Diunduh otomatis melalui `composer install`.
- `/writable` - Bersifat dinamis dan unik per *server* (log, cache, sesi).
- `.env` - Menyimpan rahasia sistem dan profil lingkungan yang tidak boleh dibocorkan.
