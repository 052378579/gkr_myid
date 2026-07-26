# Mesin Pencari Visual Gracia (gkr.my.id)

**Mesin Pencari Gracia** adalah platform pencarian cerdas terpadu yang memadukan arsitektur **CodeIgniter 4 (PHP 8.2+)** dan **Layanan Mikro Python (FastAPI + PyTorch MobileNetV3 + FAISS)** sebagai inti pemrosesan Vektor Kecerdasan Buatan (*Artificial Intelligence*).

Sistem hibrida ini dirancang khusus untuk mengindeks, menelusuri, dan merekomendasikan katalog furniture serta swatch bahan. Sistem ini mengotomatiskan pengolahan korpus foto dari direktori lokal (`/var/www/FOTO` -> `https://foto.gkr.my.id`) menjadi data vektor 576-dimensi untuk pencarian gambar visual (*Image-to-Image Search*), disajikan dengan antarmuka yang reaktif, cepat, intuitif, dan responsif.

---

## 🚀 Fitur Unggulan Sistem

* **Pencarian Visual AI (Image-to-Image Search):**
  Mengunggah sampel foto produk/kain untuk mencocokkan kemiripan visual secara presisi menggunakan ekstraksi fitur PyTorch *MobileNetV3-Small* (576 dimensi) dan FAISS Vector Database (*Cosine Similarity* $\ge 0.68$, $k=15$).
* **Klasifikasi Gambar Gabungan (Hybrid AI + Meta Keywords):**
  Pencarian tingkat lanjut yang tidak hanya bertumpu pada kemiripan vektor visual murni (*Blackbox AI*), melainkan dipadukan dengan kontrol parameter klasifikasi tekstual (`keywords`) demi hasil penelusuran spesifik yang lebih akurat dan terarah.
* **Zero-Footprint Storage (Auto-Cleanup):**
  Gambar unggahan pengguna ditransfer via `multipart/form-data` ke layanan FastAPI dan **seketika dimusnahkan (`unlink()`)** dari storage web server begitu hasil didapatkan, menjaga penyimpanan server tetap bersih.
* **Agregasi Identitas Visual Multi-Sudut (AI Harmonization):**
  Sistem secara cerdas menyatukan berbagai variasi foto dari sudut berbeda (`_depan`, `-B`, `-C`, `-D`, `-E`, `samping`, `perspektif`) menjadi satu entitas produk tunggal yang bersih. Skrip Python (`build_index_new.py`) dan PHP (`CrawlerLib.php`) berbagi rumus *Regex* yang 100% harmonis.
* **Dasbor Pelatih AI & Terminal Streaming HTTP (`/admin/ai`):**
  Menyajikan dasbor operasi pelatih AI dengan tampilan konsol terminal peretas (*hacker style* `#1e1e1e`). Mengalirkan baris log komputasi Python secara *real-time* via *HTTP ReadableStream API* tanpa risiko *PHP Timeout*.
* **Desain UI/UX Modern & Reaktif (Harmonisasi Modal):**
  Antarmuka terpusat bergaya minimalis dengan dukungan **Dark Mode** native. Hasil pencarian visual disajikan dalam galeri ubin interaktif (*Masonry Grid*) bernuansa "Kecocokan visual". Antarmuka Admin memelopori standar Harmonisasi Modal 2-Kolom (Zonasi Edit Tekstual murni vs Zonasi Pratinjau Visual berskala 8:5) yang elegan dan mencegah kesalahan ketik (*typo*) berkat integrasi Dropdown Kaskade ketat.
* **Arsitektur RESTful API Terstandarisasi:**
  Seluruh *endpoint* API terisolasi di sub-direktori `app/Controllers/Api/` (termasuk `GraciaApi.php` dan `VersiApi.php`) dengan format balasan JSON baku: `{"status": "...", "pesan": "...", "data": [...]}`.
* **Zero-DB Hit Changelog & Auto-Versioning:**
  Riwayat rilis aplikasi disimpan murni pada *flat-file* statis `/public/versi.json` untuk meniadakan *query overhead* ke MySQL. Penomoran rilis dikelola otomatis menggunakan skema kalender dinamis (`0.{Bulan}.{Tanggal}`).
* **Integrasi Telegram Chatbot Enterprise:**
  Asisten virtual cerdas (`@Bot`) di ujung jari yang didukung fitur Asinkronisasi *Anti-Timeout* (lewat `fastcgi_finish_request`), pencarian hibrida yang kebal *typo* (*MySQL Full-Text* dipadu algoritma *AI Levenshtein Distance*), serta Keamanan Lapis Ganda (*Secret Token* + RBAC). Seluruh rekam pencarian dari lapangan disuntikkan secara senyap ke Dasbor Log Web.
* **Auto-Detect Environment (Kunci Pengaman Mode):**
  Deteksi otomatis di `public/index.php`. Akses via LAN (`192.168.1.4`) atau ZeroTier (`10.147.17.40`) beralih ke mode *Development*, sementara akses dari IP Publik/Domain Publik mengunci ketat sistem ke mode *Production* untuk menyembunyikan *stack trace*.
* **Sistem Otorisasi & Audit Log (RBAC):**
  Diperkuat filter `SuperAdminFilter` yang mengunci akses ke seluruh turunan rute `/admin/*` khusus bagi sesi `id_user = 1`. Log Audit (Cari & User) mencatat IP asli pengunjung menembus *Reverse Proxy* dan dilengkapi *Auto-Reload 5 Menit*.

---

## 🛠️ Stack Teknologi & Topologi Infrastruktur

* **Spesifikasi Server:** Armbian OS (Debian bookworm) Linux 6.12 pada peranti Amlogic S905x.
* **Akses IP Server:** LAN `192.168.1.4` | VPN ZeroTier `10.147.17.40`.
* **Web Backend:** CodeIgniter 4 (PHP 8.2+, MVC).
* **AI Microservice:** Python 3, FastAPI, PyTorch, FAISS (`http://127.0.0.1:5000` via daemon `ai_scanner.service`).
* **Web Frontend:** Vue.js 3 (CDN), Bootstrap 5.3 (Native Dark Mode), FontAwesome 6, ReadableStream API.
* **Database:** MySQL / MariaDB.
* **Katalog Foto Lokal:** Disajikan statis via `https://foto.gkr.my.id` dari direktori server `/var/www/FOTO`.

---

## 📁 Ikhtisar Arsitektur Direktori Utama

* **`app/Controllers/`**: Mengelola alur kontrol antarmuka publik dan administratif (`Admin.php`, `AiCrawler.php`, `Search.php`).
* **`app/Controllers/Api/`**: Sub-direktori steril layanan RESTful API murni (`GraciaApi.php`, `ImageSearchApi.php`, `VersiApi.php`, `CrawlerApi.php`).
* **`app/Filters/`**: Perisai keamanan otentikasi (`AuthFilter.php` & `SuperAdminFilter.php`).
* **`app/Libraries/`**: Library pendukung (`CrawlerLib.php` berotak Regex harmonis).
* **`app/Models/`**: Representasi tabel SQL (`ImageModel.php`, `MaterialModel.php`, `SiteModel.php`, `LogCariModel.php`, `LogUserModel.php`).
* **`app/Views/layout/`**: Kerangka utama UI terpusat (`main.php` & `admin_layout.php`). Seluruh view wajib mewarisi kerangka ini.
* **`public/`**: Titik masuk web (`index.php`), aset statis, dan flat-file changelog (`versi.json`).
* **`python_services/`**: Karantina kecerdasan buatan Python (`build_index_new.py` & `main_new.py`).

---

## 🔒 Konvensi Keamanan & Pengelolaan Berkas

1. **Aturan Keamanan Database Backup:** File dump database (`.sql`) **dilarang keras** berada di direktori web (`/var/www/gkr_myid`). Semua berkas backup wajib dievakuasi ke direktori terisolasi (`/root/backups/`).
2. **Kertas Kerja Internal vs Etalase Publik:**
   * **Etalase Publik (Lacak Git):** `README.md`, `Tautan.md`, `struktur_folder.md`, `git.txt`.
   * **Dokumen Internal (Gitignore):** `PRD.md`, `Memory.md`, `Skills.md`, `StyleGuide.md`, `Task.md`.
