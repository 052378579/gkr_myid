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

    /**
     * Mengambil doodle yang sedang aktif (berdasarkan range tanggal hari ini).
     *
     * @return array|null Mengembalikan 1 record doodle aktif (array) atau null jika tidak ada.
     */
    public function getActiveDoodle()
    {
        $today = date('Y-m-d');
        return $this->where('status', 'aktif')
                    ->where('tgl_mulai <=', $today)
                    ->where('tgl_selesai >=', $today)
                    ->orderBy('created_at', 'DESC') // Prioritaskan doodle yang paling baru dibuat jika ada tumpang tindih
                    ->first();
    }
}
