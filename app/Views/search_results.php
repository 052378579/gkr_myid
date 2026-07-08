<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Pencarian: <?= esc($query) ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/fancybox/3.3.5/jquery.fancybox.min.css') ?>">
<style>
    .hover-bg:hover {
        background-color: #e4e7eb;
    }
    body { background-color: #fff; }
    .header-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-left: 20px;
        padding-right: 20px;
        padding-top: 25px;
        padding-bottom: 25px;
        width: 100%;
        position: sticky;
        top: 0;
        z-index: 1030;
        background-color: #fff;
        transition: box-shadow 0.2s, padding-bottom 0.2s;
    }
    .header-container.scrolled {
        box-shadow: 0 1px 6px rgba(32,33,36,.28);
        padding-bottom: 15px;
        border-bottom: 1px solid #ebebeb;
    }
    .desktop-left-wrapper {
        display: flex;
        align-items: center;
        flex-grow: 1;
    }
    @media (min-width: 768px) {
        .mobile-left-icons { display: none; }
        .search-icon-left { display: none; }
        .mic-btn, .lens-btn { display: none; }
        .bell-icon { display: none; }
    }
    .logo-container {
        margin-right: 35px;
    }
    .logo-container img {
        height: 35px;
        width: auto;
    }
    .search-box {
        position: relative;
        max-width: 690px;
        width: 100%;
        display: flex;
        align-items: center;
        background: #fff;
        border: 1px solid #dfe1e5;
        box-shadow: 0 1px 6px rgba(32,33,36,.28);
        border-radius: 24px;
        height: 46px;
        padding: 0 14px 0 20px;
    }
    .search-box:hover, .search-box:focus-within {
        box-shadow: 0 1px 6px rgba(32,33,36,.28);
        border-color: rgba(223,225,229,0);
    }
    .search-input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 16px;
        background: transparent;
        color: #202124;
    }
    .search-actions {
        display: flex;
        align-items: center;
    }
    .clear-btn {
        background: none;
        border: none;
        color: #70757a;
        font-size: 16px;
        cursor: pointer;
        padding: 0 12px;
        display: flex;
        align-items: center;
    }
    .divider {
        height: 28px;
        border-left: 1px solid #dfe1e5;
        margin: 0 4px;
    }
    .search-button {
        background: none;
        border: none;
        color: #4285f4;
        font-size: 16px;
        cursor: pointer;
        padding: 0 12px;
        display: flex;
        align-items: center;
    }
    .header-right-icons {
        display: flex;
        align-items: center;
        margin-left: auto;
    }
    .icon-btn {
        color: #5f6368;
        font-size: 18px;
        padding: 8px;
        border-radius: 50%;
        margin-right: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }
    .icon-btn:hover {
        background-color: rgba(60,64,67,0.08);
        color: #5f6368;
    }
    .profile-btn {
        display: flex;
        align-items: center;
        text-decoration: none;
    }
    .profile-btn img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }
    .tabs-container {
        padding-left: 20px;
        display: flex;
        justify-content: flex-start;
        border-bottom: 1px solid #ebebeb;
    }
    .image-results-container {
        padding-left: 20px;
        padding-right: 20px;
        flex: 1;
    }
    /* Teks Navbar kini selaras (20px) dengan Hasil Pencarian di Desktop */
    .nav-tabs {
        border-bottom: none;
    }
    .nav-tabs .nav-link {
        border: none;
        color: #5f6368;
        padding: 10px 12px;
        font-size: 14px;
        margin-right: 20px;
    }
    .nav-tabs .nav-link:hover {
        border: none;
    }
    .nav-tabs .nav-link.active {
        color: #202124;
        border-bottom: 3px solid #202124;
        font-weight: 500;
        background: none;
    }
    
    .results-container {
        padding-left: 20px;
        padding-top: 15px;
        max-width: 800px;
    }
    .result-count {
        color: #70757a;
        font-size: 13px;
        margin-bottom: 10px;
    }
    .site-result {
        margin-bottom: 20px;
    }
    .site-result .title a {
        color: #1a0dab;
        font-size: 18px;
        text-decoration: none;
        font-weight: 400;
        text-transform: capitalize;
    }
    .site-result .title a:hover {
        text-decoration: underline;
    }
    .site-result .url {
        color: #006621;
        font-size: 14px;
        margin-top: 0;
        margin-bottom: 0;
        word-break: break-all;
    }
    .site-result .description {
        color: #545454;
        font-size: 13px;
        line-height: 1.4;
        text-transform: capitalize;
    }
    
    .masonry-grid { width: 100%; }
    .grid-item { width: 200px; margin-bottom: 15px; position: relative; }
    .grid-item img { width: 100%; height: auto; cursor: pointer; display: block; }
    .grid-item .details {
        visibility: hidden;
        position: absolute;
        bottom: 0px;
        left: 0px;
        width: 100%;
        background-color: rgba(0,0,0,0.8);
        color: #fff;
        padding: 4px 6px;
        box-sizing: border-box;
    }
    .grid-item .image-title {
        font-size: 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-transform: capitalize;
    }
    .grid-item .image-domain {
        font-size: 10px;
        color: #ccc;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .grid-item:hover .details {
        visibility: visible;
    }
    
    @media (max-width: 767px) {
        .desktop-left-wrapper { display: contents; }
        .header-container {
            display: grid;
            grid-template-columns: 1fr auto;
            grid-template-rows: auto auto;
            grid-template-areas: 
                "logo right"
                "search search";
            gap: 15px 0;
            padding-left: 15px;
            padding-right: 15px;
            padding-top: 15px;
            padding-bottom: 15px;
            margin-bottom: 10px;
        }
        .header-container.scrolled {
            padding-bottom: 10px;
        }
        .logo-container { grid-area: logo; display: flex; justify-content: center; margin-right: 0; }
        .header-right-icons { grid-area: right; display: flex; justify-content: flex-end; align-items: center; }
        .search-box { grid-area: search; max-width: 100%; margin: 0; box-shadow: 0 1px 6px rgba(32,33,36,.28); border-radius: 24px; padding: 0 14px; }
        .grid-icon { display: none; }
        
        .tabs-container {
            padding-left: 15px;
            padding-right: 15px;
            overflow-x: auto;
            white-space: nowrap;
        }
        .tabs-container::-webkit-scrollbar { display: none; }
        .nav-tabs { flex-wrap: nowrap; border-bottom: 1px solid #ebebeb; }
        .nav-tabs .nav-item { display: inline-block; }
        .image-results-container {
            padding-left: 10px;
            padding-right: 10px;
        }
        .masonry-grid {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
            height: auto !important;
        }
        .grid-item {
            position: static !important;
            width: auto !important;
            margin-bottom: 0 !important;
        }
        .grid-item img {
            border-radius: 12px;
            background: #f8f9fa;
        }
        .grid-item .details {
            position: static !important;
            visibility: visible !important;
            background: transparent !important;
            padding: 8px 0 16px 0 !important;
        }
        .grid-item .image-title {
            color: #3c4043 !important;
            font-size: 13px !important;
            line-height: 1.4 !important;
            white-space: normal !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            margin-bottom: 2px !important;
        }
        .grid-item .image-domain {
            color: #70757a !important;
            font-size: 11px !important;
        }
    }
    
    /* Pagination Styles */
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 40px;
        margin-bottom: 40px;
    }
    .pagination-logo {
        display: flex;
        align-items: flex-start;
    }
    .page-number-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
    }
    .page-number-box img {
        height: 40px;
    }
    .page-number-box span {
        font-size: 14px;
        color: #1a0dab;
        margin-top: 2px;
    }
    .page-number-box.active span {
        color: #000; 
        font-weight: normal;
    }
    .page-number-box:hover span {
        text-decoration: underline;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="header-container" id="header-container">
        <div class="desktop-left-wrapper">
            <a href="<?= base_url() ?>" class="logo-container">
                <img src="<?= base_url('assets/images/Gracia_logo.png') ?>" alt="Gracia Logo">
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

        <div class="header-right-icons d-flex align-items-center gap-3">
            <div class="dropdown grid-icon">
                <a href="#" class="text-dark text-decoration-none d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px; border-radius: 50%; background-color: transparent; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f1f3f4'" onmouseout="this.style.backgroundColor='transparent'">
                    <i class="fas fa-th fs-5" style="color: #5f6368;"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg border-0 rounded-4" style="width: 320px; background-color: #f0f4f9;">
                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <a href="#" class="text-decoration-none text-dark d-block p-2 rounded-3 hover-bg">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Google_Business_Profile_Icon.svg/512px-Google_Business_Profile_Icon.svg.png" style="width:45px; height:45px;" class="mb-1">
                                <div class="small text-truncate">Pengelola...</div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="#" class="text-decoration-none text-dark d-block p-2 rounded-3 hover-bg">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/aa/Google_Maps_icon_%282020%29.svg/512px-Google_Maps_icon_%282020%29.svg.png" style="width:45px; height:45px;" class="mb-1">
                                <div class="small">Maps</div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="#" class="text-decoration-none text-dark d-block p-2 rounded-3 hover-bg">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/512px-Google_%22G%22_logo.svg.png" style="width:45px; height:45px;" class="mb-1">
                                <div class="small">Telusuri</div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="#" class="text-decoration-none text-dark d-block p-2 rounded-3 hover-bg">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Google_Calendar_icon_%282020%29.svg/512px-Google_Calendar_icon_%282020%29.svg.png" style="width:45px; height:45px;" class="mb-1">
                                <div class="small">Kalender</div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="#" class="text-decoration-none text-dark d-block p-2 rounded-3 hover-bg">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/Google_News_icon.svg/512px-Google_News_icon.svg.png" style="width:45px; height:45px;" class="mb-1">
                                <div class="small">Berita</div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="#" class="text-decoration-none text-dark d-block p-2 rounded-3 hover-bg">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9b/Google_Meet_icon_%282020%29.svg/512px-Google_Meet_icon_%282020%29.svg.png" style="width:45px; height:45px;" class="mb-1">
                                <div class="small">Meet</div>
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
            <a href="<?= base_url('profile') ?>" class="profile-btn rounded-circle overflow-hidden d-inline-block text-decoration-none shadow-sm" style="width: 38px; height: 38px; border: 2px solid #ffffff;">
                <img src="<?= $avatarUrl ?>" alt="Avatar" class="w-100 h-100 object-fit-cover">
            </a>
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
                    <div class="grid-item">
                        <a href="<?= esc($image['imageUrl']) ?>" data-fancybox="gallery" 
                           data-caption="<?= esc($image['title'] ?: $image['alt']) ?>"
                           data-siteurl="<?= esc($image['siteUrl']) ?>"
                           onclick="updateImageCount(<?= $image['id'] ?>)">
                            <img src="<?= esc($image['imageUrl']) ?>" alt="<?= esc($image['alt']) ?>" 
                                 onerror="setBroken(this, '<?= esc($image['imageUrl']) ?>')">
                            <div class="details">
                                <div class="image-title"><?= esc($image['title'] ?: $image['alt']) ?></div>
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
    
    <footer class="mt-auto py-3 text-center" style="background-color: #f2f2f2; border-top: 1px solid #e4e4e4; color: #70757a; font-size: 0.9rem; margin-top: 40px !important;">
        Dikembangkan oleh <a href="https://rnd.gkr.my.id" class="text-decoration-none" style="color: #2B3385; font-weight: 500;">RND</a> &copy; 2026 &bull; v1.0.0
    </footer>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/jquery-3.3.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/fancybox/3.3.5/jquery.fancybox.min.js') ?>"></script>
<script src="<?= base_url('assets/js/masonry/4.2.2/masonry.pkgd.min.js') ?>"></script>

<script>
    $(document).ready(function() {
        $("[data-fancybox]").fancybox({
            buttons: ["zoom", "slideShow", "fullScreen", "thumbs", "close"]
        });
    });

    $(window).on("load", function() {
        if ($('.masonry-grid').length) {
            $('.masonry-grid').masonry({
                itemSelector: '.grid-item',
                columnWidth: 200,
                gutter: 15
            });
        }
    });

    function updateLinkCount(id) {
        fetch('<?= base_url('api/updateLinkCount') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + id
        });
    }

    function updateImageCount(id) {
        fetch('<?= base_url('api/updateImageCount') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + id
        });
    }

    function setBroken(imgElement, src) {
        imgElement.style.display = 'none';
        fetch('<?= base_url('api/setBroken') ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'src=' + encodeURIComponent(src)
        }).then(() => {
            if ($('.masonry-grid').length) {
                $('.masonry-grid').masonry('layout');
            }
        });
    }

    $(window).scroll(function() {
        if ($(window).scrollTop() > 10) {
            $('#header-container').addClass('scrolled');
        } else {
            $('#header-container').removeClass('scrolled');
        }
    });
</script>
<?= $this->endSection() ?>
