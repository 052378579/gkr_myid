<?php

namespace App\Controllers;

use App\Models\SiteModel;
use App\Models\ImageModel;

class Search extends BaseController
{
    public function index()
    {
        $query = $this->request->getGet('q') ?? '';
        $type = $this->request->getGet('type') ?? 'sites';
        $page = (int)($this->request->getGet('page') ?? 1);
        $pageSize = 20;

        if (empty(trim($query))) {
            return redirect()->to('/');
        }

        $siteModel = new SiteModel();
        $imageModel = new ImageModel();

        $data = [
            'query' => $query,
            'type'  => $type,
            'page'  => $page,
        ];

        if ($type === 'sites') {
            $data['totalResults'] = $siteModel->like('title', $query)
                                              ->orLike('description', $query)
                                              ->orLike('url', $query)
                                              ->countAllResults(false);
            $data['results'] = $siteModel->like('title', $query)
                                         ->orLike('description', $query)
                                         ->orLike('url', $query)
                                         ->paginate($pageSize, 'default', $page);
            $data['pager'] = $siteModel->pager;
        } else {
            $data['totalResults'] = $imageModel->like('title', $query)
                                               ->orLike('alt', $query)
                                               ->orLike('imageUrl', $query)
                                               ->where('broken', 0)
                                               ->countAllResults(false);
            $data['results'] = $imageModel->like('title', $query)
                                          ->orLike('alt', $query)
                                          ->orLike('imageUrl', $query)
                                          ->where('broken', 0)
                                          ->paginate($pageSize, 'default', $page);
            $data['pager'] = $imageModel->pager;
        }

        return view('search_results', $data);
    }
}
