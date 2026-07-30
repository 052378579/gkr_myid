<?php

namespace App\Controllers;

use App\Models\LogCariModel;

class AwanKata extends BaseController
{
    public function index()
    {
        $logCariModel = new LogCariModel();
        
        // Ambil top 150 kata kunci paling sering dicari
        $rawData = $logCariModel->getWordCloudData(150);
        
        $wordList = [];
        foreach ($rawData as $row) {
            // Bersihkan teks (huruf kecil) agar seragam dan hapus spasi berlebih
            $kata = strtolower(trim($row['kata_kunci']));
            if (!empty($kata)) {
                // WordCloud2.js mengharapkan format array [kata, bobot]
                $wordList[] = [$kata, (int)$row['frekuensi']];
            }
        }

        $jsonPath = FCPATH . 'versi.json';
        $version = 'v1.0.0';
        
        if (file_exists($jsonPath)) {
            $json = json_decode(file_get_contents($jsonPath), true);
            $versiData = isset($json['data']) ? $json['data'] : [];
            if (!empty($versiData)) {
                usort($versiData, function($a, $b) {
                    return strtotime($b['tanggal_rilis']) - strtotime($a['tanggal_rilis']);
                });
                $version = 'v' . $versiData[0]['versi'];
            }
        }

        $data = [
            'wordList' => $wordList,
            'version'  => $version
        ];

        return view('awan_kata', $data);
    }
}
