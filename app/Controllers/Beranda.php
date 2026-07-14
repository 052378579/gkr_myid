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
        $versiModel = new \App\Models\VersiModel();
        $latest = $versiModel->orderBy('tanggal_rilis', 'DESC')->first();
        $data['version'] = $latest ? 'v' . $latest['versi'] : 'v0.0.1';

        return view('beranda', $data);
    }
}
