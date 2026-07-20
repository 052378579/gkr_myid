<?php

namespace App\Models;

use CodeIgniter\Model;

class LogCariModel extends Model
{
    protected $table = 'gkr_logcari';
    protected $primaryKey = 'id_log';
    protected $allowedFields = ['id_user', 'tipe_pencarian', 'kata_kunci', 'jumlah_hasil', 'alamat_ip', 'waktu'];

    public function catatPencarian($idUser, $tipePencarian, $kataKunci, $jumlahHasil, $alamatIp)
    {
        return $this->insert([
            'id_user'        => $idUser,
            'tipe_pencarian' => $tipePencarian,
            'kata_kunci'     => $kataKunci,
            'jumlah_hasil'   => $jumlahHasil,
            'alamat_ip'      => $alamatIp
        ]);
    }
}
