<?php

namespace App\Controllers;

use App\Models\SiteModel;
use App\Models\ImageModel;

class Api extends BaseController
{
    public function __construct()
    {
        // Mengizinkan akses CORS lintas lingkungan (Dev/Prod IP & ZeroTier)
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    }

    public function updateLinkCount()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $modelSitus = new SiteModel();
            // Bypass validasi agar rule 'required' pada URL tidak memblokir penambahan counter
            $modelSitus->skipValidation(true)->where('id', $id)->set('clicks', 'clicks+1', false)->update();
            return $this->response->setJSON(['status' => 'sukses']);
        }
        return $this->response->setJSON(['status' => 'gagal']);
    }

    public function updateImageCount()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $modelGambar = new ImageModel();
            // Bypass validasi
            $modelGambar->skipValidation(true)->where('id', $id)->set('clicks', 'clicks+1', false)->update();
            return $this->response->setJSON(['status' => 'sukses']);
        }
        return $this->response->setJSON(['status' => 'gagal']);
    }

    public function setBroken()
    {
        $sumberGambar = $this->request->getPost('src');
        if ($sumberGambar) {
            // Sanitasi sederhana
            $sumberGambar = esc($sumberGambar);
            
            $modelGambar = new ImageModel();
            // Bypass validasi
            $modelGambar->skipValidation(true)->where('imageUrl', $sumberGambar)->set(['broken' => 1])->update();
            return $this->response->setJSON(['status' => 'sukses']);
        }
        return $this->response->setJSON(['status' => 'gagal']);
    }

    // Untuk antarmuka Vue.js di Panel Admin
    public function setupDb()
    {
        $db = \Config\Database::connect();
        $db->query("CREATE TABLE IF NOT EXISTS `gkr_material` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `material` varchar(100) NOT NULL,
            `warna` varchar(100) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Truncate and Insert dummy data
        $db->query("TRUNCATE TABLE `gkr_material`");
        $db->query("INSERT INTO `gkr_material` (`material`, `warna`) VALUES 
            ('teak', 'natual 002'),
            ('alumunium', 'antrachite bronze'),
            ('alumunium', 'taupe texture'),
            ('fiber', 'terrazzo'),
            ('fiber', 'concreate')
        ");
        
        return $this->response->setJSON(['status' => 'success']);
    }

    public function getMaterials()
    {
        $modelMaterial = new \App\Models\MaterialModel();
        return $this->response->setJSON(['status' => 'success', 'data' => $modelMaterial->findAll()]);
    }

    public function getSites()
    {
        $modelSitus = new SiteModel();
        $data = $modelSitus->findAll();
        $imgBaseUrl = getenv('app.imgBaseURL') ?: 'https://foto.gkr.my.id/';
        
        foreach ($data as &$item) {
            if (!empty($item['url']) && !preg_match('/^https?:\/\//i', $item['url'])) {
                if (str_starts_with($item['url'], '?')) {
                    $item['url'] = rtrim($imgBaseUrl, '/') . '/' . $item['url'];
                } else {
                    $item['url'] = rtrim($imgBaseUrl, '/') . '/' . ltrim($item['url'], '/');
                }
            }
        }
        
        return $this->response->setJSON(['data' => $data]);
    }

    public function getImages()
    {
        $modelGambar = new ImageModel();
        $data = $modelGambar->findAll();
        $imgBaseUrl = getenv('app.imgBaseURL') ?: 'https://foto.gkr.my.id/';
        
        foreach ($data as &$item) {
            if (!empty($item['imageUrl']) && !preg_match('/^https?:\/\//i', $item['imageUrl'])) {
                $item['imageUrl'] = rtrim($imgBaseUrl, '/') . '/' . ltrim($item['imageUrl'], '/');
            }
            if (!empty($item['siteUrl']) && !preg_match('/^https?:\/\//i', $item['siteUrl'])) {
                if (str_starts_with($item['siteUrl'], '?')) {
                    $item['siteUrl'] = rtrim($imgBaseUrl, '/') . '/' . $item['siteUrl'];
                } else {
                    $item['siteUrl'] = rtrim($imgBaseUrl, '/') . '/' . ltrim($item['siteUrl'], '/');
                }
            }
        }
        
        return $this->response->setJSON(['data' => $data]);
    }

    public function deleteSite($id)
    {
        $modelSitus = new SiteModel();
        $modelSitus->delete($id);
        return $this->response->setJSON(['status' => 'sukses']);
    }

    public function deleteImage($id)
    {
        $modelGambar = new ImageModel();
        $modelGambar->delete($id);
        return $this->response->setJSON(['status' => 'sukses']);
    }

    public function storeSite()
    {
        $modelSitus = new SiteModel();
        $dataBaru = [];
        
        // Sanitasi input
        if ($this->request->getPost('title')) $dataBaru['title'] = esc($this->request->getPost('title'));
        if ($this->request->getPost('url')) $dataBaru['url'] = esc($this->request->getPost('url'));
        if ($this->request->getPost('description')) $dataBaru['description'] = esc($this->request->getPost('description'));
        if ($this->request->getPost('keywords')) $dataBaru['keywords'] = esc($this->request->getPost('keywords'));
        
        // Pastikan url ada "http"
        if (!empty($dataBaru['url']) && !preg_match('/^https?:\/\//i', $dataBaru['url'])) {
            $dataBaru['url'] = 'http://' . $dataBaru['url'];
        }

        if (!empty($dataBaru)) {
            if ($modelSitus->insert($dataBaru)) {
                return $this->response->setJSON(['status' => 'sukses']);
            } else {
                return $this->response->setJSON(['status' => 'gagal', 'pesan' => $modelSitus->errors()]);
            }
        }
        return $this->response->setJSON(['status' => 'gagal']);
    }

    public function storeImage()
    {
        $modelGambar = new ImageModel();
        $dataBaru = [];
        
        // Sanitasi input
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
                return $this->response->setJSON(['status' => 'sukses']);
            } else {
                return $this->response->setJSON(['status' => 'gagal', 'pesan' => $modelGambar->errors()]);
            }
        }
        return $this->response->setJSON(['status' => 'gagal']);
    }

    public function updateSite($id)
    {
        $modelSitus = new SiteModel();
        $dataPembaruan = [];
        
        // Sanitasi input (Perlindungan XSS)
        if ($this->request->getPost('title') !== null) $dataPembaruan['title'] = esc($this->request->getPost('title'));
        if ($this->request->getPost('url') !== null) $dataPembaruan['url'] = esc($this->request->getPost('url'));
        if ($this->request->getPost('description') !== null) $dataPembaruan['description'] = esc($this->request->getPost('description'));
        if ($this->request->getPost('keywords') !== null) $dataPembaruan['keywords'] = esc($this->request->getPost('keywords'));
        if ($this->request->getPost('clicks') !== null) $dataPembaruan['clicks'] = (int)$this->request->getPost('clicks');
        
        if (!empty($dataPembaruan)) {
            if ($modelSitus->update($id, $dataPembaruan)) {
                return $this->response->setJSON(['status' => 'sukses']);
            } else {
                // Tangkap dan lempar pesan error dari validasi Model
                return $this->response->setJSON(['status' => 'gagal', 'pesan' => $modelSitus->errors()]);
            }
        }
        return $this->response->setJSON(['status' => 'gagal']);
    }

    public function updateImage($id)
    {
        $modelGambar = new ImageModel();
        $dataPembaruan = [];
        
        // Sanitasi input (Perlindungan XSS)
        if ($this->request->getPost('title') !== null) $dataPembaruan['title'] = esc($this->request->getPost('title'));
        if ($this->request->getPost('alt') !== null) $dataPembaruan['alt'] = esc($this->request->getPost('alt'));
        if ($this->request->getPost('imageUrl') !== null) $dataPembaruan['imageUrl'] = esc($this->request->getPost('imageUrl'));
        if ($this->request->getPost('siteUrl') !== null) $dataPembaruan['siteUrl'] = esc($this->request->getPost('siteUrl'));
        if ($this->request->getPost('clicks') !== null) $dataPembaruan['clicks'] = (int)$this->request->getPost('clicks');
        if ($this->request->getPost('broken') !== null) $dataPembaruan['broken'] = (int)$this->request->getPost('broken');
        
        if (!empty($dataPembaruan)) {
            if ($modelGambar->update($id, $dataPembaruan)) {
                return $this->response->setJSON(['status' => 'sukses']);
            } else {
                // Tangkap dan lempar pesan error dari validasi Model
                return $this->response->setJSON(['status' => 'gagal', 'pesan' => $modelGambar->errors()]);
            }
        }
        return $this->response->setJSON(['status' => 'gagal']);
    }

    public function getTrendData()
    {
        // Gunakan cache bawaan CI4
        $cache = \Config\Services::cache();
        $cacheKey = 'trend_data_api';
        $data = $cache->get($cacheKey);

        if ($data === null) {
            $modelSitus = new SiteModel();
            $modelGambar = new ImageModel();

            $topSites = $modelSitus->getTopClickedSites(10);
            $topImages = $modelGambar->getTopClickedImages(10);
            $combined = $modelGambar->getTopCombinedClicks(10);

            $imgBaseUrl = getenv('app.imgBaseURL') ?: 'https://foto.gkr.my.id/';

            // Perbaiki imageUrl untuk topImages dan combined
            foreach ($topImages as &$img) {
                if (!empty($img['imageUrl']) && !preg_match('/^https?:\/\//i', $img['imageUrl'])) {
                    $img['imageUrl'] = rtrim($imgBaseUrl, '/') . '/' . ltrim($img['imageUrl'], '/');
                }
            }

            foreach ($combined as &$item) {
                if (!empty($item['imageUrl']) && !preg_match('/^https?:\/\//i', $item['imageUrl'])) {
                    $item['imageUrl'] = rtrim($imgBaseUrl, '/') . '/' . ltrim($item['imageUrl'], '/');
                }
            }

            $data = [
                'topSites'  => $topSites,
                'topImages' => $topImages,
                'combined'  => $combined
            ];

            // Cache selama 15 menit (900 detik)
            $cache->save($cacheKey, $data, 900);
        }

        return $this->response->setJSON($data);
    }
}
