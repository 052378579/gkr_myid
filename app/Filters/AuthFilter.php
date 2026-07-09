<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika belum login, paksa ke halaman login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Akses ditolak.<br>Silahkan masuk terlebih dahulu');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada tugas khusus setelah request
    }
}
