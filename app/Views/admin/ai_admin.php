<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>AI Trainer<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/admin_ai.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="crawlApp" class="container d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="row g-0 w-100 shadow-lg rounded-4 overflow-hidden crawl-card" style="max-width: 1200px;">
        <!-- Left Side: Controls -->
        <div class="col-md-5 bg-body d-flex flex-column align-items-center justify-content-center p-5">
            <!-- Logo -->
            <img src="<?= base_url('Gracia_logo.png') ?>" alt="Gracia Logo" class="mb-3" style="max-height: 80px;">
            
            <h4 class="fw-bold mb-1">AI Trainer Engine</h4>
            <p class="text-muted small mb-4">Mengekstrak ciri visual dan memuat ulang otak AI</p>
            
            <a href="<?= base_url('/admin') ?>" class="btn btn-outline-secondary rounded-pill px-4 mb-4" style="font-size: 0.9rem;">Kembali ke Admin</a>
            
            <!-- Form Container -->
            <div class="w-100" style="max-width: 400px;">
                <div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">Direktori Gambar</label>
                        <input type="text" class="form-control" value="BUYER, GRACIA, SWATCHES, WEB" disabled>
                        <div class="form-text mt-1" style="font-size: 0.75rem;">Mengekstrak ciri visual dan memuat ulang otak AI.</div>
                    </div>
                    
                    <button type="button" @click="startCrawl('sync')" class="btn btn-success rounded-pill w-100 py-2 fw-bold mb-2" :disabled="isCrawling">
                        <span v-if="isCrawling" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        <i class="fa-solid fa-bolt me-1" v-if="!isCrawling"></i>
                        {{ isCrawling ? 'Sinkronisasi berjalan...' : 'Sinkronisasi Vektor' }}
                    </button>

                    <button type="button" @click="startCrawl('reset')" class="btn btn-outline-danger rounded-pill w-100 py-2 fw-bold mb-3" :disabled="isCrawling">
                        <i class="fa-solid fa-rotate me-1" v-if="!isCrawling"></i>
                        Hard Reset
                    </button>
                </div>
                
                <button v-if="isCrawling" @click="stopCrawl" class="btn btn-danger rounded-pill w-100 py-2 fw-bold mt-3">Hentikan Tampilan</button>

                <!-- Stopwatch UI -->
                <div class="mt-4 text-center" v-if="elapsedTime > 0 || isCrawling" v-cloak>
                    <div class="font-monospace fw-bold text-primary d-flex justify-content-center align-items-baseline" style="font-size: 2.2rem; letter-spacing: 2px;">
                        <span>{{ formattedTime.main }}</span><sup style="font-size: 1.2rem; margin-left: 1px;">{{ formattedTime.ms }}</sup>
                    </div>
                    <div class="text-muted small fw-medium text-uppercase mt-1" style="font-size: 0.75rem; letter-spacing: 3px;">Waktu Sinkronisasi</div>
                </div>
            </div>
        </div>

        <!-- Right Side: Terminal -->
        <div class="col-md-7 bg-dark text-light d-flex flex-column terminal-wrapper" style="background-color: #1e1e1e !important;">
            <div class="p-4 font-monospace small flex-grow-1 terminal-scroll" style="overflow-y: auto; color: #4db8ff;" ref="terminalBody">
                <div v-html="output"></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->section('styles') ?>
<meta name="page-config" data-api-do-crawl="<?= base_url('admin/ai/doCrawl') ?>">
<?= $this->endSection() ?>
<script src="<?= base_url('js/admin_ai.js') ?>?v=<?= ASSET_VERSION ?>"></script>
<?= $this->endSection() ?>
