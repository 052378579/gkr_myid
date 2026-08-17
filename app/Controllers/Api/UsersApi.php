<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UsersApi extends BaseController
{
    public function check()
    {
        $phone = $this->request->getGet('phone');

        if (empty($phone)) {
            return $this->response->setJSON([
                'status' => 'gagal',
                'pesan'  => 'Parameter phone tidak ditemukan.'
            ])->setStatusCode(200);
        }

        // Sanitasi Nomor HP
        // 1. Buang @c.us jika formatnya langsung dari WhatsApp
        $phone = str_replace('@c.us', '', $phone);
        // 2. Hapus karakter non-numerik (seperti + atau spasi)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        // 3. Ubah awalan 62 menjadi 0
        if (substr($phone, 0, 2) === '62') {
            $phone = '0' . substr($phone, 2);
        }

        $userModel = new UserModel();
        // Pencarian murni
        $user = $userModel->like('no_hp', substr($phone, 1))->first();

        // Pesan standar penolakan (sesuai instruksi)
        $pesan_gagal = 'Mohon maaf, nomor Anda belum terdaftar di sistem kami. Asisten Gracia hanya dapat melayani karyawan internal yang terdaftar. Silakan hubungi tim IT untuk pendaftaran nomor.';

        if ($user) {
            if (strtolower(trim($user['status'])) === 'aktif') {
                return $this->response->setJSON([
                    'status' => 'sukses',
                    'pesan'  => 'Karyawan terdaftar',
                    'data'   => [
                        'nama_lengkap' => $user['nama_lengkap'],
                        'divisi'       => $user['divisi']
                    ]
                ])->setStatusCode(200);
            } else {
                return $this->response->setJSON([
                    'status' => 'gagal',
                    'pesan'  => $pesan_gagal
                ])->setStatusCode(200);
            }
        }

        // Jika tidak ditemukan
        return $this->response->setJSON([
            'status' => 'gagal',
            'pesan'  => $pesan_gagal
        ])->setStatusCode(200);
    }
}



