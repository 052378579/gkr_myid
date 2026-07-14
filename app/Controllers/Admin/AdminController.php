<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
    public function index()
    {
        $versiModel = new \App\Models\VersiModel();
        $latest = $versiModel->orderBy('tanggal_rilis', 'DESC')->first();
        $version = $latest ? 'v' . $latest['versi'] : 'v1.0.0';
        
        return view('admin/admin', ['version' => $version]);
    }

    public function doodle()
    {
        $versiModel = new \App\Models\VersiModel();
        $latest = $versiModel->orderBy('tanggal_rilis', 'DESC')->first();
        $version = $latest ? 'v' . $latest['versi'] : 'v1.0.0';
        
        return view('admin/doodle', ['version' => $version]);
    }
}
