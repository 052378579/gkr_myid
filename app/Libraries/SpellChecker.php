<?php

namespace App\Libraries;

use App\Models\CariModel;

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
        $cariModel = new CariModel();

        // Ambil data untuk membangun kamus langsung dari tabel gkr_cari
        $items = $cariModel->select('judul, alt, deskripsi, kata_kunci')->findAll(5000);
        foreach ($items as $item) {
            $combinedText = ($item['judul'] ?? '') . ' ' . 
                             ($item['alt'] ?? '') . ' ' . 
                             ($item['deskripsi'] ?? '') . ' ' . 
                             ($item['kata_kunci'] ?? '');
            $this->extractWords($combinedText, $words);
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
