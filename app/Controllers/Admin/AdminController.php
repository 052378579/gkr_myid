<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
    private function getAppVersion()
    {
        $jsonPath = FCPATH . 'versi.json';
        if (file_exists($jsonPath)) {
            $json = json_decode(file_get_contents($jsonPath), true);
            $versiData = $json['data'] ?? [];
            if (!empty($versiData)) {
                usort($versiData, function($a, $b) { return strtotime($b['tanggal_rilis']) - strtotime($a['tanggal_rilis']); });
                return 'v' . $versiData[0]['versi'];
            }
        }
        return 'v1.0.0';
    }

    public function index()
    {
        return view('admin/admin', ['version' => $this->getAppVersion()]);
    }

    public function doodle()
    {
        return view('admin/doodle', ['version' => $this->getAppVersion()]);
    }
    
    public function log()
    {
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
            'version' => $this->getAppVersion(),
            'logUser' => $logUser,
            'logCari' => $logCari
        ];
        
        return view('admin/log', $data);
    }
}
