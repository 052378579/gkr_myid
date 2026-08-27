<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AiCrawler extends BaseController
{
    public function index()
    {
        return view('admin/ai_admin');
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

        $mode = $this->request->getGetPost('mode') ?? 'sync';
        $url_input = $this->request->getPost('url') ?: '/var/www/FOTO';
        $safe_url = escapeshellarg($url_input);
        
        if ($mode === 'reset') {
            echo "Menjalankan AI Scanner Engine (Mode HARD RESET) pada $url_input...\n";
            $cmd = 'cd /var/www/gkr_myid/python_services && /mnt/sdcard/ai-scanner/env-ai/bin/python -u ai_reset.py ' . $safe_url . ' 2>&1';
        } else {
            echo "Menjalankan AI Scanner Engine (Mode SINKRONISASI INKREMENTAL) pada $url_input...\n";
            $cmd = 'cd /var/www/gkr_myid/python_services && /mnt/sdcard/ai-scanner/env-ai/bin/python -u ai_sync.py ' . $safe_url . ' 2>&1';
        }

        @ob_flush(); @flush();

        // Eksekusi skrip python secara langsung dan tangkap outputnya (termasuk error)
        $handle = popen($cmd, 'r');
        
        if (is_resource($handle)) {
            while (!feof($handle)) {
                $buffer = fread($handle, 4096);
                if ($buffer !== false && $buffer !== '') {
                    echo $buffer;
                    @ob_flush(); @flush();
                }
            }
            pclose($handle);
            // (Notifikasi Webhook Telegram ganda telah dihapus, murni di-handle oleh Python)
        } else {
            echo "\n[ERROR] Gagal mengeksekusi skrip Python.\n";
            @ob_flush(); @flush();
        }
    }

    public function doJanitor()
    {
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', false);
        while (@ob_end_flush());
        ini_set('implicit_flush', true);
        ob_implicit_flush(true);
        header('Cache-Control: no-cache');
        header('Content-Type: text/plain');
        
        set_time_limit(0);
        ini_set('max_execution_time', '0');

        echo "<span style=\"color: #fcc419;\">[START] Memindai database gkr_cari untuk data yatim piatu...</span>\n";
        @ob_flush(); @flush();

        $cariModel = new \App\Models\CariModel();
        // findAll() secara otomatis mengecualikan data yang sudah di Soft Delete (karena useSoftDeletes = true)
        $allData = $cariModel->findAll();
        
        $deletedCount = 0;
        foreach($allData as $item) {
            if (empty($item['imageUrl'])) continue;
            
            $path = '/var/www/FOTO/' . $item['imageUrl'];
            if (!file_exists($path)) {
                $cariModel->delete($item['id']);
                echo "<span style=\"color: #ff6b6b;\">Mendeteksi ID " . $item['id'] . ": " . htmlspecialchars($item['imageUrl']) . " fisik HILANG. Melakukan Soft Delete...</span>\n";
                @ob_flush(); @flush();
                $deletedCount++;
                usleep(10000); // 10ms untuk animasi yang mulus di terminal
            }
        }
        
        echo "<br><span style=\"color: #51cf66;\">[SELESAI] Janitor berhasil menyembunyikan $deletedCount data yatim piatu.</span>\n";
        @ob_flush(); @flush();
    }
}
