<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>AI Trainer UI<?= $this->endSection() ?>

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
            <p class="text-muted small mb-4">Mesin sinkronisasi database vektor AI</p>
            
            <a href="<?= base_url('/admin') ?>" class="btn btn-outline-secondary rounded-pill px-4 mb-4" style="font-size: 0.9rem;">Kembali ke Admin</a>
            
            <!-- Form Container -->
            <div class="w-100" style="max-width: 400px;">
                <form @submit.prevent="startCrawl">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">Target Sinkronisasi</label>
                        <input type="text" class="form-control" value="BUYER, GRACIA, SAMPLE GRACIA, SWATCHES, WEB" disabled>
                        <div class="form-text mt-1" style="font-size: 0.75rem;">Mengekstrak ciri visual dan memuat ulang otak AI.</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-bold mb-3" :disabled="isCrawling">
                        <span v-if="isCrawling" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        <i class="fa-solid fa-wand-magic-sparkles me-1" v-if="!isCrawling"></i>
                        {{ isCrawling ? 'Sinkronisasi berjalan...' : 'Mulai Pelatihan AI' }}
                    </button>
                </form>
                
                <button v-if="isCrawling" @click="stopCrawl" class="btn btn-danger rounded-pill w-100 py-2 fw-bold mt-3">Hentikan Tampilan</button>
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
<script>
    window.AppConfig = {
        apiDoCrawl: '<?= base_url('admin/ai/doCrawl') ?>'
    };
</script>
<script src="<?= base_url('js/admin_ai.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>
