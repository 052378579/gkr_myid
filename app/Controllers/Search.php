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

        if (empty(trim($kataKunci)) && $tipe !== 'image_results') {
            return redirect()->to('/');
        }

        $modelSitus = new SiteModel();
        $modelGambar = new ImageModel();
        $doodleModel = new \App\Models\DoodleModel();

        $dataPencarian = [
            'query' => $kataKunci, // Dipertahankan 'query' untuk kompatibilitas View
            'type'  => $tipe,      // Dipertahankan 'type' untuk kompatibilitas View
            'page'  => $halaman,
        ];

        $doodle = $doodleModel->getActiveDoodle();
        if ($doodle) {
            $dataPencarian['urlLogo'] = base_url('dokumen/doodle/' . $doodle['gambar']);
            $dataPencarian['altLogo'] = $doodle['event'];
        }

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
        } elseif ($tipe === 'image_results') {
            $kodeBom = session()->get('search_kode_bom');
            $aiResults = session()->get('search_ai_results');
            
            if (empty($kodeBom)) {
                return redirect()->to('/');
            }

            if (strpos($kodeBom, 'SWATCH:') === 0) {
                // Jangan cari ke DB jika hasil utamanya adalah corak/swatch
                $dataPencarian['totalResults'] = 0;
                $dataPencarian['results'] = [];
                $dataPencarian['pager'] = null;
            } else {
                // Ambil semua kode_bom dari array AI (maksimal 5)
                $kodeBomList = [$kodeBom];
                if (!empty($aiResults) && is_array($aiResults)) {
                    $kodeBomList = array_unique(array_column($aiResults, 'kode_bom'));
                }

                // Hitung total hasil
                $modelGambar->select('cari_images.*')->groupStart();
                foreach ($kodeBomList as $kb) {
                    // Abaikan swatch di pencarian sekunder jika ada
                    if (strpos($kb, 'SWATCH:') !== 0) {
                        $modelGambar->orLike('title', $kb)->orLike('imageUrl', $kb);
                    }
                }
                $modelGambar->groupEnd()->where('broken', 0);
                $dataPencarian['totalResults'] = $modelGambar->countAllResults(false);
                
                // Ambil data halaman
                $modelGambar->select('cari_images.*')->groupStart();
                foreach ($kodeBomList as $kb) {
                    if (strpos($kb, 'SWATCH:') !== 0) {
                        $modelGambar->orLike('title', $kb)->orLike('imageUrl', $kb);
                    }
                }
                $modelGambar->groupEnd()->where('broken', 0);
                $dataPencarian['results'] = $modelGambar->paginate($batasHalaman, 'default', $halaman);
                $dataPencarian['pager'] = $modelGambar->pager;
            }

            $host = $_SERVER['HTTP_HOST'] ?? '';
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

        // Trigger Event Pencarian
        $id_user = session()->get('id_user') ?? null;
        if (!empty(trim($kataKunci)) || $tipe === 'image_results') {
            $logTipe = 'teks'; // Default tipe teks (images/all)
            $logKataKunci = $kataKunci;
            if ($tipe === 'sites') {
                $logTipe = 'situs';
            } elseif ($tipe === 'image_results') {
                $logTipe = 'gambar';
                $logKataKunci = $kodeBom ?? 'UPLOADED_IMAGE';
            }
            
            $dataLog = [
                'id_user'        => $id_user,
                'tipe_pencarian' => $logTipe,
                'kata_kunci'     => $logKataKunci,
                'jumlah_hasil'   => $dataPencarian['totalResults'] ?? 0,
                'alamat_ip'      => $this->request->getIPAddress()
            ];
            \CodeIgniter\Events\Events::trigger('log_pencarian', $dataLog);
        }

        return view('search_results', $dataPencarian);
    }
}
