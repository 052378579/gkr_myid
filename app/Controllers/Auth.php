<?php

namespace App\Controllers;

use App\Models\UserModel;

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

            return redirect()->to('/')->with('success', 'Selamat datang ' . $user['nama_lengkap'] . '');
        } else {
            return redirect()->to('/login')->with('error', 'Nomor HP tidak terdaftar');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar.');
    }
}
