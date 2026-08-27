<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Search ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= base_url('css/erp_search.css') ?>?v=<?= ASSET_VERSION ?>">
</head>
<body>

    <?php
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $dateStr = $days[date('w')] . ', ' . date('d/m/Y');
    ?>
    <div class="position-absolute top-0 end-0 p-3 d-flex align-items-center gap-3" style="z-index: 1050 !important;">
        <div class="dropdown" id="calendarDropdownWrap">
            <a href="#" id="calendarDropdownToggle" class="text-body small fw-medium text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" style="cursor: pointer; transition: color 0.2s;" onmouseover="this.style.color='var(--gkr-primary)'" onmouseout="this.style.color=''">
                <?= $dateStr ?>
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
                            <img src="<?= base_url('assets/icon/erp.png') ?>" style="width:45px; height:45px;" class="mb-1" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=E&background=random';">
                            <div class="small text-truncate">ERP</div>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="https://wickerkane.com/" class="text-decoration-none text-body d-block p-2 rounded-3 hover-bg">
                            <img src="<?= base_url('assets/icon/wickerkane.png') ?>" style="width:45px; height:45px;" class="mb-1" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=W&background=random';">
                            <div class="small">WIckerKAne</div>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="https://srv180.niagahoster.com:2096/" class="text-decoration-none text-body d-block p-2 rounded-3 hover-bg">
                            <img src="<?= base_url('assets/icon/roundcube.ico') ?>" style="width:45px; height:45px;" class="mb-1" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=WM&background=random';">
                            <div class="small">WebMail</div>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="https://3d.gkr.my.id" class="text-decoration-none text-body d-block p-2 rounded-3 hover-bg">
                            <img src="<?= base_url('assets/icon/roundcube.ico') ?>" style="width:45px; height:45px;" class="mb-1" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=3D&background=random';">
                            <div class="small">3D</div>
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
        <div class="dropdown ms-2">
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

    <!-- Main Content -->
    <div class="main-container">
        
        <?php 
        $finalUrlLogo = $urlLogo ?? base_url('Gracia_logo.png');
        $finalAltLogo = $altLogo ?? 'GRACIA';
        ?>
        <a href="<?= base_url('/') ?>">
            <img src="<?= esc($finalUrlLogo) ?>" alt="<?= esc($finalAltLogo) ?>" title="<?= esc($finalAltLogo) ?>" style="width: 250px; height: auto;" class="mb-2" onerror="this.onerror=null; this.src='<?= base_url('Gracia_logo.png') ?>';">
        </a>
        
        <div class="subtitle slider-container">
            <div class="slider-words">
                <div class="slider-words-inner">
                    <span>Live Search</span>
                    <span>Pencarian Langsung</span>
                    <span>Live Search</span>
                </div>
            </div>
            <strong>ERP (<span id="total-count">0</span>)</strong>
        </div>

        <div class="search-box">
            <input type="text" id="searchInput" class="search-input" placeholder="Cari: Kode, Nama Barang, Finishing, atau Buyer" autocomplete="off">
        </div>
        
        <div class="loader" id="loader">
            <i class="fas fa-spinner fa-spin"></i> Mencari...
        </div>

        <div class="data-wrapper position-relative w-100 px-5">
            <button class="nav-btn position-absolute top-50 start-0 translate-middle-y ms-2" id="btnPrev" onclick="changePage(-1)" disabled><i class="fas fa-chevron-left"></i></button>
            
            <table class="data-list mx-auto" id="dataTable" style="table-layout: fixed; width: 100%;">
                <thead>
                    <tr style="border-bottom: 2px solid #eee;">
                        <th class="col-kode" style="width: 180px;">Kode BOM</th>
                        <th class="col-nama" style="width: auto;">Nama Barang</th>
                        <th class="col-dimensi d-none d-md-table-cell" style="width: 220px;">Dimensi</th>
                        <th class="col-buyer d-none d-md-table-cell" style="width: 170px;">Buyer</th>
                    </tr>
                </thead>
                <tbody id="resultBody">
                    <!-- Data akan dirender di sini via AJAX -->
                </tbody>
            </table>
            
            <button class="nav-btn position-absolute top-50 end-0 translate-middle-y me-2" id="btnNext" onclick="changePage(1)" disabled><i class="fas fa-chevron-right"></i></button>
        </div>

    </div>

    <!-- Footer -->
    <footer class="mt-auto py-3 w-100 bg-body-tertiary border-top" style="font-size: 0.9rem;">
        <div class="d-flex justify-content-between align-items-center px-4">
            
            <!-- BAGIAN KIRI: Ikon Mode Gelap/Terang -->
            <div class="flex-grow-1 text-start">
                <button id="themeToggleBtn" class="btn btn-sm btn-outline-secondary border-0 text-muted hover-primary" title="Ubah Mode Tema" style="transition: color 0.2s;" onmouseover="this.style.color='var(--gkr-primary)'" onmouseout="this.style.color='inherit'">
                    <span id="themeIcon">🌓 Tema</span>
                </button>
            </div>

            <!-- BAGIAN TENGAH: Copyright -->
            <div class="text-center">
                <span class="d-none d-sm-inline">Dikembangkan oleh </span><a href="https://rnd.gkr.my.id" class="text-decoration-none" style="color: var(--gkr-primary); font-weight: 500;">RND</a> &copy; <?= date('Y') ?>
            </div>

            <!-- BAGIAN KANAN: Versi -->
            <div class="flex-grow-1 text-end">
                <a href="<?= base_url('versi') ?>" class="text-decoration-none text-muted hover-primary" style="transition: color 0.2s;" onmouseover="this.style.color='var(--gkr-primary)'" onmouseout="this.style.color='inherit'"><?= esc($version ?? 'v0.8.141') ?></a>
            </div>
            
        </div>
    </footer>

    <script src="<?= base_url('js/erp_search.js') ?>?v=<?= ASSET_VERSION ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('js/theme.js') ?>?v=<?= time() ?>"></script>
    <script src="<?= base_url('js/calendar.js') ?>?v=<?= time() ?>"></script>
</body>
</html>
