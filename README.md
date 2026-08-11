# Mesin Pencari Visual Gracia

**Mesin Pencari Gracia** adalah platform pencarian cerdas yang memadukan arsitektur **CodeIgniter 4 (PHP 8.2+)** dan **Layanan Mikro Python (FastAPI + PyTorch + FAISS)** sebagai inti pemrosesan Vektor Kecerdasan Buatan.

Sistem hibrida ini dirancang untuk mengindeks, menelusuri, dan merekomendasikan Katalog Furniture. Pengolahan foto produk secara otomatis dikonversi menjadi vektor dimensi tinggi untuk mendukung fitur *Image-to-Image Search*, disajikan dengan antarmuka yang reaktif (Vue.js), cepat, intuitif, dan responsif.

---

## 🚀 Fitur Unggulan

* **Pencarian Visual AI (Image-to-Image Search):** Menggunakan PyTorch *MobileNetV3* dan FAISS Vector Database untuk pencocokan kemiripan visual yang sangat presisi, dilengkapi dengan pemotong gambar interaktif (*Cropper.js*).
* **Pencarian Suara Bahasa Indonesia:** Dukungan penuh *Web Speech API* murni berbahasa Indonesia (`id-ID`) dengan navigasi pintar.
* **Autocomplete & Search Engine Cerdas:** Rekomendasi kata kunci *real-time* berbasis RESTful API dengan algoritma pengecualian antonim kategori dan perhitungan skor relevansi tingkat tinggi.
* **Auto-Crawler & AI Trainer:** Sistem perayap katalog terotomatisasi via Cronjob dengan manajemen sinkronisasi inkremental dan fitur pemulihan (*Hard Reset*) via dasbor UI interaktif.
* **Arsitektur Frontend Ringkas:** Pendekatan *Zero Inline Script & Style* (Lulus Audit 100%), integrasi *Vue.js*, *DOM Metadata Injection*, dan strategi *Browser Memory Cache* agresif (`ASSET_VERSION`) yang menghemat latensi hingga 90%.
* **Keamanan & Otorisasi Ketat (RBAC):** Mode privat berlapis ganda, pendaftaran Telegram Chatbot otentik, serta pelacakan jejak audit (*Real IP Audit Log*) menembus jaringan *Reverse Proxy*.
* **Dashboard Administrator Interaktif:** Dasbor manajemen visual dengan metrik performa dan grafik *ApexCharts* responsif.

---

## 🛠️ Stack Teknologi & Infrastruktur

* **Sistem Operasi:** Linux Armbian OS.
* **Web Backend:** CodeIgniter 4 (PHP 8.2+, MVC).
* **AI Microservice:** Python 3, FastAPI, PyTorch, FAISS (Berjalan via *systemd daemon*).
* **Web Frontend:** Vue.js 3, Bootstrap 5.3 (Dark Mode Adaptif), ApexCharts, FontAwesome 6.
* **Database:** MySQL / MariaDB.

---

## 📜 Lisensi & Pengembang
Dikembangkan oleh **RND &copy; 2026** khusus untuk ekosistem internal **PT. Gracia Kreasi Rotan**.
