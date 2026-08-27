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
        $jsonPath = FCPATH . 'versi.json';
        $version = 'v1.0.0';
        
        if (file_exists($jsonPath)) {
            $json = json_decode(file_get_contents($jsonPath), true);
            $versiData = isset($json['data']) ? $json['data'] : [];
            if (!empty($versiData)) {
                usort($versiData, function($a, $b) {
                    return strtotime($b['tanggal_rilis']) - strtotime($a['tanggal_rilis']);
                });
                $version = 'v' . $versiData[0]['versi'];
            }
        }

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
            // Cek Status Pengguna
            if ($user['status'] === 'pending') {
                return redirect()->to('/login')->with('error', 'Akun Anda masih dalam status Pending. Harap tunggu persetujuan Admin.');
            }
            if ($user['status'] === 'suspend') {
                return redirect()->to('/login')->with('error', 'Akun Anda telah ditangguhkan. Silakan hubungi Administrator.');
            }

            // Izinkan login jika status 'aktif'
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

            // --- NOTIFIKASI WHATSAPP WAHA (N8N) ---
            $jenis_kelamin = isset($user['jenis_kelamin']) ? $user['jenis_kelamin'] : 'L';
            $sapaan = ($jenis_kelamin === 'P') ? 'Ibu' : 'Bapak';
            
            $no_hp_format = $user['no_hp'];
            if (substr($no_hp_format, 0, 1) === '0') {
                $no_hp_format = '62' . substr($no_hp_format, 1);
            }

            $payload = json_encode([
                'no_hp'        => $no_hp_format,
                'nama_lengkap' => $user['nama_lengkap'],
                'sapaan'       => $sapaan
            ]);

            $ch = curl_init('http://localhost:5678/webhook/wa-login-notif');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2); 
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_exec($ch);
            curl_close($ch);
            // --------------------------------------

            return redirect()->to('/')->with('success', 'Selamat datang ' . $user['nama_lengkap'] . '');
        } else {
            // Catat Log Percobaan Gagal
            $logUserModel = new LogUserModel();
            $logUserModel->catatAktivitas(null, 'gagal_masuk', $this->request->getIPAddress(), (string) $this->request->getUserAgent());

            return redirect()->to('/login')->with('error', 'Nomor HP tidak terdaftar');
        }
    }

    public function daftar()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }
        $jsonPath = FCPATH . 'versi.json';
        $version = 'v1.0.0';
        
        if (file_exists($jsonPath)) {
            $json = json_decode(file_get_contents($jsonPath), true);
            $versiData = isset($json['data']) ? $json['data'] : [];
            if (!empty($versiData)) {
                usort($versiData, function($a, $b) {
                    return strtotime($b['tanggal_rilis']) - strtotime($a['tanggal_rilis']);
                });
                $version = 'v' . $versiData[0]['versi'];
            }
        }

        $data = [
            'title' => 'Pendaftaran Karyawan Baru',
            'version' => $version
        ];
        
        return view('daftar', $data);
    }

    public function processDaftar()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/daftar');
        }

        $nama_lengkap = esc($this->request->getPost('nama_lengkap'));
        $no_hp = esc($this->request->getPost('no_hp'));
        $divisi = esc($this->request->getPost('divisi'));

        if (empty($nama_lengkap) || empty($no_hp) || empty($divisi)) {
            return redirect()->to('/daftar')->with('error', 'Semua kolom wajib diisi.');
        }

        $userModel = new \App\Models\UserModel();

        if ($userModel->where('no_hp', $no_hp)->first()) {
            return redirect()->to('/daftar')->with('error', 'Nomor HP sudah terdaftar. Silakan login.');
        }

        $userModel->insert([
            'nama_lengkap' => $nama_lengkap,
            'no_hp' => $no_hp,
            'divisi' => $divisi,
            'status' => 'pending',
            'foto_profil' => 'default.png',
            'last_ip' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString()
        ]);

        return redirect()->to('/login')->with('success', 'Pendaftaran berhasil! Silakan tunggu persetujuan Admin.');
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
