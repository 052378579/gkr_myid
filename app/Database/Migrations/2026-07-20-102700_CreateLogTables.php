<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogTables extends Migration
{
    public function up()
    {
        // 1. Tabel Log User (Login & Logout)
        $this->forge->addField([
            'id_log' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'aktivitas' => [
                'type'       => 'ENUM',
                'constraint' => ['masuk', 'keluar', 'gagal_masuk'],
            ],
            'alamat_ip' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
            ],
            'agen_pengguna' => [
                'type'       => 'VARCHAR',
                'constraint' => '512',
                'null'       => true,
            ],
            'waktu' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id_log', true);
        $this->forge->createTable('gkr_loguser', true);

        // 2. Tabel Log Pencarian
        $this->forge->addField([
            'id_log' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'tipe_pencarian' => [
                'type'       => 'ENUM',
                'constraint' => ['gambar', 'situs', 'teks'],
            ],
            'kata_kunci' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'jumlah_hasil' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'alamat_ip' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
                'null'       => true,
            ],
            'waktu' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id_log', true);
        $this->forge->createTable('gkr_logcari', true);
        
        // Setup raw query for default CURRENT_TIMESTAMP because CI4 Forge doesn't cleanly support it across all DB drivers
        $db = \Config\Database::connect();
        $db->query('ALTER TABLE gkr_loguser MODIFY waktu DATETIME DEFAULT CURRENT_TIMESTAMP');
        $db->query('ALTER TABLE gkr_logcari MODIFY waktu DATETIME DEFAULT CURRENT_TIMESTAMP');
    }

    public function down()
    {
        $this->forge->dropTable('gkr_loguser', true);
        $this->forge->dropTable('gkr_logcari', true);
    }
}
