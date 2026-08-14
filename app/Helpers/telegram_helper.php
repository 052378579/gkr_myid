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

        $payload = [
            'event'     => 'crawler_done',
            'server'    => $serverLabel,
            'waktu'     => $waktu . ' WIB',
            'direktori' => $direktori,
            'info'      => $info_tambahan
        ];

        $url = "http://127.0.0.1:5678/webhook/gracia_telegram";
        
        try {
            $client = \Config\Services::curlrequest();
            $client->post($url, [
                'json'    => $payload,
                'timeout' => 5, // Local webhook, fast timeout
                'verify'  => false
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Telegram Notification Error: ' . $e->getMessage());
        }
    }
}
