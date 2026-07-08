<?php

namespace App\Controllers;

class Home extends BaseController
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
        $versiModel = new \App\Models\VersiModel();
        $latest = $versiModel->orderBy('tanggal_rilis', 'DESC')->first();
        $data['version'] = $latest ? 'v' . $latest['versi'] : 'v1.0.0';

        return view('index', $data);
    }
}
