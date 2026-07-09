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
    public function getSites()
    {
        $modelSitus = new SiteModel();
        return $this->response->setJSON(['data' => $modelSitus->findAll()]);
    }

    public function getImages()
    {
        $modelGambar = new ImageModel();
        return $this->response->setJSON(['data' => $modelGambar->findAll()]);
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
}
