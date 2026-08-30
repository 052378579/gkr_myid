<?php

namespace App\Models;

use CodeIgniter\Model;

class AiPesanModel extends Model
{
    protected $table            = 'gkr_ai_pesan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['session_id', 'role', 'content', 'media_url', 'source', 'created_at'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false; // Karena hanya ada created_at
}
