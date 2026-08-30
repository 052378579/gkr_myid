<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GRACIA - Web Crawler Engine</title>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Vue 3 -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    
    <link rel="stylesheet" href="<?= base_url('css/admin_crawl.css') ?>?v=<?= ASSET_VERSION ?>">
</head>
<body>

<div class="container-fluid main-container" id="crawlerApp">
    <div class="row w-100 m-0">
        <!-- Left Side: Controls -->
        <div class="col-md-5 bg-body d-flex flex-column align-items-center justify-content-center position-relative p-4 text-center">
            
            <!-- Fullscreen Button -->
            <button class="btn btn-sm text-primary position-absolute top-0 end-0 m-3" @click="toggleFullscreen" style="z-index: 10;" title="Fullscreen (F11)">
                <i class="fa-solid fa-expand fs-4"></i>
            </button>

            <!-- Area Rata Tengah -->
            <div class="d-flex flex-column align-items-center w-100" style="max-width: 320px;">
                <img src="/Gracia_logo.png" alt="GRACIA Logo" style="max-height: 45px;" class="mb-2">
                
                <p class="text-dark fw-bold text-center mb-0" style="font-size: 0.95rem;">Web Crawler Engine</p>
                <p class="text-muted text-center" style="font-size: 0.75rem; margin-bottom: 15px;">Mengekstrak ciri visual dan memuat ulang otak AI</p>
                
                <a href="/admin" class="btn btn-outline-secondary btn-sm rounded-pill mb-4 px-4" style="font-size: 0.8rem; padding-top: 0.4rem; padding-bottom: 0.4rem;">Kembali ke Admin</a>
                
                <div class="w-100 text-start">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold mb-1" style="font-size: 0.75rem;">Direktori / URL Target</label>
                        <input type="text" v-model="url" class="form-control form-control-sm" placeholder="/var/www/FOTO" :disabled="isProcessing">
                    </div>

                    <button type="button" class="btn btn-success btn-sm rounded-pill w-100 py-2 fw-bold mb-3" @click="startCrawling" :disabled="isProcessing || !url" style="background-color: #198754; border-color: #198754;">
                        <i class="fa-solid fa-bolt me-1"></i> Mulai Crawling
                    </button>
                    
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill w-100 py-2 fw-bold mb-2" @click="hardReset" :disabled="isProcessing" id="btnReset">
                        <i class="fa-solid fa-rotate me-1"></i> Hard Reset Database
                    </button>
                </div>

                <div class="mt-4 text-center">
                    <div class="font-monospace fw-bold text-primary" style="font-size: 2rem; letter-spacing: 2px; line-height: 1;">
                        <span>{{ timeFormatted.min }}</span>:<span>{{ timeFormatted.sec }}</span><sup style="font-size: 1rem; margin-left: 1px;">{{ timeFormatted.ms }}</sup>
                    </div>
                    <div class="text-muted fw-medium mt-1" style="font-size: 0.7rem; letter-spacing: 2px;">{{ timerLabel }}</div>
                </div>
            </div>
        </div>

        <!-- Right Side: Terminal -->
        <div class="col-md-7 bg-dark text-light d-flex flex-column terminal-wrapper" style="background-color: #1e1e1e !important;">
            <div class="p-4 font-monospace small flex-grow-1 terminal-scroll" style="overflow-y: auto; color: #a9b7c6; font-size: 14px; min-height: 0;" ref="terminalBox">
                <div v-for="(line, index) in logs" :key="index" class="terminal-line" v-html="line"></div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('js/admin_crawl.js') ?>?v=<?= ASSET_VERSION ?>"></script>
</body>
</html>

