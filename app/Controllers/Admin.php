<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function index()
    {
        $versiModel = new \App\Models\VersiModel();
        $latest = $versiModel->orderBy('tanggal_rilis', 'DESC')->first();
        $version = $latest ? 'v' . $latest['versi'] : 'v1.0.0';
        
        return view('admin', ['version' => $version]);
    }
}
