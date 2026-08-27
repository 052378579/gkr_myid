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
        // Paksa ke format IPv4 murni (Hapus prefix ::ffff: dan localhost ipv6)
        if (strpos($alamatIp, '::ffff:') === 0) {
            $alamatIp = substr($alamatIp, 7);
        } elseif ($alamatIp === '::1') {
            $alamatIp = '127.0.0.1';
        }

        return $this->insert([
            'id_user'       => $idUser,
            'aktivitas'     => $aktivitas,
            'alamat_ip'     => $alamatIp,
            'agen_pengguna' => $agenPengguna
        ]);
    }
}
