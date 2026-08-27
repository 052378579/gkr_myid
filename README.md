# Mesin Pencari Visual Gracia

**Mesin Pencari Gracia** adalah platform pencarian cerdas yang memadukan arsitektur **CodeIgniter 4 (PHP 8.2+)** dan **Layanan Mikro Python (FastAPI + PyTorch + FAISS)** sebagai inti pemrosesan Vektor Kecerdasan Buatan.

Sistem hibrida ini dirancang untuk mengindeks, menelusuri, dan merekomendasikan Katalog Furniture. Pengolahan foto produk secara otomatis dikonversi menjadi vektor dimensi tinggi untuk mendukung fitur *Image-to-Image Search*, disajikan dengan antarmuka yang reaktif (Vue.js), cepat, intuitif, dan responsif.

---

## 🚀 Fitur Unggulan

* **Pencarian Visual AI (Image-to-Image Search):** Menggunakan PyTorch *MobileNetV3* dan FAISS Vector Database untuk pencocokan kemiripan visual yang sangat presisi, dilengkapi dengan pemotong gambar interaktif (*Cropper.js*).
* **Pencarian Suara Bahasa Indonesia:** Dukungan penuh *Web Speech API* murni berbahasa Indonesia (`id-ID`) dengan navigasi pintar.
* **Autocomplete & Search Engine Cerdas:** Rekomendasi kata kunci *real-time* berbasis RESTful API dengan algoritma pengecualian antonim kategori dan perhitungan skor relevansi tingkat tinggi.
* **Pramuniaga Cerdas WAHA (Agentic AI):** Agen cerdas terintegrasi (n8n + Groq Llama-3.1 + Docker WAHA) yang mampu berkomunikasi dengan bahasa alami di WhatsApp dan melakukan *Custom Tool Calling* langsung ke basis data MySQL `gkr_cari`.
* **Auto-Crawler & Pelatih AI Dinamis (Master Scheduler n8n):** Sistem perayap katalog dan fitur *Hard Reset* AI kini terotomatisasi penuh secara harian oleh *workflow* penjadwalan n8n. Seluruh komputasi pelatih AI dinamis dipusatkan murni di SDCARD untuk efisiensi memori STB maksimal, bersandar pada referensi kokoh dari pemetaan `gkr_katalog.json`.
* **Arsitektur Frontend Ringkas (Frontend Analysis):** Pendekatan *Zero Inline Script & Style* (Lulus Audit 100%), integrasi *Vue.js*, *DOM Metadata Injection*, dan strategi *Browser Memory Cache* agresif (`ASSET_VERSION`) yang menghemat latensi hingga 90%. Perbaikan *CSS Layout Shift Bug* diatasi mutlak, seperti memangkas ruang kosong 4 piksel di sidebar normal.
* **Keamanan RBAC & Telegram Zero-Cross Webhook (Backend Analysis):** Mode privat berlapis ganda, pendaftaran Telegram Chatbot otentik (*Auto-Bind*), pelacakan jejak audit IP asli menembus *Reverse Proxy*. Terintegrasi dengan n8n via saluran lokal absolut (`127.0.0.1:5678`) yang mencegah bentrokan lalu lintas antara lingkungan DEV dan PROD. Termasuk pula fitur Chatbot Arsitektur Ganda (Perintah Cepat *Cari* vs Kolase Galeri *Album*) yang anti-beku (*Crash-Proof*).
* **Dashboard Administrator Interaktif:** Dasbor manajemen visual dengan metrik performa dan grafik *ApexCharts* responsif.

---

## 🛠️ Stack Teknologi & Infrastruktur

* **Sistem Operasi:** Linux Armbian OS.
* **Web Backend:** CodeIgniter 4 (PHP 8.2+, MVC).
* **AI Microservice:** Python 3.11.2, FastAPI, PyTorch, FAISS (Berjalan via *systemd daemon* `ai_scanner.service` di `/mnt/sdcard/ai-scanner` yang ditautkan ke `/var/www/gkr_myid/python_services` melalui *Symlink*). Aktivitas daemon dicatat mutlak ke `ai_scanner.log`.
* **Agentic AI & Orchestration:** Kontainer Docker WAHA (Port 3001), n8n Master Scheduler (Port 5678), Groq API (Llama-3.1).
* **Web Frontend:** Vue.js 3, Bootstrap 5.3 (Dark Mode Adaptif), ApexCharts, FontAwesome 6.
* **Database:** MySQL / MariaDB.

---

## 📜 Lisensi & Pengembang
Dikembangkan oleh **RND &copy; 2026** khusus untuk ekosistem internal **PT. Gracia Kreasi Rotan**.

# Rencana Implementasi: Sistem Disaster Recovery (Cadangan Otomatis SDCARD & MySQL)

Karena Anda terus memberikan lampu hijau (*Approve*), saya mengambil inisiatif untuk langsung menyusun **Rencana Arsitektur Disaster Recovery (Sistem Cadangan Darurat)**. 

Mengingat STB Amlogic menggunakan eMMC 6.5GB yang rentan dan SDCARD fisik yang bisa mengalami korupsi data sewaktu-waktu, sistem ini **sangat kritis** untuk menyelamatkan "ingatan" Kecerdasan Buatan Anda.

## Open Questions
> [!IMPORTANT]
> 1. Ke mana Anda ingin menaruh berkas hasil cadangan (*backup*)? Apakah membuat folder `/mnt/sdcard/backup_harian/` sudah cukup, atau Anda memiliki *Flashdisk/Harddisk Eksternal* lain yang terhubung ke STB?
> 2. Apakah Anda setuju jika jadwal pencadangan otomatis (Cronjob) dieksekusi setiap hari pada pukul **03:00 WIB** (saat lalu lintas ERP paling sepi)?

## Proposed Changes

### 1. Skrip Eksekutor Tunggal (`/mnt/sdcard/auto_backup.sh`)
Membuat skrip Bash elegan yang dikerjakan murni oleh mesin STB. Skrip ini akan melakukan rutinitas berikut:
- **Dump Database:** Menjalankan `mysqldump` untuk tabel `gkr_users`, `gkr_cari`, dan `gkr_material` dari database `gkr_myid`.
- **Pengarsipan Vektor AI:** Mengompresi berkas kritis AI FAISS (`produk.index`, `mapping.json`, `buku_catatan_ai.json`) dari `/mnt/sdcard/ai-scanner/`.
- **Pengarsipan Kode Web:** Mengompresi direktori `/var/www/gkr_myid/` (mengecualikan direktori `vendor/` dan `writable/cache/` agar tidak membengkak).
- **Rotasi 7-Hari (Pembersihan Otomatis):** Skrip akan dirancang cerdas untuk otomatis menghapus fail *backup* yang usianya lebih dari 7 hari, sehingga SDCARD Anda tidak akan penuh atau memicu *Kernel Panic*.

### 2. Integrasi Cronjob Armbian
Mendaftarkan skrip tersebut ke dalam *crontab* root Linux STB.
- Perintah: `0 3 * * * /bin/bash /mnt/sdcard/auto_backup.sh >> /var/log/auto_backup.log 2>&1`

### 3. Notifikasi Peringatan Dini (Opsional)
*(Jika memungkinkan)* Menambahkan baris perintah cURL ke skrip bash tersebut agar mengirim sinyal diam-diam ke Webhook n8n (`127.0.0.1:5678`) sehingga Anda mendapatkan pesan Telegram: *"✅ Backup Harian Berhasil (Ukuran: 45MB)"* setiap jam 3 pagi.

## Verification Plan
1. Menyajikan kode utuh `auto_backup.sh` sebagai Artefak yang bisa Anda ulas.
2. Memberikan perintah SSH satu baris (*one-liner*) untuk Anda jalankan agar skrip tersebut terpasang ke STB DEV Anda.
3. Menyimulasikan eksekusi manual untuk memastikan kompresi `.tar.gz` berhasil dibuat tanpa memberatkan CPU S905x.
