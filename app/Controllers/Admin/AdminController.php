<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
    public function index()
    {
        return redirect()->to('/admin/dashboard');
    }

    public function dashboard()
    {
        return view('admin/dashboard_admin', ['version' => $this->getAppVersion()]);
    }

    public function cari()
    {
        return view('admin/beranda_admin', ['version' => $this->getAppVersion()]);
    }

    public function doodle()
    {
        return view('admin/doodle_admin', ['version' => $this->getAppVersion()]);
    }
    
    public function log_cari()
    {
        $logCariModel = new \App\Models\LogCariModel();
            
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
            'logCari' => $logCari,
            'pagerCari' => $logCariModel->pager,
            'pagerCariCount' => $logCariModel->pager->getPageCount('logCari'),
            'pagerCariCurrent' => $logCariModel->pager->getCurrentPage('logCari')
        ];
        
        return view('admin/log_cari_admin', $data);
    }

    public function log_user()
    {
        $logUserModel = new \App\Models\LogUserModel();
        
        $logUser = $logUserModel
            ->select('gkr_loguser.*, gkr_users.nama_lengkap')
            ->join('gkr_users', 'gkr_users.id_user = gkr_loguser.id_user', 'left')
            ->orderBy('gkr_loguser.waktu', 'DESC')
            ->paginate(10, 'logUser');

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
            'pagerUserCurrent' => $logUserModel->pager->getCurrentPage('logUser')
        ];
        
        return view('admin/log_user_admin', $data);
    }

    public function erp_data()
    {
        $erpModel = new \App\Models\ErpModel();
        
        $search = $this->request->getGet('search') ?? '';
        $perPage = $this->request->getGet('perPage') ?? 10;

        if (!empty($search)) {
            $erpModel->like('kode_bom', $search);
        }
        
        $erpData = $erpModel
            ->orderBy('terakhir_ditarik', 'DESC')
            ->paginate($perPage, 'erpData');

        $serverIP = $_SERVER['SERVER_ADDR'] ?? '10.147.17.40';
        if (in_array($serverIP, ['127.0.0.1', '::1', 'localhost'])) {
            $serverIP = '10.147.17.40';
        }

        $data = [
            'version' => $this->getAppVersion(),
            'serverIP' => $serverIP,
            'erpData' => $erpData,
            'pager' => $erpModel->pager,
            'pagerCount' => $erpModel->pager->getPageCount('erpData'),
            'pagerCurrent' => $erpModel->pager->getCurrentPage('erpData'),
            'search' => $search,
            'perPage' => $perPage
        ];
        
        return view('admin/erp_data', $data);
    }
}
