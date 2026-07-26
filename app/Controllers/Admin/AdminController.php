<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
    public function index()
    {
        return view('admin/beranda_admin', ['version' => $this->getAppVersion()]);
    }

    public function doodle()
    {
        return view('admin/doodle_admin', ['version' => $this->getAppVersion()]);
    }
    
    public function log()
    {
        $logUserModel = new \App\Models\LogUserModel();
        $logCariModel = new \App\Models\LogCariModel();
        
        $logUser = $logUserModel
            ->select('gkr_loguser.*, gkr_users.nama_lengkap')
            ->join('gkr_users', 'gkr_users.id_user = gkr_loguser.id_user', 'left')
            ->orderBy('gkr_loguser.waktu', 'DESC')
            ->paginate(10, 'logUser');
            
        $logCari = $logCariModel
            ->select('gkr_logcari.*, gkr_users.nama_lengkap')
            ->join('gkr_users', 'gkr_users.id_user = gkr_logcari.id_user', 'left')
            ->orderBy('gkr_logcari.waktu', 'DESC')
            ->paginate(10, 'logCari');

        $serverIP = $_SERVER['SERVER_ADDR'] ?? '10.147.17.40';
        if (in_array($serverIP, ['127.0.0.1', '::1', 'localhost'])) {
            $serverIP = '10.147.17.40';
        }

        $data = [
            'version' => $this->getAppVersion(),
            'serverIP' => $serverIP,
            'logUser' => $logUser,
            'pagerUser' => $logUserModel->pager,
            'pagerUserCount' => $logUserModel->pager->getPageCount('logUser'),
            'pagerUserCurrent' => $logUserModel->pager->getCurrentPage('logUser'),
            'logCari' => $logCari,
            'pagerCari' => $logCariModel->pager,
            'pagerCariCount' => $logCariModel->pager->getPageCount('logCari'),
            'pagerCariCurrent' => $logCariModel->pager->getCurrentPage('logCari')
        ];
        
        return view('admin/log_admin', $data);
    }
}
