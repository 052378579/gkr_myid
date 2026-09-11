<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KamusModel;

class KamusController extends BaseController
{
    protected $kamusModel;

    public function __construct()
    {
        $this->kamusModel = new KamusModel();
    }

    public function index()
    {
        $data = [
            'kamusData' => $this->kamusModel->findAll()
        ];
        return view('admin/kamus', $data);
    }

    public function update()
    {
        $json = $this->request->getJSON();
        
        if (!$json || !isset($json->id) || !isset($json->kata_kunci)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak valid'])->setStatusCode(400);
        }

        // Pembersihan
        $kataKunciBersih = strtoupper(trim(preg_replace('/\s+/', ' ', $json->kata_kunci)));

        $data = [
            'kata_kunci' => $kataKunciBersih
        ];

        try {
            $this->kamusModel->update($json->id, $data);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Kamus berhasil diperbarui', 'data' => $data]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }
}

