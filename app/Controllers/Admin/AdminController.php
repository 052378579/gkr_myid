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
    public function log()
    {
        $versiModel = new \App\Models\VersiModel();
        $latest = $versiModel->orderBy('tanggal_rilis', 'DESC')->first();
        $version = $latest ? 'v' . $latest['versi'] : 'v1.0.0';

        $logUserModel = new \App\Models\LogUserModel();
        $logCariModel = new \App\Models\LogCariModel();
        
        $db = \Config\Database::connect();
        
        $logUser = $db->table('gkr_loguser')
            ->select('gkr_loguser.*, gkr_users.nama_lengkap')
            ->join('gkr_users', 'gkr_users.id_user = gkr_loguser.id_user', 'left')
            ->orderBy('gkr_loguser.waktu', 'DESC')
            ->get()->getResultArray();
            
        $logCari = $db->table('gkr_logcari')
            ->select('gkr_logcari.*, gkr_users.nama_lengkap')
            ->join('gkr_users', 'gkr_users.id_user = gkr_logcari.id_user', 'left')
            ->orderBy('gkr_logcari.waktu', 'DESC')
            ->get()->getResultArray();

        $data = [
            'version' => $version,
            'logUser' => $logUser,
            'logCari' => $logCari
        ];
        
        return view('admin/log', $data);
    }
}
