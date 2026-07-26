<?php

namespace App\Controllers;

class Beranda extends BaseController
{
    public function index(): string
    {
        $doodleModel = new \App\Models\DoodleModel();
        $doodle = $doodleModel->getActiveDoodle();
        
        $data = [];
        if ($doodle) {
            $data['urlLogo'] = base_url('dokumen/doodle/' . $doodle['gambar']);
            $data['altLogo'] = $doodle['event'];
        }
        $jsonPath = FCPATH . 'versi.json';
        $data['version'] = 'v0.0.1';
        if (file_exists($jsonPath)) {
            $json = json_decode(file_get_contents($jsonPath), true);
            $versiData = $json['data'] ?? [];
            if (!empty($versiData)) {
                usort($versiData, function($a, $b) { return strtotime($b['tanggal_rilis']) - strtotime($a['tanggal_rilis']); });
                $data['version'] = 'v' . $versiData[0]['versi'];
            }
        }

        return view('beranda', $data);
    }
}
