# Rencana Penerapan (Implementation Plan): CLI Command untuk Crawler

Fitur mesin penjelajah (*crawler*) saat ini hanya bisa dieksekusi melalui peramban web (*web browser*) dengan mengakses antarmuka pada rute `/admin/crawl`. Untuk memudahkan otomatisasi dan penjadwalan via *cronjob* atau eksekusi manual via terminal *server*, kita akan membuat sebuah perintah khusus untuk `php spark`.

## Analisis Rute Saat Ini
Berdasarkan berkas `app/Config/Routes.php` dan `app/Controllers/Crawler.php`, logika perayapan ditangani oleh metode `doCrawl` yang:
1. Menerima input POST berupa `url`.
2. Memanggil fungsi pustaka:
   - `$mesinPencari->crawlLocalDirectory($tautan)` jika target berupa direktori lokal (seperti `/var/www/FOTO`).
   - `$mesinPencari->followLinks($tautan, 1, 3)` jika target berupa URL eksternal (HTTP/HTTPS).

## Proposed Changes

### [NEW] [app/Commands/CrawlCommand.php](file://10.147.17.60/www/gkr_myid/app/Commands/CrawlCommand.php)
Kita akan membuat subkelas CLI *Command* bawaan CodeIgniter 4 agar *crawler* bisa dipanggil melalui perintah terminal `php spark`.

**Fungsionalitas yang Diharapkan:**
- Nama Perintah: `php spark crawl:run`
- Parameter Wajib: URL atau Path Direktori (contoh: `php spark crawl:run /var/www/FOTO/BUYER`)
- Output: Mencetak (*print/echo*) riwayat proses langsung (secara *real-time*) ke terminal CLI menggunakan kelas bawaan `CLI::write()`.

**Struktur File Baru:**
```php
<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\CrawlerLib;

class CrawlCommand extends BaseCommand
{
    protected $group       = 'Crawler';
    protected $name        = 'crawl:run';
    protected $description = 'Mengeksekusi Crawler (Web/Lokal) via Terminal CLI.';
    protected $usage       = 'crawl:run <url_atau_path>';
    protected $arguments   = [
        'target' => 'URL situs atau absolute path direktori (misal: /var/www/FOTO)'
    ];

    public function run(array $params)
    {
        // Logika CLI untuk mengeksekusi CrawlerLib
    }
}
```

## Verification Plan

### Manual Verification
1. Masuk ke terminal direktori root proyek (`/var/www/gkr_myid`).
2. Menjalankan perintah bawaan `php spark` dan memastikan bahwa `crawl:run` muncul dalam daftar perintah.
3. Melakukan uji coba perayapan: `php spark crawl:run /var/www/FOTO/WEB` (atau URL/folder tertentu).
4. Memastikan keluaran log perayapan tampil dengan baik di layar terminal tanpa pesan galat.
