<?php

namespace App\Models;

use CodeIgniter\Model;

class ErpModel extends Model
{
    protected $table = 'gkr_erp';
    protected $primaryKey = 'kode_bom';
    protected $returnType = 'array';
    protected $allowedFields = [
        'id_erp', 'kode_bom', 'item_master', 'item_name', 'dimensi', 
        'material', 'weaving', 'fabric', 'terakhir_ditarik', 'terakhir_diekstrak'
    ];
}
