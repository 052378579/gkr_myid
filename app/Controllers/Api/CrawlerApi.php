<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\CrawlerLib;

class CrawlerApi extends BaseController
{
    public function doCrawl()
    {
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
            $mesinPencari->crawlLocalDirectory($tautan);
        } else {
            $mesinPencari->followLinks($tautan, 1, 3);
        }
    }

    public function resetDb()
    {
        try {
            $basisData = \Config\Database::connect();
            
            if (!$basisData->table('cari_sites')->emptyTable()) {
                $error = $basisData->error();
                throw new \Exception("Gagal mengosongkan cari_sites: " . ($error['message'] ?? 'Silent Database Error'));
            }
            if (!$basisData->table('cari_images')->emptyTable()) {
                $error = $basisData->error();
                throw new \Exception("Gagal mengosongkan cari_images: " . ($error['message'] ?? 'Silent Database Error'));
            }
            
            return $this->response->setJSON([
                'status' => 'sukses',
                'pesan'  => 'Basis data berhasil di-reset',
                'data'   => null
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'gagal',
                'pesan'  => $e->getMessage(),
                'data'   => null
            ]);
        }
    }
}
