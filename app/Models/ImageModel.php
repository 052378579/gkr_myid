<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ModelGambar (ImageModel)
 * Mengelola data gambar/foto pada tabel 'cari_images'.
 * Menjamin validitas tautan gambar statis dan status kerusakan tautan (broken link).
 */
class ImageModel extends Model
{
    protected $table            = 'cari_images';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Konfigurasi Soft Deletes & Timestamps
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $createdField     = '';
    protected $updatedField     = '';
    protected $deletedField     = 'deleted_at';
    protected $dateFormat       = 'datetime';
    
    protected $protectFields    = true;
    protected $allowedFields    = ['siteUrl', 'imageUrl', 'alt', 'title', 'clicks', 'broken'];

    // Validasi Integritas Gambar
    protected $validationRules = [
        'siteUrl'  => 'required',
        'imageUrl' => 'required'
    ];
    
    protected $validationMessages = [
        'siteUrl' => [
            'required'  => 'URL sumber situs wajib disertakan.'
        ],
        'imageUrl' => [
            'required'  => 'URL gambar wajib disertakan.'
        ]
    ];

    /**
     * Mendapatkan daftar gambar yang paling banyak diklik.
     * Mengembalikan 10 data teratas secara default.
     */
    public function getTopClickedImages($limit = 10)
    {
        return $this->select('id, title, imageUrl, siteUrl, clicks')
                    ->orderBy('clicks', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Mendapatkan daftar gabungan situs dan gambar yang paling banyak diklik.
     */
    public function getTopCombinedClicks($limit = 10)
    {
        $db = \Config\Database::connect();
        
        $sql = "
            (
                SELECT 
                    'site' AS tipe, 
                    id, 
                    title, 
                    url AS link_tujuan, 
                    clicks,
                    NULL AS imageUrl
                FROM cari_sites 
                WHERE deleted_at IS NULL
            )
            UNION ALL
            (
                SELECT 
                    'image' AS tipe, 
                    id, 
                    title, 
                    siteUrl AS link_tujuan, 
                    clicks,
                    imageUrl
                FROM cari_images 
                WHERE deleted_at IS NULL
            )
            ORDER BY clicks DESC
            LIMIT ?
        ";

        return $db->query($sql, [(int)$limit])->getResultArray();
    }
}
