<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Admin</title>
    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('faviconp.ico') ?>">
    
    <style>
        /* Flexbox Layout Custom CSS */
        body {
            background-color: #f8f9fa;
        }
        .admin-wrapper {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        .admin-sidebar {
            width: 250px;
            background-color: #fff;
            border-right: 1px solid #e9ecef;
            transition: margin-left 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 1040;
        }
        .admin-sidebar.collapsed {
            margin-left: -250px;
        }
        .admin-sidebar .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
        }
        .admin-sidebar .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #495057;
            text-decoration: none;
            transition: background-color 0.2s, color 0.2s;
        }
        .admin-sidebar .sidebar-menu li a:hover,
        .admin-sidebar .sidebar-menu li a.active {
            background-color: #eef1f6;
            color: #2B3385;
            font-weight: 500;
            border-left: 4px solid #2B3385;
        }
        .admin-sidebar .sidebar-menu li a i {
            width: 24px;
            margin-right: 10px;
            text-align: center;
        }
        .admin-sidebar .sidebar-header {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #adb5bd;
            font-weight: bold;
            padding: 20px 20px 10px;
        }
        .admin-main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .admin-topbar {
            height: 60px;
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            flex-shrink: 0;
            z-index: 1030;
        }
        .admin-topbar .navbar-brand img {
            height: 30px;
            width: auto;
        }
        .admin-content-scroll {
            flex-grow: 1;
            overflow-y: auto;
            padding: 20px;
        }
        
        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1035;
        }
        @media (max-width: 767.98px) {
            .admin-sidebar {
                position: absolute;
                height: 100%;
                margin-left: -250px;
            }
            .admin-sidebar.show {
                margin-left: 0;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
    
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
        <header class="admin-topbar shadow-sm">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Vue.js 3 CDN (Versi Produksi) -->
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        if (window.innerWidth < 768) {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        } else {
            sidebar.classList.toggle('collapsed');
        }
    }
</script>

<?= $this->renderSection('scripts') ?>

<!-- Global Toast Notification -->
<?= $this->include('components/toast') ?>
</body>
</html>
