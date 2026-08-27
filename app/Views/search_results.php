<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Pencarian: <?= esc($query) ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/vendor/fancybox/jquery.fancybox.min.css') ?>?v=<?= ASSET_VERSION ?>">
<link rel="stylesheet" href="<?= base_url('css/index.css') ?>?v=<?= ASSET_VERSION ?>">
<link rel="stylesheet" href="<?= base_url('css/search.css') ?>?v=<?= ASSET_VERSION ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$dateStr = $days[date('w')] . ', ' . date('d/m/Y');
?>
<div class="header-container" id="header-container">
        <div class="desktop-left-wrapper">
            <a href="<?= base_url() ?>" class="logo-container">
                <?php if(isset($urlLogo)): ?>
                    <img src="<?= esc($urlLogo) ?>" alt="<?= esc($altLogo) ?>" title="<?= esc($altLogo) ?>">
                <?php else: ?>
                    <img src="<?= base_url('Gracia_logo.png') ?>" alt="Gracia Logo">
                <?php endif; ?>
            </a>
            
            <form action="<?= url_to('Search::index') ?>" method="GET" class="search-box">
                <input type="hidden" name="type" value="<?= esc($type) ?>">
                <input type="text" name="q" id="search-input" class="search-input" value="<?= esc($query) ?>" required>
                <div class="search-actions d-flex align-items-center gap-1">
                    <button type="button" class="icon-action-btn clear-btn" onclick="document.getElementById('search-input').value = ''; document.getElementById('search-input').focus();" title="Hapus Teks">
                        <i class="fas fa-times"></i>
                    </button>
                    <span class="divider me-1"></span>
                    <button type="button" class="icon-action-btn btn-voice-search" title="Pencarian Suara Bahasa Indonesia">
                        <i class="fa-solid fa-microphone fs-6"></i>
                    </button>
                    <button type="button" class="icon-action-btn" data-bs-toggle="modal" data-bs-target="#uploadImageModal" title="Pencarian Gambar">
                        <i class="fa-solid fa-camera fs-6"></i>
                    </button>
                    <button type="submit" class="icon-action-btn search-button" title="Cari">
                        <i class="fas fa-search fs-6"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="header-right-icons d-flex align-items-center gap-3 z-3" style="margin-left: auto !important; padding-right: 0 !important;">
            <div class="dropdown" id="calendarDropdownWrap">
                <a href="#" id="calendarDropdownToggle" class="small fw-medium text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" style="cursor: pointer; transition: color 0.2s; color: var(--bs-body-color);" onmouseover="this.style.color='var(--gkr-primary)'" onmouseout="this.style.color='var(--bs-body-color)'">
                    <span class="d-none d-md-inline"><?= $dateStr ?></span>
                    <span class="d-inline d-md-none"><?= date('d/m/y') ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow border-0 p-3 mt-2 rounded-4" style="width: 320px; z-index: 1060 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <button type="button" id="prevMonthBtn" class="btn btn-sm btn-link text-decoration-none text-body p-0 px-2"><i class="fas fa-chevron-left"></i></button>
                        <div class="text-center fw-bold" style="color: var(--gkr-primary); font-size: 0.95rem;" id="calendarMonthYearLabel"></div>
                        <button type="button" id="nextMonthBtn" class="btn btn-sm btn-link text-decoration-none text-body p-0 px-2"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <table class="table table-sm table-borderless text-center mb-0" style="font-size: 0.85rem;">
                        <thead>
                            <tr>
                                <th class="text-muted fw-bold" style="font-size: 0.8rem;">W</th>
                                <th class="fw-medium">S</th>
                                <th class="fw-medium">S</th>
                                <th class="fw-medium">R</th>
                                <th class="fw-medium">K</th>
                                <th class="fw-medium">J</th>
                                <th class="text-danger fw-medium">S</th>
                                <th class="text-danger fw-medium">M</th>
                            </tr>
                        </thead>
                        <tbody id="calendarBody">
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="dropdown">
                <a href="#" id="appsDropdownToggle" class="text-body text-decoration-none d-flex align-items-center justify-content-center bg-body-tertiary" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px; border-radius: 50%; transition: background-color 0.2s;" onmouseover="this.classList.add('bg-body-secondary')" onmouseout="this.classList.remove('bg-body-secondary')">
                    <i class="fas fa-th fs-5" style="color: var(--bs-secondary-color);"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow border-0 p-3 mt-2 rounded-4" style="width: 320px;">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <a href="http://103.39.49.86:82/desk" class="text-decoration-none text-body d-block p-2 rounded-3 hover-bg">
                                <img src="assets/icon/erp.png" style="width:45px; height:45px;" class="mb-1">
                                <div class="small text-truncate">ERP</div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="https://wickerkane.com/" class="text-decoration-none text-body d-block p-2 rounded-3 hover-bg">
                                <img src="assets/icon/wickerkane.png" style="width:45px; height:45px;" class="mb-1">
                                <div class="small text-truncate">WIckerKAne</div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="https://srv180.niagahoster.com:2096/" class="text-decoration-none text-body d-block p-2 rounded-3 hover-bg">
                                <img src="assets/icon/roundcube.ico" style="width:45px; height:45px;" class="mb-1">
                                <div class="small text-truncate">WebMail</div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="https://3d.gkr.my.id" class="text-decoration-none text-body d-block p-2 rounded-3 hover-bg">
                                <img src="assets/icon/roundcube.ico" style="width:45px; height:45px;" class="mb-1">
                                <div class="small text-truncate">3D</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php 
                $namaLengkap = session()->get('nama_lengkap') ?? 'Pengguna';
                $fotoProfil = session()->get('foto_profil');
                if (!empty($fotoProfil)) {
                    $avatarUrl = base_url('dokumen/karyawan/' . $fotoProfil);
                } else {
                    $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($namaLengkap) . "&background=2B3385&color=fff";
                }
            ?>
            <div class="dropdown">
                <a href="#" class="rounded-circle overflow-hidden text-decoration-none shadow-sm d-inline-block" data-bs-toggle="dropdown" aria-expanded="false" style="width: 38px; height: 38px; cursor: pointer; border: 2px solid #ffffff;" title="Menu Akun">
                    <img src="<?= $avatarUrl ?>" alt="Avatar" class="w-100 h-100 object-fit-cover">
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><a class="dropdown-item py-2" href="<?= base_url('profile') ?>"><i class="fas fa-user text-secondary me-2"></i>Profil</a></li>
                    <?php if (session()->get('id_user') == 1): ?>
                        <li><a class="dropdown-item py-2" href="<?= base_url('admin') ?>"><i class="fas fa-user-shield text-secondary me-2"></i>Admin</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2" href="<?= base_url('logout') ?>" style="color: var(--gkr-primary);"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="tabs-container">
        <ul class="nav nav-tabs" style="padding-left: 0 !important; margin-left: 0 !important;">
            <li class="nav-item">
                <a class="nav-link <?= $type === 'sites' ? 'active' : '' ?>" style="padding-left: 0 !important; margin-left: 0 !important;" href="<?= url_to('Search::index') ?>?q=<?= urlencode($query) ?>&type=sites">Semua</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $type === 'images' ? 'active' : '' ?>" href="<?= url_to('Search::index') ?>?q=<?= urlencode($query) ?>&type=images">Gambar</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $type === 'image_results' ? 'active' : '' ?>" href="<?= url_to('Search::index') ?>?type=image_results"><i class="fa-solid fa-camera"></i> AI <sup class="text-danger fw-bold">New</sup></a>
            </li>
        </ul>
    </div>

<div class="main-content-wrapper" style="min-height: calc(100vh - 140px); display: flex; flex-direction: column;">
    <?php if (isset($correctedQuery)): ?>
        <div class="results-container pt-2 pb-0 mb-0">
            <div class="text-dark mb-0 py-1">
                <span class="fs-6" style="color: var(--bs-body-color);">Ini adalah hasil untuk <a href="<?= url_to('Search::index') ?>?q=<?= urlencode($correctedQuery) ?>" class="fw-bold fst-italic query-link"><?= esc($correctedQuery) ?></a></span><br>
                <span class="small text-muted">Atau telusuri <a href="<?= url_to('Search::index') ?>?q=<?= urlencode($originalQuery) ?>&exact=1" class="query-link"><?= esc($originalQuery) ?></a></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($type === 'sites'): ?>
        <div class="results-container" id="knowledgeApp">
            <div class="row g-4">
                <!-- Kolom Kiri: Daftar Hasil Teks (Lebar Presisi 65% di Desktop, 100% di Mobile) -->
                <div class="col-12 col-lg-8 results-left-col">
                    <p class="result-count mb-3" style="padding-left: 0 !important; margin-left: 0 !important;">Ditemukan <?= $totalResults ?> hasil</p>
                    <?php foreach ($results as $index => $site): ?>
                        <?php 
                            $patternBOM = '/\(?\b(?:fg|Fg|FG)\s*[-_]?\s*([0-9]+)\)?/i';
                            $siteUrlClean = esc($site['url']);
                            $siteBom = esc(!empty($site['kode_bom']) ? $site['kode_bom'] : 'FG-');
                            $siteProduksi = esc(!empty($site['produksi']) ? $site['produksi'] : 'UNIT -');
                            $siteLihatBom = ($siteBom !== 'FG-') ? "BOM-{$siteBom}-001" : 'BOM-FG-';
                            $siteImg = esc(!empty($site['imageUrl']) ? $site['imageUrl'] : 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="368" height="230" viewBox="0 0 368 230"><rect width="368" height="230" fill="transparent"/></svg>');
                            
                            $rawTitle = esc($site['title']);
                            $rawDesc  = esc(!empty($site['description']) ? $site['description'] : $rawTitle);
                            
                            $siteTitle = preg_replace($patternBOM, '(FG-$1)', $rawTitle);
                            $siteDesc  = preg_replace($patternBOM, '(FG-$1)', $rawDesc);
                            
                            $erpUrl = ($siteBom !== 'FG-') ? "http://103.39.49.86:82/desk#Form/Item/{$siteBom}" : '#';
                            $pdfUrl = ($siteBom !== 'FG-') ? "http://103.39.49.86:82/printview?doctype=BOM&name=BOM-{$siteBom}-001&format=BOM%20Rincian&no_letterhead=0" : '#';
                            
                            $dirPath = 'GRACIA/2022/';
                            if (preg_match('~/\\?([^#]+)~', $siteUrlClean, $mDir)) {
                                $dirPath = $mDir[1];
                            }
                        ?>
                        <div class="site-result site-result-item mb-2" 
                             :class="{ 'active-knowledge-item': selectedIndex === <?= $index ?> }"
                             @click="selectKnowledgeItem(<?= $index ?>, '<?= addslashes($siteTitle) ?>', '<?= addslashes($siteDesc) ?>', '<?= addslashes($siteBom) ?>', '<?= addslashes($siteProduksi) ?>', '<?= addslashes($dirPath) ?>', '<?= addslashes($siteUrlClean) ?>', '<?= addslashes($siteImg) ?>', '<?= addslashes($erpUrl) ?>', '<?= addslashes($pdfUrl) ?>')">
                            <div class="title">
                                <a href="<?= $siteUrlClean ?>" target="_blank" class="gracia-title-link fw-bold text-decoration-none" onclick="updateLinkCount(<?= $site['id'] ?>)">
                                    <?= $siteTitle ?>
                                </a>
                            </div>
                            <div class="url"><?= $siteUrlClean ?></div>
                            <div class="description"><?= $siteDesc ?></div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Custom Pagination Logo cari 1 (Gambar PNG Asli pageStart/page/pageSelected/pageEnd + Pager CI4) -->
                    <?php if (isset($pager) && !empty($results)): ?>
                        <?php 
                            $currentPage = $pager->getCurrentPage();
                            $pageCount = $pager->getPageCount();
                            $startPage = max(1, $currentPage - 4);
                            $endPage = min($pageCount, $currentPage + 4);
                        ?>
                        <div class="google-pagination-container d-flex flex-column align-items-center justify-content-center w-100 text-center">
                            <div class="google-pagination-brand d-inline-flex align-items-end justify-content-center position-relative mb-4" style="gap: 0 !important;">
                                <div class="page-part" style="pointer-events: none; margin: 0; padding: 0; line-height: 1;">
                                    <img src="<?= base_url('assets/images/pageStart.png') ?>" alt="c" style="height: 38px !important; display: block !important; vertical-align: bottom !important;">
                                </div>
                                
                                <?php for ($i = $startPage; $i <= $endPage; $i++): 
                                    $isActive = ($i == $currentPage);
                                    $imgSrc = $isActive ? 'pageSelected.png' : 'page.png';
                                ?>
                                    <a href="<?= url_to('Search::index') ?>?q=<?= urlencode($query) ?>&type=<?= esc($type) ?>&page=<?= $i ?>" class="page-number-box position-relative d-inline-flex flex-column align-items-center justify-content-end text-decoration-none <?= $isActive ? 'active' : '' ?>" style="margin: 0; padding: 0; line-height: 1;">
                                        <img src="<?= base_url('assets/images/' . $imgSrc) ?>" alt="a" style="height: 38px !important; display: block !important; vertical-align: bottom !important;">
                                        <span class="page-num-label" style="position: absolute; bottom: -24px; left: 50%; transform: translateX(-50%); font-size: 0.9rem; font-weight: 700; color: <?= $isActive ? '#EA4335' : 'var(--bs-primary)' ?>;"><?= $i ?></span>
                                    </a>
                                <?php endfor; ?>
                                
                                <div class="page-part" style="pointer-events: none; margin: 0; padding: 0; line-height: 1;">
                                    <img src="<?= base_url('assets/images/pageEnd.png') ?>" alt="ri" style="height: 38px !important; display: block !important; vertical-align: bottom !important;">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($results)): ?>
                        <p>Tidak ada situs mebel yang ditemukan.</p>
                    <?php endif; ?>
                </div>

                <!-- Kolom Kanan: Google Knowledge Panel Card (Disembunyikan di Mobile, Muncul di Desktop) -->
                <?php if (!empty($results)): ?>
                <div class="d-none d-lg-block col-lg-4 knowledge-panel-col mt-0">
                    <div class="google-knowledge-card">
                        <!-- Hero Media Foto Utama (Fluid 100% Presisi, Rasio 16:10) -->
                        <img :src="activeItem.imageUrl" :alt="activeItem.title" class="google-knowledge-hero-img" style="width: 100% !important; max-width: 100% !important; height: auto !important; max-height: 230px !important; aspect-ratio: 16 / 10 !important; object-fit: contain !important; margin: 0 0 16px 0 !important; box-sizing: border-box !important; display: block !important;" @error="handleImgError">
                        
                        <!-- Header Rincian -->
                        <div class="google-knowledge-heading">RINCIAN</div>
                        
                        <!-- List Spesifikasi (Deskripsi, Kode BOM, Lihat BOM, Produksi) -->
                        <ul class="google-spec-list">
                            <li><strong>Deskripsi</strong> : <span v-text="formatTitleCase(activeItem.description)"></span></li>
                            <li><strong>Kode BOM</strong> : <span class="fw-bold" style="color: var(--gkr-primary); font-family: monospace;">{{ formatKodeBom(activeItem.kodeBom) }}</span></li>
                            <li><strong>Lihat BOM</strong> : <span class="fw-bold" style="color: var(--gkr-primary); font-family: monospace;">{{ formatLihatBom(activeItem.kodeBom) }}</span></li>
                            <li><strong>Produksi</strong> : <span class="fw-bold" style="color: var(--gkr-primary);">{{ formatProduksi(activeItem.produksi) }}</span></li>
                        </ul>
                        
                        <!-- 3 Action Pills Buttons Presisi: [ 📄 BOM ] [ 🌐 ERP ] [ 🖼️ Foto ] -->
                        <div class="google-action-pill-group">
                            <a :href="isBomAvailable(activeItem.kodeBom) ? activeItem.pdfUrl : 'javascript:void(0)'" 
                               :target="isBomAvailable(activeItem.kodeBom) ? '_blank' : '_self'"
                               :class="['google-action-pill', isBomAvailable(activeItem.kodeBom) ? 'btn-action-active' : 'disabled']" 
                               :title="isBomAvailable(activeItem.kodeBom) ? 'Cetak PDF Rincian BOM (Buka Jendela Baru)' : 'BOM belum tersedia'">
                                <i class="fa-solid fa-file-pdf me-1"></i> BOM
                            </a>
                            <a :href="isBomAvailable(activeItem.kodeBom) ? activeItem.erpUrl : 'javascript:void(0)'" 
                               :target="isBomAvailable(activeItem.kodeBom) ? '_blank' : '_self'"
                               :class="['google-action-pill', isBomAvailable(activeItem.kodeBom) ? 'btn-action-active' : 'disabled']" 
                               :title="isBomAvailable(activeItem.kodeBom) ? 'Kunjungi Form Item ERP (Buka Jendela Baru)' : 'ERP belum tersedia'">
                                <i class="fa-solid fa-globe me-1"></i> ERP
                            </a>
                            <a :href="activeItem.siteUrl" target="_blank" class="google-action-pill btn-action-active" title="Lihat Foto Katalog (Buka Jendela Baru)">
                                <i class="fa-solid fa-camera me-1"></i> Foto
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="results-container">
            <?php if ($type === 'image_results'): ?>
                <?php 
                    $kodeBom = session()->get('search_kode_bom');
                    $aiResults = session()->get('search_ai_results');
                    $confidence = session()->get('search_confidence');
                    $confText = $confidence ? round($confidence * 100, 1) . '%' : '';
                    
                    $isSwatch = false;
                    $isMulti = (!empty($aiResults) && count($aiResults) > 1);
                    $displayName = $kodeBom;
                    
                    if (strpos($kodeBom, 'SWATCH:') === 0) {
                        $isSwatch = true;
                        // Hapus prefix SWATCH: dan ganti strip (-) dengan spasi agar lebih cantik
                        $displayName = str_replace('-', ' ', substr($kodeBom, 7));
                    } elseif (!empty($results)) {
                        $rawTitle = $results[0]['title'] ?: $results[0]['alt'];
                        $displayName = ucwords(strtolower($rawTitle));
                        // Pastikan 'fg-' menjadi 'FG-'
                        $displayName = str_ireplace('(fg-', '(FG-', $displayName);
                        $displayName = str_ireplace(' fg-', ' FG-', $displayName);
                        
                        // Jaga-jaga jika fg- ada di awal string tanpa spasi/kurung
                        if (stripos($displayName, 'fg-') === 0) {
                            $displayName = 'FG-' . substr($displayName, 3);
                        }
                    }
                ?>
                <p class="result-count text-primary fw-medium" style="color: var(--gkr-primary) !important; font-size: 1.1rem; margin-bottom: 1.5rem;">
                    <?php if ($isSwatch): ?>
                        <i class="fa-solid fa-wand-magic-sparkles me-1 text-warning"></i> 
                        AI mengenali corak/material ini sebagai:
                        <strong class="text-dark fs-6 ms-1" style="letter-spacing: 0.3px;"><?= esc($displayName) ?></strong> 
                        <span class="text-muted ms-1" style="font-size: 0.85rem;"><?= $confText ? "(Akurasi: $confText)" : '' ?></span>
                    <?php else: ?>
                        Kecocokan visual
                    <?php endif; ?>
                </p>
            <?php else: ?>
                <p class="result-count">Ditemukan <?= $totalResults ?> hasil</p>
            <?php endif; ?>
        </div>
        
        <div class="image-results-container">
            <div class="masonry-grid">
                <?php foreach ($results as $image): ?>
                    <?php 
                        $rawTitle = $image['title'] ?: $image['alt'];
                        $titleCase = ucwords(strtolower($rawTitle));
                        
                        $siteUrlAttr = esc($image['siteUrl'], 'attr');
                        $captionHtml = esc($titleCase) . " <br><a href='{$siteUrlAttr}' target='_blank' style='color:#a9a9a9; text-decoration:underline;'>Lihat Gambar</a>";
                    ?>
                    <div class="grid-item">
                        <a href="<?= esc($image['imageUrl']) ?>" data-fancybox="gallery" 
                           data-caption="<?= esc($captionHtml, 'html') ?>"
                           data-siteurl="<?= esc($image['siteUrl']) ?>"
                           onclick="updateImageCount(<?= $image['id'] ?>)">
                            <img src="<?= esc($image['imageUrl']) ?>" alt="<?= esc($titleCase) ?>" 
                                 onerror="setBroken(this, '<?= esc($image['imageUrl']) ?>')">
                            <div class="details">
                                <div class="image-title"><?= esc($titleCase) ?></div>
                                <?php 
                                    $parsed = parse_url($image['siteUrl']);
                                    $domain = isset($parsed['host']) ? $parsed['host'] : $image['siteUrl'];
                                    $domain = str_replace('www.', '', $domain);
                                ?>
                                <div class="image-domain"><?= esc($domain) ?></div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($results)): ?>
                <p style="padding-left: 150px;">Tidak ada gambar yang ditemukan.</p>
            <?php endif; ?>
        </div>

        <!-- Custom Pagination Logo cari 1 untuk Tab Gambar (type=images) -->
        <?php if (isset($pager) && !empty($results)): ?>
            <?php 
                $currentPage = $pager->getCurrentPage();
                $pageCount = $pager->getPageCount();
                $startPage = max(1, $currentPage - 4);
                $endPage = min($pageCount, $currentPage + 4);
            ?>
            <div class="google-pagination-container d-flex flex-column align-items-center justify-content-center w-100 my-5 text-center">
                <div class="google-pagination-brand d-inline-flex align-items-end justify-content-center gap-0 position-relative mb-4">
                    <div class="page-number-box d-inline-flex align-items-end" style="pointer-events: none; margin: 0; padding: 0;">
                        <img src="<?= base_url('assets/images/pageStart.png') ?>" alt="c" style="height: 37px !important; display: block !important; vertical-align: bottom !important;">
                    </div>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): 
                        $isActive = ($i == $currentPage);
                        $imgSrc = $isActive ? 'pageSelected.png' : 'page.png';
                    ?>
                        <a href="<?= url_to('Search::index') ?>?q=<?= urlencode($query) ?>&type=<?= esc($type) ?>&page=<?= $i ?>" class="page-number-box position-relative d-inline-flex flex-column align-items-center justify-content-end text-decoration-none <?= $isActive ? 'active' : '' ?>" style="margin: 0; padding: 0;">
                            <img src="<?= base_url('assets/images/' . $imgSrc) ?>" alt="a" style="height: 37px !important; display: block !important; vertical-align: bottom !important;">
                            <span style="position: absolute; bottom: -22px; left: 50%; transform: translateX(-50%); font-size: 0.88rem; font-weight: 700; color: <?= $isActive ? '#EA4335' : '#4285F4' ?>;"><?= $i ?></span>
                        </a>
                    <?php endfor; ?>
                    
                    <div class="page-number-box d-inline-flex align-items-end" style="pointer-events: none; margin: 0; padding: 0;">
                        <img src="<?= base_url('assets/images/pageEnd.png') ?>" alt="ri" style="height: 37px !important; display: block !important; vertical-align: bottom !important;">
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <footer class="mt-auto py-3 position-relative w-100" style="background-color: var(--bs-tertiary-bg); border-top: 1px solid var(--bs-border-color); color: var(--bs-secondary-color); font-size: 0.9rem; margin-top: 40px !important;">
        <div class="d-flex justify-content-between align-items-center px-4">
            <!-- BAGIAN KIRI: Ikon Mode Gelap/Terang -->
            <div class="flex-grow-1 text-start">
                <button id="themeToggleBtn" class="btn btn-sm btn-outline-secondary border-0 hover-primary" title="Ubah Mode Tema" style="transition: color 0.2s; color: var(--bs-body-color);" onmouseover="this.style.color='var(--gkr-primary)'" onmouseout="this.style.color='var(--bs-body-color)'">
                    <span id="themeIcon">🌓 Tema</span>
                </button>
            </div>
            <!-- BAGIAN TENGAH: Copyright -->
            <div class="text-center">
                <span class="d-none d-sm-inline">Dikembangkan oleh </span><a href="https://rnd.gkr.my.id" class="text-decoration-none" style="color: var(--gkr-primary); font-weight: 500;">RND</a> &copy; <?= date('Y') ?>
            </div>
            <!-- BAGIAN KANAN: Versi -->
            <div class="flex-grow-1 text-end">
                <a href="<?= base_url('versi') ?>" class="text-decoration-none hover-primary" style="transition: color 0.2s; color: var(--bs-body-color);" onmouseover="this.style.color='var(--gkr-primary)'" onmouseout="this.style.color='var(--bs-body-color)'"><?= esc($version) ?></a>
            </div>
        </div>
    </footer>
</div>
    <!-- Modal Upload Gambar -->
    <div class="modal fade" id="uploadImageModal" tabindex="-1" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="uploadImageModalLabel" style="color: var(--gkr-primary);">Pencarian Gambar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div id="uploadAreaContainer">
                        <!-- Tampilan Desktop: Drag & Drop -->
                        <div id="uploadArea" class="upload-area p-5 border rounded-4 bg-body-tertiary mb-3 d-none d-md-block" style="border: 2px dashed var(--bs-border-color) !important; cursor: pointer;">
                            <i class="fa-solid fa-cloud-arrow-up fs-1 text-secondary mb-3"></i>
                            <p class="mb-0 text-muted">Tarik file gambar ke sini atau klik untuk memilih file</p>
                        </div>
                        
                        <!-- Tampilan Mobile: 2 Tombol -->
                        <div id="uploadAreaMobile" class="row g-2 mb-3 d-flex d-md-none">
                            <div class="col-6">
                                <button type="button" id="btnCamera" class="btn btn-outline-secondary border-0 bg-body-tertiary w-100 h-100 py-4 rounded-4" style="border: 2px dashed var(--bs-border-color) !important;">
                                    <i class="fa-solid fa-camera fs-1 mb-2" style="color: var(--gkr-primary);"></i>
                                    <span class="d-block text-muted small fw-medium">Ambil Foto</span>
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" id="btnGallery" class="btn btn-outline-secondary border-0 bg-body-tertiary w-100 h-100 py-4 rounded-4" style="border: 2px dashed var(--bs-border-color) !important;">
                                    <i class="fa-solid fa-images fs-1 mb-2" style="color: var(--gkr-primary);"></i>
                                    <span class="d-block text-muted small fw-medium">Pilih Galeri</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Input Files -->
                        <input type="file" id="fileInput" class="d-none" accept="image/jpeg, image/png, image/webp">
                        <input type="file" id="cameraInput" class="d-none" accept="image/jpeg, image/png, image/webp" capture="environment">
                    </div>
                    
                    <div id="previewArea" class="preview-area mb-4 position-relative d-none w-100">
                        <div class="border rounded-4 overflow-hidden w-100 bg-dark" style="height: 350px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
                            <img id="uploadPreview" src="" alt="Preview" style="display: block; max-width: 100%;">
                        </div>
                        <button type="button" id="clearImageBtn" class="btn btn-danger rounded-circle position-absolute d-flex justify-content-center align-items-center shadow" style="top: -15px; right: -15px; width: 32px; height: 32px; padding: 0; z-index: 10; font-size: 0.85rem;" title="Hapus">
                            <i class="fa-solid fa-times"></i>
                        </button>
                        <small class="text-muted d-block mt-2"><i class="fa-solid fa-crop-simple me-1"></i> Geser kotak untuk memfokuskan objek pencarian (Opsional)</small>
                    </div>
                    
                    <div id="uploadError" class="alert alert-danger py-2 small d-none"></div>
                    
                    <button id="uploadSubmitBtn" type="button" class="btn rounded-pill w-100 d-none" style="background-color: #2B3385; color: #ffffff !important;">
                        <span id="uploadSubmitText" style="color: #ffffff !important;"><i class="fa-solid fa-search me-2" style="color: #ffffff !important;"></i>Cari Berdasarkan Gambar</span>
                        <span id="uploadSubmitLoading" class="d-none" style="color: #ffffff !important;"><i class="fa-solid fa-spinner fa-spin me-2" style="color: #ffffff !important;"></i>Mencari...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script src="<?= base_url('js/vendor/jquery-3.3.1.min.js') ?>?v=<?= ASSET_VERSION ?>"></script>
<script src="<?= base_url('js/vendor/fancybox/jquery.fancybox.min.js') ?>?v=<?= ASSET_VERSION ?>"></script>
<script src="<?= base_url('js/vendor/masonry/masonry.pkgd.min.js') ?>?v=<?= ASSET_VERSION ?>"></script>

<?= $this->section('styles') ?>
<meta name="page-config" 
    data-api-update-link-count="<?= base_url('api/updateLinkCount') ?>" 
    data-api-update-image-count="<?= base_url('api/updateImageCount') ?>" 
    data-api-set-broken="<?= base_url('api/setBroken') ?>"
    data-api-search-upload="<?= base_url('api/search/upload') ?>"
    data-search-url="<?= url_to('Search::index') ?>">
<?= $this->endSection() ?>

<script src="<?= base_url('js/calendar.js') ?>?v=<?= ASSET_VERSION ?>"></script>
<script src="<?= base_url('js/voice_search.js') ?>?v=<?= ASSET_VERSION ?>"></script>
<script src="<?= base_url('js/search.js') ?>?v=<?= ASSET_VERSION ?>"></script>
<script src="<?= base_url('js/search_results.js') ?>?v=<?= ASSET_VERSION ?>"></script>
<?= $this->endSection() ?>
