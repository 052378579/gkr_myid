<?php

namespace App\Models;

use CodeIgniter\Model;

class LogCariModel extends Model
{
    protected $table = 'gkr_logcari';
    protected $primaryKey = 'id_log';
    protected $allowedFields = ['id_user', 'tipe_pencarian', 'kata_kunci', 'jumlah_hasil', 'alamat_ip', 'source', 'waktu'];

    public function catatPencarian($idUser, $tipePencarian, $kataKunci, $jumlahHasil, $alamatIp, $source = 'Web')
    {
        return $this->insert([
            'id_user'        => $idUser,
            'tipe_pencarian' => $tipePencarian,
            'kata_kunci'     => $kataKunci,
            'jumlah_hasil'   => $jumlahHasil,
            'alamat_ip'      => $alamatIp,
            'source'         => $source
        ]);
    }

    public function getWordCloudData($limit = 100)
    {
        return $this->select('kata_kunci, COUNT(*) as frekuensi')
                    ->where('tipe_pencarian', 'teks')
                    ->groupBy('kata_kunci')
                    ->orderBy('frekuensi', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
