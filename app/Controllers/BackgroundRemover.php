<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use CURLFile;

class BackgroundRemover extends Controller
{
    public function index()
    {
        return view('bg_remover_view');
    }

    public function process()
    {
        $file = $this->request->getFile('image');
        
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['error' => 'File gambar tidak valid.']);
        }

        // Pastikan direktori uploads ada dan memiliki izin tulis
        $uploadPath = FCPATH . 'uploads';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Simpan file asli sementara
        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);
        $originalPath = $uploadPath . '/' . $newName;

        // Kirim ke FastAPI (Port 5000)
        $cfile = new \CURLFile($originalPath, $file->getClientMimeType(), $file->getClientName());
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:5000/remove-bg');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => $cfile]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $resultImageBytes = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Validasi apakah FastAPI benar-benar mengembalikan gambar (HTTP 200)
        if ($httpCode === 200 && $resultImageBytes) {
            $resultName = pathinfo($newName, PATHINFO_FILENAME) . '-transparent.png';
            $resultPath = $uploadPath . '/' . $resultName;
            
            // Tulis file fisik ke folder public/uploads
            file_put_contents($resultPath, $resultImageBytes);

            return $this->response->setJSON([
                'success' => true,
                'original' => base_url('uploads/' . $newName),
                'result' => base_url('uploads/' . $resultName)
            ]);
        }

        return $this->response->setJSON(['error' => 'Gagal dari FastAPI. HTTP Code: ' . $httpCode]);
    }
}