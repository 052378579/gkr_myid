<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ModelCari (CariModel)
 * Bertanggung jawab penuh untuk mengelola operasi database pada tabel fisik 'gkr_cari' (Kolom Bahasa Indonesia).
 * Menggunakan nama kolom: judul, deskripsi, kata_kunci, klik, rusak.
 */
class CariModel extends Model
{
    protected $table            = 'gkr_cari';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Konfigurasi Soft Deletes & Timestamps
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    protected $dateFormat       = 'datetime';
    
    protected $protectFields    = true;
    protected $allowedFields    = [
        'judul',
        'alt',
        'deskripsi',
        'url',
        'imageUrl',
        'siteUrl',
        'kata_kunci',
        'kode_bom',
        'klik',
        'rusak',
        'deleted_at'
    ];

    // Validasi Keamanan & Integritas Data
    protected $validationRules = [
        'id'    => 'permit_empty|is_natural_no_zero',
        'judul' => 'required|min_length[2]'
    ];
    
    protected $validationMessages = [
        'judul' => [
            'required'   => 'Judul entitas wajib diisi.',
            'min_length' => 'Judul entitas terlalu pendek (minimal 2 karakter).'
        ]
    ];

    /**
     * Mendapatkan daftar entitas situs atau gambar yang paling banyak diklik.
     * $type = 'situs' | 'gambar' | null
     */
    public function getTopClickedEntities($type = null, $limit = 10)
    {
        $builder = $this->select('id, judul, url, imageUrl, siteUrl, klik')
                        ->orderBy('klik', 'DESC')
                        ->limit($limit);

        if ($type === 'situs') {
            $builder->groupStart()->where('imageUrl IS NULL')->orWhere('imageUrl', '')->groupEnd();
        } elseif ($type === 'gambar') {
            $builder->where('imageUrl IS NOT NULL')->where('imageUrl !=', '');
        }

        return $builder->findAll();
    }
}
