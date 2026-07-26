<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\SiteModel;
use App\Models\ImageModel;

class GraciaApi extends BaseController
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    }

    public function updateLinkCount()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $modelSitus = new SiteModel();
            $modelSitus->skipValidation(true)->where('id', $id)->set('clicks', 'clicks+1', false)->update();
            return $this->response->setJSON([
                'status' => 'sukses',
                'pesan'  => 'Berhasil memperbarui klik',
                'data'   => null
            ]);
        }
        return $this->response->setJSON([
            'status' => 'gagal',
            'pesan'  => 'ID tidak diberikan',
            'data'   => null
        ]);
    }

    public function updateImageCount()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $modelGambar = new ImageModel();
            $modelGambar->skipValidation(true)->where('id', $id)->set('clicks', 'clicks+1', false)->update();
            return $this->response->setJSON([
                'status' => 'sukses',
                'pesan'  => 'Berhasil memperbarui klik gambar',
                'data'   => null
            ]);
        }
        return $this->response->setJSON([
            'status' => 'gagal',
            'pesan'  => 'ID tidak diberikan',
            'data'   => null
        ]);
    }

    public function setBroken()
    {
        $sumberGambar = $this->request->getPost('src');
        if ($sumberGambar) {
            $sumberGambar = esc($sumberGambar);
            $modelGambar = new ImageModel();
            $modelGambar->skipValidation(true)->where('imageUrl', $sumberGambar)->set(['broken' => 1])->update();
            return $this->response->setJSON([
                'status' => 'sukses',
                'pesan'  => 'Berhasil menandai gambar rusak',
                'data'   => null
            ]);
        }
        return $this->response->setJSON([
            'status' => 'gagal',
            'pesan'  => 'Sumber gambar tidak diberikan',
            'data'   => null
        ]);
    }

    public function dropCol()
    {
        $db = \Config\Database::connect();
        try {
            $db->query("ALTER TABLE cari_images DROP COLUMN image_hash");
            return $this->response->setJSON([
                'status' => 'sukses',
                'pesan'  => 'Kolom image_hash berhasil dihapus',
                'data'   => null
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'gagal',
                'pesan'  => 'ERROR: ' . $e->getMessage(),
                'data'   => null
            ]);
        }
    }

    public function setupDb()
    {
        $db = \Config\Database::connect();
        
        // Coba tambahkan kolom keywords ke cari_images
        try {
            $db->query("ALTER TABLE cari_images ADD COLUMN keywords VARCHAR(512) DEFAULT NULL");
        } catch (\Exception $e) {
            // Abaikan jika sudah ada
        }
        
        $db->query("CREATE TABLE IF NOT EXISTS `gkr_material` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `material` varchar(100) NOT NULL,
            `warna` varchar(100) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $db->query("TRUNCATE TABLE `gkr_material`");
        $db->query("INSERT INTO `gkr_material` (`material`, `warna`) VALUES 
            ('teak', 'natual 002'),
            ('alumunium', 'antrachite bronze'),
            ('alumunium', 'taupe texture'),
            ('fiber', 'terrazzo'),
            ('fiber', 'concreate')
        ");
        
        return $this->response->setJSON([
            'status' => 'sukses',
            'pesan'  => 'Basis data material berhasil disiapkan',
            'data'   => null
        ]);
    }

    public function getMaterials()
    {
        $modelMaterial = new \App\Models\MaterialModel();
        return $this->response->setJSON([
            'status' => 'sukses',
            'pesan'  => 'Data material berhasil ditarik',
            'data'   => $modelMaterial->findAll()
        ]);
    }

    public function getSites()
    {
        $modelSitus = new SiteModel();
        $data = $modelSitus->findAll();
        $urlDasarGambar = getenv('app.imgBaseURL') ?: 'https://foto.gkr.my.id/';
        
        foreach ($data as &$barisSitus) {
            if (!empty($barisSitus['url']) && !preg_match('/^https?:\/\//i', $barisSitus['url'])) {
                if (str_starts_with($barisSitus['url'], '?')) {
                    $barisSitus['url'] = rtrim($urlDasarGambar, '/') . '/' . $barisSitus['url'];
                } else {
                    $barisSitus['url'] = rtrim($urlDasarGambar, '/') . '/' . ltrim($barisSitus['url'], '/');
                }
            }
        }
        
        return $this->response->setJSON([
            'status' => 'sukses',
            'pesan'  => 'Data situs ditemukan',
            'data'   => $data
        ]);
    }

    public function getImages()
    {
        $modelGambar = new ImageModel();
        $data = $modelGambar->findAll();
        $urlDasarGambar = getenv('app.imgBaseURL') ?: 'https://foto.gkr.my.id/';
        
        foreach ($data as &$barisGambar) {
            if (!empty($barisGambar['imageUrl']) && !preg_match('/^https?:\/\//i', $barisGambar['imageUrl'])) {
                $barisGambar['imageUrl'] = rtrim($urlDasarGambar, '/') . '/' . ltrim($barisGambar['imageUrl'], '/');
            }
            if (!empty($barisGambar['siteUrl']) && !preg_match('/^https?:\/\//i', $barisGambar['siteUrl'])) {
                if (str_starts_with($barisGambar['siteUrl'], '?')) {
                    $barisGambar['siteUrl'] = rtrim($urlDasarGambar, '/') . '/' . $barisGambar['siteUrl'];
                } else {
                    $barisGambar['siteUrl'] = rtrim($urlDasarGambar, '/') . '/' . ltrim($barisGambar['siteUrl'], '/');
                }
            }
        }
        
        return $this->response->setJSON([
            'status' => 'sukses',
            'pesan'  => 'Data gambar ditemukan',
            'data'   => $data
        ]);
    }

    public function deleteSite($id)
    {
        $modelSitus = new SiteModel();
        $modelSitus->delete($id);
        return $this->response->setJSON([
            'status' => 'sukses',
            'pesan'  => 'Situs berhasil dihapus',
            'data'   => null
        ]);
    }

    public function deleteImage($id)
    {
        $modelGambar = new ImageModel();
        $modelGambar->delete($id);
        return $this->response->setJSON([
            'status' => 'sukses',
            'pesan'  => 'Gambar berhasil dihapus',
            'data'   => null
        ]);
    }

    public function storeSite()
    {
        $modelSitus = new SiteModel();
        $dataBaru = [];
        
        if ($this->request->getPost('title')) $dataBaru['title'] = esc($this->request->getPost('title'));
        if ($this->request->getPost('url')) $dataBaru['url'] = esc($this->request->getPost('url'));
        if ($this->request->getPost('description')) $dataBaru['description'] = esc($this->request->getPost('description'));
        if ($this->request->getPost('keywords')) $dataBaru['keywords'] = esc($this->request->getPost('keywords'));
        
        if (!empty($dataBaru['url']) && !preg_match('/^https?:\/\//i', $dataBaru['url'])) {
            $dataBaru['url'] = 'http://' . $dataBaru['url'];
        }

        if (!empty($dataBaru)) {
            if ($modelSitus->insert($dataBaru)) {
                return $this->response->setJSON([
                    'status' => 'sukses',
                    'pesan'  => 'Situs berhasil ditambahkan',
                    'data'   => null
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'gagal',
                    'pesan'  => implode(", ", $modelSitus->errors()),
                    'data'   => null
                ]);
            }
        }
        return $this->response->setJSON([
            'status' => 'gagal',
            'pesan'  => 'Data tidak valid',
            'data'   => null
        ]);
    }

    public function storeImage()
    {
        $modelGambar = new ImageModel();
        $dataBaru = [];
        
        if ($this->request->getPost('title')) $dataBaru['title'] = esc($this->request->getPost('title'));
        if ($this->request->getPost('alt')) $dataBaru['alt'] = esc($this->request->getPost('alt'));
        if ($this->request->getPost('imageUrl')) $dataBaru['imageUrl'] = esc($this->request->getPost('imageUrl'));
        if ($this->request->getPost('siteUrl')) $dataBaru['siteUrl'] = esc($this->request->getPost('siteUrl'));
        
        if (!empty($dataBaru['imageUrl']) && !preg_match('/^https?:\/\//i', $dataBaru['imageUrl'])) {
            $dataBaru['imageUrl'] = 'http://' . $dataBaru['imageUrl'];
        }
        if (!empty($dataBaru['siteUrl']) && !preg_match('/^https?:\/\//i', $dataBaru['siteUrl'])) {
            $dataBaru['siteUrl'] = 'http://' . $dataBaru['siteUrl'];
        }

        if (!empty($dataBaru)) {
            if ($modelGambar->insert($dataBaru)) {
                return $this->response->setJSON([
                    'status' => 'sukses',
                    'pesan'  => 'Gambar berhasil ditambahkan',
                    'data'   => null
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'gagal',
                    'pesan'  => implode(", ", $modelGambar->errors()),
                    'data'   => null
                ]);
            }
        }
        return $this->response->setJSON([
            'status' => 'gagal',
            'pesan'  => 'Data tidak valid',
            'data'   => null
        ]);
    }

    public function updateSite($id)
    {
        $modelSitus = new SiteModel();
        $dataPembaruan = [];
        
        if ($this->request->getPost('title') !== null) $dataPembaruan['title'] = esc($this->request->getPost('title'));
        if ($this->request->getPost('url') !== null) $dataPembaruan['url'] = esc($this->request->getPost('url'));
        if ($this->request->getPost('description') !== null) $dataPembaruan['description'] = esc($this->request->getPost('description'));
        if ($this->request->getPost('keywords') !== null) $dataPembaruan['keywords'] = esc($this->request->getPost('keywords'));
        if ($this->request->getPost('clicks') !== null) $dataPembaruan['clicks'] = (int)$this->request->getPost('clicks');
        
        if (!empty($dataPembaruan)) {
            $dataPembaruan['id'] = $id; // FIX: Wajib untuk CodeIgniter 4 is_unique validation {id} placeholder
            if ($modelSitus->update($id, $dataPembaruan)) {
                return $this->response->setJSON([
                    'status' => 'sukses',
                    'pesan'  => 'Situs berhasil diperbarui',
                    'data'   => null
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'gagal',
                    'pesan'  => implode(", ", $modelSitus->errors()),
                    'data'   => null
                ]);
            }
        }
        return $this->response->setJSON([
            'status' => 'gagal',
            'pesan'  => 'Data tidak valid',
            'data'   => null
        ]);
    }

    public function updateImage($id)
    {
        // Auto-migration: Pastikan kolom keywords ada
        $db = \Config\Database::connect();
        try {
            $db->query("ALTER TABLE cari_images ADD COLUMN keywords VARCHAR(512) DEFAULT NULL");
        } catch (\Exception $e) {
            // Kolom sudah ada, lanjutkan
        }

        $modelGambar = new ImageModel();
        $dataPembaruan = [];
        
        if ($this->request->getPost('title') !== null) $dataPembaruan['title'] = esc($this->request->getPost('title'));
        if ($this->request->getPost('alt') !== null) $dataPembaruan['alt'] = esc($this->request->getPost('alt'));
        if ($this->request->getPost('imageUrl') !== null) $dataPembaruan['imageUrl'] = esc($this->request->getPost('imageUrl'));
        if ($this->request->getPost('siteUrl') !== null) $dataPembaruan['siteUrl'] = esc($this->request->getPost('siteUrl'));
        if ($this->request->getPost('clicks') !== null) $dataPembaruan['clicks'] = (int)$this->request->getPost('clicks');
        if ($this->request->getPost('broken') !== null) $dataPembaruan['broken'] = (int)$this->request->getPost('broken');
        if ($this->request->getPost('keywords') !== null) $dataPembaruan['keywords'] = esc($this->request->getPost('keywords'));
        
        if (!empty($dataPembaruan)) {
            if ($modelGambar->update($id, $dataPembaruan)) {
                return $this->response->setJSON([
                    'status' => 'sukses',
                    'pesan'  => 'Gambar berhasil diperbarui',
                    'data'   => null
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'gagal',
                    'pesan'  => implode(", ", $modelGambar->errors()),
                    'data'   => null
                ]);
            }
        }
        return $this->response->setJSON([
            'status' => 'gagal',
            'pesan'  => 'Data tidak valid',
            'data'   => null
        ]);
    }

    public function getTrendData()
    {
        $cache = \Config\Services::cache();
        $kunciCache = 'trend_data_api';
        $data = $cache->get($kunciCache);

        if ($data === null) {
            $modelSitus = new SiteModel();
            $modelGambar = new ImageModel();

            $situsTeratas = $modelSitus->getTopClickedSites(10);
            $gambarTeratas = $modelGambar->getTopClickedImages(10);
            $dataGabungan = $modelGambar->getTopCombinedClicks(10);

            $urlDasarGambar = getenv('app.imgBaseURL') ?: 'https://foto.gkr.my.id/';

            foreach ($gambarTeratas as &$gambar) {
                if (!empty($gambar['imageUrl']) && !preg_match('/^https?:\/\//i', $gambar['imageUrl'])) {
                    $gambar['imageUrl'] = rtrim($urlDasarGambar, '/') . '/' . ltrim($gambar['imageUrl'], '/');
                }
            }

            foreach ($dataGabungan as &$itemGabungan) {
                if (!empty($itemGabungan['imageUrl']) && !preg_match('/^https?:\/\//i', $itemGabungan['imageUrl'])) {
                    $itemGabungan['imageUrl'] = rtrim($urlDasarGambar, '/') . '/' . ltrim($itemGabungan['imageUrl'], '/');
                }
            }

            $data = [
                'topSites'  => $situsTeratas,
                'topImages' => $gambarTeratas,
                'combined'  => $dataGabungan
            ];

            $cache->save($kunciCache, $data, 900);
        }

        return $this->response->setJSON([
            'status' => 'sukses',
            'pesan'  => 'Data tren berhasil ditarik',
            'data'   => $data
        ]);
    }

    public function autocomplete()
    {
        $q = $this->request->getGet('q');
        if (empty(trim($q))) {
            return $this->response->setJSON([]);
        }

        $q = trim($q);
        $materialModel = new \App\Models\MaterialModel();
        $imageModel = new ImageModel();

        $results = [];

        // 1. Cari dari tabel gkr_material (material & warna)
        $materials = $materialModel->groupStart()
                                   ->like('material', $q)
                                   ->orLike('warna', $q)
                                   ->groupEnd()
                                   ->findAll(5);
        foreach ($materials as $m) {
            if (stripos($m['material'], $q) !== false) {
                $results[] = strtolower($m['material']);
            }
            if (stripos($m['warna'], $q) !== false) {
                $results[] = strtolower($m['warna']);
            }
        }

        // 2. Cari dari tabel cari_images (title)
        $images = $imageModel->like('title', $q)
                             ->where('broken', 0)
                             ->groupBy('title')
                             ->findAll(8);
        foreach ($images as $img) {
            if (!empty(trim($img['title']))) {
                $results[] = strtolower(trim($img['title']));
            }
        }

        // Filter duplikat dan ambil 8 hasil teratas
        $results = array_values(array_unique($results));
        $results = array_slice($results, 0, 8);

        return $this->response->setJSON($results);
    }
}
