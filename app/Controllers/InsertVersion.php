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
            'versi' => '0.6.15',
            'tanggal_rilis' => '2026-07-15',
            'judul' => 'Penyempurnaan Keamanan Admin & Kualitas Antarmuka UI',
            'deskripsi' => 'Rilis ini memboyong arsitektur keamanan tingkat Super Admin, rombakan sistem modal dinamis pada dasbor, perbaikan anomali pemuatan file skrip karena agresivitas tembolok peramban, serta pemolesan berbagai elemen visual (seperti Dropdown Kaskade dan marker Kalender).',
            'improvements' => json_encode([
                "Sistem keamanan Role-Based Access Control (RBAC) melalui SuperAdminFilter untuk area panel admin",
                "Evolusi Formulir Edit Situs dari kotak statis SweetAlert menjadi Bootstrap Native Modal berlajur dua",
                "Logika Dropdown Kaskade dinamis untuk mengambil material dan warna langsung dari tabel gkr_material",
                "Integrasi profil pengguna bergaya Dropdown Menu di pojok kanan beranda",
                "Otomatisasi pewarnaan merah (danger) untuk akhir pekan pada penanda kalender beranda"
            ]),
            'fixes' => json_encode([
                "Implementasi metode Cache Busting secara meluas dengan menanamkan parameter waktu pada tag script, sukses memberantas isu Browser Caching yang menahan skrip lama"
            ]),
            'patches' => json_encode([])
        ];

        $builder->insert($data);
        return $this->respondCreated(['status' => 'sukses', 'pesan' => 'Versi 0.6.15 berhasil diinput!']);
    }
}
