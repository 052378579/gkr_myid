<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CariModel;

class GraciaApi extends BaseController
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    }

    public function getBom()
    {
        $kode = $this->request->getGet('kode');

        if (empty($kode)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Parameter kode barang wajib disertakan.'
            ]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('gkr_erp');
        $builder->where('kode_bom', $kode);
        $builder->orderBy('erp_modified', 'DESC');
        $data = $builder->get()->getRowArray();

        if ($data) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'BOM tidak ditemukan di database ERP Gracia.'
            ]);
        }
    }

    public function updateLinkCount()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $cariModel = new CariModel();
            $cariModel->skipValidation(true)->where('id', $id)->set('klik', 'klik+1', false)->update();
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
            $cariModel = new CariModel();
            $cariModel->skipValidation(true)->where('id', $id)->set('klik', 'klik+1', false)->update();
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
            $cariModel = new CariModel();
            $cariModel->skipValidation(true)->where('imageUrl', $sumberGambar)->set(['rusak' => 1])->update();
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

    public function setupDb()
    {
        $db = \Config\Database::connect();
        
        try {
            // 1. Buat tabel fisik gkr_cari jika belum ada (Nama Kolom Bahasa Indonesia)
            $db->query("CREATE TABLE IF NOT EXISTS `gkr_cari` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `judul` VARCHAR(255) NOT NULL,
                `alt` VARCHAR(255) DEFAULT NULL,
                `deskripsi` TEXT DEFAULT NULL,
                `url` VARCHAR(512) DEFAULT NULL,
                `imageUrl` VARCHAR(512) DEFAULT NULL,
                `siteUrl` VARCHAR(512) DEFAULT NULL,
                `kata_kunci` VARCHAR(512) DEFAULT NULL,
                `klik` INT(11) NOT NULL DEFAULT 0,
                `rusak` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `deleted_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_klik` (`klik`),
                KEY `idx_rusak` (`rusak`),
                FULLTEXT KEY `ft_pencarian` (`judul`, `kata_kunci`, `alt`, `deskripsi`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // 2. Inisialisasi gkr_material
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
                'pesan'  => 'Basis data gkr_cari (Kolom Bahasa Indonesia) berhasil disiapkan.',
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

    public function getMaterials()
    {
        $modelMaterial = new \App\Models\MaterialModel();
        return $this->response->setJSON([
            'status' => 'sukses',
            'pesan'  => 'Data material berhasil ditarik',
            'data'   => $modelMaterial->findAll()
        ]);
    }

    private function getFotoUrlPrefix(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
        $serverIp = $_SERVER['SERVER_ADDR'] ?? '';
        $combined = strtolower($host . ' ' . $serverIp);

        if (preg_match('/192\.168\.1\.4|10\.147\.17\.40|budi\.biz\.id|localhost/', $combined)) {
            return 'https://foto.budi.biz.id/';
        }

        return 'https://foto.gkr.my.id/';
    }

    public function getSites()
    {
        $cariModel = new CariModel();
        $data = $cariModel->groupStart()->where('imageUrl IS NULL')->orWhere('imageUrl', '')->groupEnd()->orderBy('id', 'DESC')->findAll();
        $urlDasarGambar = $this->getFotoUrlPrefix();
        
        foreach ($data as &$barisSitus) {
            $barisSitus['title'] = $barisSitus['judul'];
            $barisSitus['description'] = $barisSitus['deskripsi'];
            $barisSitus['keywords'] = $barisSitus['kata_kunci'];
            $barisSitus['clicks'] = $barisSitus['klik'];

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
        $cariModel = new CariModel();
        $data = $cariModel->where('imageUrl IS NOT NULL')->where('imageUrl !=', '')->orderBy('id', 'DESC')->findAll();
        $urlDasarGambar = $this->getFotoUrlPrefix();
        
        foreach ($data as &$barisGambar) {
            $barisGambar['title'] = $barisGambar['judul'];
            $barisGambar['keywords'] = $barisGambar['kata_kunci'];
            $barisGambar['clicks'] = $barisGambar['klik'];
            $barisGambar['broken'] = $barisGambar['rusak'];

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

    public function getTopSearched()
    {
        $cariModel = new CariModel();
        
        // Ambil Top 10 Barang berdasarkan kolom 'klik'
        $topItems = $cariModel->select('id, judul, alt, deskripsi, url, imageUrl, siteUrl, kata_kunci, klik, rusak')
                             ->orderBy('klik', 'DESC')
                             ->limit(10)
                             ->findAll();
                             
        $urlDasarGambar = $this->getFotoUrlPrefix();
        foreach ($topItems as &$item) {
            $item['title'] = $item['judul'];
            $item['clicks'] = $item['klik'];
            if (!empty($item['imageUrl']) && !preg_match('/^https?:\/\//i', $item['imageUrl'])) {
                $item['imageUrl'] = rtrim($urlDasarGambar, '/') . '/' . ltrim($item['imageUrl'], '/');
            }
        }

        $totalKlikResult = (new CariModel())->selectSum('klik')->first();
        $totalKlik = (int)($totalKlikResult['klik'] ?? 0);
        $totalItems = (new CariModel())->countAllResults();
        $totalBroken = (new CariModel())->where('rusak', 1)->countAllResults();
        $totalUsers = (new \App\Models\UserModel())->countAllResults();
        
        $topProduct = $topItems[0]['judul'] ?? '-';

        return $this->response->setJSON([
            'status' => 'sukses',
            'pesan'  => 'Data KPI berhasil ditarik',
            'data'   => [
                'top10'       => $topItems,
                'totalKlik'   => $totalKlik,
                'totalItems'  => $totalItems,
                'totalBroken' => $totalBroken,
                'totalUsers'  => $totalUsers,
                'topProduct'  => $topProduct
            ]
        ]);
    }

    public function deleteSite($id)
    {
        $cariModel = new CariModel();
        $cariModel->delete($id);
        return $this->response->setJSON([
            'status' => 'sukses',
            'pesan'  => 'Situs berhasil dihapus',
            'data'   => null
        ]);
    }

    public function deleteImage($id)
    {
        $cariModel = new CariModel();
        $cariModel->delete($id);
        return $this->response->setJSON([
            'status' => 'sukses',
            'pesan'  => 'Gambar berhasil dihapus',
            'data'   => null
        ]);
    }

    public function storeSite()
    {
        $cariModel = new CariModel();
        $dataBaru = [];
        
        $title = $this->request->getPost('judul') ?? $this->request->getPost('title');
        $desc = $this->request->getPost('deskripsi') ?? $this->request->getPost('description');
        $kw = $this->request->getPost('kata_kunci') ?? $this->request->getPost('keywords');

        if ($title) $dataBaru['judul'] = esc($title);
        if ($this->request->getPost('url')) $dataBaru['url'] = esc($this->request->getPost('url'));
        if ($desc) $dataBaru['deskripsi'] = esc($desc);
        if ($kw) $dataBaru['kata_kunci'] = esc($kw);
        
        if (!empty($dataBaru['url']) && !preg_match('/^https?:\/\//i', $dataBaru['url'])) {
            $dataBaru['url'] = 'http://' . $dataBaru['url'];
        }

        if (!empty($dataBaru['judul'])) {
            if ($cariModel->insert($dataBaru)) {
                return $this->response->setJSON([
                    'status' => 'sukses',
                    'pesan'  => 'Situs berhasil ditambahkan',
                    'data'   => null
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'gagal',
                    'pesan'  => implode(", ", $cariModel->errors()),
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
        $cariModel = new CariModel();
        $dataBaru = [];
        
        $title = $this->request->getPost('judul') ?? $this->request->getPost('title');
        $kw = $this->request->getPost('kata_kunci') ?? $this->request->getPost('keywords');

        if ($title) $dataBaru['judul'] = esc($title);
        if ($this->request->getPost('alt')) $dataBaru['alt'] = esc($this->request->getPost('alt'));
        if ($this->request->getPost('imageUrl')) $dataBaru['imageUrl'] = esc($this->request->getPost('imageUrl'));
        if ($this->request->getPost('siteUrl')) $dataBaru['siteUrl'] = esc($this->request->getPost('siteUrl'));
        if ($kw) $dataBaru['kata_kunci'] = esc($kw);
        
        if (!empty($dataBaru['imageUrl']) && !preg_match('/^https?:\/\//i', $dataBaru['imageUrl'])) {
            $dataBaru['imageUrl'] = 'http://' . $dataBaru['imageUrl'];
        }
        if (!empty($dataBaru['siteUrl']) && !preg_match('/^https?:\/\//i', $dataBaru['siteUrl'])) {
            $dataBaru['siteUrl'] = 'http://' . $dataBaru['siteUrl'];
        }

        if (!empty($dataBaru['judul'])) {
            if ($cariModel->insert($dataBaru)) {
                return $this->response->setJSON([
                    'status' => 'sukses',
                    'pesan'  => 'Gambar berhasil ditambahkan',
                    'data'   => null
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'gagal',
                    'pesan'  => implode(", ", $cariModel->errors()),
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
        $cariModel = new CariModel();
        $dataPembaruan = [];
        
        $title = $this->request->getPost('judul') ?? $this->request->getPost('title');
        $desc = $this->request->getPost('deskripsi') ?? $this->request->getPost('description');
        $kw = $this->request->getPost('kata_kunci') ?? $this->request->getPost('keywords');
        $klik = $this->request->getPost('klik') ?? $this->request->getPost('clicks');

        if ($title !== null) $dataPembaruan['judul'] = esc($title);
        if ($this->request->getPost('url') !== null) $dataPembaruan['url'] = esc($this->request->getPost('url'));
        if ($desc !== null) $dataPembaruan['deskripsi'] = esc($desc);
        if ($kw !== null) $dataPembaruan['kata_kunci'] = esc($kw);
        if ($klik !== null) $dataPembaruan['klik'] = (int)$klik;
        
        if (!empty($dataPembaruan)) {
            $dataPembaruan['id'] = $id;
            if ($cariModel->update($id, $dataPembaruan)) {
                return $this->response->setJSON([
                    'status' => 'sukses',
                    'pesan'  => 'Situs berhasil diperbarui',
                    'data'   => null
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'gagal',
                    'pesan'  => implode(", ", $cariModel->errors()),
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
        $cariModel = new CariModel();
        $dataPembaruan = [];
        
        $title = $this->request->getPost('judul') ?? $this->request->getPost('title');
        $kw = $this->request->getPost('kata_kunci') ?? $this->request->getPost('keywords');
        $klik = $this->request->getPost('klik') ?? $this->request->getPost('clicks');
        $rusak = $this->request->getPost('rusak') ?? $this->request->getPost('broken');

        $desc = $this->request->getPost('deskripsi') ?? $this->request->getPost('description');
        $url = $this->request->getPost('url');
        $kodeBom = $this->request->getPost('kode_bom');

        if ($title !== null) $dataPembaruan['judul'] = esc($title);
        if ($this->request->getPost('alt') !== null) $dataPembaruan['alt'] = esc($this->request->getPost('alt'));
        if ($desc !== null) $dataPembaruan['deskripsi'] = esc($desc);
        if ($url !== null) $dataPembaruan['url'] = esc($url);
        if ($this->request->getPost('imageUrl') !== null) {
            $rawImg = esc($this->request->getPost('imageUrl'));
            $rawImg = str_replace(['https://foto.budi.biz.id/', 'http://foto.budi.biz.id/', 'https://foto.gkr.my.id/', 'http://foto.gkr.my.id/'], '', $rawImg);
            $dataPembaruan['imageUrl'] = $rawImg;
        }
        if ($this->request->getPost('siteUrl') !== null) {
            $rawSite = esc($this->request->getPost('siteUrl'));
            $rawSite = str_replace(['https://foto.budi.biz.id/', 'http://foto.budi.biz.id/', 'https://foto.gkr.my.id/', 'http://foto.gkr.my.id/'], '', $rawSite);
            $dataPembaruan['siteUrl'] = $rawSite;
        }
        if ($kodeBom !== null) {
            $cleanBom = esc(trim($kodeBom));
            if (!empty($cleanBom) && $cleanBom !== '-' && $cleanBom !== 'FG-') {
                $cleanBom = strtoupper($cleanBom);
                if (!str_starts_with($cleanBom, 'FG-') && str_starts_with($cleanBom, 'FG')) {
                    $cleanBom = 'FG-' . ltrim(substr($cleanBom, 2), '-_ ');
                }
            }
            $dataPembaruan['kode_bom'] = $cleanBom;
        }
        if ($klik !== null) $dataPembaruan['klik'] = (int)$klik;
        if ($rusak !== null) $dataPembaruan['rusak'] = (int)$rusak;
        if ($kw !== null) $dataPembaruan['kata_kunci'] = esc($kw);
        
        if (!empty($dataPembaruan)) {
            if ($cariModel->update($id, $dataPembaruan)) {
                return $this->response->setJSON([
                    'status' => 'sukses',
                    'pesan'  => 'Gambar berhasil diperbarui',
                    'data'   => null
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'gagal',
                    'pesan'  => implode(", ", $cariModel->errors()),
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
            $cariModel = new CariModel();

            $situsTeratas = $cariModel->getTopClickedEntities('situs', 10);
            $gambarTeratas = $cariModel->getTopClickedEntities('gambar', 10);
            $dataGabungan = $cariModel->getTopClickedEntities(null, 10);

            $urlDasarGambar = getenv('app.imgBaseURL') ?: 'https://foto.gkr.my.id/';

            foreach ($situsTeratas as &$situs) {
                $situs['title'] = $situs['judul'];
                $situs['clicks'] = $situs['klik'];
            }

            foreach ($gambarTeratas as &$gambar) {
                $gambar['title'] = $gambar['judul'];
                $gambar['clicks'] = $gambar['klik'];
                if (!empty($gambar['imageUrl']) && !preg_match('/^https?:\/\//i', $gambar['imageUrl'])) {
                    $gambar['imageUrl'] = rtrim($urlDasarGambar, '/') . '/' . ltrim($gambar['imageUrl'], '/');
                }
            }

            foreach ($dataGabungan as &$itemGabungan) {
                $itemGabungan['title'] = $itemGabungan['judul'];
                $itemGabungan['clicks'] = $itemGabungan['klik'];
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
        $cariModel = new CariModel();

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

        // 2. Cari dari tabel fisik gkr_cari (judul)
        $items = $cariModel->like('judul', $q)
                           ->where('rusak', 0)
                           ->groupBy('judul')
                           ->findAll(8);
        foreach ($items as $item) {
            if (!empty(trim($item['judul']))) {
                $results[] = strtolower(trim($item['judul']));
            }
        }

        // Filter duplikat dan ambil 8 hasil teratas
        $results = array_values(array_unique($results));
        $results = array_slice($results, 0, 8);

        return $this->response->setJSON($results);
    }
}
