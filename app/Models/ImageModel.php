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
}
