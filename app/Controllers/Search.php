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

            // Logika penggantian URL dinamis
            $urlPrefix = 'https://foto.gkr.my.id/';

            if (!empty($dataPencarian['results'])) {
                foreach ($dataPencarian['results'] as &$row) {
                    $row['url'] = preg_replace('/^(?:http:\/\/[^\/]+\/)?(\?[^\#]+)/', $urlPrefix . '$1', $row['url']);
                }
            }
        } else {
            $dataPencarian['totalResults'] = $modelGambar->groupStart()
                                                             ->like('title', $kataKunci)
                                                             ->orLike('alt', $kataKunci)
                                                             ->orLike('imageUrl', $kataKunci)
                                                         ->groupEnd()
                                                         ->where('broken', 0)
                                                         ->countAllResults(false);
            $dataPencarian['results'] = $modelGambar->groupStart()
                                                        ->like('title', $kataKunci)
                                                        ->orLike('alt', $kataKunci)
                                                        ->orLike('imageUrl', $kataKunci)
                                                    ->groupEnd()
                                                    ->where('broken', 0)
                                                    ->paginate($batasHalaman, 'default', $halaman);
            $dataPencarian['pager'] = $modelGambar->pager;

            $host = $_SERVER['HTTP_HOST'] ?? '';
            // Jika host mengandung IP Dev atau domain Dev (budi.biz.id)
            if (preg_match('/192\.168\.1\.4|10\.147\.17\.40|budi\.biz\.id/', $host)) {
                $urlPrefix = 'https://foto.budi.biz.id/';
            } else {
                $urlPrefix = 'https://foto.gkr.my.id/';
            }

            if (!empty($dataPencarian['results'])) {
                foreach ($dataPencarian['results'] as &$row) {
                    $row['siteUrl'] = preg_replace('/^(?:http:\/\/[^\/]+\/)?(\?[^\#]+)/', $urlPrefix . '$1', $row['siteUrl']);
                    $row['imageUrl'] = preg_replace('/^(?:http:\/\/[^\/]+\/)?(.*)/', $urlPrefix . '$1', ltrim($row['imageUrl'], '/'));
                }
            }
        }

        $versiModel = new \App\Models\VersiModel();
        $latest = $versiModel->orderBy('tanggal_rilis', 'DESC')->first();
        $dataPencarian['version'] = $latest ? 'v' . $latest['versi'] : 'v1.0.0';

        return view('search_results', $dataPencarian);
    }
}
