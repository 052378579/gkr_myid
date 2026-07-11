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
        $isAnnual = $this->request->getPost('annual_friday') == 1 || $this->request->getPost('annual_weekend') == 1 || $this->request->getPost('annual_payday') == 1;

        $rules = [
            'event'       => 'required',
            'status'      => 'required|in_list[aktif,tidak_aktif]',
            'gambar'      => [
                'rules'  => 'uploaded[gambar]|max_size[gambar,2048]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png,image/webp,image/gif]',
                'errors' => [
                    'uploaded' => 'Harap pilih gambar doodle.',
                    'max_size' => 'Ukuran gambar maksimal 2MB.'
                ]
            ]
        ];

        if (!$isAnnual) {
            $rules['tgl_mulai'] = 'required|valid_date';
            $rules['tgl_selesai'] = 'required|valid_date';
        }

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        // Tangkap data inputan
        $event     = $this->request->getPost('event');
        $status    = $this->request->getPost('status');
        $foto      = $this->request->getFile('gambar');
        $ext       = $foto->getClientExtension();
        $cleanEvent = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $event));
        
        $tahunSekarang = date('Y');

        if ($isAnnual) {
            $namaFoto = $tahunSekarang . '_annual_' . $cleanEvent . '.' . $ext;
            $foto->move(WRITEPATH . 'GKR_DOODLE', $namaFoto, true);

            $insertData = [];
            $annualFriday = $this->request->getPost('annual_friday') == 1;
            $annualWeekend = $this->request->getPost('annual_weekend') == 1;
            $annualPayday = $this->request->getPost('annual_payday') == 1;

            if ($annualFriday || $annualWeekend) {
                // Loop 1 tahun
                $startDate = new \DateTime("$tahunSekarang-01-01");
                $endDate = new \DateTime("$tahunSekarang-12-31");
                $interval = new \DateInterval('P1D');
                $period = new \DatePeriod($startDate, $interval, $endDate->modify('+1 day'));

                foreach ($period as $dt) {
                    $dayOfWeek = (int)$dt->format('w'); // 0=Sun, 5=Fri, 6=Sat
                    
                    if ($annualFriday && $dayOfWeek === 5) {
                        $tgl = $dt->format('Y-m-d');
                        $insertData[] = [
                            'event' => $event,
                            'tgl_mulai' => $tgl,
                            'tgl_selesai' => $tgl,
                            'status' => $status,
                            'gambar' => $namaFoto,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                    if ($annualWeekend && ($dayOfWeek === 6 || $dayOfWeek === 0)) {
                        $tgl = $dt->format('Y-m-d');
                        $insertData[] = [
                            'event' => $event,
                            'tgl_mulai' => $tgl,
                            'tgl_selesai' => $tgl,
                            'status' => $status,
                            'gambar' => $namaFoto,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                }
            }

            if ($annualPayday) {
                for ($m = 1; $m <= 12; $m++) {
                    $gajianDate = new \DateTime(sprintf("%04d-%02d-25", $tahunSekarang, $m));
                    $dayOfWeek = (int)$gajianDate->format('w');
                    if ($dayOfWeek === 6) { // Sabtu -> 24
                        $gajianDate->modify('-1 day');
                    } elseif ($dayOfWeek === 0) { // Minggu -> 23
                        $gajianDate->modify('-2 days');
                    }

                    $insertData[] = [
                        'event' => $event,
                        'tgl_mulai' => $gajianDate->format('Y-m-d'),
                        'tgl_selesai' => $gajianDate->format('Y-m-d'),
                        'status' => $status,
                        'gambar' => $namaFoto,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                }
            }

            // Hindari duplikasi jika memungkinkan, atau insert batch langsung
            if (!empty($insertData)) {
                $this->doodleModel->insertBatch($insertData);
            }

        } else {
            // Logika Normal
            $tgl_mulai = $this->request->getPost('tgl_mulai');
            $tgl_selesai = $this->request->getPost('tgl_selesai');
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
        }

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