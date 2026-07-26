<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ModelPengguna (UserModel)
 * Mengelola data autentikasi dan profil admin pada tabel 'gkr_users'.
 */
class UserModel extends Model
{
    protected $table            = 'gkr_users';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Konfigurasi Timestamps (created_at dan updated_at)
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    
    // Kolom yang diizinkan untuk dimanipulasi
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_lengkap', 
        'no_hp', 
        'divisi', 
        'foto_profil', 
        'access_token', 
        'last_ip', 
        'user_agent',
        'status',
        'telegram_chat_id'
    ];

    // Validasi Integritas Akun Admin
    protected $validationRules = [
        'nama_lengkap' => 'required|min_length[3]',
        'no_hp'        => 'required|numeric|is_unique[gkr_users.no_hp,id_user,{id_user}]',
        'divisi'       => 'required',
        'status'       => 'required|in_list[pending,aktif,suspend]'
    ];
    
    protected $validationMessages = [
        'nama_lengkap' => [
            'required'   => 'Nama lengkap wajib diisi.',
            'min_length' => 'Nama lengkap minimal 3 karakter.'
        ],
        'no_hp' => [
            'required'  => 'Nomor HP wajib diisi.',
            'numeric'   => 'Nomor HP hanya boleh berisi angka.',
            'is_unique' => 'Nomor HP ini sudah terdaftar.'
        ],
        'divisi' => [
            'required' => 'Divisi wajib diisi.'
        ]
    ];
}
