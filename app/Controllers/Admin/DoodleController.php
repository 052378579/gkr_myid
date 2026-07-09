<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DoodleModel;

class DoodleController extends BaseController
{
    protected $doodleModel;

    public function __construct()
    {
        $this->doodleModel = new DoodleModel();
    }



    /**
     * API READ: Mengambil semua data Doodle
     */
    public function getAll()
    {
        $data = $this->doodleModel->orderBy('tgl_mulai', 'DESC')->findAll();
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    /**
     * API CREATE: Menyimpan Doodle Baru
     */
    public function store()
    {
        $rules = [
            'event'       => 'required',
            'tgl_mulai'   => 'required|valid_date',
            'tgl_selesai' => 'required|valid_date',
            'status'      => 'required|in_list[aktif,tidak_aktif]',
            'gambar'      => [
                'rules'  => 'uploaded[gambar]|max_size[gambar,2048]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png,image/webp,image/gif]',
                'errors' => [
                    'uploaded' => 'Harap pilih gambar doodle.',
                    'max_size' => 'Ukuran gambar maksimal 2MB.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        // Tangkap data inputan
        $event     = $this->request->getPost('event');
        $tgl_mulai = $this->request->getPost('tgl_mulai');
        $foto      = $this->request->getFile('gambar');
        $ext       = $foto->getClientExtension();
        
        // --- FORMAT NAMING BARU ---
        // 1. Bersihkan nama event (hanya huruf, angka, dan underscore)
        $cleanEvent = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $event));
        
        // 2. Ambil YYYY-MM-DD dari inputan 'tgl_mulai'
        $tanggal_mulai_format = date('Y-m-d', strtotime($tgl_mulai)); 
        
        // 3. Gabungkan menjadi format final: 2026-07-08_hari_jumat.png
        $namaFoto = $tanggal_mulai_format . '_' . $cleanEvent . '.' . $ext;
        
        // Simpan ke direktori app/writable/GKR_DOODLE
        $foto->move(WRITEPATH . 'GKR_DOODLE', $namaFoto, true);

        $this->doodleModel->insert([
            'event'       => $event,
            'tgl_mulai'   => $tgl_mulai,
            'tgl_selesai' => $this->request->getPost('tgl_selesai'),
            'status'      => $this->request->getPost('status'),
            'gambar'      => $namaFoto
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Doodle berhasil ditambahkan.'
        ]);
    }

    /**
     * API UPDATE: Memperbarui Doodle
     */
    public function update()
    {
        $id_doodle = $this->request->getPost('id_doodle');
        $doodleLama = $this->doodleModel->find($id_doodle);

        if (!$doodleLama) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }

        $event = $this->request->getPost('event');
        $rules = [
            'event'       => 'required',
            'tgl_mulai'   => 'required|valid_date',
            'tgl_selesai' => 'required|valid_date',
            'status'      => 'required|in_list[aktif,tidak_aktif]',
        ];

        $foto = $this->request->getFile('gambar');

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $rules['gambar'] = [
                'rules'  => 'max_size[gambar,2048]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png,image/webp,image/gif]'
            ];
        }

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $dataUpdate = [
            'event'       => $event,
            'tgl_mulai'   => $this->request->getPost('tgl_mulai'),
            'tgl_selesai' => $this->request->getPost('tgl_selesai'),
            'status'      => $this->request->getPost('status')
        ];

        // Jika Admin mengunggah gambar baru saat update
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $ext = $foto->getClientExtension();
            $tgl_mulai = $this->request->getPost('tgl_mulai');
            
            // --- FORMAT NAMING BARU ---
            $cleanEvent = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $event));
            $tanggal_mulai_format = date('Y-m-d', strtotime($tgl_mulai));
            $namaFoto = $tanggal_mulai_format . '_' . $cleanEvent . '.' . $ext;

            // Pindahkan ke direktori app/writable/GKR_DOODLE
            $foto->move(WRITEPATH . 'GKR_DOODLE', $namaFoto, true); 

            // Hapus gambar lama jika namanya berbeda dan filenya ada
            if (!empty($doodleLama['gambar']) && $doodleLama['gambar'] !== $namaFoto && file_exists(WRITEPATH . 'GKR_DOODLE/' . $doodleLama['gambar'])) {
                unlink(WRITEPATH . 'GKR_DOODLE/' . $doodleLama['gambar']);
            }

            $dataUpdate['gambar'] = $namaFoto;
        }

        $this->doodleModel->update($id_doodle, $dataUpdate);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Doodle berhasil diperbarui.'
        ]);
    }

    /**
     * API DELETE: Menghapus Doodle
     */
    public function delete()
    {
        $id_doodle = $this->request->getPost('id_doodle');
        $doodle = $this->doodleModel->find($id_doodle);

        if ($doodle) {
            // Hapus file fisik dari direktori app/writable/GKR_DOODLE
            if (!empty($doodle['gambar']) && file_exists(WRITEPATH . 'GKR_DOODLE/' . $doodle['gambar'])) {
                unlink(WRITEPATH . 'GKR_DOODLE/' . $doodle['gambar']);
            }

            $this->doodleModel->delete($id_doodle);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Doodle berhasil dihapus.'
            ]);
        }

        return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
    }
}