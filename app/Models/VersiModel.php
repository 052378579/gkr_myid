<?php

namespace App\Models;

use CodeIgniter\Model;

class VersiModel extends Model
{
    protected $table            = 'gkr_versi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['versi', 'tanggal_rilis', 'judul', 'deskripsi', 'improvements', 'fixes', 'patches'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    
    // Custom method to run initial setup if needed
    public function setupTable()
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('gkr_versi')) {
            $sql = "CREATE TABLE gkr_versi (
                id INT AUTO_INCREMENT PRIMARY KEY,
                versi VARCHAR(20) NOT NULL COMMENT 'Nomor rilis',
                tanggal_rilis DATE NOT NULL COMMENT 'Tanggal rilis',
                judul VARCHAR(255) NOT NULL COMMENT 'Sorotan utama dari rilis',
                deskripsi TEXT COMMENT 'Ringkasan deskripsi',
                improvements JSON COMMENT 'Array fitur baru',
                fixes JSON COMMENT 'Array perbaikan bug',
                patches JSON COMMENT 'Array perbaikan minor',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );";
            $db->query($sql);
            
            // Insert dummy data
            $sql2 = "INSERT INTO gkr_versi (versi, tanggal_rilis, judul, deskripsi, improvements, fixes, patches) 
            VALUES (
                '1.1.0', 
                '2026-07-08', 
                'Optimasi Performa & Struktur Modular', 
                'Pembaruan ini berfokus pada efisiensi memori aplikasi dan pemisahan file web agar lebih modular.',
                '[\"Memisahkan skrip styling (style.css) dan JavaScript (script.js) dari index utama\", \"Menambahkan kustomisasi ikon Doodle untuk halaman beranda\"]',
                '[\"Menyesuaikan atribut package menjadi com.budi.gracia pada Android Manifest untuk mengatasi error build\", \"Menyesuaikan dependensi agar ukuran build APK lebih minimal\"]',
                '[]'
            );";
            $db->query($sql2);
        }
    }
}
