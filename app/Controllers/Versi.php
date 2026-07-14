<?php

namespace App\Controllers;

use App\Models\VersiModel;

class Versi extends BaseController
{
    public function index()
    {
        $versiModel = new VersiModel();
        
        // Auto setup table if not exists (for initial run)
        $versiModel->setupTable();
        
        $versiData = $versiModel->orderBy('tanggal_rilis', 'DESC')->findAll();
        
        // Decode JSON arrays for the view
        foreach ($versiData as &$row) {
            $row['improvements'] = json_decode($row['improvements'] ?? '[]', true);
            $row['fixes'] = json_decode($row['fixes'] ?? '[]', true);
            $row['patches'] = json_decode($row['patches'] ?? '[]', true);
            
            // Format date e.g. "June 25, 2026" (English style like the design)
            if (!empty($row['tanggal_rilis'])) {
                $row['tanggal_rilis_formatted'] = date('F j, Y', strtotime($row['tanggal_rilis']));
            } else {
                $row['tanggal_rilis_formatted'] = '';
            }
        }
        
        return view('versi', ['changelog' => $versiData]);
    }
}
