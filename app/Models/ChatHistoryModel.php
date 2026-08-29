<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatHistoryModel extends Model
{
    protected $table            = 'gkr_chat_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['chat_id', 'no_hp', 'sender', 'intent', 'message', 'media_url', 'created_at'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';
}
