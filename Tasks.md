# Tasks & Backlog: Mesin Pencari Gracia (gkr.my.id)

Dokumen ini berisi daftar tugas (*tasks*), perbaikan (*bug fixes*), dan peningkatan fitur (*enhancements*) yang dapat diimplementasikan di masa mendatang.

## 🎯 Prioritas Tinggi (High Priority)
- [ ] **Konfigurasi Environment (Dev/Prod):**
  - Buat pengaturan base URL yang dinamis pada file `.env` CodeIgniter 4 agar otomatis menyesuaikan dengan environment Dev (`192.168.1.4` / `10.147.17.40`) dan Prod (`192.168.1.17` / `10.147.17.60`).
- [ ] **Konfigurasi CORS di API:**
  - Konfigurasikan header CORS agar backend dapat merespons request yang datang dari berbagai alamat IP topologi (LAN lokal maupun ZeroTier).
- [ ] **Sistem Autentikasi (Login):**
  - Buat halaman `/login`.
  - Proteksi akses ke `/admin`, `/crawl`, dan `/crawler/resetDb` menggunakan CI4 Filters (`AuthFilter`).
  - Redirect pengguna tidak terotentikasi kembali ke `/` atau halaman login.
- [ ] **Keamanan API Backend:**
  - Tambahkan validasi dan sanitasi input pada endpoint API di `Api.php` (mencegah XSS pada judul dan deskripsi).
- [ ] **Optimasi & Pemetaan URL Crawler:**
  - Pastikan Crawler memetakan *path* dari direktori lokal `/var/www/FOTO/...` menjadi URL statis `https://foto.gkr.my.id/...` sebelum menyimpan ke database.
  - Tambahkan fitur *depth limit* (batas kedalaman klik tautan) pada script rekursif di `Crawler.php` untuk mencegah *infinity loop* pada situs yang besar.

## 🚀 Prioritas Menengah (Medium Priority)
- [ ] **Manajemen Error Gambar (Broken Links):**
  - Implementasikan script Cron Job otomatis (di server backend) atau fitur di panel admin untuk secara rutin memverifikasi apakah URL di tabel `cari_images` merespons dengan status `200 OK`. Jika tidak, secara otomatis set kolom `broken = 1`.
- [ ] **Fitur Recycle Bin (Trash):**
  - Tambahkan tab khusus di `/admin` untuk menampilkan item yang terkena *Soft Delete* (`deleted_at IS NOT NULL`).
  - Sediakan tombol "Restore" (mengosongkan `deleted_at`) dan "Permanent Delete".

## 🛠️ Prioritas Rendah / Kosmetik (Low Priority)
- [ ] **Dark Mode:**
  - Tambahkan *toggle* saklar Tema Gelap (Dark Mode) di antarmuka publik (`/`, `/cari`) maupun panel Admin.
- [ ] **Autocomplete Pencarian:**
  - Integrasikan saran pencarian *real-time* (dropdown autocomplete) di bawah kolom input pada saat pengguna mengetik kata kunci.
- [ ] **Ekspor Data:**
  - Tambahkan tombol di `/admin` untuk mengekspor data situs/gambar ke format `.csv` atau Excel.
