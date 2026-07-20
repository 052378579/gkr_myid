Implementation_Plan.md
Dokumen ini memuat langkah-langkah teknis komprehensif untuk mengimplementasikan sistem pelacakan aktivitas (Audit Trail) pada modul otentikasi (gkr_loguser) dan modul pencarian (gkr_logcari) di lingkungan CodeIgniter 4.

Tahap 1: Eksekusi Skema Database (DDL)
Langkah pertama adalah membuat tabel penyimpanan log yang terisolasi dari tabel operasional utama agar performa transaksi tidak terganggu.

SQL
-- 1. Tabel Log Otentikasi
CREATE TABLE `gkr_loguser` (
  `id_log` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_user` int(11) UNSIGNED NOT NULL,
  `aktivitas` enum('login','logout','failed_login') NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `waktu` datetime DEFAULT current_timestamp(),
  CONSTRAINT `fk_loguser_user` FOREIGN KEY (`id_user`) REFERENCES `gkr_users` (`id_user`) ON DELETE CASCADE
);

-- 2. Tabel Log Pencarian
CREATE TABLE `gkr_logcari` (
  `id_log` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_user` int(11) UNSIGNED DEFAULT NULL,
  `tipe_pencarian` enum('images','sites') NOT NULL,
  `kata_kunci` varchar(255) NOT NULL,
  `jumlah_hasil` int(11) NOT NULL DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  CONSTRAINT `fk_logcari_user` FOREIGN KEY (`id_user`) REFERENCES `gkr_users` (`id_user`) ON DELETE SET NULL
);
Tahap 2: Integrasi Backend (CodeIgniter 4)
Implementasi pada sisi pengontrol (Controller) memerlukan penangkapan IP dan User Agent secara dinamis.

A. Modul Otentikasi (AuthController.php)
Sisipkan logika pencatatan log pada titik verifikasi kredensial pengguna dan saat sesi dihancurkan.

PHP
<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function attemptLogin()
    {
        $no_hp = $this->request->getPost('no_hp');
        $password = $this->request->getPost('password');
        
        $userModel = new UserModel();
        $user = $userModel->where('no_hp', $no_hp)->first();

        $ipAddress = $this->request->getIPAddress();
        $userAgent = $this->request->getUserAgent()->getAgentString();

        if ($user && password_verify($password, $user['password'])) {
            // Skenario 1: Login Berhasil
            $this->db->table('gkr_loguser')->insert([
                'id_user'    => $user['id_user'],
                'aktivitas'  => 'login',
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ]);

            // Set sesi dan arahkan ke Dashboard
            session()->set('id_user', $user['id_user']);
            return redirect()->to('/dashboard');
        } else {
            // Skenario 2: Percobaan Login Gagal
            if ($user) {
                $this->db->table('gkr_loguser')->insert([
                    'id_user'    => $user['id_user'],
                    'aktivitas'  => 'failed_login',
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent
                ]);
            }
            return redirect()->back()->with('error', 'Kredensial tidak valid');
        }
    }

    public function logout()
    {
        if (session()->has('id_user')) {
            // Skenario 3: Logout
            $this->db->table('gkr_loguser')->insert([
                'id_user'    => session()->get('id_user'),
                'aktivitas'  => 'logout',
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString()
            ]);
        }
        
        session()->destroy();
        return redirect()->to('/login');
    }
}
B. Modul Pencarian (SearchController.php)
Sisipkan logika pencatatan log tepat setelah hasil pencarian dihitung (count), namun sebelum proses rendering (pengembalian Response) agar tidak memanipulasi output JSON atau HTML.

PHP
<?php

namespace App\Controllers;

use App\Models\ImageModel;
use CodeIgniter\Controller;

class SearchController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function findImages()
    {
        $kata_kunci = $this->request->getGet('q');
        $id_user = session()->get('id_user') ?? null; 
        $ipAddress = $this->request->getIPAddress();

        // Logika pencarian utama (Database atau AI Python API)
        $imageModel = new ImageModel();
        $hasil_pencarian = $imageModel->like('title', $kata_kunci)->findAll();
        $jumlah_hasil = count($hasil_pencarian);

        // Rekam aktivitas pencarian ke log (Background Process Simulation)
        if (!empty($kata_kunci)) {
            $this->db->table('gkr_logcari')->insert([
                'id_user'        => $id_user,
                'tipe_pencarian' => 'images',
                'kata_kunci'     => $kata_kunci,
                'jumlah_hasil'   => $jumlah_hasil,
                'ip_address'     => $ipAddress
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $hasil_pencarian
        ]);
    }
}
Tahap 3: Praktik Terbaik & Optimasi (Best Practices)
Agar proses penyisipan (INSERT) tidak memperlambat waktu respon mesin pencarian (terutama jika tabel log sudah membengkak menjadi jutaan baris), sistem harus mengadopsi mekanisme non-pemblokiran (non-blocking).

CodeIgniter 4 Events:
Gunakan fitur Events pada CI4. Ubah perintah $this->db->table('gkr_logcari')->insert(...) menjadi pemicu Event:
\CodeIgniter\Events\Events::trigger('log_pencarian', $dataLog);

Pemrosesan Asinkron (Post-System):
Daftarkan Event tersebut untuk dieksekusi setelah CI4 selesai mengirimkan respon ke peramban pengguna. Ini memastikan layar pengguna langsung memuat gambar tanpa harus menunggu transaksi SQL pencatatan log selesai.