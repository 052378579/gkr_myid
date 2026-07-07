# Product Requirements Document (PRD): Mesin Pencari Gracia (gkr.my.id)

## 1. Tujuan Utama (Core Objective)
Membangun mesin pencari web (Search Engine) bernama **Mesin Pencari Gracia** (untuk domain gkr.my.id) guna mengindeks, menemukan, dan menampilkan situs dan gambar, dengan fokus khusus pada **Katalog Furniture**. Sistem ini bertugas mengotomatiskan pengumpulan data dari direktori lokal **/var/www/FOTO** maupun berbagai situs referensi melalui mekanisme *web scraping/crawling* yang berjalan secara rekursif, lalu menyajikannya kepada pengguna akhir dengan antarmuka yang bersih, cepat, dan intuitif. Sistem ini menggunakan **CodeIgniter 4**.

## 2. Topologi Jaringan & Infrastruktur
Sistem ini dide-deploy pada dua lingkungan terpisah untuk memastikan kestabilan antara pengembangan dan operasional produksi:

**Lingkungan Development (Dev):**
*   **Akses IP Lokal (LAN):** `192.168.1.4` (Digunakan untuk pengujian lokal atau akses intranet).
*   **Akses IP VPN (ZeroTier):** `10.147.17.40` (Akses remote/manajemen admin secara aman).

**Lingkungan Production (Prod):**
*   **Akses IP Lokal (LAN):** Dinamis (saat ini `192.168.1.17`).
*   **Akses IP VPN (ZeroTier):** `10.147.17.60` (Akses remote disamakan untuk kemudahan kontrol).

**Direktori Server:**
*   **Direktori Aplikasi (Backend CI4):** `/var/www/gkr_myid`
*   **Direktori Katalog Lokal (FOTO):** `/var/www/FOTO`. File di direktori ini akan disajikan secara statis ke publik melalui tautan **`https://foto.gkr.my.id`**. Oleh karena itu, *Crawler* harus memetakan struktur file lokal tersebut menjadi relative path statis di dalam *database*.

## 3. Spesifikasi UI/UX (Tampilan Visual)
* **Halaman Beranda (Home - `/`):** 
  * Tampilan minimalis dan terpusat (mirip Google).
  * Menampilkan logo kustom ("Gracia").
  * Terdapat kotak input pencarian dan tombol "Cari".
* **Halaman Hasil Pencarian (`/cari`):**
  * **Header:** Menampilkan logo dan bilah pencarian persisten.
  * **Navigasi Tab:** Sistem tab untuk memfilter kategori hasil pencarian: **Semua** (Situs) dan **Gambar**.
  * **Tampilan Hasil Situs:** Menampilkan daftar link dengan judul, cuplikan deskripsi (snippet), dan URL situs (dengan tautan ke URL statis `https://foto.gkr.my.id` untuk sumber lokal).
  * **Tampilan Hasil Gambar:** Menampilkan galeri gambar menggunakan tata letak dinamis (*Masonry grid layout*). Gambar dapat diklik untuk memunculkan pratinjau ukuran penuh bergaya *lightbox* (*Fancybox*).
  * **Paginasi:** Sistem paginasi grafis menggunakan komponen/view CodeIgniter.
* **Halaman Panel Admin (`/admin`):**
  * Antarmuka *backend* interaktif berbasis tabel menggunakan **Vue.js 3 (CDN)** dan **Bootstrap 5.3**.
  * Tema modern dengan elemen *Glassmorphism* dan navigasi *fixed top*.
  * Fitur pencarian instan (real-time filtering) dan paginasi *client-side*.
  * Modul Edit menggunakan *Modal Box* (menampilkan pratinjau thumbnail untuk gambar).
* **Halaman Crawler (`/crawl`):**
  * Antarmuka split-screen (form input di kiri, terminal *live streaming* di kanan) menggunakan **Vue.js 3 (CDN)** dan **Bootstrap 5.3**.
  * Terminal interaktif yang menampilkan log proses crawling secara real-time.
* **Elemen Desain Khusus:**
  * **Warna Aksen (*Accent Color*):** Menggunakan kode hex **`#2B3385`** untuk elemen interaktif (tombol, indikator tab, pagination).
  * **Inisialisasi Vue:** Diwajibkan selalu menggunakan container ID `<div id="gkr">` dan dipanggil via `.mount('#gkr');` di setiap file view/halaman.

## 4. Daftar Fitur Inti (Core Features)
* **Mesin Penjelajah Web / Crawler Bot:**
  * **Web Crawling:** Ekstraksi HTML menggunakan `DomDocumentParser` untuk mengambil `<title>`, `<meta description/keywords>`, dan tag `<img>`. Menelusuri tautan secara rekursif.
  * **Local Directory Crawling:** Memindai direktori lokal server **/var/www/FOTO** untuk mengindeks foto/katalog secara massal.
  * **Streaming Output:** Menggunakan *Fetch API ReadableStream* untuk menampilkan proses log ke UI secara real-time.
  * **Stop & Reset:** Mekanisme pembatalan (AbortController) dan fitur pembersihan database (`/crawler/resetDb`).
* **Mesin Pencarian (Search Engine):**
  * Pencarian berbasis kata kunci (kueri) yang memilah data menggunakan CodeIgniter 4 Models (`SiteModel` dan `ImageModel`).
  * Pelacakan statistik (jumlah klik) via endpoint API CI4 (`/api/updateLinkCount`, `/api/updateImageCount`).
* **Manajemen Data (Admin Panel):**
  * Operasi CRUD penuh menggunakan CI4 Models dengan fitur *Soft Delete* (`$useSoftDeletes = true`).
  * Integrasi *UrlRewriter* sebagai Service/Library untuk menormalkan URL.

## 5. Batasan Teknis & Standar Kode (Code Convention)
* **Stack Teknologi:** 
  * **Backend:** PHP (Framework CodeIgniter 4).
  * **Frontend (Semua Area):** Vue.js 3 (CDN), Bootstrap 5.3, HTML5, CSS3.
  * **Database:** MySQL/MariaDB (via CI4 Query Builder/Models).
* **Ketergantungan Pihak Ketiga (Assets):**
  * Bootstrap 5.3 (CSS/JS), Vue.js 3 (CDN), Fancybox untuk lightbox gambar, Masonry untuk grid dinamis, SweetAlert2.
* **Standar Bahasa:** 
  * **Wajib menggunakan Bahasa Indonesia** untuk penamaan seluruh variabel, metode (fungsi), *class*, dan komentar internal di dalam kode, terkecuali saat harus mematuhi penamaan wajib dari *library* pihak ketiga atau framework.
* **Performa & Eksekusi:**
  * Skrip crawler memakan resource server (PHP timeout/memory limit).
  * *Hotlinking Gambar:* Dikelola lewat sistem pelaporan broken link endpoint (`/api/setBroken`).

## 6. Keamanan & Edge Cases
* **Sistem Autentikasi:** Akan diimplementasikan menggunakan CI4 Filters untuk melindungi `/admin`, `/crawl`, dan `/crawler/resetDb`.
* **Jebakan Crawling (Spider Traps):** Halaman dengan URL dinamis tanpa batas bisa memicu *infinity loop*.
* **Pencarian Kosong:** Dicegah di level frontend dan divalidasi oleh CI4 Controllers.