<?php

namespace App\Models;

use CodeIgniter\Model;

class ErpModel extends Model
{
    protected $table = 'gkr_erp';
    protected $primaryKey = 'kode_bom';
    protected $returnType = 'array';
    protected $allowedFields = [
        'kode_bom', 'item_name', 'dimensi', 'bom_name', 'packing', 'finishing', 'load_40', 'load_40_hc', 'buyer', 'minimum_selling_price', 'suggested_selling_price', 'erp_modified', 'created_at', 'updated_at'
    ];
}
