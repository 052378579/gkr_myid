<?php

namespace App\Controllers;

use App\Models\SiteModel;
use App\Models\ImageModel;

class Api extends BaseController
{
    public function updateLinkCount()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $siteModel = new SiteModel();
            $siteModel->where('id', $id)->set('clicks', 'clicks+1', false)->update();
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error']);
    }

    public function updateImageCount()
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $imageModel = new ImageModel();
            $imageModel->where('id', $id)->set('clicks', 'clicks+1', false)->update();
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error']);
    }

    public function setBroken()
    {
        $src = $this->request->getPost('src');
        if ($src) {
            $imageModel = new ImageModel();
            $imageModel->where('imageUrl', $src)->set(['broken' => 1])->update();
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error']);
    }

    // For Vue.js Admin panel
    public function getSites()
    {
        $siteModel = new SiteModel();
        return $this->response->setJSON(['data' => $siteModel->findAll()]);
    }

    public function getImages()
    {
        $imageModel = new ImageModel();
        return $this->response->setJSON(['data' => $imageModel->findAll()]);
    }

    public function deleteSite($id)
    {
        $siteModel = new SiteModel();
        $siteModel->delete($id);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function deleteImage($id)
    {
        $imageModel = new ImageModel();
        $imageModel->delete($id);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function updateSite($id)
    {
        $siteModel = new SiteModel();
        $data = [];
        if ($this->request->getPost('title') !== null) $data['title'] = $this->request->getPost('title');
        if ($this->request->getPost('url') !== null) $data['url'] = $this->request->getPost('url');
        if ($this->request->getPost('description') !== null) $data['description'] = $this->request->getPost('description');
        if ($this->request->getPost('keywords') !== null) $data['keywords'] = $this->request->getPost('keywords');
        if ($this->request->getPost('clicks') !== null) $data['clicks'] = $this->request->getPost('clicks');
        
        if (!empty($data)) {
            $siteModel->update($id, $data);
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error']);
    }

    public function updateImage($id)
    {
        $imageModel = new ImageModel();
        $data = [];
        if ($this->request->getPost('title') !== null) $data['title'] = $this->request->getPost('title');
        if ($this->request->getPost('alt') !== null) $data['alt'] = $this->request->getPost('alt');
        if ($this->request->getPost('imageUrl') !== null) $data['imageUrl'] = $this->request->getPost('imageUrl');
        if ($this->request->getPost('siteUrl') !== null) $data['siteUrl'] = $this->request->getPost('siteUrl');
        if ($this->request->getPost('clicks') !== null) $data['clicks'] = $this->request->getPost('clicks');
        if ($this->request->getPost('broken') !== null) $data['broken'] = $this->request->getPost('broken');
        
        if (!empty($data)) {
            $imageModel->update($id, $data);
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error']);
    }
}
