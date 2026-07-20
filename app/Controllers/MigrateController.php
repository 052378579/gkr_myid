<?php
namespace App\Controllers;

class MigrateController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();
        
        $forge->dropTable('gkr_loguser', true);
        $forge->dropTable('gkr_logcari', true);
        
        $sqlLogUser = "
        CREATE TABLE IF NOT EXISTS `gkr_loguser` (
          `id_log` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `id_user` int(11) DEFAULT NULL,
          `aktivitas` enum('masuk','keluar','gagal_masuk') NOT NULL,
          `alamat_ip` varchar(45) NOT NULL,
          `agen_pengguna` varchar(512) DEFAULT NULL,
          `waktu` datetime DEFAULT current_timestamp(),
          PRIMARY KEY (`id_log`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        $sqlLogCari = "
        CREATE TABLE IF NOT EXISTS `gkr_logcari` (
          `id_log` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `id_user` int(11) DEFAULT NULL,
          `tipe_pencarian` enum('gambar','situs','teks') NOT NULL,
          `kata_kunci` varchar(255) NOT NULL,
          `jumlah_hasil` int(11) NOT NULL DEFAULT 0,
          `alamat_ip` varchar(45) DEFAULT NULL,
          `waktu` datetime DEFAULT current_timestamp(),
          PRIMARY KEY (`id_log`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $db->query($sqlLogUser);
            $db->query($sqlLogCari);
            echo "Migrasi SANGAT SUKSES! Tabel gkr_loguser dan gkr_logcari telah diciptakan ulang secara murni melalui Query Raw (alamat_ip, waktu).";
        } catch (\Throwable $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
