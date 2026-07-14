# Implementasi Redesign Halaman Admin

Berdasarkan analisis menyeluruh dari gambar animasi (GIF) `admin_revisi.jpg` dan kode `app/Views/admin/admin.php` saat ini, berikut adalah perubahan struktural yang diperlukan untuk merealisasikan desain baru:

## Analisis Perubahan Halaman (Route `/admin`)

1. **Perubahan Arsitektur Layout Utama**:
   - Saat ini, halaman admin (`admin.php`) memiliki *navbar* dan *footer* yang di-hardcode di dalamnya, serta tidak memiliki *sidebar*.
   - **Desain Baru**: Membutuhkan struktur layout dengan Sidebar di kiri (yang bisa *collapse* / disembunyikan menggunakan ikon hamburger) dan Top Navbar di atas.
   - **Solusi**: Kita perlu membuat file layout khusus admin (`app/Views/layout/admin_layout.php`) sehingga struktur Sidebar dan Navbar ini bisa digunakan bersama oleh halaman admin lainnya (seperti Manajemen Versi, Doodle, Crawler, dan Karyawan).

2. **Top Navbar Baru**:
   - Kiri: Ikon Hamburger (untuk *toggle sidebar*) dan Logo teks "GRACIA" (italic, bold).
   - Kanan: Tanggal hari ini (contoh: "Selasa, 14/07/2026"), ikon profil/avatar pengguna, dan ikon *logout*.

3. **Sidebar (Auto-hide / Collapsible)**:
   - Header kecil: "MENU UTAMA"
   - Daftar Menu beserta ikon:
     - Dashboard (ikon spedometer)
     - **Mesin Pencari** (ikon teropong/search) -> Ini adalah menu aktif saat berada di route `/admin`.
     - Crawler (ikon database)
     - Doodle (ikon gambar)
   - Separator/Garis pembatas
   - Menu Bawah: "Ke Beranda" (ikon panah/keluar)

4. **Perubahan Konten Utama (`admin.php`)**:
   - Navbar atas bawaan dan Footer akan dihapus karena sudah ditangani oleh `admin_layout.php`.
   - Tab **Doodle** akan **DIPINDAHKAN** dari halaman ini. Dalam desain baru, "Doodle" memiliki menu sendiri di sidebar, bukan lagi berupa tab bersebelahan dengan "Situs" dan "Gambar".
   - Tab yang tersisa di halaman `/admin` (Mesin Pencari) hanyalah **Situs** dan **Gambar**.
   - Tata letak tabel dan penomoran halaman (pagination) tidak mengalami perubahan signifikan.

## Open Questions
- Karena tab **Doodle** dipindahkan ke Sidebar, apakah saya harus membuat *route* baru (contoh: `/admin/doodle`) dan *view* baru (`admin/doodle.php`) khusus untuk mengelola Doodle, dengan memindahkan kode Doodle dari `admin.php` ke sana?
- Untuk fitur *auto-hide* sidebar, apakah Anda lebih suka menggunakan komponen standar Bootstrap `offcanvas` (yang muncul melayang dari samping), atau menggunakan CSS kustom dengan *Flexbox* (di mana sidebar menggeser konten utama saat dibuka)?

## Proposed Changes

### 1. Membuat Layout Khusus Admin
#### [NEW] admin_layout.php (file:///192.168.1.4/www/gkr_myid/app/Views/layout/admin_layout.php)
- Membuat struktur dasar HTML (melanjutkan atau mewarisi `layout/main.php` atau berdiri sendiri).
- Menambahkan Top Navbar dengan tombol toggle.
- Menambahkan Sidebar dengan *state* CSS yang bisa dilipat (menggunakan sedikit JavaScript untuk toggle class).
- Mendefinisikan `<?= $this->renderSection('content') ?>` di sebelah kanan sidebar.

### 2. Mengubah Halaman Utama Admin
#### [MODIFY] admin.php (file:///192.168.1.4/www/gkr_myid/app/Views/admin/admin.php)
- Mengubah `extend` dari `layout/main` menjadi `layout/admin_layout`.
- Menghapus blok `<nav>` dan `<footer>` internal.
- Menghapus tab "Doodle" dari daftar tab.

### 3. Ekstraksi Fitur Doodle (Jika Disetujui)
#### [NEW] doodle.php (file:///192.168.1.4/www/gkr_myid/app/Views/admin/doodle.php)
- Memindahkan kode tabel, modal, dan logika UI terkait manajemen Doodle dari `admin.php` ke file terpisah.
#### [MODIFY] Routes & Controller
- Mendaftarkan *route* `/admin/doodle` ke controller yang sesuai.

## Verification Plan
1. Membuat layout baru dan menyambungkannya.
2. Membuka `/admin` di browser untuk memastikan sidebar dapat di-klik untuk membuka/menutup dengan mulus.
3. Memastikan semua tautan sidebar mengarah ke alamat yang benar.
4. Mengecek tampilan mobile responsif.
