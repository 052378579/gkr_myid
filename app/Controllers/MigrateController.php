<?php
namespace App\Controllers;

class MigrateController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        try {
            // 1. Rename kolom ke Bahasa Indonesia jika masih menggunakan nama lama
            try {
                $db->query("ALTER TABLE `gkr_cari` 
                    CHANGE COLUMN `title` `judul` VARCHAR(255) NOT NULL,
                    CHANGE COLUMN `description` `deskripsi` TEXT DEFAULT NULL,
                    CHANGE COLUMN `keywords` `kata_kunci` VARCHAR(512) DEFAULT NULL,
                    CHANGE COLUMN `clicks` `klik` INT(11) NOT NULL DEFAULT 0,
                    CHANGE COLUMN `broken` `rusak` TINYINT(1) NOT NULL DEFAULT 0;");
            } catch (\Throwable $e) {
                // Kolom sudah diubah nama sebelumnya
            }

            // 2. Perbarui indeks
            try {
                $db->query("ALTER TABLE `gkr_cari` DROP INDEX `idx_clicks`;");
            } catch (\Throwable $e) {}
            
            try {
                $db->query("ALTER TABLE `gkr_cari` DROP INDEX `idx_broken`;");
            } catch (\Throwable $e) {}

            try {
                $db->query("ALTER TABLE `gkr_cari` DROP INDEX `ft_pencarian`;");
            } catch (\Throwable $e) {}

            try {
                $db->query("ALTER TABLE `gkr_cari` ADD KEY `idx_klik` (`klik`);");
            } catch (\Throwable $e) {}

            try {
                $db->query("ALTER TABLE `gkr_cari` ADD KEY `idx_rusak` (`rusak`);");
            } catch (\Throwable $e) {}

            try {
                $db->query("ALTER TABLE `gkr_cari` ADD FULLTEXT KEY `ft_pencarian` (`judul`, `kata_kunci`, `alt`, `deskripsi`);");
            } catch (\Throwable $e) {}

            echo "Migrasi Penyelarasan Kolom Bahasa Indonesia SANGAT SUKSES! Kolom gkr_cari (judul, deskripsi, kata_kunci, klik, rusak) telah diperbarui.";
        } catch (\Throwable $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
