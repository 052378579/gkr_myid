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
                if (preg_match('/^(cari|album)\s+(.*)/i', $pesanMasuk, $cocok)) {
                    $modeAlbum = strtolower($cocok[1]) === 'album';
                    $kataKunci = $cocok[2];
                    $this->prosesPencarian($id_obrolan, $kataKunci, $karyawan['id_user'], $modeAlbum);
                } else if (preg_match('/^\/(start|daftar)/i', $pesanMasuk)) {
                    $pesanAktif = "✅ *Akun Anda Sudah Terhubung!*\n\n";
                    $pesanAktif .= "Halo *" . $karyawan['nama_lengkap'] . "*! Akun Telegram Anda telah aktif dan terhubung.\n\n";
                    $pesanAktif .= "Ketik *Cari <Nama Barang>* (Cepat) atau *Album <Nama Barang>* (Galeri).\n";
                    $pesanAktif .= "Contoh: *Cari Alcova*";
                    
                    $this->kirimPesanTelegram($id_obrolan, $pesanAktif);
                } else {
                    // Balasan jika tidak menggunakan format yang benar
                    $pesanPanduan = "Halo " . $karyawan['nama_lengkap'] . "! Saya Asisten Gracia 🤖\n\n";
                    $pesanPanduan .= "Ketik *Cari <Nama Barang>* (Cepat) atau *Album <Nama Barang>* (Galeri).\n";
                    $pesanPanduan .= "Contoh: *Album Alcova*";
                    
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
                $pesanSukses .= "Kini Anda dapat langsung menggunakan perintah *Cari <Nama Barang>* atau *Album <Nama Barang>*.\n";
                $pesanSukses .= "Contoh: *Album Alcova*";

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
    private function prosesPencarian($id_obrolan, $kataKunci, $id_user = null, $modeAlbum = false)
    {
        $cariModel = new CariModel();
        $db = \Config\Database::connect();
        $kataKunciAman = $db->escapeString($kataKunci);

        // TAHAP 1: FULL-TEXT SEARCH
        $hasilSemua = $cariModel->where('imageUrl IS NOT NULL')
                                 ->where('imageUrl !=', '')
                                 ->where("MATCH(judul, kata_kunci, alt, deskripsi) AGAINST('{$kataKunciAman}*' IN BOOLEAN MODE)", null, false)
                                 ->findAll();
                                 
        $hasilSitusSemua = [];
        if (empty($hasilSemua)) {
            $hasilSitusSemua = $cariModel->groupStart()->where('imageUrl IS NULL')->orWhere('imageUrl', '')->groupEnd()
                                    ->where("MATCH(judul, kata_kunci, alt, deskripsi) AGAINST('{$kataKunciAman}*' IN BOOLEAN MODE)", null, false)
                                    ->findAll();
        }

        $isTypo = false;
        $judulDikoreksi = $kataKunci;

        // TAHAP 2: LEVENSHTEIN DISTANCE
        if (empty($hasilSemua) && empty($hasilSitusSemua)) {
            $semuaEntitas = $cariModel->select('id, judul')->findAll();
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
                $isTypo = true;
                $judulDikoreksi = $entitasDitemukan['judul'];
                $kataKunciAmanTypo = $db->escapeString($judulDikoreksi);
                
                // Cari ulang menggunakan FTS berdasarkan kata yang sudah dikoreksi
                $hasilSemua = $cariModel->where('imageUrl IS NOT NULL')
                                 ->where('imageUrl !=', '')
                                 ->where("MATCH(judul, kata_kunci, alt, deskripsi) AGAINST('{$kataKunciAmanTypo}*' IN BOOLEAN MODE)", null, false)
                                 ->findAll();
                                 
                if (empty($hasilSemua)) {
                     $hasilSitusSemua = $cariModel->groupStart()->where('imageUrl IS NULL')->orWhere('imageUrl', '')->groupEnd()
                                    ->where("MATCH(judul, kata_kunci, alt, deskripsi) AGAINST('{$kataKunciAmanTypo}*' IN BOOLEAN MODE)", null, false)
                                    ->findAll();
                }
                
                // Fallback terakhir
                if (empty($hasilSemua) && empty($hasilSitusSemua)) {
                     $entitasFix = $cariModel->find($entitasDitemukan['id']);
                     if (!empty($entitasFix['imageUrl'])) {
                         $hasilSemua = [$entitasFix];
                     } else {
                         $hasilSitusSemua = [$entitasFix];
                     }
                }
            }
        }
        
        $totalHasil = count($hasilSemua) + count($hasilSitusSemua);

        if ($totalHasil > 0) {
            // Ambil item teratas
            $itemUtama = !empty($hasilSemua) ? $hasilSemua[0] : $hasilSitusSemua[0];
            $judulMentah = $itemUtama['judul'];
            $judulRapih = ucwords(strtolower($judulMentah));
            $urlPencarian = base_url('cari?q=' . urlencode($judulMentah) . '&type=images');

            // --- LOGIKA ALBUM ---
            if ($modeAlbum) {
                // Logika Penyelamat 1 (Typo Trap)
                if ($isTypo) {
                    $keterangan = "🔍 *Mungkin yang Anda cari '{$judulRapih}'*";
                    if ($totalHasil > 1) {
                        $keterangan .= " (Total {$totalHasil} Varian)\n\n";
                    } else {
                        $keterangan .= "\n\n";
                    }
                    $keterangan .= "*{$judulRapih}*\n";
                    $keterangan .= "Klik: [Lihat Detail Barangnya]({$urlPencarian})";
                    if ($totalHasil > 1) {
                        $sisa = $totalHasil - 1;
                        $keterangan .= "\n\n👉 [Lihat {$sisa} Varian {$judulRapih} Lainnya di Web]({$urlPencarian})";
                    }
                    
                    if (!empty($itemUtama['imageUrl'])) {
                        $urlGambar = (strpos($itemUtama['imageUrl'], 'http') === 0) ? $itemUtama['imageUrl'] : 'https://foto.gkr.my.id/' . ltrim($itemUtama['imageUrl'], '/');
                        $this->kirimFotoTelegram($id_obrolan, $urlGambar, $keterangan);
                    } else {
                        $this->kirimPesanTelegram($id_obrolan, $keterangan);
                    }
                } else {
                    // Eksekusi Album Normal
                    if (!empty($hasilSemua) && count($hasilSemua) > 1) {
                        $keteranganAlbum = "👉 [Lihat Semua Varian {$judulRapih} di Web]({$urlPencarian})";
                        $suksesAlbum = $this->kirimAlbumTelegram($id_obrolan, $hasilSemua, $keteranganAlbum);
                        
                        // Logika Penyelamat 2 (Bom Waktu Broken Link)
                        if (!$suksesAlbum) {
                            $keteranganDarurat = "⚠️ *Tidak ada gambar \"{$judulRapih}\" yang dapat dimuat secara massal (Terdapat gangguan jaringan server gambar).*\n\n";
                            $keteranganDarurat .= "Klik: [Lihat Detail Barangnya]({$urlPencarian})\n";
                            if ($totalHasil > 1) {
                                $sisa = $totalHasil - 1;
                                $keteranganDarurat .= "👉 [Lihat {$sisa} Varian {$judulRapih} Lainnya di Web]({$urlPencarian})";
                            }
                            $this->kirimPesanTelegram($id_obrolan, $keteranganDarurat);
                        }
                    } else {
                        // Jika minta album tapi hasil cuma 1 (atau hanya situs)
                        $keterangan = "Pencarian Ditemukan!\n\n*{$judulRapih}*\nKlik: [Lihat Selengkapnya]({$urlPencarian})";
                        if (!empty($itemUtama['imageUrl'])) {
                            $urlGambar = (strpos($itemUtama['imageUrl'], 'http') === 0) ? $itemUtama['imageUrl'] : 'https://foto.gkr.my.id/' . ltrim($itemUtama['imageUrl'], '/');
                            $this->kirimFotoTelegram($id_obrolan, $urlGambar, $keterangan);
                        } else {
                            $this->kirimPesanTelegram($id_obrolan, $keterangan);
                        }
                    }
                }
            } 
            // --- LOGIKA CARI NORMAL (HIBRIDA) ---
            else {
                if ($isTypo) {
                    $keterangan = "🔍 *Mungkin yang Anda cari '{$judulRapih}'*";
                } else {
                    $keterangan = "Pencarian Ditemukan!";
                }
                
                if ($totalHasil > 1) {
                    $keterangan .= " (Total {$totalHasil} Varian)\n\n";
                } else {
                    $keterangan .= "\n\n";
                }
                
                $keterangan .= "*{$judulRapih}*\n";
                $keterangan .= "Klik: [Lihat Detail Barangnya]({$urlPencarian})";
                
                if ($totalHasil > 1) {
                    $sisa = $totalHasil - 1;
                    $keterangan .= "\n\n👉 [Lihat {$sisa} Varian {$judulRapih} Lainnya di Web]({$urlPencarian})";
                }

                if (!empty($itemUtama['imageUrl'])) {
                    $urlGambar = (strpos($itemUtama['imageUrl'], 'http') === 0) ? $itemUtama['imageUrl'] : 'https://foto.gkr.my.id/' . ltrim($itemUtama['imageUrl'], '/');
                    $this->kirimFotoTelegram($id_obrolan, $urlGambar, $keterangan);
                } else {
                    $this->kirimPesanTelegram($id_obrolan, $keterangan);
                }
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

    /**
     * Helper: Kirim Album MediaGroup via API Telegram
     * Mengembalikan true jika sukses, false jika gagal (broken link dll)
     */
    private function kirimAlbumTelegram($id_obrolan, $hasilSemua, $keteranganUtama)
    {
        $url = $this->apiUrl . $this->botToken . '/sendMediaGroup';
        
        $media = [];
        $maksimalAlbum = 10;
        $counter = 0;
        
        foreach ($hasilSemua as $item) {
            if ($counter >= $maksimalAlbum) break;
            
            $urlGambar = (strpos($item['imageUrl'], 'http') === 0) ? $item['imageUrl'] : 'https://foto.gkr.my.id/' . ltrim($item['imageUrl'], '/');
            
            $photoObj = [
                'type'  => 'photo',
                'media' => $urlGambar
            ];
            
            if ($counter === 0) {
                $photoObj['caption'] = $keteranganUtama;
                $photoObj['parse_mode'] = 'Markdown';
            }
            
            $media[] = $photoObj;
            $counter++;
        }
        
        $data = [
            'chat_id' => $id_obrolan,
            'media'   => json_encode($media)
        ];

        $klien = \Config\Services::curlrequest(['http_errors' => false]);
        $response = $klien->post($url, ['form_params' => $data]);
        
        if ($response->getStatusCode() !== 200) {
            log_message('error', 'Telegram SendMediaGroup Error: ' . $response->getBody());
            return false;
        }
        
        return true;
    }
}
