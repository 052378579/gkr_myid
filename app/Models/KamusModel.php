<?php

namespace App\Models;

use CodeIgniter\Model;

class KamusModel extends Model
{
    protected $table            = 'gkr_kamus';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kategori', 'kata_kunci'];

    // Dates
    protected $useTimestamps = false;
}

