# Product Requirements Document (PRD): Doogle (gkr_my.id) - CodeIgniter 4 Version

## 1. Tujuan Utama (Core Objective)
Membangun mesin pencari web khusus (Search Engine) bernama **Doogle** (untuk domain gkr_my.id) guna mengindeks, menemukan, dan menampilkan situs dan gambar, dengan fokus khusus pada katalog furniture. Sistem ini bertugas mengotomatiskan pengumpulan data dari berbagai situs referensi maupun direktori lokal melalui mekanisme *web scraping/crawling* yang berjalan secara rekursif, lalu menyajikannya kepada pengguna akhir dengan antarmuka yang bersih, cepat, dan intuitif. Sistem ini direfaktor menggunakan **CodeIgniter 4**.

## 2. Spesifikasi UI/UX (Tampilan Visual)
* **Halaman Beranda (Home - `/`):** 
  * Tampilan minimalis dan terpusat (mirip Google).
  * Menampilkan logo kustom ("Gracia").
  * Terdapat kotak input pencarian dan tombol "Cari".
* **Halaman Hasil Pencarian (`/cari`):**
  * **Header:** Menampilkan logo dan bilah pencarian persisten.
  * **Navigasi Tab:** Sistem tab untuk memfilter kategori hasil pencarian: **Semua** (Situs) dan **Gambar**.
  * **Tampilan Hasil Situs:** Menampilkan daftar link dengan judul, cuplikan deskripsi (snippet), dan URL situs.
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

## 3. Daftar Fitur Inti (Core Features)
* **Mesin Penjelajah Web / Crawler Bot:**
  * **Web Crawling:** Ekstraksi HTML menggunakan `DomDocumentParser` untuk mengambil `<title>`, `<meta description/keywords>`, dan tag `<img>`. Menelusuri tautan secara rekursif.
  * **Local Directory Crawling:** Memindai direktori lokal server untuk mengindeks foto/katalog secara massal.
  * **Streaming Output:** Menggunakan *Fetch API ReadableStream* untuk menampilkan proses log ke UI secara real-time.
  * **Stop & Reset:** Mekanisme pembatalan (AbortController) dan fitur pembersihan database (`/reset-db`).
* **Mesin Pencarian (Search Engine):**
  * Pencarian berbasis kata kunci (kueri) yang memilah data menggunakan CodeIgniter 4 Models (`SiteModel` dan `ImageModel`).
  * Pelacakan statistik (jumlah klik) via endpoint API CI4 (`/api/updateLinkCount`, `/api/updateImageCount`).
* **Manajemen Data (Admin Panel):**
  * Operasi CRUD penuh menggunakan CI4 Models dengan fitur *Soft Delete* (`$useSoftDeletes = true`).
  * Integrasi *UrlRewriter* sebagai Service/Library untuk menormalkan URL.

## 4. Batasan Teknis (Technical Constraints)
* **Stack Teknologi:** 
  * **Backend:** PHP (Framework CodeIgniter 4).
  * **Frontend (Semua Area):** Vue.js 3 (CDN), Bootstrap 5.3, HTML5, CSS3.
  * **Database:** MySQL/MariaDB (via CI4 Query Builder/Models).
* **Ketergantungan Pihak Ketiga (Assets):**
  * Bootstrap 5.3 (CSS/JS), Vue.js 3 (CDN), Fancybox untuk lightbox gambar, Masonry untuk grid dinamis, SweetAlert2.
* **Performa & Eksekusi:**
  * Skrip crawler memakan resource server (PHP timeout/memory limit).
  * *Hotlinking Gambar:* Dikelola lewat sistem pelaporan broken link endpoint (`/api/setBroken`).

## 5. Keamanan & Edge Cases
* **Sistem Autentikasi:** Akan diimplementasikan menggunakan CI4 Filters untuk melindungi `/admin`, `/crawl`, dan `/reset-db`.
* **Jebakan Crawling (Spider Traps):** Halaman dengan URL dinamis tanpa batas bisa memicu *infinity loop*.
* **Pencarian Kosong:** Dicegah di level frontend dan divalidasi oleh CI4 Controllers.