<?php

namespace App\Libraries;

use App\Models\SiteModel;
use App\Models\ImageModel;

class SpellChecker
{
    public function getCorrection(string $query): ?string
    {
        $query = strtolower(trim($query));
        if (empty($query) || strlen($query) < 3) {
            return null; // Terlalu pendek untuk dikoreksi
        }

        $cache = \Config\Services::cache();
        $cacheKey = 'search_dictionary_words';
        $words = $cache->get($cacheKey);

        if (!$words) {
            $words = $this->buildDictionary();
            // Cache selama 6 jam
            $cache->save($cacheKey, $words, 6 * 3600);
        }

        // Cek apakah query sudah ada di kamus
        if (in_array($query, $words)) {
            return null; // Query sudah valid
        }

        $shortest = -1;
        $closest = null;

        foreach ($words as $word) {
            $lev = levenshtein($query, $word);

            if ($lev == 0) {
                return null;
            }

            if ($lev <= $shortest || $shortest < 0) {
                $closest  = $word;
                $shortest = $lev;
            }
        }

        // Threshold jarak levenshtein (toleransi salah ketik)
        $threshold = 2; 
        if (strlen($query) > 5) {
            $threshold = 3;
        }

        if ($shortest > 0 && $shortest <= $threshold) {
            return $closest;
        }

        return null;
    }

    private function buildDictionary(): array
    {
        $words = [];
        $siteModel = new SiteModel();
        $imageModel = new ImageModel();

        // Ambil data untuk membangun kamus (dilimit agar tidak berat)
        $sites = $siteModel->select('title, description')->findAll(5000);
        foreach ($sites as $site) {
            $this->extractWords($site['title'] . ' ' . $site['description'], $words);
        }

        // Pastikan kolom alt ada di cari_images, kalau tidak gunakan title
        try {
            $images = $imageModel->select('title, alt')->findAll(5000);
            foreach ($images as $img) {
                $alt = $img['alt'] ?? '';
                $this->extractWords($img['title'] . ' ' . $alt, $words);
            }
        } catch (\Exception $e) {
            // Jika kolom alt tidak ada
            $images = $imageModel->select('title')->findAll(5000);
            foreach ($images as $img) {
                $this->extractWords($img['title'], $words);
            }
        }

        // Hilangkan duplikat dan index ulang
        return array_values(array_unique($words));
    }

    private function extractWords(string $text, array &$words)
    {
        // Hilangkan tanda baca dan ubah ke huruf kecil
        $text = strtolower(preg_replace('/[^\w\s]/', '', $text));
        $tokens = explode(' ', $text);
        
        foreach ($tokens as $token) {
            $token = trim($token);
            // Simpan kata yang panjangnya >= 3 dan bukan angka
            if (strlen($token) >= 3 && !is_numeric($token)) {
                $words[] = $token;
            }
        }
    }
}
