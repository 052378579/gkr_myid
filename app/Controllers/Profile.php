<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $id_user = session()->get('id_user');
        
        $data = [
            'title' => 'Profil Saya',
            'user'  => $userModel->find($id_user)
        ];
        
        return view('profile', $data);
    }

    public function update()
    {
        // Pastikan request adalah POST
        if (!$this->request->is('post')) {
            return redirect()->to('/profile');
        }

        $userModel = new UserModel();
        $id_user = session()->get('id_user');
        $currentUser = $userModel->find($id_user);

        // Ambil data input
        $nama_lengkap = $this->request->getPost('nama_lengkap');
        $divisi       = $this->request->getPost('divisi');
        $access_token = $this->request->getPost('access_token');

        // Sanitasi input
        $nama_lengkap = esc($nama_lengkap);
        $divisi       = esc($divisi);
        
        // Data yang akan di-update
        $updateData = [
            'nama_lengkap' => $nama_lengkap,
            'divisi'       => $divisi,
        ];

        // Update token jika diisi
        if (!empty($access_token)) {
            $updateData['access_token'] = esc($access_token);
        }

        // --- PENANGANAN UPLOAD FOTO PROFIL ---
        $foto = $this->request->getFile('foto_profil');
        
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            // Validasi file (harus gambar, maks 2MB)
            $validationRule = [
                'foto_profil' => [
                    'label' => 'Foto Profil',
                    'rules' => 'is_image[foto_profil]|mime_in[foto_profil,image/jpg,image/jpeg,image/png]|max_size[foto_profil,2048]',
                ],
            ];

            if (!$this->validate($validationRule)) {
                return redirect()->to('/profile')->with('error', $this->validator->getErrors()['foto_profil']);
            }

            // Format penamaan file: {DIVISI}_{NAMA_LENGKAP}.jpg
            // Hapus spasi dan ganti dengan underscore, ubah jadi huruf besar
            $cleanDivisi = strtoupper(preg_replace('/\s+/', '_', trim($divisi)));
            $cleanNama   = strtoupper(preg_replace('/\s+/', '_', trim($nama_lengkap)));
            
            // Kita paksa ekstensi file menjadi .jpg agar seragam
            $newName = $cleanDivisi . '_' . $cleanNama . '.jpg';
            
            // Lokasi penyimpanan tersembunyi
            $uploadPath = WRITEPATH . 'KARYAWAN/';
            
            // Hapus foto lama jika namanya berbeda dengan yang baru ATAU sekadar menimpa yang lama
            if (!empty($currentUser['foto_profil']) && $currentUser['foto_profil'] !== $newName) {
                if (file_exists($uploadPath . $currentUser['foto_profil'])) {
                    unlink($uploadPath . $currentUser['foto_profil']);
                }
            }

            // Pindahkan file dan paksa ubah namanya
            // Jika file dengan nama tersebut sudah ada, otomatis akan tertimpa
            $foto->move($uploadPath, $newName, true);
            
            $updateData['foto_profil'] = $newName;
        } else {
            // Jika tidak ada foto baru yang diunggah, tapi nama/divisi berubah, kita harus me-rename file lama
            if (!empty($currentUser['foto_profil'])) {
                $cleanDivisi = strtoupper(preg_replace('/\s+/', '_', trim($divisi)));
                $cleanNama   = strtoupper(preg_replace('/\s+/', '_', trim($nama_lengkap)));
                $expectedName = $cleanDivisi . '_' . $cleanNama . '.jpg';
                
                if ($currentUser['foto_profil'] !== $expectedName) {
                    $uploadPath = WRITEPATH . 'KARYAWAN/';
                    if (file_exists($uploadPath . $currentUser['foto_profil'])) {
                        rename($uploadPath . $currentUser['foto_profil'], $uploadPath . $expectedName);
                        $updateData['foto_profil'] = $expectedName;
                    }
                }
            }
        }

        // Lakukan pembaruan ke database
        try {
            $userModel->update($id_user, $updateData);
            
            // Segarkan session agar perubahan UI instan
            session()->set([
                'nama_lengkap' => $nama_lengkap,
                'divisi'       => $divisi,
                'foto_profil'  => isset($updateData['foto_profil']) ? $updateData['foto_profil'] : $currentUser['foto_profil']
            ]);
            
            return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->to('/profile')->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }
}
