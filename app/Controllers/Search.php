<?php

namespace App\Controllers;

use App\Models\CariModel;

class Search extends BaseController
{
    /**
     * Mendapatkan prefix URL foto secara dinamis sesuai lingkungan server (DEV vs PROD)
     */
    private function getFotoUrlPrefix(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
        $serverIp = $_SERVER['SERVER_ADDR'] ?? '';
        $combined = strtolower($host . ' ' . $serverIp);

        if (preg_match('/192\.168\.1\.4|10\.147\.17\.40|budi\.biz\.id|localhost/', $combined)) {
            return 'https://foto.budi.biz.id/';
        }

        return 'https://foto.gkr.my.id/';
    }

    /**
     * Memformat array baris data hasil pencarian dengan prefix URL foto yang sesuai
     */
    private function formatResultUrls(array &$results, string $urlPrefix): void
    {
        if (empty($results)) return;

        foreach ($results as &$row) {
            $row['title'] = $row['judul'] ?? $row['title'] ?? '';
            $row['description'] = $row['deskripsi'] ?? $row['description'] ?? '';

            // Format URL Situs (Galeri atau Alamat Utama)
            if (!empty($row['url'])) {
                if (str_starts_with($row['url'], '?')) {
                    $row['url'] = $urlPrefix . $row['url'];
                } elseif (!str_starts_with($row['url'], 'http://') && !str_starts_with($row['url'], 'https://')) {
                    $row['url'] = $urlPrefix . ltrim($row['url'], '/');
                }
            }

            // Format Site URL
            if (!empty($row['siteUrl'])) {
                if (str_starts_with($row['siteUrl'], '?')) {
                    $row['siteUrl'] = $urlPrefix . $row['siteUrl'];
                } elseif (!str_starts_with($row['siteUrl'], 'http://') && !str_starts_with($row['siteUrl'], 'https://')) {
                    $row['siteUrl'] = $urlPrefix . ltrim($row['siteUrl'], '/');
                }
            }

            // Format Image URL
            if (!empty($row['imageUrl'])) {
                if (!str_starts_with($row['imageUrl'], 'http://') && !str_starts_with($row['imageUrl'], 'https://')) {
                    $row['imageUrl'] = $urlPrefix . ltrim($row['imageUrl'], '/');
                }
            }
        }
    }

    public function index()
    {
        // Sanitasi input pencarian: gunakan string mentah yang bersih untuk kueri SQL
        $kataKunciRaw = trim($this->request->getGet('q') ?? '');
        $kataKunci = esc($kataKunciRaw);
        $tipe = esc($this->request->getGet('type') ?? 'sites');
        $halaman = (int)($this->request->getGet('page') ?? 1);
        $batasHalaman = 20;

        if (empty($kataKunciRaw) && $tipe !== 'image_results') {
            return redirect()->to('/');
        }

        $cariModel = new CariModel();
        $doodleModel = new \App\Models\DoodleModel();

        $dataPencarian = [
            'query' => $kataKunci,
            'type'  => $tipe,
            'page'  => $halaman,
        ];

        $doodle = $doodleModel->getActiveDoodle();
        if ($doodle) {
            $dataPencarian['urlLogo'] = base_url('dokumen/doodle/' . $doodle['gambar']);
            $dataPencarian['altLogo'] = $doodle['event'];
        }

        $urlPrefix = $this->getFotoUrlPrefix();

        if ($tipe === 'sites') {
            // Tab "Semua": Mencari pada seluruh produk
            $dataPencarian['totalResults'] = $cariModel->groupStart()
                                                            ->like('judul', $kataKunciRaw)
                                                            ->orLike('deskripsi', $kataKunciRaw)
                                                            ->orLike('url', $kataKunciRaw)
                                                            ->orLike('kata_kunci', $kataKunciRaw)
                                                        ->groupEnd()
                                                        ->countAllResults(false);
            $dataPencarian['results'] = $cariModel->groupStart()
                                                      ->like('judul', $kataKunciRaw)
                                                      ->orLike('deskripsi', $kataKunciRaw)
                                                      ->orLike('url', $kataKunciRaw)
                                                      ->orLike('kata_kunci', $kataKunciRaw)
                                                  ->groupEnd()
                                                  ->paginate($batasHalaman, 'default', $halaman);
            $dataPencarian['pager'] = $cariModel->pager;

            $this->formatResultUrls($dataPencarian['results'], $urlPrefix);

        } elseif ($tipe === 'image_results') {
            $kodeBom = session()->get('search_kode_bom');
            $aiResults = session()->get('search_ai_results');
            
            if (empty($kodeBom)) {
                return redirect()->to('/');
            }

            if (strpos($kodeBom, 'SWATCH:') === 0) {
                $dataPencarian['totalResults'] = 0;
                $dataPencarian['results'] = [];
                $dataPencarian['pager'] = null;
            } else {
                $kodeBomList = [$kodeBom];
                if (!empty($aiResults) && is_array($aiResults)) {
                    $kodeBomList = array_unique(array_column($aiResults, 'kode_bom'));
                }

                $cariModel->where('imageUrl IS NOT NULL')->where('imageUrl !=', '')->groupStart();
                foreach ($kodeBomList as $kb) {
                    if (strpos($kb, 'SWATCH:') !== 0) {
                        $cariModel->orLike('judul', $kb)->orLike('imageUrl', $kb);
                    }
                }
                $cariModel->groupEnd()->where('rusak', 0);
                $dataPencarian['totalResults'] = $cariModel->countAllResults(false);
                
                $cariModel->where('imageUrl IS NOT NULL')->where('imageUrl !=', '')->groupStart();
                foreach ($kodeBomList as $kb) {
                    if (strpos($kb, 'SWATCH:') !== 0) {
                        $cariModel->orLike('judul', $kb)->orLike('imageUrl', $kb);
                    }
                }
                $cariModel->groupEnd()->where('rusak', 0);
                $dataPencarian['results'] = $cariModel->paginate($batasHalaman, 'default', $halaman);
                $dataPencarian['pager'] = $cariModel->pager;
            }

            $this->formatResultUrls($dataPencarian['results'], $urlPrefix);

        } else {
            // Tab "Gambar" (type === 'images')
            $dataPencarian['totalResults'] = $cariModel->where('imageUrl IS NOT NULL')
                                                      ->where('imageUrl !=', '')
                                                      ->groupStart()
                                                          ->like('judul', $kataKunciRaw)
                                                          ->orLike('alt', $kataKunciRaw)
                                                          ->orLike('imageUrl', $kataKunciRaw)
                                                          ->orLike('kata_kunci', $kataKunciRaw)
                                                      ->groupEnd()
                                                      ->where('rusak', 0)
                                                      ->countAllResults(false);
            $dataPencarian['results'] = $cariModel->where('imageUrl IS NOT NULL')
                                                  ->where('imageUrl !=', '')
                                                  ->groupStart()
                                                      ->like('judul', $kataKunciRaw)
                                                      ->orLike('alt', $kataKunciRaw)
                                                      ->orLike('imageUrl', $kataKunciRaw)
                                                      ->orLike('kata_kunci', $kataKunciRaw)
                                                  ->groupEnd()
                                                  ->where('rusak', 0)
                                                  ->paginate($batasHalaman, 'default', $halaman);
            $dataPencarian['pager'] = $cariModel->pager;

            $this->formatResultUrls($dataPencarian['results'], $urlPrefix);
        }

        // Fallback SpellChecker jika hasil pencarian 0
        $exact = $this->request->getGet('exact');
        if ($dataPencarian['totalResults'] == 0 && empty($exact) && $tipe !== 'image_results' && !empty(trim($kataKunci))) {
            $spellChecker = new \App\Libraries\SpellChecker();
            $koreksi = $spellChecker->getCorrection($kataKunci);
            
            if ($koreksi) {
                $dataPencarian['originalQuery'] = $kataKunci;
                $dataPencarian['correctedQuery'] = $koreksi;
                
                $kataKunci = $koreksi;
                $dataPencarian['query'] = $kataKunci;

                if ($tipe === 'sites') {
                    $dataPencarian['totalResults'] = $cariModel->groupStart()
                                                                    ->like('judul', $kataKunci)
                                                                    ->orLike('deskripsi', $kataKunci)
                                                                    ->orLike('url', $kataKunci)
                                                                ->groupEnd()
                                                                ->countAllResults(false);
                    $dataPencarian['results'] = $cariModel->groupStart()
                                                              ->like('judul', $kataKunci)
                                                              ->orLike('deskripsi', $kataKunci)
                                                              ->orLike('url', $kataKunci)
                                                          ->groupEnd()
                                                          ->paginate($batasHalaman, 'default', $halaman);
                    $dataPencarian['pager'] = $cariModel->pager;

                    $this->formatResultUrls($dataPencarian['results'], $urlPrefix);
                } else {
                    $dataPencarian['totalResults'] = $cariModel->where('imageUrl IS NOT NULL')
                                                              ->where('imageUrl !=', '')
                                                              ->groupStart()
                                                                  ->like('judul', $kataKunci)
                                                                  ->orLike('alt', $kataKunci)
                                                                  ->orLike('imageUrl', $kataKunci)
                                                              ->groupEnd()
                                                              ->where('rusak', 0)
                                                              ->countAllResults(false);
                    $dataPencarian['results'] = $cariModel->where('imageUrl IS NOT NULL')
                                                          ->where('imageUrl !=', '')
                                                          ->groupStart()
                                                              ->like('judul', $kataKunci)
                                                              ->orLike('alt', $kataKunci)
                                                              ->orLike('imageUrl', $kataKunci)
                                                          ->groupEnd()
                                                          ->where('rusak', 0)
                                                          ->paginate($batasHalaman, 'default', $halaman);
                    $dataPencarian['pager'] = $cariModel->pager;

                    $this->formatResultUrls($dataPencarian['results'], $urlPrefix);
                }
            }
        }

        $jsonPath = FCPATH . 'versi.json';
        $dataPencarian['version'] = 'v1.0.0';
        if (file_exists($jsonPath)) {
            $json = json_decode(file_get_contents($jsonPath), true);
            $versiData = $json['data'] ?? [];
            if (!empty($versiData)) {
                usort($versiData, function($a, $b) { return strtotime($b['tanggal_rilis']) - strtotime($a['tanggal_rilis']); });
                $dataPencarian['version'] = 'v' . $versiData[0]['versi'];
            }
        }

        // Trigger Event Log Pencarian
        $id_user = session()->get('id_user') ?? null;
        if (!empty(trim($kataKunci)) || $tipe === 'image_results') {
            $logTipe = 'teks';
            $logKataKunci = $kataKunci;
            if ($tipe === 'sites') {
                $logTipe = 'situs';
            } elseif ($tipe === 'image_results') {
                $logTipe = 'gambar (MobileNetV3 & FAISS Vector Database)';
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
