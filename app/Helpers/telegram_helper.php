<?php

if (!function_exists('send_telegram_notification')) {
    /**
     * Helper untuk mengirim Notifikasi Telegram
     *
     * @param string $jenis_proses  Misal: 'Crawler', 'AI Trainer'
     * @param string $direktori     Lokasi direktori yang diproses
     * @param string $info_tambahan Pesan status/jumlah item tambahan
     */
    function send_telegram_notification($jenis_proses, $direktori, $info_tambahan)
    {
        $botToken = env('BOT_TOKEN');
        $chatId   = env('CHAT_ID');

        if (empty($botToken) || empty($chatId)) {
            return;
        }

        // Deteksi Label Server (DEV vs PROD)
        $serverIp   = $_SERVER['SERVER_ADDR'] ?? '';
        $serverHost = $_SERVER['HTTP_HOST'] ?? gethostname();
        $env        = defined('ENVIRONMENT') ? ENVIRONMENT : 'production';
        
        // Menangkap IP fisik langsung dari Sistem Operasi (Tangguh di CLI & Reverse Proxy)
        $machineIps = function_exists('shell_exec') ? trim(@shell_exec('hostname -I 2>/dev/null')) : '';
        
        $identitas = strtolower($serverIp . ' | ' . $serverHost . ' | ' . $env . ' | ' . $machineIps);
        
        if (str_contains($identitas, '192.168.1.4') || str_contains($identitas, '10.147.17.40') || str_contains($identitas, 'gkr.budi.biz.id') || $env === 'development') {
            $serverLabel = 'DEV';
        } elseif (str_contains($identitas, '192.168.1.17') || str_contains($identitas, '10.147.17.60') || str_contains($identitas, 'gkr.my.id')) {
            $serverLabel = 'PROD';
        } else {
            $serverLabel = 'TIDAK DIKENAL';
        }

        date_default_timezone_set('Asia/Jakarta');
        $waktu = date('d-m-Y H:i:s');

        $pesan = "🤖 <b>Auto {$jenis_proses} Selesai!</b>\n\n";
        $pesan .= "🖥️ <b>Server:</b> " . $serverLabel . "\n";
        $pesan .= "📂 <b>Direktori:</b> " . htmlspecialchars($direktori) . "\n";
        $pesan .= "⏰ <b>Waktu:</b> " . $waktu . " WIB\n\n";
        $pesan .= "💾 " . htmlspecialchars($info_tambahan);

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        
        try {
            $client = \Config\Services::curlrequest();
            $client->post($url, [
                'form_params' => [
                    'chat_id'    => $chatId,
                    'text'       => $pesan,
                    'parse_mode' => 'HTML'
                ],
                'timeout' => 5, // Eksekusi sinkron dengan limit 5 detik standar
                'verify'  => false
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Telegram Notification Error: ' . $e->getMessage());
        }
    }
}
