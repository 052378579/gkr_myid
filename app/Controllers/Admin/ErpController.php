<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;

class ErpController extends BaseController
{
    public function terminalUI()
    {
        return view('admin/erp_view');
    }
    
    public function resetDb()
    {
        $db = \Config\Database::connect();
        try {
            $db->table('gkr_erp')->truncate();
            return $this->response->setJSON(['status' => 'ok']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function executeLiveStream($scriptName, $args = null)
    {
        ignore_user_abort(true);
        set_time_limit(0); 
        if(session_id()) { session_write_close(); }

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); 
        
        while (ob_get_level() > 0) { ob_end_flush(); }

        $scriptPath = escapeshellarg(ROOTPATH . "python_services/{$scriptName}");
        
        $pythonBin = '/mnt/sdcard/ai-scanner/env-ai/bin/python3';
        if (!file_exists($pythonBin)) {
            $pythonBin = 'python3';
        }
        
        if ($args !== null) {
            $safeArgs = escapeshellarg($args);
            $command = "{$pythonBin} {$scriptPath} {$safeArgs} 2>&1";
        } else {
            $command = "{$pythonBin} {$scriptPath} 2>&1";
        }
        
        $handle = popen($command, 'r');

        if ($handle) {
            while (!feof($handle)) {
                $buffer = fgets($handle);
                if ($buffer !== false) {
                    echo "data: " . nl2br(htmlspecialchars($buffer)) . "\n\n";
                    flush(); 
                }
                if (connection_aborted()) { break; }
            }
            pclose($handle);
        } else {
            echo "data: <span style='color:red;'>[SYSTEM ERROR] Gagal mengeksekusi python.</span>\n\n";
            flush();
        }
        
        if (!connection_aborted()) {
            echo "data: [EOF]\n\n"; 
            flush();
        }
        exit(); 
    }

    public function streamCrawl()
    {
        $this->executeLiveStream('erp_crawl.py');
    }

    public function ekstrak()
    {
        $this->executeLiveStream('erp_ekstrak.py', '--inc');
    }

    public function lanjutan()
    {
        $this->executeLiveStream('erp_update.py');
    }
}

