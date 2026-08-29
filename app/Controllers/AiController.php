<?php

namespace App\Controllers;

use App\Models\ChatHistoryModel;
use CodeIgniter\RESTful\ResourceController;

class AiController extends ResourceController
{
    protected $chatHistoryModel;
    protected $session;

    public function __construct()
    {
        $this->chatHistoryModel = new ChatHistoryModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        return view('ai/index');
    }

    public function getMessages()
    {
        $userId = $this->session->get('id_user');
        
        $db = \Config\Database::connect();
        $user = $db->table('gkr_users')->where('id_user', $userId)->get()->getRow();
        
        if (!$user || empty($user->no_hp)) {
            return $this->response->setJSON(['error' => 'Nomor HP tidak ditemukan untuk sesi ini.']);
        }

        $messages = $this->chatHistoryModel->where('no_hp', $user->no_hp)
                                           ->orderBy('created_at', 'ASC')
                                           ->findAll();

        return $this->response->setJSON($messages);
    }

    public function sendMessage()
    {
        $userId = $this->session->get('id_user');
        
        $db = \Config\Database::connect();
        $user = $db->table('gkr_users')->where('id_user', $userId)->get()->getRow();
        
        if (!$user || empty($user->no_hp)) {
            return $this->response->setJSON(['error' => 'Akses ditolak. Nomor HP tidak ditemukan.'])->setStatusCode(401);
        }

        $noHp = $user->no_hp;
        $chatId = $user->waha_chat_id ?: $noHp . '@s.whatsapp.net';
        $messageText = $this->request->getPost('message');

        if (empty($messageText)) {
            return $this->response->setJSON(['error' => 'Pesan tidak boleh kosong.'])->setStatusCode(400);
        }

        $this->chatHistoryModel->insert([
            'chat_id'    => $chatId,
            'no_hp'      => $noHp,
            'sender'     => 'user',
            'message'    => $messageText,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // URL Webhook n8n khusus Web
        $n8nWebhookUrl = 'http://127.0.0.1:5678/webhook/gracia-web'; 
        
        $client = \Config\Services::curlrequest();
        try {
            $response = $client->post($n8nWebhookUrl, [
                'json' => [
                    'text' => $messageText,
                    'chatId' => $chatId,
                    'no_hp' => $noHp,
                    'msg_type' => 'text',
                    'source' => 'web'
                ],
                'http_errors' => false,
                'timeout' => 60
            ]);
            
            $aiResponse = (string) $response->getBody();
            
            $decodedResponse = json_decode($aiResponse, true);
            $finalAiText = '';
            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_array($decodedResponse) && isset($decodedResponse[0])) { $decodedResponse = $decodedResponse[0]; }
                if (isset($decodedResponse['output'])) {
                    $finalAiText = $decodedResponse['output'];
                } else if (isset($decodedResponse['text'])) {
                    $finalAiText = $decodedResponse['text'];
                } else {
                    $finalAiText = $aiResponse; 
                }
            } else {
                $finalAiText = $aiResponse;
            }

            if (empty(trim($finalAiText))) {
                $finalAiText = "Maaf, saya tidak dapat merespons saat ini.";
            }

            $this->chatHistoryModel->insert([
                'chat_id'    => $chatId,
                'no_hp'      => $noHp,
                'sender'     => 'bot',
                'message'    => $finalAiText,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => 'success', 'reply' => $finalAiText]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Gagal menghubungi server AI: ' . $e->getMessage()])->setStatusCode(500);
        }
    }
}

