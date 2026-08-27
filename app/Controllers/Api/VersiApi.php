<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class VersiApi extends BaseController
{
    private $jsonFile = FCPATH . 'versi.json';

    private function getJsonData()
    {
        if (!file_exists($this->jsonFile)) {
            return ['nama_file' => 'versi.json', 'nama_proyek' => 'gkr_myid', 'tanggal_diperbarui' => '', 'versi_diperbarui' => '', 'data' => []];
        }
        return json_decode(file_get_contents($this->jsonFile), true);
    }

    private function saveJsonData($data)
    {
        $data['tanggal_diperbarui'] = date('d-m-Y H:i:s') . ' WIB';
        $data['versi_diperbarui'] = date('0.n.d');
        file_put_contents($this->jsonFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function getAll()
    {
        $json = $this->getJsonData();
        $data = isset($json['data']) ? $json['data'] : [];
        
        usort($data, function($a, $b) {
            return strtotime($b['tanggal_rilis']) - strtotime($a['tanggal_rilis']);
        });
        
        return $this->response->setJSON([
            'status' => 'sukses',
            'pesan'  => 'Data riwayat berhasil ditarik',
            'data'   => $data
        ]);
    }

    public function store()
    {
        $json = $this->getJsonData();
        $dataArray = isset($json['data']) ? $json['data'] : [];
        
        $maxId = 0;
        foreach ($dataArray as $item) {
            if ($item['id'] > $maxId) {
                $maxId = $item['id'];
            }
        }
        
        $newData = [
            'id' => $maxId + 1,
            'versi' => $this->request->getPost('versi'),
            'tanggal_rilis' => $this->request->getPost('tanggal_rilis'),
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'improvements' => $this->request->getPost('improvements') ?? [],
            'fixes' => $this->request->getPost('fixes') ?? [],
            'patches' => $this->request->getPost('patches') ?? [],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $dataArray[] = $newData;
        $json['data'] = $dataArray;
        
        $this->saveJsonData($json);
        
        return $this->response->setJSON([
            'status' => 'sukses',
            'pesan'  => 'Versi berhasil ditambahkan',
            'data'   => null
        ]);
    }

    public function update()
    {
        $json = $this->getJsonData();
        $dataArray = isset($json['data']) ? $json['data'] : [];
        $id = (int) $this->request->getPost('id');
        
        $updated = false;
        foreach ($dataArray as &$item) {
            if ($item['id'] === $id) {
                $item['versi'] = $this->request->getPost('versi');
                $item['tanggal_rilis'] = $this->request->getPost('tanggal_rilis');
                $item['judul'] = $this->request->getPost('judul');
                $item['deskripsi'] = $this->request->getPost('deskripsi');
                $item['improvements'] = $this->request->getPost('improvements') ?? [];
                $item['fixes'] = $this->request->getPost('fixes') ?? [];
                $item['patches'] = $this->request->getPost('patches') ?? [];
                $updated = true;
                break;
            }
        }
        
        if ($updated) {
            $json['data'] = $dataArray;
            $this->saveJsonData($json);
            return $this->response->setJSON([
                'status' => 'sukses',
                'pesan'  => 'Versi berhasil diperbarui',
                'data'   => null
            ]);
        }
        
        return $this->response->setJSON([
            'status' => 'gagal',
            'pesan'  => 'Data versi tidak ditemukan',
            'data'   => null
        ]);
    }

    public function delete()
    {
        $json = $this->getJsonData();
        $dataArray = isset($json['data']) ? $json['data'] : [];
        $id = (int) $this->request->getPost('id');
        
        $initialCount = count($dataArray);
        $dataArray = array_filter($dataArray, function($item) use ($id) {
            return $item['id'] !== $id;
        });
        
        $dataArray = array_values($dataArray);
        
        if (count($dataArray) < $initialCount) {
            $json['data'] = $dataArray;
            $this->saveJsonData($json);
            return $this->response->setJSON([
                'status' => 'sukses',
                'pesan'  => 'Versi berhasil dihapus',
                'data'   => null
            ]);
        }
        
        return $this->response->setJSON([
            'status' => 'gagal',
            'pesan'  => 'Data versi tidak ditemukan',
            'data'   => null
        ]);
    }
}
