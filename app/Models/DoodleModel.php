<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ModelDoodle (DoodleModel)
 * Mengelola data logo tematik harian pada tabel 'gkr_doodle'.
 */
class DoodleModel extends Model
{
    protected $table            = 'gkr_doodle';
    protected $primaryKey       = 'id_doodle';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Konfigurasi Timestamps
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    
    // Kolom yang diizinkan
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event', 
        'gambar', 
        'tgl_mulai', 
        'tgl_selesai', 
        'status'
    ];

    // Validasi Integritas Tema
    protected $validationRules = [
        'event'       => 'required|min_length[3]',
        'gambar'      => 'required',
        'status'      => 'required|in_list[aktif,tidak_aktif]',
        'tgl_mulai'   => 'required|valid_date',
        'tgl_selesai' => 'required|valid_date'
    ];
    
    protected $validationMessages = [
        'event' => [
            'required'   => 'Nama event wajib diisi.',
            'min_length' => 'Nama event terlalu pendek.'
        ],
        'gambar' => [
            'required' => 'File gambar wajib disertakan.'
        ],
        'status' => [
            'required' => 'Status wajib dipilih.',
            'in_list'  => 'Status tidak valid.'
        ],
        'tgl_mulai' => [
            'required'   => 'Tanggal mulai wajib diisi.',
            'valid_date' => 'Format tanggal mulai salah.'
        ],
        'tgl_selesai' => [
            'required'   => 'Tanggal selesai wajib diisi.',
            'valid_date' => 'Format tanggal selesai salah.'
        ]
    ];
}
