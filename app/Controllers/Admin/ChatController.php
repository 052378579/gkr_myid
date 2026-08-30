<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ChatHistoryModel;

class ChatController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'WAHA Chat History'
        ];
        return view('admin/chat_admin', $data);
    }

    public function api_get_logs()
    {
        $chatModel = new ChatHistoryModel();
        
        $action = $this->request->getGet('action');
        
        if ($action === 'sessions') {
            $search = $this->request->getGet('search') ?? '';
            $sessions = $chatModel->getActiveSessions($search);
            return $this->response->setJSON(['status' => 'success', 'data' => $sessions]);
        }
        
        if ($action === 'detail') {
            $noHp = $this->request->getGet('no_hp');
            if (empty($noHp)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'no_hp is required']);
            }
            $chatLogs = $chatModel->getChatByNumber($noHp);
            return $this->response->setJSON(['status' => 'success', 'data' => $chatLogs]);
        }
        
        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid action']);
    }

    public function api_clear_chat()
    {
        // Double check for superadmin access (just in case filter is missing)
        if (session()->get('id_user') != 1 && session()->get('role') !== 'superadmin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $noHp = $this->request->getPost('no_hp');
        if (empty($noHp)) {
            $json = $this->request->getJSON();
            if ($json && isset($json->no_hp)) {
                $noHp = $json->no_hp;
            }
        }

        if (empty($noHp)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'no_hp is required']);
        }

        $chatModel = new ChatHistoryModel();
        $chatModel->clearChatByNumber($noHp);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Chat history cleared successfully']);
    }
}

