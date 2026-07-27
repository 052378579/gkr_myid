<?php

namespace App\Libraries;

use App\Models\CariModel;

class CrawlerLib 
{
    private $cariModel;
    public $alreadyCrawled = [];
    public $crawling = [];
    public $alreadyFoundImages = [];

    /**
     * Cerdas mendeteksi output untuk Web (dengan HTML) atau CLI (Plain text/CLI colors)
     */
    private function out($htmlMsg, $cliColor = null)
    {
        if (is_cli()) {
            $cleanMsg = strip_tags(str_replace(['<br>', '<br/>', '<br />', "\n"], '', $htmlMsg));
            if ($cliColor && class_exists('CodeIgniter\CLI\CLI')) {
                \CodeIgniter\CLI\CLI::write($cleanMsg, $cliColor);
            } else {
                echo $cleanMsg . PHP_EOL;
            }
        } else {
            echo $htmlMsg . "<br>\n";
        }
        @ob_flush(); @flush();
    }

    public function __construct() 
    {
        $this->cariModel = new CariModel();
    }

    public function linkExists($url) 
    {
        $url = str_replace('192.168.1.17:81', 'foto.gkr.my.id', $url);
        return $this->cariModel->where('url', $url)->first() !== null;
    }
    
    public function imageExists($src) 
    {
        $src = str_replace('192.168.1.17:81', 'foto.gkr.my.id', $src);
        return $this->cariModel->where('imageUrl', $src)->first() !== null;
    }

    /**
     * Menyimpan 1 Entitas Produk Tunggal Lengkap ke tabel gkr_cari
     */
    public function insertProductItem($siteUrl, $imageUrl, $title, $description, $keywords, $alt)
    {
        $siteUrl  = str_replace('192.168.1.17:81', 'foto.gkr.my.id', $siteUrl);
        $imageUrl = str_replace('192.168.1.17:81', 'foto.gkr.my.id', $imageUrl);

        return $this->cariModel->insert([
            'judul'      => $title,
            'alt'        => $alt,
            'deskripsi'  => $description,
            'url'        => $siteUrl,
            'imageUrl'   => $imageUrl,
            'siteUrl'    => $siteUrl,
            'kata_kunci' => $keywords,
            'klik'       => 0,
            'rusak'      => 0
        ]);
    }
    
    public function insertLink($url, $title, $description, $keywords)
    {
        $url = str_replace('192.168.1.17:81', 'foto.gkr.my.id', $url);
        return $this->cariModel->insert([
            'url'        => $url,
            'judul'      => $title,
            'deskripsi'  => $description,
            'kata_kunci' => $keywords,
            'klik'       => 0
        ]);
    }
    
    public function insertImage($url, $src, $alt, $title) 
    {
        $url = str_replace('192.168.1.17:81', 'foto.gkr.my.id', $url);
        $src = str_replace('192.168.1.17:81', 'foto.gkr.my.id', $src);
        return $this->cariModel->insert([
            'siteUrl'  => $url,
            'imageUrl' => $src,
            'alt'      => $alt,
            'judul'    => $title,
            'klik'     => 0,
            'rusak'    => 0
        ]);
    }
    
    public function createLink($src, $url)
    {
        $parsed = parse_url($url);
        $scheme = $parsed["scheme"] ?? 'http';
        $host = $parsed["host"] ?? '';
        
        if(substr($src, 0, 2) == "//") 
            $src =  $scheme . ":" . $src;
        else if(substr($src, 0, 1) == "/") 
            $src = $scheme . "://" . $host . $src;
        else if(substr($src, 0, 2) == "./") 
            $src = $scheme . "://" . $host . dirname($parsed["path"] ?? '/') . substr($src, 1);
        else if(substr($src, 0, 3) == "../") 
            $src = $scheme . "://" . $host . "/" . $src;
        else if(substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http") 
            $src = $scheme . "://" . $host . "/" . $src;
    
        return $src;
    }
    
    public function getDetails($url)
    {
        $parser = new DomDocumentParser($url);
        $titleArray = $parser->getTitleTags();
    
        if(sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) return;
    
        $title = $titleArray->item(0)->nodeValue;
        $title = str_replace("\n", "", $title);
        if($title == "") return;
    
        $description = "";
        $keywords = "";
        $metasArray = $parser->getMetaTags();
    
        foreach($metasArray as $meta) {
            if($meta->getAttribute("name") == "description") {
                $description = $meta->getAttribute("content");
            }
            if($meta->getAttribute("name") == "keywords") {
                $keywords = $meta->getAttribute("content");
            }
        }	
    
        $description = str_replace("\n", "", $description);
        $keywords = str_replace("\n", "", $keywords);
    
        if($this->linkExists($url)) {
            $this->out("$url already exists", 'yellow');
        } else if($this->insertLink($url, $title, $description, $keywords)) {
            $this->out("SUCCESS: $url", 'green');
        } else {
            $this->out("ERROR: Failed to insert $url", 'red');
        }
    
        $imageArray = $parser->getImages();
        foreach($imageArray as $image) {
            $src = $image->getAttribute("src");
            $alt = $image->getAttribute("alt");
            $imgTitle = $image->getAttribute("title");
    
            if(!$imgTitle && !$alt) continue;
    
            $src = $this->createLink($src, $url);
    
            if(!in_array($src, $this->alreadyFoundImages)) {
                $this->alreadyFoundImages[] = $src;
    
                if($this->imageExists($src)) {
                    $this->out("$src already exists", 'yellow');
                } else if($this->insertImage($url, $src, $alt, $imgTitle)) {
                    $this->out("SUCCESS: $src", 'green');
                } else {
                    $this->out("ERROR: Failed to insert $src", 'red');
                }
            }
        }
        
        $this->out("<b>URL:</b> $url, <b>Title:</b> $title, <b>Description:</b> $description, <b>keywords:</b> $keywords");
    }
    
    public function followLinks($url, $depth = 1, $maxDepth = 3)
    {
        if ($depth > $maxDepth) return;

        $parser = new DomDocumentParser($url);
        $linkList = $parser->getLinks();
    
        foreach($linkList as $link) {
            $href = $link->getAttribute("href");
    
            if(strpos($href, "#") !== false) continue;
            else if(substr($href, 0, 11) == "javascript:") continue;
    
            $href = $this->createLink($href, $url);
    
            if(!in_array($href, $this->alreadyCrawled)) {
                $this->alreadyCrawled[] = $href;
                $this->crawling[] = $href;
                $this->getDetails($href);
            }
        }
    
        array_shift($this->crawling);
    
        foreach($this->crawling as $site) {
            $this->followLinks($site, $depth + 1, $maxDepth);
        }
    }

    public function crawlLocalDirectory($targetPath)
    {
        $rootPath = '/var/www/FOTO';
        $itemsAdded = 0;
        
        $targetPath = rtrim($targetPath, '/');
        
        if (!str_starts_with($targetPath, $rootPath)) {
            $this->out("<span style='color: #dc3545;'>[ERROR] Path harus berawalan $rootPath</span>", 'red');
            return;
        }

        $this->out("<span style='color: #a9a9a9;'>Memulai scan direktori lokal (1 Produk = 1 Baris):</span> $targetPath", 'white');
        
        if ($targetPath === $rootPath) {
            $foldersToScan = [$rootPath . '/BUYER', $rootPath . '/GRACIA', $rootPath . '/SWATCHES', $rootPath . '/WEB'];
        } else {
            $foldersToScan = [$targetPath];
        }

        foreach ($foldersToScan as $folderPath) {
            if (!is_dir($folderPath)) {
                $this->out("<span style='color: #dc3545;'>[ERROR] Folder tidak ditemukan:</span> $folderPath", 'red');
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($folderPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                $relativePath = substr($item->getPathname(), strlen(rtrim($rootPath, '/')) + 1);
                $relativePath = str_replace('\\', '/', $relativePath);

                if ($item->isFile()) {
                    $ext = strtolower($item->getExtension());
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $filename = $item->getFilename();
                        $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
                        
                        $parentFolder = basename($item->getPath());
                        
                        if (str_starts_with(strtoupper($filename), 'IMG_') || str_starts_with(strtoupper($filename), 'DCIM_')) {
                            $title = $parentFolder;
                        } else {
                            $baseName = preg_replace('/[ _-]*(depan|belakang|samping|perspektif|detail|b|c|d|e)$/i', '', $filenameWithoutExt);
                            $title = str_replace(['-', '_'], ' ', $baseName);
                            $title = trim($title);
                            $title = preg_replace('/\(?\bfg\s*([0-9]+)\)?/i', '(FG-$1)', $title);
                        }
                        
                        $description = $title;
                        $keywordsArray = explode(' ', strtolower($title));
                        $keywords = implode(', ', $keywordsArray);
                        $alt = $title;
                        
                        $imageUrl = $relativePath;
                        $parentRelativeDir = dirname($relativePath);
                        if ($parentRelativeDir === '.') {
                            $parentRelativeDir = '';
                        }
                        
                        $siteUrl = '?' . $parentRelativeDir . '#pid=' . $filename;
                        
                        // Periksa apakah gambar atau situs sudah pernah terindeks
                        if ($this->imageExists($imageUrl) || $this->linkExists($siteUrl)) {
                             $this->out("<span style='color: #4db8ff;'>[INFO]</span> <span style='color: #4a9c8f;'>Skip: $title sudah ada</span>", 'cyan');
                        } else {
                            // Input 1 Baris Produk Tunggal Utuh
                            if ($this->insertProductItem($siteUrl, $imageUrl, $title, $description, $keywords, $alt)) {
                                $itemsAdded++;
                                $this->out("<span style='color: #28a745;'>[SUCCESS]</span> <span style='color: #d4d4d4;'>Menambahkan produk: $title</span>", 'green');
                            } else {
                                $errors = implode(", ", $this->cariModel->errors());
                                $this->out("<span style='color: #dc3545;'>[ERROR] Gagal menambah produk: $errors</span>", 'red');
                            }
                        }
                    }
                }
            }
        }
        
        $kesimpulan = "SELESAI: Berhasil menambahkan $itemsAdded produk tunggal ke tabel gkr_cari.";
        $this->out("<span style='color: #4db8ff;'>[INFO]</span> <span style='color: #ffffff;'>" . $kesimpulan . "</span>", 'yellow');
        
        return $kesimpulan;
    }
}
