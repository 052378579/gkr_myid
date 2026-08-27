<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>AI Trainer<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/admin_ai.css') ?>">
<meta name="page-config" data-api-do-crawl="<?= base_url('admin/ai/doCrawl') ?>" data-api-do-janitor="<?= base_url('admin/ai/doJanitor') ?>">
<style>
    /* Full screen split layout overrides */
    html, body { height: 100vh; overflow: hidden; margin: 0; padding: 0; }
    body { font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important; }
    .main-container { flex-grow: 1; display: flex; overflow: hidden; padding: 0; margin: 0; height: 100vh; }
    .terminal-wrapper { height: 100%; border-left: 5px solid #333; }
    
    .terminal-scroll {
        scrollbar-width: thin;
        scrollbar-color: #555 #1e1e1e;
    }
    .terminal-scroll::-webkit-scrollbar { width: 8px; }
    .terminal-scroll::-webkit-scrollbar-track { background: #1e1e1e; }
    .terminal-scroll::-webkit-scrollbar-thumb { background-color: #555; border-radius: 4px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="crawlApp" class="container-fluid main-container">
    <div class="row w-100 m-0">
        <!-- Left Side: Controls -->
        <div class="col-md-5 bg-body d-flex flex-column align-items-center justify-content-center position-relative p-4 text-center">
            
            <!-- Fullscreen Button -->
            <button class="btn btn-sm text-primary position-absolute top-0 end-0 m-3" onclick="toggleFullscreen()" style="z-index: 10;" title="Fullscreen (F11)">
                <i class="fa-solid fa-expand fs-4"></i>
            </button>

            <!-- Area Rata Tengah -->
            <div class="d-flex flex-column align-items-center w-100" style="max-width: 350px;">
                <!-- Logo -->
                <img src="<?= base_url('Gracia_logo.png') ?>" alt="Gracia Logo" class="mb-2" style="max-height: 45px;">
                
                <h6 class="fw-bold text-dark mt-1 mb-0">AI Trainer Engine</h6>
                <p class="text-muted small mb-4">Mengekstrak ciri visual dan memuat ulang otak AI</p>
                
                <a href="<?= base_url('/admin') ?>" class="btn btn-outline-secondary btn-sm rounded-pill mb-4 px-4 border-2">Kembali ke Admin</a>
                
                <!-- Form Container -->
                <div class="w-100 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">Direktori Gambar</label>
                        <input type="text" v-model="url" class="form-control" placeholder="/var/www/FOTO" required :disabled="isCrawling">
                        <div class="form-text mt-1" style="font-size: 0.75rem;">Mengekstrak ciri visual dan memuat ulang otak AI.</div>
                    </div>
                    
                    <button type="button" @click="startCrawl('sync')" class="btn btn-success rounded-pill w-100 py-2 fw-bold mb-3" :disabled="isCrawling">
                        <span v-if="isCrawling" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        <i class="fa-solid fa-bolt me-1" v-if="!isCrawling"></i>
                        {{ isCrawling ? 'Sinkronisasi berjalan...' : 'Sinkronisasi Vektor' }}
                    </button>

                    <button type="button" @click="startCrawl('reset')" class="btn btn-outline-danger rounded-pill w-100 py-2 fw-bold mb-3" style="border-width: 2px;" :disabled="isCrawling">
                        <i class="fa-solid fa-rotate me-1" v-if="!isCrawling"></i>
                        Hard Reset
                    </button>
                    
                    <button v-if="isCrawling" @click="stopCrawl" class="btn btn-danger rounded-pill w-100 py-2 fw-bold mt-2">
                        Batalkan
                    </button>
                </div>

                <!-- Stopwatch UI -->
                <div class="mt-4 text-center" v-if="elapsedTime > 0 || isCrawling" v-cloak>
                    <div class="font-monospace fw-bold text-primary d-flex justify-content-center align-items-baseline" style="font-size: 2.2rem; letter-spacing: 2px;">
                        <span>{{ formattedTime.main }}</span><sup style="font-size: 1.2rem; margin-left: 1px;">{{ formattedTime.ms }}</sup>
                    </div>
                    <div class="text-muted small fw-medium mt-1" style="font-size: 0.75rem; letter-spacing: 3px;">WAKTU PROSES</div>
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
    
    <!-- Floating Janitor Button -->
    <button @click="startJanitor" class="btn btn-warning shadow-lg position-fixed bottom-0 end-0 m-4 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 12px; z-index: 1050; border: 2px solid #212529; transition: all 0.2s;" title="Jalankan Janitor (Pembersih Sinkronisasi)" :disabled="isCrawling || isJanitorRunning" :class="{'opacity-50': isCrawling || isJanitorRunning}">
        <i class="fa-solid fa-broom fs-4" :class="{'fa-bounce': isJanitorRunning}"></i>
    </button>
</div>

<script>
    // Toggle Fullscreen Function
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.log(`Error attempting to enable fullscreen: ${err.message}`);
            });
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }
</script>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/admin_ai.js') ?>?v=0.8.141"></script>
<?= $this->endSection() ?>
