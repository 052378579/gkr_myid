<?php

namespace App\Controllers;

class GoController extends BaseController
{
    public function erp($kode)
    {
        // Bersihkan kode dari spasi/enter yang mungkin terbawa dari n8n
        $clean_kode = trim(urldecode($kode));

        // Rute khusus untuk cetak BOM (Case Insensitive & Tahan Spasi)
        if (strpos(strtoupper($clean_kode), 'BOM-') === 0) {
            return redirect()->to("http://103.39.49.86:82/printview?doctype=BOM&name=" . urlencode($clean_kode) . "&format=BOM%20Rincian&no_letterhead=0");
        }
        
        // Rute standar untuk katalog Item
        return redirect()->to("http://103.39.49.86:82/desk#Form/Item/" . urlencode($clean_kode));
    }
}
