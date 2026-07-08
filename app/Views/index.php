<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Beranda<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$dateStr = $days[date('w')] . ', ' . date('d/m/Y');
?>

<div class="position-absolute top-0 end-0 p-3 d-flex align-items-center gap-3 z-3">
    <div class="dropdown" id="calendarDropdownWrap">
        <a href="#" id="calendarDropdownToggle" class="text-dark small fw-medium text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" style="cursor: pointer; transition: color 0.2s;" onmouseover="this.style.color='#2B3385'" onmouseout="this.style.color='#212529'">
            <?= $dateStr ?>
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
                </div><!--
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
                </div>-->
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
    <a href="<?= base_url('profile') ?>" class="rounded-circle overflow-hidden text-decoration-none shadow-sm" style="width: 38px; height: 38px; cursor: pointer; display: inline-block; border: 2px solid #ffffff;" title="Profil Karyawan">
        <img src="<?= $avatarUrl ?>" alt="Avatar" class="w-100 h-100 object-fit-cover">
    </a>

    <a href="<?= base_url('logout') ?>" class="text-decoration-none d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 50%; color: #2B3385; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f1f3f4'" onmouseout="this.style.backgroundColor='transparent'" title="Keluar">
        <i class="fas fa-sign-out-alt fs-5" style="color: #2B3385;"></i>
    </a>
</div>

<link rel="stylesheet" href="<?= base_url('css/index.css') ?>">

<div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 100vh;" id="app">
    <div class="text-center mb-4">
        <?php 
            $finalUrlLogo = $urlLogo ?? base_url('assets/images/Gracia_logo.png');
            $finalAltLogo = $altLogo ?? 'Gracia Logo';
        ?>
        <img src="<?= esc($finalUrlLogo) ?>" alt="<?= esc($finalAltLogo) ?>" class="mb-3 doodle-img" onerror="this.onerror=null; this.src='<?= base_url('assets/images/Gracia_logo.png') ?>';">
        <div class="d-flex justify-content-center align-items-center gap-2" style="font-size: 0.95rem;">
            <a href="https://foto.gkr.my.id/?BUYER" style="color: #2B3385; text-decoration: none;" class="fw-medium">FOTO BUYER</a>
            <span style="color: #2B3385;">|</span>
            <a href="https://foto.gkr.my.id/?GRACIA" style="color: #2B3385; text-decoration: none;" class="fw-medium">FOTO GRACIA</a>
        </div>
    </div>

    <div class="w-100" style="max-width: 580px;">
        <form @submit.prevent="search" class="d-flex flex-column align-items-center gap-4">
            <input type="text" v-model="query" class="form-control form-control-lg rounded-pill px-4 border" style="box-shadow: 0 1px 6px rgba(32,33,36,.1) !important; border-color: #dfe1e5 !important; height: 50px;" autofocus required>
            <div class="google-ai-container">
                <button type="submit" class="btn btn-light rounded-pill text-secondary shadow-sm btn-mode-ai" style="background-color: #f8f9fa; min-width: 120px; font-size: 0.95rem;">
                    Cari
                </button>
            </div>
            <div class="mt-2">
                <a href="https://docs.google.com/viewer?url=https://wickerkane.com/WIckerKAne-IFEX-2026.pdf" target="_blank" rel="noopener noreferrer" class="text-decoration-none fw-medium" style="color: #2B3385; font-size: 0.95rem;">
                    <i class="fa-solid fa-book-open"></i> Katalog 2026
                </a>
            </div>
        </form>
    </div>
</div>

<footer class="fixed-bottom py-3 w-100" style="background-color: #f2f2f2; border-top: 1px solid #e4e4e4; color: #70757a; font-size: 0.9rem;">
    <div class="text-center w-100">
        Dikembangkan oleh <a href="https://rnd.gkr.my.id" class="text-decoration-none" style="color: #2B3385; font-weight: 500;">RND</a> &copy; <?= date('Y') ?>
    </div>
    <div class="position-absolute" style="right: 20px; top: 50%; transform: translateY(-50%);">
        <a href="<?= base_url('versi') ?>" class="text-decoration-none text-muted hover-primary" style="transition: color 0.2s;" onmouseover="this.style.color='#2B3385'" onmouseout="this.style.color='inherit'"><?= esc($version) ?></a>
    </div>
</footer>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.AppConfig = {
        searchUrl: '<?= url_to('Search::index') ?>'
    };
</script>
<script src="<?= base_url('js/calendar.js') ?>"></script>
<script src="<?= base_url('js/index.js') ?>"></script>
<?= $this->endSection() ?>
