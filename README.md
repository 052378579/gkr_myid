# Mesin Pencari Visual Gracia

**Mesin Pencari Gracia** adalah platform pencarian cerdas yang memadukan arsitektur **CodeIgniter 4 (PHP 8.2+)** dan **Layanan Mikro Python (FastAPI + PyTorch + FAISS)** sebagai inti pemrosesan Vektor Kecerdasan Buatan.

Sistem hibrida ini dirancang untuk mengindeks, menelusuri, dan merekomendasikan Katalog Furniture. Pengolahan foto produk secara otomatis dikonversi menjadi vektor dimensi tinggi untuk mendukung fitur *Image-to-Image Search*, disajikan dengan antarmuka yang reaktif (Vue.js), cepat, intuitif, dan responsif.

---

## 🚀 Fitur Unggulan

* **Pencarian Visual AI (Image-to-Image Search):** Menggunakan PyTorch *MobileNetV3* dan FAISS Vector Database untuk pencocokan kemiripan visual yang sangat presisi, dilengkapi dengan pemotong gambar interaktif (*Cropper.js*).
* **Pencarian Suara Bahasa Indonesia:** Dukungan penuh *Web Speech API* murni berbahasa Indonesia (`id-ID`) dengan navigasi pintar.
* **Autocomplete & Search Engine Cerdas:** Rekomendasi kata kunci *real-time* berbasis RESTful API dengan algoritma pengecualian antonim kategori dan perhitungan skor relevansi tingkat tinggi.
* **Pramuniaga Cerdas WAHA (Agentic AI):** Agen cerdas terintegrasi (n8n + Groq Llama-3.1 + Docker WAHA) yang mampu berkomunikasi dengan bahasa alami di WhatsApp dan melakukan *Custom Tool Calling* langsung ke basis data MySQL `gkr_cari`.
* **Auto-Crawler & Pelatih AI Dinamis (Master Scheduler n8n):** Sistem perayap katalog dan fitur *Hard Reset* AI kini terotomatisasi penuh secara harian oleh *workflow* penjadwalan n8n. Seluruh komputasi pelatih AI dinamis dipusatkan murni di SDCARD untuk efisiensi memori STB maksimal.
* **Arsitektur Frontend Ringkas:** Pendekatan *Zero Inline Script & Style* (Lulus Audit 100%), integrasi *Vue.js*, *DOM Metadata Injection*, dan strategi *Browser Memory Cache* agresif (`ASSET_VERSION`) yang menghemat latensi hingga 90%.
* **Keamanan RBAC & Telegram Zero-Cross Webhook:** Mode privat berlapis ganda, pendaftaran Telegram Chatbot otentik (*Auto-Bind*), pelacakan jejak audit IP asli menembus *Reverse Proxy*. Terintegrasi dengan n8n via saluran lokal absolut (`127.0.0.1:5678`) yang mencegah bentrokan lalu lintas antara lingkungan DEV dan PROD. Termasuk pula fitur Chatbot Arsitektur Ganda (Perintah Cepat *Cari* vs Kolase Galeri *Album*) yang anti-beku (*Crash-Proof*).
* **Dashboard Administrator Interaktif:** Dasbor manajemen visual dengan metrik performa dan grafik *ApexCharts* responsif.

---

## 🛠️ Stack Teknologi & Infrastruktur

* **Sistem Operasi:** Linux Armbian OS.
* **Web Backend:** CodeIgniter 4 (PHP 8.2+, MVC).
* **AI Microservice:** Python 3.11.2, FastAPI, PyTorch, FAISS (Berjalan via *systemd daemon* di `/mnt/sdcard/ai-scanner`).
* **Agentic AI & Orchestration:** Kontainer Docker WAHA (Port 3001), n8n Master Scheduler (Port 5678), Groq API (Llama-3.1).
* **Web Frontend:** Vue.js 3, Bootstrap 5.3 (Dark Mode Adaptif), ApexCharts, FontAwesome 6.
* **Database:** MySQL / MariaDB.

---

## 📜 Lisensi & Pengembang
Dikembangkan oleh **RND &copy; 2026** khusus untuk ekosistem internal **PT. Gracia Kreasi Rotan**.
