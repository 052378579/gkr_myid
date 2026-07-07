# Style Guide: Mesin Pencari Gracia (gkr.my.id) - CodeIgniter 4 Version

## 1. Standar Penulisan Kode PHP (CodeIgniter 4)
* **Gunakan Bahasa Indonesia:** Gunakan Bahasa Indonesia untuk penamaan variabel, fungsi, class, dan komentar. (Kecuali library pihak ketiga, gunakan bahasa Inggris).
* **Konvensi PSR-12:** Ikuti standar penulisan kode PSR-12 (namespace yang sesuai, penamaan class PascalCase, penamaan method camelCase).
* **MVC Pattern:** Pisahkan logika bisnis (Models), kontrol alur/endpoint (Controllers), dan presentasi visual (Views).
* **Return JSON untuk API:** Saat membuat endpoint untuk UI Vue.js, selalu manfaatkan fitur CI4 Response, jangan melakukan `echo` langsung:
  ```php
  return $this->response->setJSON(['status' => 'success', 'data' => $data]);
  ```
* **Error Handling:** Gunakan mekanisme Exception bawaan CodeIgniter 4.

## 2. Panduan UI/UX (CSS & Desain)
* **Warna dan Tema Utama:**
  * Latar belakang utama: Dominan terang/putih.
  * *Accent Color* (Warna Aksen): Biru Bootstrap (`#2B3385`) untuk elemen interaktif (tombol cari, indikator tab, dan pagination).
* **Framework CSS (Bootstrap 5.3):**
  * Gunakan **Bootstrap 5.3** secara penuh. Manfaatkan utility classes (seperti `mb-3`, `text-secondary`, `d-flex`, `gap-2`) daripada membuat class CSS kustom.
* **Pendekatan Desain UI Modern:**
  * Implementasikan *Glassmorphism* (misal `backdrop-filter: blur()`, `bg-white bg-opacity-75`) untuk komponen *fixed-top* (navbar/header).
  * Gunakan desain membulat (*rounded corners*) dengan class `rounded-3` atau `rounded-pill` pada tombol dan kartu untuk memberi kesan modern dan ramah (*friendly UI*).

## 3. Standar Penulisan JavaScript (Vue 3 CDN & Bootstrap 5.3)
* **.mount('#gkr');** adalah wajib untuk inisialisasi Vue di setiap file View.
* **Vue.js 3 (CDN):** 
  * Karena menggunakan Vue via CDN (bukan SFC/Vite), manfaatkan Vue Composition API (`Vue.createApp`, `setup()`, `ref`) atau Options API di dalam tag `<script>`.
  * Biarkan Vue yang mengurus pembaruan DOM secara reaktif. Hindari manipulasi DOM secara manual (vanilla JS DOM traversal / jQuery) pada area yang sudah dikelola oleh Vue.
* **Asynchronous Logic:** Gunakan `async/await` dengan `fetch()` API murni untuk interaksi data dengan controller CI4, karena ini lebih modern dibanding `$.ajax`.
* **Komponen Bootstrap JS:** Inisialisasi komponen interaktif Bootstrap 5.3 (seperti Modal, Toast, Tooltip) melalui *Vanilla JavaScript* sesuai dokumentasi, bukan via jQuery.

## 4. Penamaan Database dan Models (CI4)
* **Tabel MySQL:** Pertahankan format aslinya yang menggunakan huruf kecil jamak (`cari_sites`, `cari_images`).
* **CI4 Models:** 
  * Class Model dinamai menggunakan gaya PascalCase dengan akhiran `Model` (misal: `SiteModel`, `ImageModel`).
  * Selalu tetapkan `$primaryKey = 'id'`, `$useSoftDeletes = true`, dan `$deletedField = 'deleted_at'` pada properti model terkait untuk menjaga konsistensi arsitektur lama.
