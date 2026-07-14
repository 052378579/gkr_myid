# Tasks & Backlog: Mesin Pencari Gracia (gkr.my.id)

Dokumen ini berisi daftar tugas (*tasks*), perbaikan (*bug fixes*), dan peningkatan fitur (*enhancements*) berdasarkan standar pengembangan menggunakan **CodeIgniter 4**, **Vue.js 3 (CDN)**, dan **Bootstrap 5.3**.

## ✅ Tugas Terselesaikan (Completed)
- `[x]` **Manajemen Situs (Admin):** Memigrasikan fungsi Edit Situs dari *SweetAlert* murni ke UI Native Bootstrap Modal 2-Kolom dengan reaktivitas penuh.
- `[x]` **Database Setup (Kata Kunci):** Mengimplementasikan entitas baru tabel `gkr_material` (Kata kunci material & warna) beserta model dan proses prapengisian (*seeding*) mandiri.
- `[x]` **Role-Based Access Control:** Merancang `SuperAdminFilter` di `app/Filters` yang mencegat rute `/admin/*` hanya kepada pengguna dengan tingkatan *id_user = 1*.
- `[x]` **Perbaikan Bug Cache:** Menyertakan versi *timestamp* `?v=<?= time() ?>` pada pemanggilan aset JS di *views* publik dan admin (termasuk *calendar* dan fitur panel lainnya) agar peramban selalu mengambil versi termutakhir secara instan tanpa tembolok (*cache*).
- `[x]` **Penyempurnaan UI Frontend:** Menyuntikkan fungsionalitas UI seperti ikon profil berbentuk *Dropdown Menu* interaktif, pewarnaan biru merah kalender beranda yang dinamis mengikuti perputaran pekan, dsb.

## 🎯 Prioritas Tinggi (High Priority)
- `[ ]` **Penyempurnaan Arsitektur RESTful API:**
  - Migrasi seluruh pengolahan data asinkron agar sepenuhnya mengikuti standar RESTful (menggunakan *method* GET, POST, PUT, DELETE yang konsisten).
  - Pastikan semua *Controller* API merespons dengan JSON standar yang memuat status, pesan, dan data.
- `[ ]` **Standardisasi Template Layout:**
  - Pastikan semua *View* mengimplementasikan kerangka dari `app/Views/layout`. Tidak boleh ada halaman yang menggunakan struktur HTML mandiri di luar *layout* utama.
- `[ ]` **Konfigurasi Environment (Dev/Prod):**
  - Buat pengaturan base URL yang dinamis pada file `.env` CodeIgniter 4 agar otomatis menyesuaikan dengan environment.
- `[ ]` **Konfigurasi CORS di API:**
  - Konfigurasikan header CORS agar backend RESTful API dapat merespons request dengan aman dari berbagai IP topologi.
- `[ ]` **Konsistensi Bahasa Indonesia:**
  - Lakukan *code review* menyeluruh pada Models, Views, dan Controllers untuk memastikan semua variabel, metode, dan komentar murni menggunakan Bahasa Indonesia.

## 🚀 Prioritas Menengah (Medium Priority)
- `[ ]` **Manajemen Kata Kunci (Tabel Material):**
  - Buat halaman CRUD tersendiri di panel Admin untuk mengatur ketersediaan opsi warna dan material pada tabel `gkr_material`. Saat ini tabel tersebut dimigrasi namun belum bisa di-edit dari panel Admin.
- `[ ]` **Optimasi & Pemetaan URL Crawler:**
  - Pastikan Crawler memetakan *path* dari direktori lokal `/var/www/FOTO/...` menjadi URL statis.
- `[ ]` **Manajemen Error Gambar (Broken Links):**
  - Implementasikan skrip otomatis untuk memverifikasi tautan gambar dan mengubah statusnya jika terdeteksi rusak (404/500).
- `[ ]` **Fitur Recycle Bin (Trash) di Panel Admin:**
  - Manfaatkan *Soft Delete* (Model CI4) untuk menampilkan daftar item terhapus dan fitur pemulihan (*Restore*).

## 🛠️ Prioritas Rendah / Kosmetik (Low Priority)
- `[ ]` **Peningkatan Vue.js 3 & Bootstrap 5.3:**
  - Pastikan transisi dan animasi menggunakan utilitas Bootstrap 5.3 dan *state management* lokal dengan Vue.js 3.
- `[ ]` **Dark Mode (Tema Gelap):**
  - Implementasikan tema gelap memanfaatkan variabel CSS dari Bootstrap 5.3.
- `[ ]` **Autocomplete Pencarian:**
  - Integrasikan sistem saran pencarian *real-time* yang terhubung ke endpoint RESTful API.
