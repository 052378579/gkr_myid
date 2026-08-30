<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP - Engine</title>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link rel="stylesheet" href="<?= base_url('css/admin_erp.css') ?>?v=<?= ASSET_VERSION ?>">
</head>
<body>

<div class="container-fluid main-container">
    <div class="row w-100 m-0">
        <!-- Left Side: Controls -->
        <div class="col-md-5 bg-body d-flex flex-column align-items-center justify-content-center position-relative p-4 text-center">
            
            <!-- Fullscreen Button -->
            <button class="btn btn-sm text-primary position-absolute top-0 end-0 m-3" onclick="toggleFullscreen()" style="z-index: 10;" title="Fullscreen (F11)">
                <i class="fa-solid fa-expand fs-4"></i>
            </button>

            <!-- Area Rata Tengah -->
            <div class="d-flex flex-column align-items-center w-100" style="max-width: 320px;">
                <img src="/Gracia_logo.png" alt="GRACIA Logo" style="max-height: 45px;" class="mb-2">
                
                <h5 class="fw-bold mb-0">ERP Data Engine</h5>
                <p class="text-muted" style="font-size: 0.8rem; margin-bottom: 15px;">Arsitektur Sinkronisasi 3 Tombol</p>
                
                <a href="/admin" class="btn btn-outline-secondary btn-sm rounded-pill mb-4 px-4">Kembali ke Admin</a>
                
                <div class="w-100 text-start">
                    <button id="btnReset" type="button" class="btn btn-outline-danger btn-sm rounded-pill w-100 py-2 fw-bold mb-3" onclick="hardReset()">
                        <i class="fa-solid fa-rotate me-1"></i> 1. HARD RESET & CRAWL
                    </button>
                    
                    <button id="btnEkstrak" type="button" class="btn btn-success btn-sm rounded-pill w-100 py-2 fw-bold mb-3" onclick="jalankanEkstrak('ekstrak')">
                        <i class="fa-solid fa-bolt me-1"></i> 2. EKSTRAK (LENGKAPI)
                    </button>
                    
                    <button id="btnLanjutan" type="button" class="btn btn-warning btn-sm rounded-pill w-100 py-2 fw-bold mb-2 text-dark" onclick="jalankanLanjutan()">
                        <i class="fa-solid fa-forward-step me-1"></i> 3. LANJUTKAN (UPDATE)
                    </button>
                    
                    <button id="btnStop" type="button" class="btn btn-danger btn-sm rounded-pill w-100 py-2 fw-bold mt-2" onclick="hentikanTerminal()" style="display:none;">
                        Batalkan
                    </button>
                </div>

                <div class="mt-4 text-center">
                    <div class="font-monospace fw-bold text-primary" style="font-size: 2rem; letter-spacing: 2px; line-height: 1;">
                        <span id="sw-min">00</span>:<span id="sw-sec">00</span><sup style="font-size: 1rem; margin-left: 1px;" id="sw-ms">00</sup>
                    </div>
                    <div class="text-muted fw-medium mt-1" style="font-size: 0.7rem; letter-spacing: 2px;" id="sw-label">WAKTU PROSES</div>
                </div>
            </div>
        </div>

        <!-- Right Side: Terminal -->
        <div class="col-md-7 bg-dark text-light d-flex flex-column terminal-wrapper" style="background-color: #1e1e1e !important;">
            <div class="p-4 font-monospace small flex-grow-1 terminal-scroll" style="overflow-y: auto; color: #a9b7c6; font-size: 14px; min-height: 0;" id="terminalBox">
                <div class="terminal-line"><span class="prompt">root@server:~#</span> Menunggu instruksi (3 Tombol Sinkronisasi Inkremental)...</div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('js/admin_erp.js') ?>?v=<?= ASSET_VERSION ?>"></script>
</body>
</html>

