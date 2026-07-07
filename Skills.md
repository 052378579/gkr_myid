# Skills & Developer Guidelines - CodeIgniter 4 Version

Dokumen ini berisi panduan teknis bagi AI Assistant atau developer yang akan mengelola pengembangan **Mesin Pencari Gracia** dengan ekosistem CodeIgniter 4, Vue.js 3 (cdn), dan Bootstrap 5.3.

## 1. Aturan Penulisan Kode (Sangat Penting)
* **Kewajiban Bahasa Indonesia:**
  Semua penamaan variabel, metode (fungsi), nama *class*, dan penulisan komentar di dalam kode **WAJIB** menggunakan Bahasa Indonesia. Pengecualian hanya berlaku saat berinteraksi dengan API bawaan framework atau library pihak ketiga (misal: penamaan tabel database default CI4, konfigurasi `.env`, dll).

## 2. Pemrosesan Database (Wajib CI4 Models)
* **Gunakan CodeIgniter Query Builder / Models:**
  Tinggalkan query *PDO native* murni. Gunakan Model CI4 untuk setiap transaksi CRUD guna memaksimalkan keamanan bawaan sistem.
  *Contoh increment klik:*
  ```php
  $siteModel = new \App\Models\SiteModel();
  $siteModel->where('id', $id)->set('clicks', 'clicks+1', false)->update();
  ```
* **Memanfaatkan Fitur Soft Delete Otomatis:**
  Pastikan Model dikonfigurasi dengan `$useSoftDeletes = true`. Dengan demikian:
  - Pemanggilan metode `$model->delete($id)` otomatis memperbarui kolom `deleted_at`.
  - Pemanggilan `$model->findAll()` otomatis hanya akan mengambil rekaman yang masih aktif (`deleted_at IS NULL`).

## 3. Pengelolaan Frontend (Vue 3 & Bootstrap 5.3)
* **Integrasi Vue 3 via CDN:**
  Tulis logika *Vue component* atau *instance* di file JS terpisah (`public/js/app.js`) atau pada blok `<script>` khusus di bagian penutup View CodeIgniter. Gunakan *Composition API* (`setup()`) atau *Options API*.
* **Eksploitasi Bootstrap 5.3:**
  Andalkan sepenuhnya class utilitas dan sistem grid **Bootstrap 5.3**. Hindari penciptaan properti styling manual untuk tata letak umum, cukup gunakan class bawaan Bootstrap.
* **Interaksi API Murni (Tanpa jQuery):**
  Untuk komunikasi asinkron, gunakan bawaan *Fetch API* browser. Jangan gunakan sintaks `$.ajax()`.

## 4. Crawler, Environment, dan Live Streaming
* **Manajemen URL Gambar & Environment:**
  Selalu pastikan endpoint crawler atau rendering gambar dari lokal `/var/www/FOTO` dialihkan menjadi statis via `https://foto.gkr.my.id`. Gunakan variabel lingkungan di `.env` (misalnya `app.baseURL`) untuk menghindari *hardcoding* URL yang bisa pecah saat berpindah dari Dev (`192.168.1.4` / `10.147.17.40`) ke Prod (`192.168.1.17` / `10.147.17.60`).
* **Menangani Live Stream (Pada Controller CI4):**
  Crawler bot menuntut tampilan eksekusi *live* waktu nyata di layar. Saat membuat endpoint streaming (misal `Crawler::doCrawl()`), matikan *output buffering*. Lakukan echo string/log secara iteratif dan ikuti dengan `ob_flush(); flush();`.
* **Posisi Library Kustom:**
  Skrip yang tidak berkaitan langsung dengan HTTP Request (seperti `UrlRewriter` dan `DomDocumentParser`) wajib diposisikan ke dalam struktur `app/Libraries/`.

## 5. Aturan Routing
* **Pendefinisian URL di Routes.php:**
  Matikan fitur *auto-routing* bawaan CodeIgniter demi keamanan dan kejelasan kode. Daftarkan secara eksplisit semua route dalam file `app/Config/Routes.php`.
* **Endpoint API RESTful:**
  Format response untuk semua fungsi CRUD panel kontrol harus konsisten menggunakan `return $this->response->setJSON(...)`.
