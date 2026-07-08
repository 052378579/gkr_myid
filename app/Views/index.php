<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Beranda<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$dateStr = $days[date('w')] . ', ' . date('d/m/Y');
?>

<div class="position-absolute top-0 end-0 p-3 d-flex align-items-center gap-3 z-3">
    <span class="text-dark small fw-medium"><?= $dateStr ?></span>
    
    <div class="dropdown">
        <a href="#" class="text-dark text-decoration-none d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px; border-radius: 50%; background-color: #f1f3f4; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#e8eaed'" onmouseout="this.style.backgroundColor='#f1f3f4'">
            <i class="fas fa-th fs-5" style="color: #5f6368;"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg border-0 rounded-4" style="width: 320px; background-color: #f0f4f9;">
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

    <a href="<?= base_url('logout') ?>" class="text-danger text-decoration-none fw-medium d-flex align-items-center">
        <i class="fas fa-sign-out-alt me-1" style="font-size: 32px;"></i>
    </a>
</div>

<style>
    .hover-bg:hover {
        background-color: #e4e7eb;
    }

    /* --- Rainbow Animation for Cari Button --- */
    .google-ai-container {
        position: relative;
        display: inline-block;
    }

    .google-ai-container::before {
        content: '';
        position: absolute;
        top: 2px;
        left: -2px;
        right: 2px;
        bottom: -4px;
        z-index: 1;
        background: linear-gradient(
            90deg, 
            #4285F4 0%, 
            #34A853 30%, 
            #FBBC05 60%, 
            #EA4335 100%
        );
        background-size: 200% auto;
        border-radius: 100px;
        filter: blur(5px);
        opacity: 0;
        transition: opacity 0.4s ease, background-position 0.4s ease;
    }

    .google-ai-container:hover::before {
        opacity: 0.85;
        animation: geserPelangi 2s linear infinite;
    }

    .google-ai-container:hover .btn-mode-ai {
        background-color: #ffffff !important;
    }

    @keyframes geserPelangi {
        0% { background-position: 0% center; }
        100% { background-position: 200% center; }
    }

    .btn-mode-ai {
        position: relative;
        z-index: 2;
        transition: background-color 0.3s ease;
    }
</style>

<div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 100vh;" id="app">
    <div class="text-center mb-4">
        <img src="<?= base_url('assets/images/Gracia_logo.png') ?>" alt="Gracia Logo" style="max-width: 250px; height: auto;" class="mb-3">
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

<footer class="fixed-bottom py-3 text-center w-100" style="background-color: #f2f2f2; border-top: 1px solid #e4e4e4; color: #70757a; font-size: 0.9rem;">
    Dikembangkan oleh <a href="https://rnd.gkr.my.id" class="text-decoration-none" style="color: #2B3385; font-weight: 500;">RND</a> &copy; 2026 &bull; v1.0.0
</footer>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const { createApp, ref } = Vue;

    createApp({
        setup() {
            const query = ref('');

            const search = () => {
                if(query.value.trim() !== '') {
                    window.location.href = '<?= url_to('Search::index') ?>?q=' + encodeURIComponent(query.value);
                }
            };

            return {
                query,
                search
            }
        }
    }).mount('#app');
</script>
<?= $this->endSection() ?>
