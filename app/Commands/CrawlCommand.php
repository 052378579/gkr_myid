<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\CrawlerLib;

class CrawlCommand extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Crawler';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'crawl:run';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Menjalankan Crawler (Web/Lokal) via Terminal CLI';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'crawl:run <url_atau_path>';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'target' => 'URL situs atau absolute path direktori (misal: /var/www/FOTO)'
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $target = array_shift($params);

        if (empty($target)) {
            CLI::error('Kesalahan: Anda harus menyertakan URL tautan atau target direktori lokal');
            CLI::write('Contoh: php spark crawl:run /var/www/FOTO/BUYER', 'yellow');
            return;
        }

        CLI::write("Mulai memindai (crawling) target: {$target}", 'green');
        CLI::newLine();

        $mesinPencari = new CrawlerLib();

        $kesimpulan = "Proses selesai.";
        if (str_starts_with($target, '/var/www/FOTO')) {
            // URL Statis / Direktori Lokal
            $kesimpulan = $mesinPencari->crawlLocalDirectory($target);
        } else {
            // Tautan Eksternal/URL
            $mesinPencari->followLinks($target, 1, 3);
            $kesimpulan = "SELESAI: Crawling eksternal selesai dilakukan";
        }

        CLI::newLine();
        CLI::write('Proses crawling telah selesai dilakukan', 'green');
        
        // Mematikan notifikasi bawaan CodeIgniter agar tidak dobel dengan N8N
        // this->sendTelegramNotification($target, $kesimpulan);
    }
    private function sendTelegramNotification($target, $kesimpulan)
    {
        $botToken = env('BOT_TOKEN');
        $chatId   = env('CHAT_ID');

        if (empty($botToken) || empty($chatId)) {
            CLI::write('Peringatan: Kredensial Telegram (BOT_TOKEN/CHAT_ID) tidak ditemukan di .env', 'yellow');
            return;
        }

        // Deteksi Label Server (DEV vs PROD)
        $serverIp    = $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname());
        $environment = defined('ENVIRONMENT') ? ENVIRONMENT : 'production';
        $serverLabel = (str_contains($serverIp, '192.168.1.4') || str_contains($serverIp, '10.147.17.40') || $environment === 'development') ? 'DEV' : 'PROD';

        date_default_timezone_set('Asia/Jakarta');
        $waktu = date('d-m-Y H:i:s');

        // Extract atau susun ringkasan item
        if (preg_match('/Berhasil menambahkan (\d+) produk/i', $kesimpulan, $matches)) {
            $itemInfo = $matches[1] . " Item Baru Ditambahkan";
        } else {
            $itemInfo = $kesimpulan;
        }

        $pesan = "🤖 <b>Auto Crawler Selesai!</b>\n\n";
        $pesan .= "🖥️ <b>Server:</b> " . $serverLabel . "\n";
        $pesan .= "📂 <b>Direktori:</b> " . htmlspecialchars($target) . "\n";
        $pesan .= "⏰ <b>Waktu:</b> " . $waktu . " WIB\n\n";
        $pesan .= "💾 " . htmlspecialchars($itemInfo);

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'chat_id'    => $chatId,
            'parse_mode' => 'HTML',
            'text'       => $pesan
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        curl_close($ch);
    }
}
