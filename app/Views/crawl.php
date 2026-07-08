<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Crawler Engine<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/crawl.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="crawlApp" class="container d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="row g-0 w-100 shadow-lg rounded-4 overflow-hidden crawl-card" style="max-width: 1200px;">
        <!-- Left Side: Controls -->
        <div class="col-md-5 bg-white d-flex flex-column align-items-center justify-content-center p-5">
            <!-- Logo -->
            <img src="<?= base_url('assets/images/Gracia_logo.png') ?>" alt="Doogle Logo" class="mb-3" style="max-height: 80px;">
            
            <h4 class="fw-bold mb-1">Crawler Engine</h4>
            <p class="text-muted small mb-4">Mesin crawling situs dan direktori lokal</p>
            
            <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary rounded-pill px-4 mb-4" style="font-size: 0.9rem;">Kembali ke Beranda</a>
            
            <!-- Form Container -->
            <div class="w-100" style="max-width: 400px;">
                <form @submit.prevent="startCrawl">
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 0.85rem; color: #555;">Lokasi Direktori Lokal</label>
                        <input type="text" v-model="url" class="form-control" placeholder="/var/www/FOTO" required :disabled="isCrawling" style="background-color: #f8f9fa;">
                        <div class="form-text mt-1" style="font-size: 0.75rem;">Mengindeks Direktori Foto Produk</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-bold mb-3" :disabled="isCrawling">
                        <span v-if="isCrawling" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        {{ isCrawling ? 'Crawling...' : 'Mulai' }}
                    </button>
                    
                    <button type="button" @click="resetDb" class="btn btn-outline-danger rounded-pill w-100 py-2 fw-bold" :disabled="isCrawling">
                        <i class="fas fa-trash-alt me-1"></i> Reset DB
                    </button>
                </form>
                
                <button v-if="isCrawling" @click="stopCrawl" class="btn btn-danger rounded-pill w-100 py-2 fw-bold mt-3">Batalkan</button>
            </div>
        </div>

        <!-- Right Side: Terminal -->
        <div class="col-md-7 bg-dark text-light d-flex flex-column terminal-wrapper" style="background-color: #1e1e1e !important;">
            <div class="p-4 font-monospace small flex-grow-1 terminal-scroll" style="overflow-y: auto; color: #a9a9a9;" ref="terminalBody">
                <div v-html="output"></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.AppConfig = {
        apiDoCrawl: '<?= base_url('crawler/doCrawl') ?>',
        apiResetDb: '<?= base_url('crawler/resetDb') ?>'
    };
</script>
<script src="<?= base_url('js/crawl.js') ?>"></script>
<?= $this->endSection() ?>
