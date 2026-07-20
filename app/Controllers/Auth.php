<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LogUserModel;

class Auth extends BaseController
{
    public function index()
    {
        // Jika sudah login, lempar ke beranda
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }
        $versiModel = new \App\Models\VersiModel();
        $latest = $versiModel->orderBy('tanggal_rilis', 'DESC')->first();
        $version = $latest ? 'v' . $latest['versi'] : 'v1.0.0';

        $data = [
            'title' => 'Login',
            'version' => $version
        ];
        
        return view('login', $data);
    }

    public function process()
    {
        // Pastikan bukan metode GET
        if (!$this->request->is('post')) {
            return redirect()->to('/login');
        }

        $no_hp = $this->request->getPost('no_hp');
        
        // Sanitasi dasar
        $no_hp = esc($no_hp);

        // Validasi input
        if (empty($no_hp)) {
            return redirect()->to('/login')->with('error', 'Nomor HP tidak boleh kosong');
        }

        $userModel = new UserModel();
        
        // Cari pengguna berdasarkan no_hp
        $user = $userModel->where('no_hp', $no_hp)->first();

        if ($user) {
            // Set session data
            $sessionData = [
                'isLoggedIn'   => true,
                'id_user'      => $user['id_user'],
                'nama_lengkap' => $user['nama_lengkap'],
                'divisi'       => $user['divisi'],
                'foto_profil'  => $user['foto_profil']
            ];
            session()->set($sessionData);
            
            // Perbarui last_ip
            $userModel->update($user['id_user'], [
                'last_ip' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString()
            ]);

            // Kirim notifikasi Telegram
            $this->sendTelegramNotification($user, $this->request->getIPAddress());

            // Catat Log Aktivitas
            $logUserModel = new LogUserModel();
            $logUserModel->catatAktivitas($user['id_user'], 'masuk', $this->request->getIPAddress(), (string) $this->request->getUserAgent());

            return redirect()->to('/')->with('success', 'Selamat datang ' . $user['nama_lengkap'] . '');
        } else {
            // Catat Log Percobaan Gagal
            $logUserModel = new LogUserModel();
            $logUserModel->catatAktivitas(null, 'gagal_masuk', $this->request->getIPAddress(), (string) $this->request->getUserAgent());

            return redirect()->to('/login')->with('error', 'Nomor HP tidak terdaftar');
        }
    }

    public function logout()
    {
        // Tangkap data pengguna sebelum session dihancurkan
        if (session()->get('isLoggedIn')) {
            $user_temp = [
                'nama_lengkap' => session()->get('nama_lengkap'),
                'divisi'       => session()->get('divisi'),
                'no_hp'        => '-'
            ];
            $ipAddress = $this->request->getIPAddress();
            
            // Kirim notifikasi Logout
            $this->sendTelegramNotification($user_temp, $ipAddress, 'Logout');

            // Catat Log Aktivitas
            $logUserModel = new LogUserModel();
            $logUserModel->catatAktivitas(session()->get('id_user'), 'keluar', $ipAddress, (string) $this->request->getUserAgent());
        }

        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar.');
    }

    private function sendTelegramNotification($user, $ipAddress, $jenisAksi = 'Login')
    {
        $botToken = "8784963582:AAG90wLXKxfKEXa3aLy0sxURZbbyrZnqP9Q";
        $chatId   = "8784856529";
        
        // Atur timezone sesuai lokasi server/user
        date_default_timezone_set('Asia/Jakarta');
        $waktu = date('d-m-Y H:i');
        
        // Membedakan ikon dan judul pesan
        $icon = ($jenisAksi === 'Login') ? '🔓' : '🔒';
        
        $pesan = "{$icon} <b>Notifikasi {$jenisAksi}</b>\n\n";
        $pesan .= "<b>Nama:</b> " . $user['nama_lengkap'] . "\n";
        $pesan .= "<b>Divisi:</b> " . $user['divisi'] . "\n";
        $pesan .= "<b>No. HP:</b> " . $user['no_hp'] . "\n";
        $pesan .= "<b>Waktu:</b> " . $waktu . " WIB\n";
        
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        
        try {
            $client = \Config\Services::curlrequest();
            $client->post($url, [
                'form_params' => [
                    'chat_id'    => $chatId,
                    'text'       => $pesan,
                    'parse_mode' => 'HTML'
                ],
                'timeout' => 5,
                'verify'  => false
            ]);
        } catch (\Exception $e) {
            // Catat error ke log agar bisa di-debug tanpa merusak proses login
            log_message('error', 'Telegram API Error: ' . $e->getMessage());
        }
    }
}
