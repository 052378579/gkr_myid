<?php

namespace App\Controllers;

use App\Libraries\CrawlerLib;

class Crawler extends BaseController
{
    public function index()
    {
        return view('crawl');
    }

    public function doCrawl()
    {
        // Nonaktifkan output buffering untuk mengirim aliran langsung (live stream) ke layar
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', false);
        while (@ob_end_flush());
        ini_set('implicit_flush', true);
        ob_implicit_flush(true);
        header('Cache-Control: no-cache');

        $tautan = $this->request->getPost('url');
        
        if (empty($tautan)) {
            echo "URL/Tautan wajib diisi.\n";
            return;
        }

        echo "Mulai memindai (crawling) URL: $tautan <br>\n";
        @ob_flush(); @flush();

        $mesinPencari = new CrawlerLib();
        
        if (str_starts_with($tautan, '/var/www/FOTO')) {
            // URL Statis untuk pemetaan akan ditangani oleh CrawlerLib
            $mesinPencari->crawlLocalDirectory($tautan);
        } else {
            $mesinPencari->followLinks($tautan, 1, 3); // Batas kedalaman rekursif (max depth 3)
        }
    }

    public function resetDb()
    {
        $basisData = \Config\Database::connect();
        
        // Mengosongkan tabel data
        $basisData->table('cari_sites')->truncate();
        $basisData->table('cari_images')->truncate();
        
        return $this->response->setJSON(['status' => 'sukses']);
    }
}
