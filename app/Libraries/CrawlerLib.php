<?php

namespace App\Libraries;

use App\Models\SiteModel;
use App\Models\ImageModel;

class CrawlerLib 
{
    private $siteModel;
    private $imageModel;
    public $alreadyCrawled = [];
    public $crawling = [];
    public $alreadyFoundImages = [];

    public function __construct() 
    {
        $this->siteModel = new SiteModel();
        $this->imageModel = new ImageModel();
    }

    public function linkExists($url) 
    {
        $url = str_replace('192.168.1.17:81', 'foto.gkr.my.id', $url);
        return $this->siteModel->where('url', $url)->first() !== null;
    }
    
    public function imageExists($src) 
    {
        $src = str_replace('192.168.1.17:81', 'foto.gkr.my.id', $src);
        return $this->imageModel->where('imageUrl', $src)->first() !== null;
    }
    
    public function insertLink($url, $title, $description, $keywords)
    {
        $url = str_replace('192.168.1.17:81', 'foto.gkr.my.id', $url);
        return $this->siteModel->insert([
            'url' => $url,
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'clicks' => 0
        ]);
    }
    
    public function insertImage($url, $src, $alt, $title) 
    {
        $url = str_replace('192.168.1.17:81', 'foto.gkr.my.id', $url);
        $src = str_replace('192.168.1.17:81', 'foto.gkr.my.id', $src);
        return $this->imageModel->insert([
            'siteUrl' => $url,
            'imageUrl' => $src,
            'alt' => $alt,
            'title' => $title,
            'clicks' => 0,
            'broken' => 0
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
            echo "$url already exists<br>";
        } else if($this->insertLink($url, $title, $description, $keywords)) {
            echo "SUCCESS: $url<br>";
        } else {
            echo "ERROR: Failed to insert $url<br>";
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
                    echo "$src already exists<br>";
                } else if($this->insertImage($url, $src, $alt, $imgTitle)) {
                    echo "SUCCESS: $src<br>";
                } else {
                    echo "ERROR: Failed to insert $src<br>";
                }
            }
        }
        
        echo "<b>URL:</b> $url, <b>Title:</b> $title, <b>Description:</b> $description, <b>keywords:</b> $keywords<br>";
        @ob_flush(); @flush();
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
                @ob_flush(); @flush();
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
        $baseDomain = 'https://foto.gkr.my.id/';
        
        $sitesAdded = 0;
        $imagesAdded = 0;
        
        // Remove trailing slash for consistency
        $targetPath = rtrim($targetPath, '/');
        
        if (!str_starts_with($targetPath, $rootPath)) {
            echo "<span style='color: #dc3545;'>[ERROR] Path harus berawalan $rootPath</span><br>\n";
            return;
        }

        echo "<span style='color: #a9a9a9;'>Memulai scan direktori lokal:</span> $targetPath<br>\n";
        @ob_flush(); @flush();
        
        // Determine folders to scan
        if ($targetPath === $rootPath) {
            $foldersToScan = [$rootPath . '/BUYER', $rootPath . '/GRACIA', $rootPath . '/SWATCHES', $rootPath . '/WEB'];
        } else {
            $foldersToScan = [$targetPath];
        }

        foreach ($foldersToScan as $folderPath) {
            if (!is_dir($folderPath)) {
                echo "<span style='color: #dc3545;'>[ERROR] Folder tidak ditemukan:</span> $folderPath<br>\n";
                @ob_flush(); @flush();
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($folderPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                // Get relative path without leading slash relative to rootPath '/var/www/FOTO'
                $relativePath = substr($item->getPathname(), strlen(rtrim($rootPath, '/')) + 1);
                $relativePath = str_replace('\\', '/', $relativePath); // for windows compatibility if any

                if ($item->isFile()) {
                    $ext = strtolower($item->getExtension());
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $filename = $item->getFilename();
                        $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
                        
                        $parentFolder = basename($item->getPath());
                        
                        // Check naming logic
                        if (str_starts_with(strtoupper($filename), 'IMG_') || str_starts_with(strtoupper($filename), 'DCIM_')) {
                            $title = $parentFolder;
                        } else {
                            // [AI HARMONIZATION] Hapus kata/kode sudut pandang (Termasuk B, C, D, E) sebelum diformat
                            $baseName = preg_replace('/[ _-]*(depan|belakang|samping|perspektif|detail|b|c|d|e)$/i', '', $filenameWithoutExt);
                            
                            // Extract from filename: Play-Adobe 40616-0011 -> Play Adobe 40616 0011
                            $title = str_replace(['-', '_'], ' ', $baseName);
                            
                            // Bersihkan spasi berlebih
                            $title = trim($title);
                            
                            // Format FG codes, e.g. (fg 42918) -> (FG-42918)
                            $title = preg_replace('/\(?\bfg\s*([0-9]+)\)?/i', '(FG-$1)', $title);
                        }
                        
                        $description = $title;
                        
                        // Keywords: split title by space
                        $keywordsArray = explode(' ', strtolower($title));
                        $keywords = implode(', ', $keywordsArray);
                        
                        $alt = $title;
                        
                        // imageUrl format: SWATCHES/FABRIC/SUNBRELLA/Play-Adobe 40616-0011.webp
                        $imageUrl = $relativePath;
                        
                        // siteUrl format: ?SWATCHES/FABRIC/SUNBRELLA#pid=Play-Adobe 40616-0011.webp
                        $parentRelativeDir = dirname($relativePath);
                        if ($parentRelativeDir === '.') {
                            $parentRelativeDir = '';
                        }
                        
                        $siteUrl = '?' . $parentRelativeDir . '#pid=' . $filename;
                        
                        if ($this->imageExists($imageUrl)) {
                             echo "<span style='color: #4db8ff;'>[INFO]</span> <span style='color: #4a9c8f;'>Skip: $title sudah ada</span><br>\n";
                        } else {
                            // Insert into cari_sites
                            if (!$this->linkExists($siteUrl)) {
                                if ($this->insertLink($siteUrl, $title, $description, $keywords)) {
                                    $sitesAdded++;
                                } else {
                                    $errors = implode(", ", $this->siteModel->errors());
                                    echo "<span style='color: #dc3545;'>[ERROR] Gagal menambah situs: $errors</span><br>\n";
                                }
                            }
                            
                            // Insert into cari_images
                            if ($this->insertImage($siteUrl, $imageUrl, $alt, $title)) {
                                $imagesAdded++;
                                echo "<span style='color: #28a745;'>[SUCCESS]</span> <span style='color: #d4d4d4;'>Menambahkan: $title</span><br>\n";
                            } else {
                                $errors = implode(", ", $this->imageModel->errors());
                                echo "<span style='color: #dc3545;'>[ERROR] Gagal menambah gambar: $errors</span><br>\n";
                            }
                        }
                    }
                }
                @ob_flush(); @flush();
            }
        }
        
        echo "<span style='color: #4db8ff;'>[INFO]</span> <span style='color: #ffffff;'>SELESAI: Berhasil menambahkan $sitesAdded tautan ke cari_sites dan $imagesAdded gambar ke cari_images.</span><br>\n";
    }
}
