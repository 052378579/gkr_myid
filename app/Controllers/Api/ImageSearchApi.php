<?php

namespace App\Controllers\Api;

use App\Models\ImageModel;
use App\Models\SiteModel;
use CodeIgniter\RESTful\ResourceController;

class ImageSearchApi extends ResourceController
{
    public function upload()
    {
        $file = $this->request->getFile('image');
        
        if (!$file || !$file->isValid()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'gagal',
                'pesan'  => 'Tidak ada gambar yang diunggah atau file tidak valid',
                'data'   => null
            ]);
        }

        // Validasi mime type
        $mime = $file->getMimeType();
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'gagal',
                'pesan'  => 'Hanya menerima format JPG, PNG, atau WEBP',
                'data'   => null
            ]);
        }

        // Pindahkan ke folder writable/uploads sementara
        $newName = $file->getRandomName();
        $uploadPath = WRITEPATH . 'uploads/';
        $file->move($uploadPath, $newName);
        
        $fullPath = $uploadPath . $newName;

        try {
            // Panggil AI Scanner FastAPI (Microservice)
            $client = \Config\Services::curlrequest();
            $response = $client->post('http://127.0.0.1:5000/scan', [
                'multipart' => [
                    'file' => new \CURLFile($fullPath)
                ]
            ]);

            $body = json_decode($response->getBody());
            
            if (!isset($body->status) || $body->status !== 'success') {
                $errorMsg = $body->message ?? 'Produk tidak dikenali oleh AI.';
                unlink($fullPath);
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'gagal',
                    'pesan'  => $errorMsg,
                    'data'   => null
                ]);
            }
            
            $kodeBom = $body->kode_bom;
            $confidence = $body->confidence;
            
            $aiResults = [];
            if (isset($body->results)) {
                $aiResults = json_decode(json_encode($body->results), true);
            }

            // Simpan hasil ke dalam PHP Session untuk digunakan oleh Search.php
            session()->set('search_kode_bom', $kodeBom);
            session()->set('search_confidence', $confidence);
            session()->set('search_ai_results', $aiResults);

            // Hapus file sementara setelah diproses
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            return $this->response->setJSON([
                'status' => 'sukses',
                'pesan'  => 'Gambar berhasil diproses',
                'data'   => null
            ]);

        } catch (\Exception $e) {
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'gagal',
                'pesan'  => 'Terjadi kesalahan internal: ' . $e->getMessage(),
                'data'   => null
            ]);
        }
    }
}
