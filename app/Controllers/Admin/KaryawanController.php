<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class KaryawanController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Pengaturan Karyawan (Pengguna)'
        ];
        return view('admin/karyawan', $data);
    }

    public function getAll()
    {
        $karyawan = $this->userModel->orderBy('divisi', 'ASC')->orderBy('nama_lengkap', 'ASC')->findAll();
        return $this->response->setJSON($karyawan);
    }

    public function store()
    {
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'no_hp'        => $this->request->getPost('no_hp'),
            'divisi'       => $this->request->getPost('divisi'),
        ];

        if ($this->userModel->save($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data karyawan berhasil ditambahkan.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data.', 'errors' => $this->userModel->errors()]);
        }
    }

    public function update()
    {
        $id = $this->request->getPost('id_user');
        
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'no_hp'        => $this->request->getPost('no_hp'),
            'divisi'       => $this->request->getPost('divisi'),
        ];

        if ($this->userModel->update($id, $data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data karyawan berhasil diperbarui.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui data.', 'errors' => $this->userModel->errors()]);
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id_user');

        if ($this->userModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data karyawan berhasil dihapus.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data.']);
        }
    }
}
