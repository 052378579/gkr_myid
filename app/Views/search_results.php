<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Pencarian: <?= esc($query) ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/fancybox/3.3.5/jquery.fancybox.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/search.css') ?>">
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
                <div class="search-actions">
                    <button type="button" class="clear-btn" onclick="document.getElementById('search-input').value = ''; document.getElementById('search-input').focus();"><i class="fas fa-times"></i></button>
                    <span class="divider"></span>
                    <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>

        <div class="header-right-icons d-flex align-items-center gap-3 z-3">
            <div class="dropdown" id="calendarDropdownWrap">
                <a href="#" id="calendarDropdownToggle" class="text-dark small fw-medium text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" style="cursor: pointer; transition: color 0.2s;" onmouseover="this.style.color='#2B3385'" onmouseout="this.style.color='#212529'">
                    <span class="d-none d-md-inline"><?= $dateStr ?></span>
                    <span class="d-inline d-md-none"><?= date('d/m/y') ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg rounded-4" style="width: 320px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.5) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <button type="button" id="prevMonthBtn" class="btn btn-sm btn-link text-decoration-none text-dark p-0 px-2"><i class="fas fa-chevron-left"></i></button>
                        <div class="text-center fw-bold" style="color: #2B3385; font-size: 0.95rem;" id="calendarMonthYearLabel"></div>
                        <button type="button" id="nextMonthBtn" class="btn btn-sm btn-link text-decoration-none text-dark p-0 px-2"><i class="fas fa-chevron-right"></i></button>
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
                <a href="#" id="appsDropdownToggle" class="text-dark text-decoration-none d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px; border-radius: 50%; background-color: #f1f3f4; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#e8eaed'" onmouseout="this.style.backgroundColor='#f1f3f4'">
                    <i class="fas fa-th fs-5" style="color: #5f6368;"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg rounded-4" style="width: 320px; background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.5) !important;">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <a href="http://103.39.49.86:82/desk" class="text-decoration-none text-dark d-block p-2 rounded-3 hover-bg">
                                <img src="assets/icon/erp.png" style="width:45px; height:45px;" class="mb-1">
                                <div class="small text-truncate">ERP</div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="https://wickerkane.com/" class="text-decoration-none text-dark d-block p-2 rounded-3 hover-bg">
                                <img src="assets/icon/wickerkane.png" style="width:45px; height:45px;" class="mb-1">
                                <div class="small">WIckerKAne</div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="https://srv180.niagahoster.com:2096/" class="text-decoration-none text-dark d-block p-2 rounded-3 hover-bg">
                                <img src="assets/icon/roundcube.ico" style="width:45px; height:45px;" class="mb-1">
                                <div class="small">WebMail</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php 
                $namaLengkap = session()->get('nama_lengkap') ?? 'User';
                $fotoProfil = session()->get('foto_profil');
                if (!empty($fotoProfil)) {
                    $avatarUrl = base_url('dokumen/karyawan/' . $fotoProfil);
                } else {
                    $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($namaLengkap) . "&background=2B3385&color=fff";
                }
            ?>
            <div class="dropdown">
                <a href="#" class="rounded-circle overflow-hidden text-decoration-none shadow-sm d-inline-block" data-bs-toggle="dropdown" aria-expanded="false" style="width: 38px; height: 38px; cursor: pointer; border: 2px solid #ffffff;" title="Menu Profil">
                    <img src="<?= $avatarUrl ?>" alt="Avatar" class="w-100 h-100 object-fit-cover">
                </a>
                <div class="dropdown-menu dropdown-menu-end p-2 shadow-sm rounded-3" style="min-width: 150px; border: 1px solid #e4e4e4; margin-top: 10px;">
                    <a class="dropdown-item d-flex align-items-center rounded-2 py-2 mb-1" href="<?= base_url('profile') ?>" style="color: #333; gap: 10px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                        <i class="fas fa-user text-secondary" style="font-size: 1rem; width: 20px; text-align: center;"></i> 
                        <span style="font-size: 0.95rem;">Profil</span>
                    </a>
                    <div class="dropdown-divider my-1" style="border-color: #eee;"></div>
                    <a class="dropdown-item d-flex align-items-center rounded-2 py-2 mt-1" href="<?= base_url('logout') ?>" style="color: #2B3385; gap: 10px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                        <i class="fas fa-sign-out-alt" style="font-size: 1rem; width: 20px; text-align: center;"></i> 
                        <span style="font-size: 0.95rem;">Keluar</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="tabs-container">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link <?= $type === 'sites' ? 'active' : '' ?>" href="<?= url_to('Search::index') ?>?q=<?= urlencode($query) ?>&type=sites">Semua</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $type === 'images' ? 'active' : '' ?>" href="<?= url_to('Search::index') ?>?q=<?= urlencode($query) ?>&type=images">Gambar</a>
            </li>
        </ul>
    </div>

<div class="main-content-wrapper" style="min-height: calc(100vh - 140px); display: flex; flex-direction: column;">
    <?php if ($type === 'sites'): ?>
        <div class="results-container">
            <p class="result-count">Ditemukan <?= $totalResults ?> hasil</p>

            <?php foreach ($results as $site): ?>
                <div class="site-result">
                    <div class="title">
                        <a href="<?= esc($site['url']) ?>" target="_blank" onclick="updateLinkCount(<?= $site['id'] ?>)">
                            <?= esc($site['title']) ?>
                        </a>
                    </div>
                    <div class="url"><?= esc($site['url']) ?></div>
                    <div class="description"><?= esc($site['description']) ?></div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($results)): ?>
                <p>Tidak ada situs yang ditemukan.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="results-container">
            <p class="result-count">Ditemukan <?= $totalResults ?> hasil</p>
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
    <?php endif; ?>

    <!-- Custom Pagination -->
    <?php if (isset($pager) && !empty($results)): ?>
        <div class="pagination-container" style="margin-top: auto;">
            <div class="pagination-logo">
                    <div class="page-number-box" style="pointer-events: none;">
                        <img src="<?= base_url('assets/images/pageStart.png') ?>" alt="D" style="height: 37px;">
                    </div>
                
                <?php 
                $currentPage = $pager->getCurrentPage();
                $pageCount = $pager->getPageCount();
                $startPage = max(1, $currentPage - 4);
                $endPage = min($pageCount, $currentPage + 4);
                
                for ($i = $startPage; $i <= $endPage; $i++): 
                    $isActive = ($i == $currentPage);
                    $imgSrc = $isActive ? 'pageSelected.png' : 'page.png';
                ?>
                    <a href="<?= url_to('Search::index') ?>?q=<?= urlencode($query) ?>&type=<?= esc($type) ?>&page=<?= $i ?>" class="page-number-box <?= $isActive ? 'active' : '' ?>">
                            <img src="<?= base_url('assets/images/' . $imgSrc) ?>" alt="o" style="height: 37px;">
                        <span><?= $i ?></span>
                    </a>
                <?php endfor; ?>
                                <div class="page-number-box" style="pointer-events: none;">
                        <img src="<?= base_url('assets/images/pageEnd.png') ?>" alt="gle" style="height: 43px;">
                    </div>
            </div>
        </div>
    <?php endif; ?>
    
    <footer class="mt-auto py-3 position-relative w-100" style="background-color: #f2f2f2; border-top: 1px solid #e4e4e4; color: #70757a; font-size: 0.9rem; margin-top: 40px !important;">
        <div class="text-center w-100">
            <span class="d-none d-sm-inline">Dikembangkan oleh </span><a href="https://rnd.gkr.my.id" class="text-decoration-none" style="color: #2B3385; font-weight: 500;">RND</a> &copy; <?= date('Y') ?>
        </div>
        <div class="position-absolute" style="right: 20px; top: 50%; transform: translateY(-50%);">
            <a href="<?= base_url('versi') ?>" class="text-decoration-none text-muted hover-primary" style="transition: color 0.2s;" onmouseover="this.style.color='#2B3385'" onmouseout="this.style.color='inherit'"><?= esc($version) ?></a>
        </div>
    </footer>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/jquery-3.3.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/fancybox/3.3.5/jquery.fancybox.min.js') ?>"></script>
<script src="<?= base_url('assets/js/masonry/4.2.2/masonry.pkgd.min.js') ?>"></script>

<script>
    window.AppConfig = {
        apiUpdateLinkCount: '<?= base_url('api/updateLinkCount') ?>',
        apiUpdateImageCount: '<?= base_url('api/updateImageCount') ?>',
        apiSetBroken: '<?= base_url('api/setBroken') ?>'
    };
</script>
<script src="<?= base_url('js/calendar.js') ?>"></script>
<script src="<?= base_url('js/search.js') ?>"></script>
<?= $this->endSection() ?>
