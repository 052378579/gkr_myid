<?php

namespace App\Controllers;

use App\Libraries\CrawlerLib;

class Crawler extends BaseController
{
    public function index()
    {
        return view('crawl');
    }

    public function doCrawl()
    {
        // Disable output buffering for live stream
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', false);
        while (@ob_end_flush());
        ini_set('implicit_flush', true);
        ob_implicit_flush(true);
        header('Cache-Control: no-cache');

        $url = $this->request->getPost('url');
        
        if (empty($url)) {
            echo "URL is required.\n";
            return;
        }

        echo "Mulai crawling URL: $url <br>\n";
        @ob_flush(); @flush();

        $crawler = new CrawlerLib();
        $crawler->followLinks($url, 1, 3); // max depth 3
        
        echo "<br>Selesai crawling.<br>\n";
    }
}
