<?php

namespace App\Controllers\Admin;

use App\Models\VersiModel;
use App\Controllers\BaseController;

class VersiController extends BaseController
{
    public function index()
    {
        return view('admin/versi');
    }

    public function getAll()
    {
        $versiModel = new VersiModel();
        $data = $versiModel->orderBy('tanggal_rilis', 'DESC')->findAll();
        
        // Ensure JSON fields are parsed correctly for the frontend
        foreach ($data as &$row) {
            $row['improvements'] = json_decode($row['improvements'] ?? '[]', true);
            $row['fixes'] = json_decode($row['fixes'] ?? '[]', true);
            $row['patches'] = json_decode($row['patches'] ?? '[]', true);
        }
        
        return $this->response->setJSON($data);
    }

    public function store()
    {
        $versiModel = new VersiModel();
        
        $data = [
            'versi' => $this->request->getPost('versi'),
            'tanggal_rilis' => $this->request->getPost('tanggal_rilis'),
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'improvements' => json_encode($this->request->getPost('improvements') ?? []),
            'fixes' => json_encode($this->request->getPost('fixes') ?? []),
            'patches' => json_encode($this->request->getPost('patches') ?? [])
        ];
        
        if ($versiModel->insert($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Versi berhasil ditambahkan']);
        }
        
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menambahkan versi']);
    }

    public function update()
    {
        $versiModel = new VersiModel();
        $id = $this->request->getPost('id');
        
        $data = [
            'versi' => $this->request->getPost('versi'),
            'tanggal_rilis' => $this->request->getPost('tanggal_rilis'),
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'improvements' => json_encode($this->request->getPost('improvements') ?? []),
            'fixes' => json_encode($this->request->getPost('fixes') ?? []),
            'patches' => json_encode($this->request->getPost('patches') ?? [])
        ];
        
        if ($versiModel->update($id, $data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Versi berhasil diperbarui']);
        }
        
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui versi']);
    }

    public function delete()
    {
        $versiModel = new VersiModel();
        $id = $this->request->getPost('id');
        
        if ($versiModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Versi berhasil dihapus']);
        }
        
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus versi']);
    }
}
