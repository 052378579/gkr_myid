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
        
        if ($mode === 'reset') {
            echo "Menjalankan AI Scanner Engine (Mode HARD RESET)...\n";
            $cmd = 'cd /var/www/gkr_myid/python_services && /mnt/sdcard/ai-scanner/env-ai/bin/python -u ai_reset.py 2>&1';
        } else {
            echo "Menjalankan AI Scanner Engine (Mode SINKRONISASI INKREMENTAL)...\n";
            $cmd = 'cd /var/www/gkr_myid/python_services && /mnt/sdcard/ai-scanner/env-ai/bin/python -u ai_sync.py 2>&1';
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
        } else {
            echo "\n[ERROR] Gagal mengeksekusi skrip Python.\n";
            @ob_flush(); @flush();
        }
    }
}
