<?php

namespace App\Controllers;

use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Exceptions\HTTPException;

class Dokumen extends BaseController
{
    /**
     * Penyalur berkas fisik dari direktori writable/KARYAWAN
     * 
     * @param string $filename Nama file yang diminta
     */
    public function karyawan($filename = null)
    {
        if (empty($filename)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Amankan nama file dari direktori traversal
        $filename = basename($filename);
        
        $path = WRITEPATH . 'KARYAWAN/' . $filename;

        if (file_exists($path)) {
            $file = new File($path);
            $mime = $file->getMimeType();

            // Atur header Content-Type sesuai dengan MIME type berkas
            $this->response->setContentType($mime);
            
            // Baca dan pancarkan isi file
            return $this->response->setBody(file_get_contents($path));
        }

        // Jika tidak ditemukan, kembalikan 404
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    /**
     * Penyalur berkas fisik dari direktori writable/GKR_DOODLE
     * 
     * @param string $filename Nama file yang diminta
     */
    public function doodle($filename = null)
    {
        if (empty($filename)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Amankan nama file dari direktori traversal
        $filename = basename($filename);
        
        $path = WRITEPATH . 'GKR_DOODLE/' . $filename;

        if (file_exists($path)) {
            $file = new File($path);
            $mime = $file->getMimeType();

            // Atur header Content-Type sesuai dengan MIME type berkas
            $this->response->setContentType($mime);
            
            // Baca dan pancarkan isi file
            return $this->response->setBody(file_get_contents($path));
        }

        // Jika tidak ditemukan, kembalikan 404
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
}
