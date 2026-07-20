<?php

namespace App\Models;

use CodeIgniter\Model;

class LogUserModel extends Model
{
    protected $table = 'gkr_loguser';
    protected $primaryKey = 'id_log';
    protected $allowedFields = ['id_user', 'aktivitas', 'alamat_ip', 'agen_pengguna', 'waktu'];

    public function catatAktivitas($idUser, $aktivitas, $alamatIp, $agenPengguna)
    {
        return $this->insert([
            'id_user'       => $idUser,
            'aktivitas'     => $aktivitas,
            'alamat_ip'     => $alamatIp,
            'agen_pengguna' => $agenPengguna
        ]);
    }
}
