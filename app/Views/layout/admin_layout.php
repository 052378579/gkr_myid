<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Admin</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="<?= base_url('vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('vendor/fontawesome/css/all.min.css') ?>">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('faviconp.ico') ?>">
    
    <link rel="stylesheet" href="<?= base_url('css/admin_layout.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>?v=<?= time() ?>">
    <meta name="app-config" 
        data-base-url="<?= base_url() ?>" 
        data-csrf-token="<?= csrf_token() ?>">
    
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-body text-body">

<div class="admin-wrapper">
    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Optional brand area inside sidebar if needed, but it's in topbar per design. -->
        <div class="sidebar-header mt-3">MENU UTAMA</div>
        <ul class="sidebar-menu">
            <li>
                <a href="<?= base_url('admin/dashboard') ?>" class="<?= (uri_string() == 'admin/dashboard' || uri_string() == 'admin') ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/cari') ?>" class="<?= (uri_string() == 'admin/cari') ? 'active' : '' ?>">
                    <i class="fas fa-search"></i> Mesin Pencari
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/crawl') ?>" class="<?= (uri_string() == 'admin/crawl' || uri_string() == 'crawl') ? 'active' : '' ?>">
                    <i class="fas fa-spider"></i> Crawler
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/ai') ?>" class="<?= (uri_string() == 'admin/ai') ? 'active' : '' ?>">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> AI Trainer
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/doodle') ?>" class="<?= (uri_string() == 'admin/doodle') ? 'active' : '' ?>">
                    <i class="fas fa-palette"></i> Doodle
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/karyawan') ?>" class="<?= (uri_string() == 'admin/karyawan') ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Karyawan
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/log') ?>" class="<?= (uri_string() == 'admin/log') ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-list"></i> Log
                </a>
            </li>
            <li class="mt-auto">
                <hr class="my-2" style="border-top: 2px solid #2B3385; opacity: 0.3;">
                <a href="<?= base_url('/') ?>">
                    <i class="fas fa-sign-out-alt fa-flip-horizontal"></i> Ke Beranda
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Flex Container -->
    <div class="admin-main-content">
        <!-- Topbar -->
        <header class="admin-topbar shadow-sm position-relative">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-body text-decoration-none border-0 shadow-none p-2 fs-5 me-3" id="sidebarToggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="<?= base_url('/') ?>" class="text-decoration-none d-flex align-items-center gap-2">
                    <img src="<?= base_url('Gracia_logo.png') ?>" alt="GRACIA Logo" style="height: 24px; width: auto;">
                </a>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown" id="calendarDropdownWrap">
                    <a href="#" id="calendarDropdownToggle" class="text-body fw-medium text-decoration-none d-none d-md-block" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" style="font-size: 0.9rem; cursor: pointer; transition: color 0.2s;" onmouseover="this.style.color='var(--gkr-primary, #2B3385)'" onmouseout="this.style.color=''">
                        <?php
                            $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                            echo $hari[date('w')] . ', ' . date('d/m/Y');
                        ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg rounded-4 border-0" style="width: 320px; background: rgba(var(--bs-body-bg-rgb), 0.95); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid var(--bs-border-color) !important;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <button type="button" id="prevMonthBtn" class="btn btn-sm btn-link text-decoration-none text-body p-0 px-2"><i class="fas fa-chevron-left"></i></button>
                            <div class="text-center fw-bold" style="color: #2B3385; font-size: 0.95rem;" id="calendarMonthYearLabel"></div>
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
                
                <?php 
                    $namaLengkap = session()->get('nama_lengkap') ?? 'Admin';
                    $fotoProfil = session()->get('foto_profil');
                    if (!empty($fotoProfil)) {
                        $avatarUrl = base_url('dokumen/karyawan/' . $fotoProfil);
                    } else {
                        $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($namaLengkap) . "&background=2B3385&color=fff";
                    }
                ?>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?= $avatarUrl ?>" alt="User" width="32" height="32" class="rounded-circle border" style="object-fit: cover;">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="fas fa-user me-2 text-muted"></i>Profil</a></li>
                        <?php if (session()->get('id_user') == 1): ?>
                            <li><a class="dropdown-item" href="<?= base_url('admin') ?>"><i class="fas fa-user-shield text-muted me-2"></i>Admin</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></li>
                    </ul>
                </div>
            </div>
            <!-- Progress Bar for Reload -->
            <div id="reloadProgressBar" style="position: absolute; bottom: 0; left: 0; height: 3px; background-color: #2B3385; width: 0%; display: none; transition: width 1s linear;"></div>
        </header>

        <!-- Content Scroll Area -->
        <main class="admin-content-scroll bg-body-tertiary text-body">
            <?= $this->renderSection('content') ?>
            
            <footer class="mt-auto pt-3 border-top text-muted small pb-2 w-100 px-3">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <!-- Rata Kiri (Left): Ikon Tema -->
                    <div class="text-start flex-shrink-0 d-flex align-items-center">
                        <button id="themeToggleBtn" class="btn btn-sm btn-outline-secondary rounded-circle" title="Ubah Tema" style="width: 32px; height: 32px; padding: 0; line-height: 1;">
                            <span id="themeIcon" style="font-size: 0.9rem;">☀️</span>
                        </button>
                    </div>

                    <!-- Rata Tengah (Center): Kredit Pengembang (Utuh 1 Baris) -->
                    <div class="text-center text-muted text-nowrap flex-shrink-0 px-2" style="min-width: 0;">
                        <span class="d-none d-sm-inline">Dikembangkan oleh </span><span style="color: #2B3385;" class="fw-bold">RND</span> &copy; <?= date('Y') ?>
                    </div>

                    <!-- Rata Kanan (Right): Teks Versi -->
                    <div class="text-end flex-shrink-0">
                        <a href="<?= base_url('admin/versi') ?>" class="text-decoration-none text-muted fw-medium" style="transition: color 0.2s;" onmouseover="this.style.color='#2B3385'" onmouseout="this.style.color='inherit'"><?= isset($version) ? esc($version) : 'v0.8.1' ?></a>
                    </div>
                </div>
            </footer>
        </main>
    </div>
</div>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="<?= base_url('vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- Vue.js 3 -->
<script src="<?= base_url('vendor/vue/vue.global.prod.js') ?>"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url('vendor/sweetalert2/sweetalert2.all.min.js') ?>"></script>
<script src="<?= base_url('js/config.js') ?>?v=<?= time() ?>"></script>
<script src="<?= base_url('js/theme.js') ?>?v=<?= time() ?>"></script>
<script src="<?= base_url('js/admin_layout.js') ?>?v=<?= time() ?>"></script>
<script src="<?= base_url('js/calendar.js') ?>?v=<?= time() ?>"></script>

<?= $this->renderSection('scripts') ?>

<!-- Global Toast Notification -->
<?= $this->include('components/toast') ?>
</body>
</html>
