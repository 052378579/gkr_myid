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
    <div class="dropdown ms-2">
        <a href="#" class="rounded-circle overflow-hidden text-decoration-none shadow-sm d-inline-block" data-bs-toggle="dropdown" aria-expanded="false" style="width: 38px; height: 38px; cursor: pointer; border: 2px solid #ffffff;" title="Menu Akun">
            <img src="<?= $avatarUrl ?>" alt="Avatar" class="w-100 h-100 object-fit-cover">
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
            <li><a class="dropdown-item py-2" href="<?= base_url('profile') ?>"><i class="fas fa-user text-secondary me-2"></i>Profil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item py-2" href="<?= base_url('logout') ?>" style="color: #2B3385;"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></li>
        </ul>
    </div>
</div>

<link rel="stylesheet" href="<?= base_url('css/index.css') ?>">

<div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 100vh;" id="app">
    <div class="text-center mb-4">
        <?php 
            $finalUrlLogo = $urlLogo ?? base_url('Gracia_logo.png');
            $finalAltLogo = $altLogo ?? 'PT. Gracia Kreasi Rotan';
        ?>
        <img src="<?= esc($finalUrlLogo) ?>" alt="<?= esc($finalAltLogo) ?>" title="<?= esc($finalAltLogo) ?>" class="mb-3 doodle-img" onerror="this.onerror=null; this.src='<?= base_url('Gracia_logo.png') ?>';">
        <?php $imgBaseUrl = getenv('app.imgBaseURL') ?: 'https://foto.gkr.my.id/'; ?>
        <div class="d-flex justify-content-center align-items-center gap-2" style="font-size: 0.95rem;">
            <a href="<?= esc($imgBaseUrl) ?>?BUYER" style="color: #2B3385; text-decoration: none;" class="fw-medium">FOTO BUYER</a>
            <span style="color: #2B3385;">|</span>
            <a href="<?= esc($imgBaseUrl) ?>?GRACIA" style="color: #2B3385; text-decoration: none;" class="fw-medium">FOTO GRACIA</a>
            <span style="color: #2B3385;">|</span>
            <a href="https://gamtek.gkr.my.id" style="color: #2B3385; text-decoration: none;" class="fw-medium">GAMTEK</a><sup class="text-danger">New</sup>
        </div>
    </div>

    <div class="w-100" style="max-width: 580px;">
        <form @submit.prevent="search" class="d-flex flex-column align-items-center gap-4 w-100">
            <div class="position-relative w-100">
                <input type="text" v-model="query" class="form-control form-control-lg rounded-pill px-4 border" style="box-shadow: 0 1px 6px rgba(32,33,36,.1) !important; border-color: #dfe1e5 !important; height: 50px; padding-right: 60px !important;" autofocus required>
                <button type="button" class="btn text-secondary position-absolute top-50 end-0 translate-middle-y border-0" style="margin-right: 12px; background: transparent;" data-bs-toggle="modal" data-bs-target="#uploadImageModal" title="Pencarian Gambar">
                    <i class="fa-solid fa-camera fs-5 hover-primary" onmouseover="this.style.color='#2B3385'" onmouseout="this.style.color='inherit'"></i>
                </button>
            </div>
            <div class="google-ai-container">
                <button type="submit" class="btn btn-light rounded-pill text-secondary shadow-sm btn-mode-ai" style="background-color: #f8f9fa; min-width: 120px; font-size: 0.95rem;">
                    Cari
                </button>
            </div>
            <div class="mt-3 d-flex justify-content-center align-items-center">
                <a href="#" data-bs-toggle="modal" data-bs-target="#uploadImageModal" class="text-decoration-none fw-medium" style="color: #2B3385; font-size: 0.95rem;">
                    <i class="fa-solid fa-camera"></i> Pencarian Gambar <sup class="text-danger">New</sup>
                </a>
                <span class="text-muted mx-3">|</span>
                <a href="https://docs.google.com/viewer?url=https://wickerkane.com/WIckerKAne-IFEX-2026.pdf" target="_blank" rel="noopener noreferrer" class="text-decoration-none fw-medium" style="color: #2B3385; font-size: 0.95rem;">
                    <i class="fa-solid fa-book-open"></i> Katalog 2026
                </a>
            </div>
        </form>
    </div>

    <!-- Modal Upload Gambar -->
    <div class="modal fade" id="uploadImageModal" tabindex="-1" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="uploadImageModalLabel" style="color: #2B3385;">Pencarian Gambar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div v-if="!uploadPreviewUrl" class="upload-area p-5 border rounded-4 bg-light mb-3" style="border: 2px dashed #ccc !important; cursor: pointer;" @click="$refs.fileInput.click()" @dragover.prevent @drop.prevent="handleDrop">
                        <i class="fa-solid fa-cloud-arrow-up fs-1 text-secondary mb-3"></i>
                        <p class="mb-0 text-muted">Tarik file gambar ke sini atau klik untuk memilih file</p>
                        <input type="file" class="d-none" ref="fileInput" accept="image/jpeg, image/png, image/webp" @change="handleFileSelect">
                    </div>
                    
                    <div v-else class="preview-area mb-3 position-relative">
                        <img :src="uploadPreviewUrl" alt="Preview" class="img-fluid rounded-3 shadow-sm" style="max-height: 250px; object-fit: contain;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle" @click="clearImage" title="Hapus">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    
                    <div v-if="uploadError" class="alert alert-danger py-2 small">{{ uploadError }}</div>
                    
                    <button v-if="uploadFile" type="button" class="btn rounded-pill w-100 text-white" style="background-color: #2B3385;" @click="uploadAndSearch" :disabled="isUploading">
                        <span v-if="isUploading"><i class="fa-solid fa-spinner fa-spin me-2"></i>Mencari...</span>
                        <span v-else><i class="fa-solid fa-search me-2"></i>Cari Berdasarkan Gambar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="fixed-bottom py-3 w-100" style="background-color: #f2f2f2; border-top: 1px solid #e4e4e4; color: #70757a; font-size: 0.9rem;">
    <div class="text-center w-100">
        <span class="d-none d-sm-inline">Dikembangkan oleh </span><a href="https://rnd.gkr.my.id" class="text-decoration-none" style="color: #2B3385; font-weight: 500;">RND</a> &copy; <?= date('Y') ?>
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
<script src="<?= base_url('js/calendar.js') ?>?v=<?= time() ?>"></script>
<script src="<?= base_url('js/index.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>
