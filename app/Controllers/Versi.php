<?php

namespace App\Controllers;

class Versi extends BaseController
{
    private $jsonFile = FCPATH . 'versi.json';

    public function index()
    {
        // 1. Injeksi / Seed Data jika file belum ada
        if (!file_exists($this->jsonFile)) {
            $this->seedInitialData();
        }

        // 2. Baca file JSON
        $jsonContent = file_get_contents($this->jsonFile);
        $parsedData = json_decode($jsonContent, true);
        
        // 3. Ambil sub-elemen "data"
        $versiData = isset($parsedData['data']) ? $parsedData['data'] : [];

        // 4. Urutkan berdasarkan tanggal_rilis (terbaru di atas)
        usort($versiData, function($a, $b) {
            return strtotime($b['tanggal_rilis']) - strtotime($a['tanggal_rilis']);
        });

        // 5. Pemformatan Tanggal Tambahan untuk UI
        foreach ($versiData as &$row) {
            if (!empty($row['tanggal_rilis'])) {
                $row['tanggal_rilis_formatted'] = date('d/m/Y', strtotime($row['tanggal_rilis']));
            } else {
                $row['tanggal_rilis_formatted'] = '';
            }
            // improvements, fixes, dan patches sudah berbentuk Array dari JSON murni
        }

        // Paginasi Logika
        $page = $this->request->getVar('page') ?? 1;
        $page = (int)$page;
        if ($page < 1) $page = 1;
        
        $perPage = 3;
        $total = count($versiData);
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        
        $pagedData = array_slice($versiData, $offset, $perPage);

        return view('versi', [
            'changelog' => $pagedData,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    private function seedInitialData()
    {
        $data = [
            'nama_file' => 'versi.json',
            'nama_proyek' => 'gkr_myid',
            'tanggal_diperbarui' => date('d-m-Y H:i:s') . ' WIB',
            'versi_diperbarui' => date('0.n.d'),
            'data' => [
                [
                    'id' => 1,
                    'versi' => '0.6.9',
                    'tanggal_rilis' => '2026-07-08',
                    'judul' => 'Optimasi Performa & Struktur Modular',
                    'deskripsi' => 'Pembaruan ini berfokus pada efisiensi memori aplikasi dan pemisahan file web agar lebih modular.',
                    'improvements' => [],
                    'fixes' => [],
                    'patches' => [],
                    'created_at' => '2026-07-08 06:17:12'
                ],
                [
                    'id' => 2,
                    'versi' => '0.6.10',
                    'tanggal_rilis' => '2026-07-10',
                    'judul' => 'Pembaruan Modul Pencarian & Crawler',
                    'deskripsi' => 'Optimasi tata letak gambar, perbaikan pada pencarian, serta penanganan error pada Crawler.',
                    'improvements' => [
                        'URL hasil pencarian dinamis.',
                        'Menerapkan CSS Flexbox fallback pada grid gambar untuk menghilangkan efek tampilan melompat (FOUC).'
                    ],
                    'fixes' => [
                        'Memperbaiki bug presedensi kueri di Search.php penyebab endless-loop pada gambar rusak (broken images).',
                        'Memperbaiki Crawler.php menangkap dan mengembalikan pesan error asli saat proses Reset Database gagal.'
                    ],
                    'patches' => [
                        'Menerapkan parameter cache-buster pada skrip crawl.js agar peramban selalu memuat file terbaru.',
                        'Memperbaiki pewarnaan link CSS judul gambar agar selalu konsisten berwarna putih saat di-hover maupun setelah diklik (visited).'
                    ],
                    'created_at' => '2026-07-10 07:41:08'
                ],
                [
                    'id' => 3,
                    'versi' => '0.6.11',
                    'tanggal_rilis' => '2026-07-11',
                    'judul' => 'Penyempurnaan Arsitektur RESTful API & Modul Admin',
                    'deskripsi' => 'Pembaruan versi 0.6.11 berfokus pada transisi penuh ke arsitektur RESTful API, penyempurnaan UI/UX, dan pembaruan spesifik pada modul dasbor Admin (termasuk fitur Doodle).',
                    'improvements' => [
                        'Penyempurnaan arsitektur RESTful API terpusat (GET, POST, PUT, DELETE)',
                        'Standardisasi template layout (app/Views/layout)',
                        'Pengaturan environment dinamis pada file .env',
                        'Integrasi Vue.js 3 dan desain Glassmorphism dengan Bootstrap 5.3',
                        'Pemetaan dinamis URL Crawler direktori lokal'
                    ],
                    'fixes' => [
                        'Konfigurasi header CORS di endpoint API',
                        'Konsistensi penggunaan Bahasa Indonesia di seluruh basis kode',
                        'Penanganan otomatis broken links (Error 404/500) pada gambar'
                    ],
                    'patches' => [
                        'Penambahan fitur Recycle Bin (Soft Delete) di dasbor admin',
                        'Pembaruan logika dan UI pada modul DoodleController dan admin.js',
                        'Persiapan implementasi tema gelap (Dark Mode)'
                    ],
                    'created_at' => '2026-07-10 19:39:34'
                ],
                [
                    'id' => 4,
                    'versi' => '0.6.13',
                    'tanggal_rilis' => '2026-07-13',
                    'judul' => 'Modul LHP & RND Sample PO',
                    'deskripsi' => 'Penambahan fitur Laporan Harian Pegawai dengan proteksi sesi otentikasi serta modul RND Sample Purchase Order dengan arsitektur Multi-Items per PO.',
                    'improvements' => [
                        'Sistem otentikasi sesi /login',
                        'Rute LHP dengan kustomisasi visual',
                        'Rute RND Sample PO Multi-Item'
                    ],
                    'fixes' => [
                        'Memperbaiki lingkup kompilasi Vue pada Modal Box',
                        'Melepas batasan UNIQUE KEY pada nomor_po'
                    ],
                    'patches' => [],
                    'created_at' => '2026-07-13 03:54:28'
                ],
                [
                    'id' => 5,
                    'versi' => '0.7.15',
                    'tanggal_rilis' => '2026-07-15',
                    'judul' => 'Penyempurnaan Keamanan Admin & Kualitas Antarmuka UI',
                    'deskripsi' => 'Rilis ini memboyong arsitektur keamanan tingkat Super Admin, rombakan sistem modal dinamis pada dasbor, perbaikan anomali pemuatan file skrip karena agresivitas tembolok peramban, serta pemolesan berbagai elemen visual (seperti Dropdown Kaskade dan marker Kalender).',
                    'improvements' => [
                        'Sistem keamanan Role-Based Access Control (RBAC) melalui SuperAdminFilter untuk melindungi area panel admin',
                        'Evolusi Formulir Edit Situs dari kotak statis SweetAlert menjadi Bootstrap Native Modal berlajur dua',
                        'Logika Dropdown Kaskade dinamis untuk mengambil material dan warna langsung dari tabel gkr_material',
                        'Integrasi profil pengguna bergaya Dropdown Menu di pojok kanan beranda',
                        'Otomatisasi pewarnaan merah (danger) untuk akhir pekan pada penanda kalender beranda'
                    ],
                    'fixes' => [
                        'Implementasi metode Cache Busting secara meluas dengan menanamkan parameter waktu (?v=time()) pada tag script, sukses memberantas isu Browser Caching yang menahan skrip lama'
                    ],
                    'patches' => [],
                    'created_at' => '2026-07-14 15:06:59'
                ],
                [
                    'id' => 6,
                    'versi' => '0.7.17',
                    'tanggal_rilis' => '2026-07-16',
                    'judul' => 'Era Kecerdasan Buatan: Pencarian Visual & Terminal Streaming',
                    'deskripsi' => 'Pembaruan paling revolusioner tahun ini yang mengintegrasikan Mesin Pelatih AI Python ke dalam kerangka CodeIgniter 4, memungkinkan pencarian visual cerdas dan pengawasan terminal secara real-time.',
                    'improvements' => [
                        'Integrasi MobileNetV3 PyTorch dan Basis Data FAISS untuk fitur pencarian Kecocokan Visual',
                        'Pembangunan Dasbor AI Trainer Engine (/crawl/ai) dengan antarmuka Live Terminal bergaya Hacker',
                        'Pendirian arsitektur layanan mikro Python (FastAPI) internal di port 5000',
                        'Implementasi metode Continuous HTTP Streaming dengan ReadableStream Vue.js',
                        'Perombakan 8 pilar dokumen arsitektur (PRD, Memory, dsb) untuk merefleksikan pergeseran ekosistem AI'
                    ],
                    'fixes' => [
                        'Mematahkan hambatan birokrasi Hak Akses Linux melalui operasi proxy internal FastAPI',
                        'Menghilangkan fenomena Silent Timeout dengan manipulasi set_time_limit(0) dan parameter unbuffered',
                        'Penyesuaian ekspresi reguler pada AI Trainer untuk mengindeks 100% korpus gambar tanpa syarat format nama (No-FG Indexing)'
                    ],
                    'patches' => [
                        'Evolusi tata bahasa antarmuka AI menjadi lebih elegan (Sistem merekomendasikan -> Kecocokan visual)',
                        'Penambahan logo ikon kamera pada navigasi tab hasil pencarian'
                    ],
                    'created_at' => '2026-07-16 15:38:30'
                ],
                [
                    'id' => 7,
                    'versi' => '0.7.18',
                    'tanggal_rilis' => '2026-07-18',
                    'judul' => 'Evolusi Kecerdasan Agregasi Multi-Sudut & Standarisasi Dokumentasi Arsitektur',
                    'deskripsi' => 'Rilis ini memboyong perombakan algoritma fundamental (Harmonisasi Regex AI) yang memungkinkan sistem secara cerdas mengenali dan menyatukan foto produk dari ragam sudut kamera (depan, samping, perspektif, dan kode variasi B/C/D/E) menjadi satu identitas visual yang utuh. Cakupan radar mesin perayap (Crawler) juga sukses diekspansi hingga ranah direktori /WEB, dibarengi dengan penyusunan ulang 8 pilar dokumentasi proyek guna menjamin kemudahan pemeliharaan oleh tim pengembang di masa depan.',
                    'improvements' => [
                        'Injeksi Kecerdasan Agregasi Identitas Visual Multi-Sudut (Varian B/C/D/E) menggunakan mesin Regex tangguh yang selaras antara sistem Python AI dan PHP Web Crawler (The Harmonization Principle).',
                        'Ekspansi jangkauan penyisiran mesin perayap otomatis dan Pelatih AI untuk memindai seluruh direktori katalog /WEB.',
                        'Standarisasi 8 dokumen arsitektur inti (PRD, Skills, Memory, Task, StyleGuide, README, Tautan, dan struktur_folder) secara komprehensif agar mencapai tingkat Enterprise.'
                    ],
                    'fixes' => [
                        'Penyempurnaan logika pemotongan karakter (Regex) untuk meredam dan mentoleransi error akibat anomali salah ketik (typo) seperti spasi ganda atau tanda hubung beruntun pada nama file.'
                    ],
                    'patches' => [],
                    'created_at' => '2026-07-18 09:28:29'
                ],
                [
                    'id' => 8,
                    'versi' => '0.7.19',
                    'tanggal_rilis' => '2026-07-19',
                    'judul' => 'Harmonisasi Dokumentasi Proyek & Optimasi Penelusuran AI',
                    'deskripsi' => 'Pembaruan ini merangkum penyelarasan seluruh dokumen spesifikasi arsitektur perangkat lunak (PRD, Memory, dsb) secara komprehensif, serta konsolidasi direktori pemindaian AI untuk mencegah duplikasi indeks memori.',
                    'improvements' => [
                        'Penyusunan menyeluruh 5 Dokumen Inti Proyek (PRD, Memory, Skills, Task, StyleGuide)',
                        'Sinkronisasi dokumen README, Tautan, dan Struktur Folder dengan kapabilitas terbaru arsitektur AI'
                    ],
                    'fixes' => [
                        'Penyatuan folder /WEB ke dalam target pemindaian utama untuk mencegah bug duplikasi memori vektor',
                        'Validasi keselarasan kredensial database dan konfigurasi daemon Systemd antara DEV dan PROD'
                    ],
                    'patches' => [],
                    'created_at' => '2026-07-19 00:28:12'
                ],
                [
                    'id' => 9,
                    'versi' => '0.7.20',
                    'tanggal_rilis' => '2026-07-20',
                    'judul' => 'Integrasi Dasbor Admin, Validasi Karyawan & Perbaikan IP Tracking',
                    'deskripsi' => 'Pembaruan masif berfokus pada pengalaman administrasi (Admin Layout), pengamanan data input karyawan, penyempurnaan tabel Log Audit, serta perbaikan sistem deteksi proksi IP pengunjung.',
                    'improvements' => [
                        'Menyelaraskan halaman Manajemen Karyawan ke dalam Admin Layout dengan akses navigasi lewat Sidebar',
                        'Mengubah kotak teks bebas Divisi menjadi menu Dropdown (Marketing, Produksi, RND) untuk mencegah typo',
                        'Menambahkan tautan rahasia Super Admin pada menu dropdown profil pengguna',
                        'Memindahkan endpoint Pelatih AI ke /admin/ai demi kebersihan hierarki',
                        'Penyelarasan 8 dokumen Knowledge Base Proyek secara total'
                    ],
                    'fixes' => [
                        'Memperbaiki bug deteksi IP 127.0.0.1 pada sistem Log dengan mengaktifkan array asosiatif X-Forwarded-For pada proxyIPs CodeIgniter'
                    ],
                    'patches' => [
                        'Eksekusi TRUNCATE pada tabel cari_sites untuk mereset riwayat pencarian lawas'
                    ],
                    'created_at' => '2026-07-20 07:35:01'
                ]
            ]
        ];

        file_put_contents($this->jsonFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}
