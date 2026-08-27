<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SuperAdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Pertama pastikan mereka sudah login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.<br>Silahkan masuk terlebih dahulu');
        }

        // Cek spesifik apakah user tersebut adalah id_user = 1 (Super Admin)
        if (session()->get('id_user') != 1) {
            // Jika bukan super admin, redirect kembali ke halaman utama
            return redirect()->to('/beranda')->with('error', 'Akses ditolak. Halaman ini khusus untuk Super Admin.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada tugas khusus setelah request
    }
}
