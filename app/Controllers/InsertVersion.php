<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;

class InsertVersion extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('gkr_versi');
        
        $data = [
            'versi' => '0.7.18',
            'tanggal_rilis' => '2026-07-18',
            'judul' => 'Evolusi Kecerdasan Agregasi Multi-Sudut & Standarisasi Dokumentasi Arsitektur',
            'deskripsi' => 'Rilis ini memboyong perombakan algoritma fundamental (Harmonisasi Regex AI) yang memungkinkan sistem secara cerdas mengenali dan menyatukan foto produk dari ragam sudut kamera (depan, samping, perspektif, dan kode variasi B/C/D/E) menjadi satu identitas visual yang utuh. Cakupan radar mesin perayap (Crawler) juga sukses diekspansi hingga ranah direktori /WEB, dibarengi dengan penyusunan ulang 8 pilar dokumentasi proyek guna menjamin kemudahan pemeliharaan oleh tim pengembang di masa depan.',
            'improvements' => json_encode([
                "Injeksi Kecerdasan Agregasi Identitas Visual Multi-Sudut (Varian B/C/D/E) menggunakan mesin Regex tangguh yang selaras antara sistem Python AI dan PHP Web Crawler (The Harmonization Principle).",
                "Ekspansi jangkauan penyisiran mesin perayap otomatis dan Pelatih AI untuk memindai seluruh direktori katalog /WEB.",
                "Standarisasi 8 dokumen arsitektur inti (PRD, Skills, Memory, Task, StyleGuide, README, Tautan, dan struktur_folder) secara komprehensif agar mencapai tingkat Enterprise."
            ]),
            'fixes' => json_encode([
                "Penyempurnaan logika pemotongan karakter (Regex) untuk meredam dan mentoleransi error akibat anomali salah ketik (typo) seperti spasi ganda atau tanda hubung beruntun pada nama file."
            ]),
            'patches' => json_encode([])
        ];

        $builder->insert($data);
        return $this->respondCreated(['status' => 'sukses', 'pesan' => 'Versi 0.7.18 berhasil diinput!']);
    }
}
