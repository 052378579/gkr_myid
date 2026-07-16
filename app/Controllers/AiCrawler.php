<?php

namespace App\Controllers;

class AiCrawler extends BaseController
{
    public function index()
    {
        return view('ai_crawl');
    }

    public function doCrawl()
    {
        // Nonaktifkan output buffering untuk live streaming
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', false);
        while (@ob_end_flush());
        ini_set('implicit_flush', true);
        ob_implicit_flush(true);
        header('Cache-Control: no-cache');
        header('Content-Type: text/plain');
        
        // Mencegah PHP Timeout di tengah jalan saat memproses ribuan file
        set_time_limit(0);
        ini_set('max_execution_time', '0');

        echo "Menghubungi AI Scanner Engine di port 5000...\n";
        @ob_flush(); @flush();

        // Gunakan cURL untuk menembak endpoint FastAPI secara live
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:5000/build_index");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0); // Mencegah cURL terputus karena proses ML memakan waktu lama
        
        // PENTING: Tangkap output secara bertahap dan langsung cetak ke layar
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) {
            echo $data;
            @ob_flush(); @flush();
            return strlen($data);
        });

        curl_exec($ch);
        
        if (curl_errno($ch)) {
            echo "\n[ERROR] Koneksi ke AI Engine gagal: " . curl_error($ch) . "\n";
            @ob_flush(); @flush();
        }
        
        curl_close($ch);
    }
}
