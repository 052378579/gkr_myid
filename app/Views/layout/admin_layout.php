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
    
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>?v=<?= time() ?>">
    
    <?= $this->renderSection('styles') ?>
</head>
<body>

<div class="admin-wrapper">
    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Optional brand area inside sidebar if needed, but it's in topbar per design. -->
        <div class="sidebar-header mt-3">MENU UTAMA</div>
        <ul class="sidebar-menu">
            <li>
                <a href="<?= base_url('admin') ?>" class="<?= (uri_string() == 'admin') ? 'active' : '' ?>">
                    <i class="fas fa-search"></i> Mesin Pencari
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/crawl') ?>" class="<?= (uri_string() == 'admin/crawl' || uri_string() == 'crawl') ? 'active' : '' ?>">
                    <i class="fas fa-spider"></i> Crawler
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/doodle') ?>" class="<?= (uri_string() == 'admin/doodle') ? 'active' : '' ?>">
                    <i class="fas fa-palette"></i> Doodle
                </a>
            </li>
            <li><hr class="dropdown-divider my-2"></li>
            <li>
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
                <button class="btn btn-light border-0 me-3" id="sidebarToggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="<?= base_url('admin') ?>" class="text-decoration-none d-flex align-items-center gap-2">
                    <img src="<?= base_url('Gracia_logo.png') ?>" alt="GRACIA Logo" style="height: 24px; width: auto;">
                </a>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <span class="d-none d-md-block fw-medium" style="font-size: 0.9rem;">
                    <?php
                        $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                        $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                        echo $hari[date('w')] . ', ' . date('d/m/Y');
                    ?>
                </span>
                
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
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></li>
                    </ul>
                </div>
            </div>
            <!-- Progress Bar for Reload -->
            <div id="reloadProgressBar" style="position: absolute; bottom: 0; left: 0; height: 3px; background-color: #2B3385; width: 0%; display: none; transition: width 1s linear;"></div>
        </header>

        <!-- Content Scroll Area -->
        <main class="admin-content-scroll">
            <?= $this->renderSection('content') ?>
            
            <footer class="mt-4 pt-3 border-top text-center text-muted small pb-2">
                Dikembangkan oleh <span style="color: #2B3385;" class="fw-bold">RND</span> &copy; <?= date('Y') ?> &bull; 
                <a href="<?= base_url('admin/versi') ?>" class="text-decoration-none text-muted" style="transition: color 0.2s;" onmouseover="this.style.color='#2B3385'" onmouseout="this.style.color='inherit'"><?= isset($version) ? esc($version) : '0.0.1' ?></a>
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

<script src="<?= base_url('js/admin_layout.js') ?>?v=<?= time() ?>"></script>

<?= $this->renderSection('scripts') ?>

<!-- Global Toast Notification -->
<?= $this->include('components/toast') ?>
</body>
</html>
