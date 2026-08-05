<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Beranda<?= $this->endSection() ?>

<?= $this->section('content') ?>

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
                        <img src="assets/icon/erp.png" style="width:45px; height:45px;" class="mb-1">
                        <div class="small text-truncate">ERP</div>
                    </a>
                </div>
                <div class="col-4">
                    <a href="https://wickerkane.com/" class="text-decoration-none text-body d-block p-2 rounded-3 hover-bg">
                        <img src="assets/icon/wickerkane.png" style="width:45px; height:45px;" class="mb-1">
                        <div class="small">WIckerKAne</div>
                    </a>
                </div>
                <div class="col-4">
                    <a href="https://srv180.niagahoster.com:2096/" class="text-decoration-none text-body d-block p-2 rounded-3 hover-bg">
                        <img src="assets/icon/roundcube.ico" style="width:45px; height:45px;" class="mb-1">
                        <div class="small">WebMail</div>
                    </a>
                </div>
                <div class="col-4">
                    <a href="https://3d.gkr.my.id" class="text-decoration-none text-body d-block p-2 rounded-3 hover-bg">
                        <img src="assets/icon/roundcube.ico" style="width:45px; height:45px;" class="mb-1">
                        <div class="small">3D</div>
                    </a>
                </div><!--
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
            <?php if (session()->get('id_user') == 1): ?>
                <li><a class="dropdown-item py-2" href="<?= base_url('admin') ?>"><i class="fas fa-user-shield text-secondary me-2"></i>Admin</a></li>
            <?php endif; ?>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item py-2" href="<?= base_url('logout') ?>" style="color: var(--gkr-primary);"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></li>
        </ul>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<link rel="stylesheet" href="<?= base_url('css/index.css') ?>?v=<?= time() ?>">

<div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 100vh;" id="app" v-cloak>
    <div class="text-center mb-4">
        <?php 
            $finalUrlLogo = $urlLogo ?? base_url('Gracia_logo.png');
            $finalAltLogo = $altLogo ?? 'PT. Gracia Kreasi Rotan';
        ?>
        <img src="<?= esc($finalUrlLogo) ?>" alt="<?= esc($finalAltLogo) ?>" title="<?= esc($finalAltLogo) ?>" class="mb-3 doodle-img" onerror="this.onerror=null; this.src='<?= base_url('Gracia_logo.png') ?>';">
        <?php $imgBaseUrl = getenv('app.imgBaseURL') ?: 'https://foto.gkr.my.id/'; ?>
        <div class="d-flex justify-content-center align-items-center gap-2" style="font-size: 0.95rem;">
            <a href="<?= esc($imgBaseUrl) ?>?BUYER" style="color: var(--gkr-primary); text-decoration: none;" class="fw-medium">FOTO BUYER</a>
            <span style="color: var(--gkr-primary);">|</span>
            <a href="<?= esc($imgBaseUrl) ?>?GRACIA" style="color: var(--gkr-primary); text-decoration: none;" class="fw-medium">FOTO GRACIA</a>
            <span style="color: var(--gkr-primary);">|</span>
            <a href="https://gamtek.gkr.my.id" style="color: var(--gkr-primary); text-decoration: none;" class="fw-medium">GAMTEK</a>
        </div>
    </div>

    <div class="w-100" style="max-width: 580px;">
        <form action="<?= url_to('Search::index') ?>" method="GET" @submit.prevent="search" class="d-flex flex-column align-items-center gap-4 w-100">
            <div class="position-relative w-100 d-block google-ai-container">
                <input type="text" v-model="query" @input="fetchSuggestions" @keydown.down.prevent="navigateDown" @keydown.up.prevent="navigateUp" @keydown.enter.prevent="selectCurrentSuggestion" @focus="handleFocus" @blur="handleBlur" class="form-control form-control-lg rounded-pill px-4 border input-mode-ai" style="box-shadow: 0 1px 6px rgba(32,33,36,.1) !important; border-color: #dfe1e5 !important; height: 50px; padding-right: 90px !important;" autocomplete="off" autofocus required>
                <div class="position-absolute top-50 end-0 translate-middle-y d-flex align-items-center me-2" style="z-index: 3;">
                    <button type="button" class="btn text-secondary border-0 p-1 btn-voice-search" style="background: transparent;" title="Telusuri dengan suara">
                        <i class="fa-solid fa-microphone fs-5 hover-primary" onmouseover="this.style.color='var(--gkr-primary)'" onmouseout="this.style.color='inherit'"></i>
                    </button>
                    <button type="button" class="btn text-secondary border-0 p-1 me-1 ms-1" style="background: transparent;" data-bs-toggle="modal" data-bs-target="#uploadImageModal" title="Telusuri pakai gambar">
                        <i class="fa-solid fa-camera fs-5 hover-primary" onmouseover="this.style.color='var(--gkr-primary)'" onmouseout="this.style.color='inherit'"></i>
                    </button>
                </div>
                <ul v-if="showSuggestions && suggestions.length > 0" class="autocomplete-dropdown list-unstyled position-absolute w-100 bg-body shadow-sm rounded-4 mt-1 overflow-hidden" style="z-index: 1000; border: 1px solid var(--bs-border-color); text-align: left; transition: all 0.2s ease;">
                    <li v-for="(item, index) in suggestions" :key="index" @mousedown.prevent="selectSuggestion(item)" class="px-4 py-2 d-flex align-items-center autocomplete-item" :class="{'bg-body-tertiary': index === activeIndex}" style="cursor: pointer; transition: background 0.1s ease;">
                        <i class="fas fa-search me-3 text-muted" style="font-size: 0.9rem;"></i>
                        <span class="fw-medium text-body" style="font-size: 0.95rem;">{{ item }}</span>
                    </li>
                </ul>
            </div>
            <div>
                <button type="submit" class="btn bg-body-tertiary rounded-pill text-body shadow-sm px-4 border" style="min-width: 120px; font-size: 0.95rem; height: 42px;">
                    Cari
                </button>
            </div>
            <div class="mt-3 d-flex flex-column flex-md-row justify-content-center align-items-center gap-2 gap-md-0">            
                <div class="d-flex justify-content-center align-items-center">
                    <a href="https://3d.gkr.my.id" target="_blank" rel="noopener noreferrer" class="text-decoration-none fw-medium" style="color: var(--gkr-primary); font-size: 0.95rem;">
                        <i class="fa-brands fa-unity"></i> 3D Viewer
                    </a>
                    <span class="text-muted mx-3">|</span>
                    <a href="https://docs.google.com/viewer?url=https://wickerkane.com/WIckerKAne-IFEX-2026.pdf" target="_blank" rel="noopener noreferrer" class="text-decoration-none fw-medium" style="color: var(--gkr-primary); font-size: 0.95rem;">
                        <i class="fa-solid fa-book-open"></i> Katalog 2026
                    </a>
                </div>
                <span class="text-muted mx-3 d-none d-md-inline">|</span>
                <div class="d-flex justify-content-center align-items-center mt-1 mt-md-0">
                    <a href="https://web.telegram.org/k/#@gracia_searchbot" target="_blank" rel="noopener noreferrer" class="text-decoration-none fw-medium d-inline-flex align-items-center justify-content-start" style="color: var(--gkr-primary); font-size: 0.95rem; width: 195px;">
                        <i class="fa-brands fa-telegram me-1"></i>
                        <span class="typewriter-text">Telegram Chatbot</span>
                        <sup class="text-danger fade-in-new ms-1">New</sup>
                    </a>
                </div>
            </div>
        </form>
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
                    <div v-if="!uploadPreviewUrl">
                        <!-- Tampilan Desktop: Drag & Drop -->
                        <div class="upload-area p-5 border rounded-4 bg-body-tertiary mb-3 d-none d-md-block" style="border: 2px dashed var(--bs-border-color) !important; cursor: pointer;" @click="$refs.fileInput.click()" @dragover.prevent @drop.prevent="handleDrop">
                            <i class="fa-solid fa-cloud-arrow-up fs-1 text-secondary mb-3"></i>
                            <p class="mb-0 text-muted">Tarik file gambar ke sini atau klik untuk memilih file</p>
                        </div>
                        
                        <!-- Tampilan Mobile: 2 Tombol -->
                        <div class="row g-2 mb-3 d-flex d-md-none">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-secondary border-0 bg-body-tertiary w-100 h-100 py-4 rounded-4" style="border: 2px dashed var(--bs-border-color) !important;" @click="$refs.cameraInput.click()">
                                    <i class="fa-solid fa-camera fs-1 mb-2" style="color: var(--gkr-primary);"></i>
                                    <span class="d-block text-muted small fw-medium">Ambil Foto</span>
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-secondary border-0 bg-body-tertiary w-100 h-100 py-4 rounded-4" style="border: 2px dashed var(--bs-border-color) !important;" @click="$refs.fileInput.click()">
                                    <i class="fa-solid fa-images fs-1 mb-2" style="color: var(--gkr-primary);"></i>
                                    <span class="d-block text-muted small fw-medium">Pilih Galeri</span>
                                </button>
                            </div>
                        </div>

                        <!-- Input Files -->
                        <input type="file" class="d-none" ref="fileInput" accept="image/jpeg, image/png, image/webp" @change="handleFileSelect">
                        <input type="file" class="d-none" ref="cameraInput" accept="image/jpeg, image/png, image/webp" capture="environment" @change="handleFileSelect">
                    </div>
                    
                    <div v-else class="preview-area mb-4 position-relative w-100">
                        <div class="border rounded-4 overflow-hidden w-100 bg-dark" style="height: 350px; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
                            <img :src="uploadPreviewUrl" id="vueUploadPreview" alt="Preview" style="display: block; max-width: 100%;">
                        </div>
                        <button type="button" class="btn btn-danger rounded-circle position-absolute d-flex justify-content-center align-items-center shadow" style="top: -15px; right: -15px; width: 32px; height: 32px; padding: 0; z-index: 10; font-size: 0.85rem;" @click="clearImage" title="Hapus">
                            <i class="fa-solid fa-times"></i>
                        </button>
                        <small class="text-muted d-block mt-2"><i class="fa-solid fa-crop-simple me-1"></i> Geser kotak untuk memfokuskan objek pencarian (Opsional)</small>
                    </div>
                    
                    <div v-if="uploadError" class="alert alert-danger py-2 small">{{ uploadError }}</div>
                    
                    <button v-if="uploadFile" type="button" class="btn rounded-pill w-100" style="background-color: #2B3385; color: #ffffff !important;" @click="uploadAndSearch" :disabled="isUploading">
                        <span v-if="isUploading" style="color: #ffffff !important;"><i class="fa-solid fa-spinner fa-spin me-2" style="color: #ffffff !important;"></i>Mencari...</span>
                        <span v-else style="color: #ffffff !important;"><i class="fa-solid fa-search me-2" style="color: #ffffff !important;"></i>Cari Berdasarkan Gambar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="fixed-bottom py-3 w-100 bg-body-tertiary border-top" style="font-size: 0.9rem;">
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
            <a href="<?= base_url('versi') ?>" class="text-decoration-none text-muted hover-primary" style="transition: color 0.2s;" onmouseover="this.style.color='var(--gkr-primary)'" onmouseout="this.style.color='inherit'"><?= esc($version) ?></a>
        </div>
        
    </div>
</footer>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<?= $this->section('styles') ?>
<meta name="page-config" data-search-url="<?= url_to('Search::index') ?>">
<?= $this->endSection() ?>
<script src="<?= base_url('js/calendar.js') ?>?v=<?= time() ?>"></script>
<script src="<?= base_url('js/voice_search.js') ?>?v=<?= time() ?>"></script>
<script src="<?= base_url('js/index.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>
