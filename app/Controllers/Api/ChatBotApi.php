<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CariModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

/**
 * Endpoint Webhook Telegram Bot Gracia
 * Berfungsi sebagai penerima asinkron interaksi Karyawan di Telegram.
 */
class ChatBotApi extends BaseController
{
    use ResponseTrait;

    private $botToken;
    private $apiUrl = 'https://api.telegram.org/bot';

    public function __construct()
    {
        // Secara dinamis memanggil variabel dari file .env
        $this->botToken = getenv('BOT_TOKEN');
    }

    /**
     * Endpoint penerima Webhook (POST /api/chatbot/webhook)
     */
    public function webhook()
    {
        // LAPIS 1: Menangkis Serangan Hacker (Validasi Token Rahasia)
        $secretToken = getenv('BOT_SECRET_TOKEN');
        $headerToken = $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '';
        
        if ($headerToken !== $secretToken) {
            return $this->response->setStatusCode(403, 'Forbidden: Invalid Secret Token');
        }

        // 1. Tangkap Payload JSON asinkron dari Telegram
        $data_mentah = file_get_contents('php://input');
        $pembaruan = json_decode($data_mentah, true);

        // --- MANUVER ASINKRONISASI (PILAR 2) ---
        // Memaksa penutupan koneksi ke Telegram dengan HTTP 200 OK agar terhindar dari Timeout
        if (function_exists('fastcgi_finish_request')) {
            http_response_code(200);
            header('Connection: close');
            fastcgi_finish_request(); 
        } else {
            http_response_code(200);
            header('Connection: close');
            $length = ob_get_length();
            if ($length !== false) {
                header('Content-Length: ' . $length);
            }
            @ob_end_flush();
            @ob_flush();
            flush();
        }
        // ---------------------------------------

        // Pastikan ada pesan masuk berupa teks
        if (isset($pembaruan['message']['text'])) {
            $id_obrolan = $pembaruan['message']['chat']['id'];
            $pesanMasuk = trim($pembaruan['message']['text']);

            // LAPIS 2: Satpam Internal (Verifikasi ID Telegram dan Status Aktif)
            $userModel = new UserModel();
            $karyawan = $userModel->where('telegram_chat_id', $id_obrolan)->first();

            // Jika karyawan BELUM terdaftar di database via telegram_chat_id
            if (!$karyawan || $karyawan['status'] !== 'aktif') {
                $this->prosesAutoBind($id_obrolan, $pesanMasuk, $userModel);
            } else {
                // Karyawan Terverifikasi & Aktif, Lanjut Logika Pencarian
                if (preg_match('/^cari\s+(.*)/i', $pesanMasuk, $cocok)) {
                    $kataKunci = $cocok[1];
                    $this->prosesPencarian($id_obrolan, $kataKunci, $karyawan['id_user']);
                } else if (preg_match('/^\/(start|daftar)/i', $pesanMasuk)) {
                    $pesanAktif = "✅ *Akun Anda Sudah Terhubung!*\n\n";
                    $pesanAktif .= "Halo *" . $karyawan['nama_lengkap'] . "*! Akun Telegram Anda telah aktif dan terhubung.\n\n";
                    $pesanAktif .= "Ketik *Cari <Nama Barang>* untuk mencari katalog.\n";
                    $pesanAktif .= "Contoh: *Cari Alcova*";
                    
                    $this->kirimPesanTelegram($id_obrolan, $pesanAktif);
                } else {
                    // Balasan jika tidak menggunakan format 'Cari'
                    $pesanPanduan = "Halo " . $karyawan['nama_lengkap'] . "! Saya Asisten Gracia 🤖\n\n";
                    $pesanPanduan .= "Ketik *Cari <Nama Barang>* untuk mencari katalog.\n";
                    $pesanPanduan .= "Contoh: *Cari Alcova*";
                    
                    $this->kirimPesanTelegram($id_obrolan, $pesanPanduan);
                }
            }
        }

        // Response wajib 200 OK standar REST API agar Telegram tidak spam notifikasi
        return $this->response->setJSON(['status' => 'sukses', 'pesan' => 'Webhook berhasil diproses']);
    }

    /**
     * Logika Pendaftaran Otomatis (Auto-Bind) telegram_chat_id berbasis nomor HP
     */
    private function prosesAutoBind($id_obrolan, $pesanMasuk, $userModel)
    {
        // Ekstraksi nomor HP dari pesan (Mendukung format: /daftar 08..., /start 08..., atau langsung 08... / 628...)
        preg_match('/(?:[\/\#]?(?:daftar|start|register)\s+)?(\+?62|0)?(8[0-9]{8,12})/', $pesanMasuk, $matches);

        if (!empty($matches[2])) {
            $inputDigits = '0' . $matches[2]; // Normalisasi format ke 08...
            $inputRaw    = preg_replace('/[^0-9]/', '', $pesanMasuk);

            // Cari user berdasarkan no_hp (format 08... atau angka mentah)
            $userFound = $userModel->where('no_hp', $inputDigits)
                                   ->orWhere('no_hp', $inputRaw)
                                   ->first();

            if ($userFound) {
                if ($userFound['status'] !== 'aktif') {
                    $pesanGagal = "⚠️ *Akun Dinonaktifkan!*\n\nProfil Karyawan *" . $userFound['nama_lengkap'] . "* terdaftar tetapi dalam status *" . $userFound['status'] . "*. Silakan hubungi Administrator.";
                    $this->kirimPesanTelegram($id_obrolan, $pesanGagal);
                    return;
                }

                // Jalankan AUTO-BIND DATABASE
                $userModel->update($userFound['id_user'], [
                    'telegram_chat_id' => $id_obrolan
                ]);

                $pesanSukses = "✅ *Pendaftaran Otomatis Berhasil!*\n\n";
                $pesanSukses .= "Halo *" . $userFound['nama_lengkap'] . "*! Akun Telegram Anda berhasil terhubung dengan profil Karyawan Gracia:\n";
                $pesanSukses .= "👤 *Nama:* " . $userFound['nama_lengkap'] . "\n";
                $pesanSukses .= "🏢 *Divisi:* " . $userFound['divisi'] . "\n";
                $pesanSukses .= "📱 *No HP:* " . $userFound['no_hp'] . "\n\n";
                $pesanSukses .= "Kini Anda dapat langsung menggunakan perintah *Cari <Nama Barang>* untuk mencari katalog.\n";
                $pesanSukses .= "Contoh: *Cari Alcova*";

                $this->kirimPesanTelegram($id_obrolan, $pesanSukses);
                return;
            } else {
                $pesanNotFound = "❌ *Nomor HP Tidak Ditemukan!*\n\nNomor HP *" . $inputDigits . "* tidak terdaftar dalam sistem Karyawan Gracia. Silakan periksa kembali atau hubungi Administrator.";
                $this->kirimPesanTelegram($id_obrolan, $pesanNotFound);
                return;
            }
        }

        // Jika pesan hanya berupa /start atau instruksi awal tanpa nomor HP
        $pesanInstruksi = "🤖 *Selamat Datang di Bot Asisten Gracia!*\n\n";
        $pesanInstruksi .= "Akun Telegram Anda belum terhubung dengan profil Karyawan Gracia.\n\n";
        $pesanInstruksi .= "Silakan balas pesan ini dengan perintah:\n";
        $pesanInstruksi .= "👉 `/daftar <Nomor_HP>` atau ketik Nomor HP terdaftar Anda.\n\n";
        $pesanInstruksi .= "*Contoh:* `/daftar 08123456789`";

        $this->kirimPesanTelegram($id_obrolan, $pesanInstruksi);
    }

    /**
     * Mengirim tombol Hubungi Admin untuk User Tidak Dikenal
     */
    private function kirimPeringatanAdmin($id_obrolan)
    {
        $url = $this->apiUrl . $this->botToken . '/sendMessage';
        
        // Membangun Tombol Khusus (Inline Keyboard)
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Nomor HP belum terdaftar. Hubungi Admin', 'url' => 'https://t.me/sansAkbar']
                ]
            ]
        ];

        $data = [
            'chat_id' => $id_obrolan,
            'text' => "❌ *Akses Ditolak!*\n\nID Telegram Anda belum terdaftar dalam sistem perusahaan Gracia. Silakan hubungi Administrator untuk mendaftarkan akun Anda.",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode($keyboard)
        ];

        $klien = \Config\Services::curlrequest();
        $klien->post($url, ['form_params' => $data]);
    }

    /**
     * Fungsi Logika Pencarian Visual di Tabel Fisik gkr_cari (Nama Kolom Bahasa Indonesia)
     */
    private function prosesPencarian($id_obrolan, $kataKunci, $id_user = null)
    {
        $cariModel = new CariModel();

        // TAHAP 1: FULL-TEXT SEARCH di gkr_cari (Akurasi Relevansi)
        $db = \Config\Database::connect();
        $kataKunciAman = $db->escapeString($kataKunci);

        $hasilGambar = $cariModel->where('imageUrl IS NOT NULL')
                                 ->where('imageUrl !=', '')
                                 ->where("MATCH(judul, kata_kunci, alt, deskripsi) AGAINST('{$kataKunciAman}*' IN BOOLEAN MODE)", null, false)
                                 ->first(); 

        $hasilSitus = $cariModel->groupStart()->where('imageUrl IS NULL')->orWhere('imageUrl', '')->groupEnd()
                                ->where("MATCH(judul, kata_kunci, alt, deskripsi) AGAINST('{$kataKunciAman}*' IN BOOLEAN MODE)", null, false)
                                ->first();

        // TAHAP 2: LEVENSHTEIN DISTANCE (Penyelamat Typo)
        $isTypo = false;
        if (!$hasilGambar && !$hasilSitus) {
            $semuaEntitas = $cariModel->select('id, judul, imageUrl')->findAll();
            $jarakTerdekat = 100;
            $entitasDitemukan = null;

            foreach ($semuaEntitas as $item) {
                $jarak = levenshtein(strtolower($kataKunci), strtolower($item['judul']));
                if ($jarak < $jarakTerdekat && $jarak <= 3) {
                    $jarakTerdekat = $jarak;
                    $entitasDitemukan = $item;
                }
            }

            if ($entitasDitemukan) {
                if (!empty($entitasDitemukan['imageUrl'])) {
                    $hasilGambar = $cariModel->find($entitasDitemukan['id']);
                } else {
                    $hasilSitus = $cariModel->find($entitasDitemukan['id']);
                }
                $isTypo = true;
            }
        }

        if ($hasilGambar || $hasilSitus) {
            // 1. Ambil Judul Utama (Prioritas: Gambar -> Situs)
            $judulMentah = $hasilGambar ? $hasilGambar['judul'] : $hasilSitus['judul'];
            
            // 2. Format menjadi Title Case (Huruf Kapital di Setiap Kata)
            $judulRapih = ucwords(strtolower($judulMentah));
            
            // 3. Buat URL Pencarian Dinamis menggunakan judul asli dari DB agar akurat
            $urlPencarian = base_url('cari?q=' . urlencode($judulMentah) . '&type=images');
        
            // 4. Susun Caption Sederhana (Dengan format Bold Markdown di Judul)
            if ($isTypo) {
                $keterangan = "🔍 *Mungkin Maksud Anda:*\n\n";
            } else {
                $keterangan = "Pencarian Ditemukan!\n\n";
            }
            $keterangan .= "*{$judulRapih}*\n";
            $keterangan .= "Klik: [Lihat Selengkapnya]({$urlPencarian})";

            // Jika ada gambar, kirim sebagai SendPhoto dengan Caption
            if ($hasilGambar && !empty($hasilGambar['imageUrl'])) {
                $urlGambar = (strpos($hasilGambar['imageUrl'], 'http') === 0) ? $hasilGambar['imageUrl'] : 'https://foto.gkr.my.id/' . ltrim($hasilGambar['imageUrl'], '/');
                $this->kirimFotoTelegram($id_obrolan, $urlGambar, $keterangan);
            } else {
                // Jika hanya situs, kirim sebagai teks
                $this->kirimPesanTelegram($id_obrolan, $keterangan);
            }

            // --- LOG AUDIT (PILAR 3) ---
            if ($id_user) {
                $logModel = new \App\Models\LogCariModel();
                $jumlahHasil = 1;
                $logModel->catatPencarian($id_user, 'Visual Bot', $kataKunci, $jumlahHasil, 'Telegram-Bot', 'Telegram');
            }
            
        } else {
            // Jika data tidak ada di tabel gkr_cari
            $this->kirimPesanTelegram($id_obrolan, "Maaf, '*{$kataKunci}*' tidak ditemukan!");

            // --- LOG AUDIT (PILAR 3) ---
            if ($id_user) {
                $logModel = new \App\Models\LogCariModel();
                $jumlahHasil = 0;
                $logModel->catatPencarian($id_user, 'Visual Bot', $kataKunci, $jumlahHasil, 'Telegram-Bot', 'Telegram');
            }
        }
    }

    /**
     * Helper: Kirim Pesan Teks via API Telegram
     */
    private function kirimPesanTelegram($id_obrolan, $teks)
    {
        $url = $this->apiUrl . $this->botToken . '/sendMessage';
        $data = [
            'chat_id' => $id_obrolan,
            'text' => $teks,
            'parse_mode' => 'Markdown'
        ];

        $klien = \Config\Services::curlrequest(['http_errors' => false]);
        $response = $klien->post($url, ['form_params' => $data]);
        
        if ($response->getStatusCode() !== 200) {
            log_message('error', 'Telegram SendMessage Error: ' . $response->getBody());
        }
    }

    /**
     * Helper: Kirim Foto via API Telegram
     */
    private function kirimFotoTelegram($id_obrolan, $url_gambar, $keterangan)
    {
        $url = $this->apiUrl . $this->botToken . '/sendPhoto';
        $data = [
            'chat_id' => $id_obrolan,
            'photo' => $url_gambar, 
            'caption' => $keterangan,
            'parse_mode' => 'Markdown'
        ];

        $klien = \Config\Services::curlrequest(['http_errors' => false]);
        $response = $klien->post($url, ['form_params' => $data]);
        
        if ($response->getStatusCode() !== 200) {
            log_message('error', 'Telegram SendPhoto Error: ' . $response->getBody());
            // FALLBACK: Jika gambar rusak/hilang, Bot tetap mengirimkan teks balasan agar tidak terlihat "mogok"
            $this->kirimPesanTelegram($id_obrolan, $keterangan . "\n\n*(Visual gambar gagal dimuat dari server, namun tautan di atas tetap dapat Anda akses)*");
        }
    }
}
