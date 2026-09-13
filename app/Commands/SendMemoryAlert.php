<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SendMemoryAlert extends BaseCommand
{
    protected $group       = 'Gracia';
    protected $name        = 'telegram:send-alert';
    protected $description = 'Mengirim peringatan memori kritis via Telegram Helper.';

    public function run(array $params)
    {
        $pesan = $params[0] ?? 'Peringatan Memori Kritis pada Peladen!';
        
        // Muat helper Telegram terpusat
        helper('telegram');

        // ID Admin Anda (sesuaikan dengan target chat ID administrator)
        $adminChatId = '8784856529'; 

        // Kirim pesan menggunakan fungsi dari telegram_helper.php
        if (function_exists('kirim_notifikasi_telegram')) {
            kirim_notifikasi_telegram($adminChatId, urldecode($pesan));
            CLI::write("Notifikasi memori berhasil dikirim ke Telegram.", 'green');
        } else {
            CLI::error("Fungsi telegram_helper tidak ditemukan.");
        }
    }
}
