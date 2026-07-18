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
                'rules'  => 'uploaded[gambar]|max_size[gambar,2048]|is_image[gambar]|mime_in[gambar,image/png,image/webp,image/gif]',
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
        $tgl_selesai = $this->request->getPost('tgl_selesai');
        $status    = $this->request->getPost('status');
        $foto      = $this->request->getFile('gambar');
        $ext       = $foto->getClientExtension();
        $cleanEvent = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $event));
        
        $tanggal_mulai_format = date('Y-m-d', strtotime($tgl_mulai)); 
        $namaFoto = $tanggal_mulai_format . '_' . $cleanEvent . '.' . $ext;
        $foto->move(WRITEPATH . 'GKR_DOODLE', $namaFoto, true);

        $this->doodleModel->insert([
            'event'       => $event,
            'tgl_mulai'   => $tgl_mulai,
            'tgl_selesai' => $tgl_selesai,
            'status'      => $status,
            'gambar'      => $namaFoto
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Doodle berhasil ditambahkan.'
        ]);
    }

    /**
     * API CREATE: Generate Recurring Doodles (Weekend & Payday) for a given year
     */
    public function generateRecurring()
    {
        $tahun = $this->request->getPost('tahun') ?? date('Y');
        $insertData = [];

        // 1. Generate Weekend Doodles (Friday to Sunday)
        $startDate = new \DateTime("$tahun-01-01");
        $endDate = new \DateTime("$tahun-12-31");
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($startDate, $interval, $endDate->modify('+1 day'));

        foreach ($period as $dt) {
            $dayOfWeek = (int)$dt->format('w'); // 5 = Friday
            if ($dayOfWeek === 5) {
                $friday = clone $dt;
                $sunday = clone $dt;
                $sunday->modify('+2 days');

                $insertData[] = [
                    'event' => 'Akhir Pekan ' . $friday->format('d M'),
                    'tgl_mulai' => $friday->format('Y-m-d'),
                    'tgl_selesai' => $sunday->format('Y-m-d'),
                    'status' => 'aktif',
                    'gambar' => 'doodle_weekend.png',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
        }

        // 2. Generate Payday Doodles
        for ($m = 1; $m <= 12; $m++) {
            $gajianDate = new \DateTime(sprintf("%04d-%02d-25", $tahun, $m));
            $dayOfWeek = (int)$gajianDate->format('w');
            if ($dayOfWeek === 6) { // Sabtu -> 24
                $gajianDate->modify('-1 day');
            } elseif ($dayOfWeek === 0) { // Minggu -> 23
                $gajianDate->modify('-2 days');
            }

            $insertData[] = [
                'event' => 'Hari Gajian',
                'tgl_mulai' => $gajianDate->format('Y-m-d'),
                'tgl_selesai' => $gajianDate->format('Y-m-d'),
                'status' => 'aktif',
                'gambar' => 'doodle_gajian.png',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        if (!empty($insertData)) {
            $this->doodleModel->insertBatch($insertData);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Doodle berulang untuk tahun ' . $tahun . ' berhasil dibuat.'
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
                'rules'  => 'max_size[gambar,2048]|is_image[gambar]|mime_in[gambar,image/png,image/webp,image/gif]'
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