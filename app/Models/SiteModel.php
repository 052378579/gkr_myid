<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ModelSitus (SiteModel)
 * Bertanggung jawab penuh untuk mengelola operasi database pada tabel 'cari_sites'.
 * Meliputi validasi URL dan penanganan Soft Deletes.
 */
class SiteModel extends Model
{
    protected $table            = 'cari_sites';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Konfigurasi Soft Deletes & Timestamps
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true; // Diaktifkan untuk mendukung deleted_at
    protected $createdField     = '';   // Dikosongkan karena tidak ada di DB
    protected $updatedField     = '';   // Dikosongkan karena tidak ada di DB
    protected $deletedField     = 'deleted_at';
    protected $dateFormat       = 'datetime';
    
    protected $protectFields    = true;
    protected $allowedFields    = ['url', 'title', 'description', 'keywords', 'clicks'];

    // Validasi Keamanan API (Mencegah input kotor)
    protected $validationRules = [
        'url'   => 'required|is_unique[cari_sites.url,id,{id}]',
        'title' => 'required|min_length[3]'
    ];
    
    protected $validationMessages = [
        'url' => [
            'required'  => 'URL situs wajib diisi.',

            'is_unique' => 'URL situs sudah terdaftar di pangkalan data.'
        ],
        'title' => [
            'required'   => 'Judul situs wajib diisi.',
            'min_length' => 'Judul situs terlalu pendek (minimal 3 karakter).'
        ]
    ];

    /**
     * Mendapatkan daftar situs yang paling banyak diklik.
     * Mengembalikan 10 data teratas secara default.
     */
    public function getTopClickedSites($limit = 10)
    {
        return $this->select('id, title, url, clicks')
                    ->orderBy('clicks', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
