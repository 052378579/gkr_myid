# Analisis & Rencana Implementasi (Sorting & Pagination)

Berdasarkan tinjauan pada kode yang ada dan permintaan Anda terkait rute `/admin/karyawan`, berikut adalah analisis dan rekomendasinya.

## Analisis & Rekomendasi
1. **Pengurutan (Sorting):**
   Saat ini, data diambil dengan pengurutan berdasarkan `id_user` secara menurun (terbaru di atas) di dalam `KaryawanController.php`. Mengubah pengurutan berdasarkan `divisi` (ASC) kemudian `nama_lengkap` (ASC) akan sangat membantu secara visual karena data karyawan akan terkelompok rapi sesuai divisinya, lalu diurutkan secara alfabetis.
2. **Paginasi (Pagination):**
   Melihat implementasi pada `/admin` (di mana tabel Situs dan Gambar menggunakan `Vue.js` untuk memotong *array* data dengan `slice`), kita dapat menerapkan pendekatan serupa untuk tabel Karyawan. Pendekatan **Client-side Pagination** ini sangat cepat dan interaktif (tanpa *reload* halaman), dan sangat cocok bila total data karyawan masih dalam hitungan ratusan hingga ribuan.
   **Rekomendasi:** Mengimplementasikan fitur paginasi berbasis *Vue computed properties* di sisi *client* (mirip seperti rute `/admin`) untuk menjaga konsistensi arsitektur *frontend* pada proyek Anda.

## User Review Required
> [!IMPORTANT]
> Harap tinjau rencana perubahan pada *Controller* dan *View* di bawah ini. Jika Anda menyetujui pendekatannya (Paginasi *Client-Side* menggunakan Vue), klik tombol **Proceed**.

## Proposed Changes

### [MODIFY] [KaryawanController.php](file:///192.168.1.4/www/gkr_myid/app/Controllers/Admin/KaryawanController.php)
- Mengubah kueri pengambilan data pada _method_ `getAll()`:
  Dari: `$this->userModel->orderBy('id_user', 'DESC')->findAll();`
  Menjadi: `$this->userModel->orderBy('divisi', 'ASC')->orderBy('nama_lengkap', 'ASC')->findAll();`

### [MODIFY] [admin_karyawan.php](file:///192.168.1.4/www/gkr_myid/app/Views/admin_karyawan.php)
- **Logika Vue.js:**
  - Menambahkan *state* reaktif `perPage` (default 10) dan `currentPage` (default 1).
  - Menambahkan *computed properties* `paginatedKaryawan` dan `totalPages` untuk memotong (`slice`) `daftarKaryawan` berdasarkan halaman aktif.
- **Antarmuka (Template):**
  - Mengubah perulangan tabel agar menggunakan `v-for="item in paginatedKaryawan"`.
  - Menambahkan _dropdown_ "Tampilkan: 10, 25, 50" di area atas tabel (sebelah kanan judul).
  - Menambahkan *footer* navigasi paginasi (Tombol **Sebelumnya** dan **Berikutnya**) pada bagian bawah tabel.

## Verification Plan
1. Mengakses rute `/admin/karyawan` dan memverifikasi bahwa daftar diurutkan per Divisi lalu Nama Lengkap.
2. Mengubah jumlah data yang tampil menjadi 10, 25, atau 50.
3. Mencoba berpindah halaman menggunakan tombol "Sebelumnya" dan "Berikutnya" untuk memastikan pemotongan data berjalan akurat.
