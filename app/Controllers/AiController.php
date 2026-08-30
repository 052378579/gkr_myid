<?php

namespace App\Controllers;

use App\Models\ChatHistoryModel;
use App\Models\AiSesiModel;
use App\Models\AiPesanModel;
use CodeIgniter\RESTful\ResourceController;

class AiController extends ResourceController
{
    protected $chatHistoryModel;
    protected $aiSesiModel;
    protected $aiPesanModel;
    protected $session;

    public function __construct()
    {
        $this->chatHistoryModel = new ChatHistoryModel();
        $this->aiSesiModel      = new AiSesiModel();
        $this->aiPesanModel     = new AiPesanModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $doodleModel = new \App\Models\DoodleModel();
        $doodle = $doodleModel->getActiveDoodle();
        
        $data = [];
        if ($doodle) {
            $data['urlLogo'] = base_url('dokumen/doodle/' . $doodle['gambar']);
            $data['altLogo'] = $doodle['event'];
        }
        
        return view('ai/index', $data);
    }

    public function getSessions()
    {
        $userId = $this->session->get('id_user');
        if (!$userId) return $this->response->setJSON(['error' => 'Not authenticated'])->setStatusCode(401);

        $sessions = $this->aiSesiModel->where('user_id', $userId)
                                      ->orderBy('updated_at', 'DESC')
                                      ->findAll();
        
        return $this->response->setJSON($sessions);
    }

    public function createSession()
    {
        $userId = $this->session->get('id_user');
        if (!$userId) return $this->response->setJSON(['error' => 'Not authenticated'])->setStatusCode(401);

        $title = $this->request->getPost('title') ?: 'Sesi Baru';

        $data = [
            'user_id' => $userId,
            'title'   => $title
        ];

        $this->aiSesiModel->insert($data);
        $id = $this->aiSesiModel->getInsertID();

        return $this->response->setJSON([
            'status' => 'success',
            'session' => [
                'id' => $id,
                'title' => $title
            ]
        ]);
    }

    public function deleteSession($id)
    {
        $userId = $this->session->get('id_user');
        if (!$userId) return $this->response->setJSON(['error' => 'Not authenticated'])->setStatusCode(401);

        $session = $this->aiSesiModel->where('id', $id)->where('user_id', $userId)->first();
        if (!$session) {
            return $this->response->setJSON(['error' => 'Sesi tidak ditemukan'])->setStatusCode(404);
        }

        $this->aiSesiModel->delete($id);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function getMessages()
    {
        $userId = $this->session->get('id_user');
        $sessionId = $this->request->getGet('session_id');
        
        $db = \Config\Database::connect();
        $user = $db->table('gkr_users')->where('id_user', $userId)->get()->getRow();
        
        if (!$user || empty($user->no_hp)) {
            return $this->response->setJSON(['error' => 'Nomor HP tidak ditemukan untuk sesi ini.']);
        }

        if (empty($sessionId) || $sessionId === 'main') {
            $messages = $this->chatHistoryModel->where('no_hp', $user->no_hp)
                                               ->orderBy('id', 'ASC')
                                               
                                               ->findAll();
            return $this->response->setJSON($messages);
        } else {
            // Sesi khusus
            // Cek apakah sesi ini milik user
            $session = $this->aiSesiModel->where('id', $sessionId)->where('user_id', $userId)->first();
            if (!$session) {
                return $this->response->setJSON(['error' => 'Sesi tidak ditemukan.'])->setStatusCode(404);
            }

            $rawMessages = $this->aiPesanModel->where('session_id', $sessionId)
                                              ->orderBy('id', 'ASC')
                                              
                                              ->findAll();
            
            // Format pesan agar sesuai dengan format frontend (sender, message, source, created_at)
            $formattedMessages = [];
            foreach ($rawMessages as $msg) {
                $formattedMessages[] = [
                    'id' => $msg->id,
                    'sender' => ($msg->role === 'user') ? 'user' : 'bot',
                    'message' => $msg->content,
                    'media_url' => $msg->media_url ?? null,
                    'source' => $msg->source,
                    'created_at' => $msg->created_at
                ];
            }
            return $this->response->setJSON($formattedMessages);
        }
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
        $sessionId = $this->request->getPost('session_id') ?: 'main';

        if (empty($messageText)) {
            return $this->response->setJSON(['error' => 'Pesan tidak boleh kosong.'])->setStatusCode(400);
        }

        if ($sessionId === 'main') {
            $this->chatHistoryModel->insert([
                'chat_id'    => $chatId,
                'no_hp'      => $noHp,
                'sender'     => 'user',
                'message'    => $messageText,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Validasi sesi
            $session = $this->aiSesiModel->where('id', $sessionId)->where('user_id', $userId)->first();
            if (!$session) {
                return $this->response->setJSON(['error' => 'Sesi tidak valid.'])->setStatusCode(404);
            }

            // Update title jika ini pesan pertama
            $pesanCount = $this->aiPesanModel->where('session_id', $sessionId)->countAllResults();
            if ($pesanCount === 0) {
                $newTitle = mb_substr(strip_tags($messageText), 0, 30) . (mb_strlen($messageText) > 30 ? '...' : '');
                $this->aiSesiModel->update($sessionId, ['title' => $newTitle]);
            }

            $this->aiPesanModel->insert([
                'session_id' => $sessionId,
                'role'       => 'user',
                'content'    => $messageText,
                'source'     => 'web',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

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
                    'source' => 'web',
                    'session_id' => $sessionId
                ],
                'http_errors' => false,
                'timeout' => 60
            ]);
            
            $aiResponse = (string) $response->getBody();
            
            $decodedResponse = json_decode($aiResponse, true);
            $finalAiText = '';
            $mediaUrl = null;
            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_array($decodedResponse) && isset($decodedResponse[0])) { $decodedResponse = $decodedResponse[0]; }
                if (isset($decodedResponse['url_gambar'])) {
                    $mediaUrl = $decodedResponse['url_gambar'];
                }
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

            if ($sessionId === 'main') {
                $this->chatHistoryModel->insert([
                    'chat_id'    => $chatId,
                    'no_hp'      => $noHp,
                    'sender'     => 'bot',
                    'message'    => $finalAiText,
                    'media_url'  => $mediaUrl,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                try {
                    $this->aiPesanModel->insert([
                        'session_id' => $sessionId,
                        'role'       => 'assistant',
                        'content'    => $finalAiText,
                        'media_url'  => $mediaUrl,
                        'source'     => 'web',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                } catch (\Exception $dbError) {
                    // Jika gagal (kemungkinan kolom media_url belum ada), kita coba alter table on the fly
                    $db = \Config\Database::connect();
                    try {
                        $db->query("ALTER TABLE gkr_ai_pesan ADD COLUMN media_url TEXT NULL");
                    } catch (\Exception $e) {}
                    
                    // Coba insert ulang
                    try {
                        $this->aiPesanModel->insert([
                            'session_id' => $sessionId,
                            'role'       => 'assistant',
                            'content'    => $finalAiText,
                            'media_url'  => $mediaUrl,
                            'source'     => 'web',
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    } catch (\Exception $fallbackError) {
                        // Fallback total tanpa media_url
                        $this->aiPesanModel->insert([
                            'session_id' => $sessionId,
                            'role'       => 'assistant',
                            'content'    => $finalAiText,
                            'source'     => 'web',
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }

            return $this->response->setJSON(['status' => 'success', 'reply' => $finalAiText, 'media_url' => $mediaUrl]);

        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Gagal menghubungi server AI: ' . $e->getMessage()])->setStatusCode(500);
        }
    }

    public function updateDb()
    {
        $db = \Config\Database::connect();
        try {
            $db->query("ALTER TABLE gkr_ai_pesan ADD COLUMN media_url TEXT NULL AFTER content");
            return $this->response->setJSON(['status' => 'success']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }
}

