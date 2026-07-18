<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Pencarian: <?= esc($query) ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/fancybox/3.3.5/jquery.fancybox.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/search.css') ?>?v=<?= time() ?>">
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
                    <button type="button" class="btn border-0 p-0" style="background: transparent; margin: 0 6px; color: var(--bs-body-color);" data-bs-toggle="modal" data-bs-target="#uploadImageModal" title="Pencarian Gambar">
                        <i class="fa-solid fa-camera fs-6 hover-primary" onmouseover="this.style.color='var(--gkr-primary)'" onmouseout="this.style.color='inherit'"></i>
                    </button>
                    <button type="submit" class="search-button" style="margin-left: 6px;"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>

        <div class="header-right-icons d-flex align-items-center gap-3 z-3">
            <div class="dropdown" id="calendarDropdownWrap">
                <a href="#" id="calendarDropdownToggle" class="small fw-medium text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" style="cursor: pointer; transition: color 0.2s; color: var(--bs-body-color);" onmouseover="this.style.color='var(--gkr-primary)'" onmouseout="this.style.color='var(--bs-body-color)'">
                    <span class="d-none d-md-inline"><?= $dateStr ?></span>
                    <span class="d-inline d-md-none"><?= date('d/m/y') ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg rounded-4" style="width: 320px; background: rgba(var(--bs-body-bg-rgb), 0.9); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid var(--bs-border-color) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <button type="button" id="prevMonthBtn" class="btn btn-sm btn-link text-decoration-none text-dark p-0 px-2"><i class="fas fa-chevron-left"></i></button>
                        <div class="text-center fw-bold" style="color: var(--gkr-primary); font-size: 0.95rem;" id="calendarMonthYearLabel"></div>
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
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg rounded-4" style="width: 320px; background: rgba(var(--bs-body-bg-rgb), 0.7); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid var(--bs-border-color) !important;">
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
                    <a class="dropdown-item d-flex align-items-center rounded-2 py-2 mt-1" href="<?= base_url('logout') ?>" style="color: var(--gkr-primary); gap: 10px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
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
            <li class="nav-item">
                <a class="nav-link <?= $type === 'image_results' ? 'active' : '' ?>" href="<?= url_to('Search::index') ?>?type=image_results"><i class="fa-solid fa-camera"></i> AI <sup class="text-danger fw-bold">New</sup></a>
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
                    <div id="uploadArea" class="upload-area p-5 border rounded-4 bg-light mb-3" style="border: 2px dashed #ccc !important; cursor: pointer;">
                        <i class="fa-solid fa-cloud-arrow-up fs-1 text-secondary mb-3"></i>
                        <p class="mb-0 text-muted">Tarik file gambar ke sini atau klik untuk memilih file</p>
                        <input type="file" id="fileInput" class="d-none" accept="image/jpeg, image/png, image/webp">
                    </div>
                    
                    <div id="previewArea" class="preview-area mb-3 position-relative d-none">
                        <img id="uploadPreview" src="" alt="Preview" class="img-fluid rounded-3 shadow-sm" style="max-height: 250px; object-fit: contain;">
                        <button type="button" id="clearImageBtn" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle" title="Hapus">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    
                    <div id="uploadError" class="alert alert-danger py-2 small d-none"></div>
                    
                    <button id="uploadSubmitBtn" type="button" class="btn rounded-pill w-100 d-none" style="background-color: var(--gkr-primary); color: var(--gkr-primary-text) !important;">
                        <span id="uploadSubmitText"><i class="fa-solid fa-search me-2"></i>Cari Berdasarkan Gambar</span>
                        <span id="uploadSubmitLoading" class="d-none"><i class="fa-solid fa-spinner fa-spin me-2"></i>Mencari...</span>
                    </button>
                </div>
            </div>
        </div>
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
<script>
document.addEventListener("DOMContentLoaded", function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const previewArea = document.getElementById('previewArea');
    const uploadPreview = document.getElementById('uploadPreview');
    const clearImageBtn = document.getElementById('clearImageBtn');
    const uploadError = document.getElementById('uploadError');
    const uploadSubmitBtn = document.getElementById('uploadSubmitBtn');
    const uploadSubmitText = document.getElementById('uploadSubmitText');
    const uploadSubmitLoading = document.getElementById('uploadSubmitLoading');
    let currentFile = null;

    if (!uploadArea) return;

    uploadArea.addEventListener('click', () => fileInput.click());
    
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.backgroundColor = '#e9ecef';
    });
    uploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadArea.style.backgroundColor = '';
    });
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.style.backgroundColor = '';
        if (e.dataTransfer.files.length) processFile(e.dataTransfer.files[0]);
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) processFile(e.target.files[0]);
    });

    function processFile(file) {
        uploadError.classList.add('d-none');
        if (!file) return;
        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            showError('Hanya format JPG, PNG, atau WEBP yang didukung.');
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            showError('Ukuran gambar maksimal 5MB.');
            return;
        }
        currentFile = file;
        uploadPreview.src = URL.createObjectURL(file);
        uploadArea.classList.add('d-none');
        previewArea.classList.remove('d-none');
        uploadSubmitBtn.classList.remove('d-none');
    }

    clearImageBtn.addEventListener('click', () => {
        currentFile = null;
        fileInput.value = '';
        uploadPreview.src = '';
        uploadArea.classList.remove('d-none');
        previewArea.classList.add('d-none');
        uploadSubmitBtn.classList.add('d-none');
        uploadError.classList.add('d-none');
    });

    function showError(msg) {
        uploadError.textContent = msg;
        uploadError.classList.remove('d-none');
    }

    uploadSubmitBtn.addEventListener('click', async () => {
        if (!currentFile) return;
        uploadSubmitBtn.disabled = true;
        uploadSubmitText.classList.add('d-none');
        uploadSubmitLoading.classList.remove('d-none');
        uploadError.classList.add('d-none');

        const formData = new FormData();
        formData.append('image', currentFile);

        try {
            const res = await fetch('/api/search/upload', { method: 'POST', body: formData });
            const data = await res.json();
            if (!res.ok) {
                const errorMsg = (data.messages && data.messages.error) || data.message || data.error || 'Terjadi kesalahan.';
                throw new Error(errorMsg);
            }
            if (data.status === 'success') {
                // Redirect ke hasil pencarian gambar
                let baseUrl = window.AppConfig && window.AppConfig.searchUrl ? window.AppConfig.searchUrl : '/cari';
                window.location.href = baseUrl + '?type=image_results';
            } else {
                showError('Terjadi kesalahan saat memproses gambar.');
            }
        } catch (err) {
            showError(err.message);
        } finally {
            uploadSubmitBtn.disabled = false;
            uploadSubmitText.classList.remove('d-none');
            uploadSubmitLoading.classList.add('d-none');
        }
    });
});
</script>
<?= $this->endSection() ?>
