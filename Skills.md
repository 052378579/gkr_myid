# Skills & Developer Guidelines - CodeIgniter 4 Version

Dokumen ini berisi panduan teknis bagi AI Assistant atau developer yang akan mengelola pengembangan sistem Doogle dengan ekosistem CodeIgniter 4, Vue.js 3, dan Bootstrap 5.3.

## 1. Pemrosesan Database (Wajib CI4 Models)
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

## 2. Pengelolaan Frontend (Vue 3 & Bootstrap 5.3)
* **Integrasi Vue 3 via CDN:**
  Tulis logika *Vue component* atau *instance* di file JS terpisah (`public/assets/js/app.js`) atau pada blok `<script>` khusus di bagian penutup View CodeIgniter. Gunakan *Composition API* (`setup()`) atau *Options API*.
* **Eksploitasi Bootstrap 5.3:**
  Andalkan sepenuhnya class utilitas dan sistem grid **Bootstrap 5.3**. Hindari penciptaan properti styling manual untuk tata letak umum (seperti `display: flex`, `margin`, `color`, `paddings`), dan cukup gunakan class `d-flex`, `mb-3`, `text-primary`, `p-4`, dll.
* **Interaksi API Murni (Tanpa jQuery):**
  Untuk komunikasi asinkron (misal: panel admin AJAX Vue), gunakan bawaan *Fetch API* browser. Jangan gunakan sintaks `$.ajax()`.

## 3. Crawler dan Live Streaming
* **Menangani Live Stream (Pada Controller CI4):**
  Crawler bot menuntut tampilan eksekusi *live* waktu nyata di layar. Saat membuat endpoint streaming (misal `Crawler::runAction()`), matikan *output buffering*. Lakukan echo string/log secara iteratif dan ikuti dengan `ob_flush(); flush();`. (Perlu juga memastikan *Debug Toolbar* CI4 dinonaktifkan di environment production untuk endpoint spesifik ini).
* **Posisi Library Kustom:**
  Skrip yang tidak berkaitan langsung dengan HTTP Request (seperti `UrlRewriter` dan `DomDocumentParser`) wajib diposisikan ke dalam struktur `app/Libraries/`.

## 4. Aturan Routing
* **Pendefinisian URL di Routes.php:**
  Matikan fitur *auto-routing* bawaan CodeIgniter demi keamanan dan kejelasan kode. Daftarkan secara eksplisit semua route dalam file `app/Config/Routes.php` (Contoh: `$routes->post('/api/update-link-count', 'Api::updateLinkCount');`).
* **Endpoint API RESTful:**
  Format response untuk semua fungsi CRUD panel kontrol harus konsisten menggunakan `return $this->response->setJSON(...)`.
