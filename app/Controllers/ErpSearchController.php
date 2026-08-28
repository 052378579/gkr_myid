<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ErpSearchController extends BaseController
{
    public function index()
    {
        $doodleModel = new \App\Models\DoodleModel();
        $doodle = $doodleModel->getActiveDoodle();
        
        $data = [];
        if ($doodle) {
            $data['urlLogo'] = base_url('dokumen/doodle/' . $doodle['gambar']);
            $data['altLogo'] = $doodle['event'];
        }

        // Fetch dynamic version from versi.json
        $jsonPath = FCPATH . 'versi.json';
        $data['version'] = 'v0.8.15';
        if (file_exists($jsonPath)) {
            $json = json_decode(file_get_contents($jsonPath), true);
            $versiData = $json['data'] ?? [];
            if (!empty($versiData)) {
                usort($versiData, function($a, $b) { return strtotime($b['tanggal_rilis']) - strtotime($a['tanggal_rilis']); });
                $data['version'] = 'v' . $versiData[0]['versi'];
            }
        }

        return view('erp_search', $data);
    }

    public function liveSearch()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('gkr_erp');

        $query = $this->request->getGet('q');
        $page = (int) $this->request->getGet('page');
        if ($page < 1) $page = 1;

        $limit = 10;
        $offset = ($page - 1) * $limit;

        if (!empty($query)) {
            $builder->groupStart()
                    ->like('kode_bom', $query)
                    ->orLike('item_name', $query)
                    ->orLike('dimensi', $query)
                    ->orLike('finishing', $query)
                    ->orLike('buyer', $query)
                    ->groupEnd();
        }

        // Count total results for pagination
        $totalResults = $builder->countAllResults(false);

        // Fetch data
        $builder->select('kode_bom, bom_name, item_name, dimensi, finishing, buyer, erp_modified')
                ->orderBy('erp_modified', 'DESC')
                ->limit($limit, $offset);
        
        $results = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $results,
            'total' => $totalResults,
            'page' => $page,
            'limit' => $limit
        ]);
    }
}
