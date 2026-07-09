<?php

namespace App\Controllers;

use App\Models\SiteModel;
use App\Models\ImageModel;

class Search extends BaseController
{
    public function index()
    {
        // Sanitasi input pencarian untuk mencegah Reflected XSS
        $kataKunci = esc($this->request->getGet('q') ?? '');
        $tipe = esc($this->request->getGet('type') ?? 'sites');
        $halaman = (int)($this->request->getGet('page') ?? 1);
        $batasHalaman = 20;

        if (empty(trim($kataKunci))) {
            return redirect()->to('/');
        }

        $modelSitus = new SiteModel();
        $modelGambar = new ImageModel();

        $dataPencarian = [
            'query' => $kataKunci, // Dipertahankan 'query' untuk kompatibilitas View
            'type'  => $tipe,      // Dipertahankan 'type' untuk kompatibilitas View
            'page'  => $halaman,
        ];

        if ($tipe === 'sites') {
            $dataPencarian['totalResults'] = $modelSitus->like('title', $kataKunci)
                                                        ->orLike('description', $kataKunci)
                                                        ->orLike('url', $kataKunci)
                                                        ->countAllResults(false);
            $dataPencarian['results'] = $modelSitus->like('title', $kataKunci)
                                                   ->orLike('description', $kataKunci)
                                                   ->orLike('url', $kataKunci)
                                                   ->paginate($batasHalaman, 'default', $halaman);
            $dataPencarian['pager'] = $modelSitus->pager;
        } else {
            $dataPencarian['totalResults'] = $modelGambar->like('title', $kataKunci)
                                                         ->orLike('alt', $kataKunci)
                                                         ->orLike('imageUrl', $kataKunci)
                                                         ->where('broken', 0)
                                                         ->countAllResults(false);
            $dataPencarian['results'] = $modelGambar->like('title', $kataKunci)
                                                    ->orLike('alt', $kataKunci)
                                                    ->orLike('imageUrl', $kataKunci)
                                                    ->where('broken', 0)
                                                    ->paginate($batasHalaman, 'default', $halaman);
            $dataPencarian['pager'] = $modelGambar->pager;
        }

        return view('search_results', $dataPencarian);
    }
}
