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

    /**
     * Get grouped list of active chat sessions
     */
    public function getActiveSessions($search = '')
    {
        $builder = $this->builder();
        $builder->select('gkr_chat_history.no_hp, MAX(gkr_chat_history.created_at) as last_activity, COUNT(gkr_chat_history.id) as total_messages, u.nama_lengkap, u.foto_profil');
        $builder->join('gkr_users u', 'u.no_hp = gkr_chat_history.no_hp', 'left');
        
        if (!empty($search)) {
            $builder->groupStart();
            $builder->like('gkr_chat_history.no_hp', $search);
            $builder->orLike('gkr_chat_history.intent', $search);
            $builder->orLike('u.nama_lengkap', $search);
            $builder->groupEnd();
        }
        
        $builder->groupBy('gkr_chat_history.no_hp');
        $builder->orderBy('last_activity', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get all messages for a specific phone number
     */
    public function getChatByNumber($noHp)
    {
        $chatLogs = $this->where('no_hp', $noHp)
                         ->orderBy('created_at', 'ASC')->orderBy("FIELD(sender, 'user', 'bot')")
                         ->findAll();
                         
        // Fix Race Condition (Bot saved before User within 10 seconds window)
        $count = count($chatLogs);
        for ($i = 0; $i < $count - 1; $i++) {
            $current = $chatLogs[$i];
            $next = $chatLogs[$i + 1];
            
            if ($current['sender'] === 'bot' && $next['sender'] === 'user') {
                $time1 = strtotime($current['created_at']);
                $time2 = strtotime($next['created_at']);
                
                // If user message is within 10 seconds AFTER bot message, swap them
                if ($time2 >= $time1 && ($time2 - $time1) <= 10) {
                    $chatLogs[$i] = $next;
                    $chatLogs[$i + 1] = $current;
                    // Skip the next element since we swapped
                    $i++;
                }
            }
        }
        
        return $chatLogs;
    }

    /**
     * Permanently delete all messages for a specific phone number
     */
    public function clearChatByNumber($noHp)
    {
        return $this->where('no_hp', $noHp)->delete();
    }
}

