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
        
        $tipe = $this->request->getGet('tipe');
        $sumber = $this->request->getGet('sumber');

        $logCariModel->select('gkr_logcari.*, gkr_users.nama_lengkap')
                     ->join('gkr_users', 'gkr_users.id_user = gkr_logcari.id_user', 'left');

        if (!empty($tipe)) {
            $logCariModel->where('gkr_logcari.tipe_pencarian', $tipe);
        }
        if (!empty($sumber)) {
            $logCariModel->where('gkr_logcari.source', $sumber);
        }

        $logCari = $logCariModel->orderBy('gkr_logcari.waktu', 'DESC')
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
            'pagerCariCurrent' => $logCariModel->pager->getCurrentPage('logCari'),
            'filterTipe' => $tipe,
            'filterSumber' => $sumber
        ];
        
        return view('admin/log_cari_admin', $data);
    }

    public function export_log()
    {
        $logCariModel = new \App\Models\LogCariModel();
        
        $tipe = $this->request->getGet('tipe');
        $sumber = $this->request->getGet('sumber');
        $format = $this->request->getGet('format');

        $logCariModel->select('gkr_logcari.*, gkr_users.nama_lengkap')
                     ->join('gkr_users', 'gkr_users.id_user = gkr_logcari.id_user', 'left');

        if (!empty($tipe)) {
            $logCariModel->where('gkr_logcari.tipe_pencarian', $tipe);
        }
        if (!empty($sumber)) {
            $logCariModel->where('gkr_logcari.source', $sumber);
        }

        $dataLog = $logCariModel->orderBy('gkr_logcari.waktu', 'DESC')->findAll();

        if ($format === 'excel') {
            header("Content-Type: application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=Log_Pencarian_" . date('Ymd_His') . ".xls");
            
            echo '<table border="1">
                <tr>
                    <th>Waktu</th>
                    <th>Platform</th>
                    <th>Nama Pengguna</th>
                    <th>Kata Kunci</th>
                    <th>Tipe</th>
                    <th>Alamat IP</th>
                </tr>';
            foreach ($dataLog as $log) {
                $nama = $log['nama_lengkap'] ?? 'Tamu / Anonim';
                $tipeStr = $log['tipe_pencarian'];
                if ($log['kata_kunci'] === 'UPLOADED_IMAGE') {
                    $tipeStr = 'AI Vision';
                    $log['kata_kunci'] = 'Foto';
                }
                echo '<tr>
                    <td>' . $log['waktu'] . '</td>
                    <td>' . $log['source'] . '</td>
                    <td>' . htmlspecialchars($nama) . '</td>
                    <td>' . htmlspecialchars($log['kata_kunci']) . '</td>
                    <td>' . htmlspecialchars($tipeStr) . '</td>
                    <td>' . htmlspecialchars($log['alamat_ip'] ?? '-') . '</td>
                </tr>';
            }
            echo '</table>';
            exit;
        } else {
            // Default to CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=Log_Pencarian_' . date('Ymd_His') . '.csv');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Waktu', 'Platform', 'Nama Pengguna', 'Kata Kunci', 'Tipe', 'Alamat IP']);
            
            foreach ($dataLog as $log) {
                $nama = $log['nama_lengkap'] ?? 'Tamu / Anonim';
                $tipeStr = $log['tipe_pencarian'];
                if ($log['kata_kunci'] === 'UPLOADED_IMAGE') {
                    $tipeStr = 'AI Vision';
                    $log['kata_kunci'] = 'Foto';
                }
                fputcsv($output, [
                    $log['waktu'],
                    $log['source'],
                    $nama,
                    $log['kata_kunci'],
                    $tipeStr,
                    $log['alamat_ip'] ?? '-'
                ]);
            }
            fclose($output);
            exit;
        }
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
            ->orderBy('erp_modified', 'DESC')
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
