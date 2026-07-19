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
    protected $description = 'Mengeksekusi Crawler (Web/Lokal) via Terminal CLI.';

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
            CLI::error('Kesalahan: Anda harus menyertakan URL tautan atau target direktori lokal.');
            CLI::write('Contoh: php spark crawl:run /var/www/FOTO/BUYER', 'yellow');
            return;
        }

        CLI::write("Mulai memindai (crawling) target: {$target}", 'green');
        CLI::newLine();

        $mesinPencari = new CrawlerLib();

        if (str_starts_with($target, '/var/www/FOTO')) {
            // URL Statis / Direktori Lokal
            $mesinPencari->crawlLocalDirectory($target);
        } else {
            // Tautan Eksternal/URL
            $mesinPencari->followLinks($target, 1, 3);
        }

        CLI::newLine();
        CLI::write('Proses crawling telah selesai dilakukan.', 'green');
    }
}
