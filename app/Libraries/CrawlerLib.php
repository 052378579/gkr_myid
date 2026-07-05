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
        return $this->siteModel->where('url', $url)->first() !== null;
    }
    
    public function imageExists($src) 
    {
        return $this->imageModel->where('imageUrl', $src)->first() !== null;
    }
    
    public function insertLink($url, $title, $description, $keywords)
    {
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
}
