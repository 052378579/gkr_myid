<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'gkr_material';
    protected $primaryKey = 'id';
    protected $allowedFields = ['material', 'warna'];

    public function __construct()
    {
        parent::__construct();

        // Create table and seed if it doesn't exist
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('gkr_material')) {
            $db->query("CREATE TABLE `gkr_material` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `material` varchar(100) NOT NULL,
                `warna` varchar(100) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            $db->query("INSERT INTO `gkr_material` (`material`, `warna`) VALUES 
                ('teak', 'natual 002'),
                ('alumunium', 'antrachite bronze'),
                ('alumunium', 'taupe texture'),
                ('fiber', 'terrazzo'),
                ('fiber', 'concreate')
            ");
        }
    }
}
