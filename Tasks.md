# Tasks & Backlog: Doogle (gkr_my.id)

Dokumen ini berisi daftar tugas (*tasks*), perbaikan (*bug fixes*), dan peningkatan fitur (*enhancements*) yang dapat diimplementasikan di masa mendatang.

## 🎯 Prioritas Tinggi (High Priority)
- [ ] **Sistem Autentikasi (Login):**
  - Buat halaman `login.php`.
  - Proteksi akses ke `admin.php`, `crawl.php`, dan `reset_db.php` menggunakan Session.
  - Redirect pengguna tidak terotentikasi kembali ke `index.php` atau halaman login.
- [ ] **Keamanan API Backend:**
  - Tambahkan validasi dan sanitasi input pada endpoint API di `admin.php` (mencegah XSS pada judul dan deskripsi).
- [ ] **Optimasi Eksekusi Crawler:**
  - Tambahkan fitur *depth limit* (batas kedalaman klik tautan) pada script rekursif `followLinks()` di `crawl.php` untuk mencegah *infinity loop* pada situs yang besar.

## 🚀 Prioritas Menengah (Medium Priority)
- [ ] **Pemisahan File Logika (Refactoring):**
  - Pisahkan logika backend `admin.php?action=...` menjadi file tersendiri di dalam folder `api/` (misal: `api/admin_api.php`).
  - Pisahkan logika crawler `crawl.php?action=...` ke dalam controller terpisah (misal: `api/crawl_api.php`) agar antarmuka HTML lebih bersih.
- [ ] **Manajemen Error Gambar (Broken Links):**
  - Implementasikan script Cron Job otomatis (di server backend) atau fitur di panel admin untuk secara rutin memverifikasi apakah URL di tabel `cari_images` merespons dengan status `200 OK`. Jika tidak, secara otomatis set kolom `broken = 1`.
- [ ] **Fitur Recycle Bin (Trash):**
  - Tambahkan tab khusus di `admin.php` untuk menampilkan item yang terkena *Soft Delete* (`deleted_at IS NOT NULL`).
  - Sediakan tombol "Restore" (mengosongkan `deleted_at`) dan "Permanent Delete".

## 🛠️ Prioritas Rendah / Kosmetik (Low Priority)
- [ ] **Dark Mode:**
  - Tambahkan *toggle* saklar Tema Gelap (Dark Mode) di antarmuka publik (`index.php`, `cari.php`) maupun panel Admin.
- [ ] **Autocomplete Pencarian:**
  - Integrasikan saran pencarian *real-time* (dropdown autocomplete) di bawah kolom input pada saat pengguna mengetik kata kunci.
- [ ] **Ekspor Data:**
  - Tambahkan tombol di `admin.php` untuk mengekspor data situs/gambar ke format `.csv` atau Excel.
