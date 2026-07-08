<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Crawler Engine<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Prevent scrolling on the body for a true split-screen feel */
    body {
        overflow: hidden;
        background-color: #f4f6f9;
    }
    
    /* Custom Scrollbar for Terminal */
    .terminal-scroll {
        scrollbar-width: thin;
        scrollbar-color: #555 #1e1e1e;
    }
    .terminal-scroll::-webkit-scrollbar {
        width: 8px;
    }
    .terminal-scroll::-webkit-scrollbar-track {
        background: #1e1e1e; 
    }
    .terminal-scroll::-webkit-scrollbar-thumb {
        background: #555; 
        border-radius: 4px;
    }
    .terminal-scroll::-webkit-scrollbar-thumb:hover {
        background: #777; 
    }
    
    /* Responsive layout constraints */
    .crawl-card {
        height: auto;
    }
    .terminal-wrapper {
        height: 500px;
    }
    @media (min-width: 768px) {
        .crawl-card {
            height: 85vh;
        }
        .terminal-wrapper {
            height: 100%;
        }
    }
</style>
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
    const { createApp, ref, nextTick } = Vue;

    createApp({
        setup() {
            const url = ref('');
            const isCrawling = ref(false);
            const output = ref('doogleBot@server:~# Menunggu perintah...\n');
            const terminalBody = ref(null);
            let abortController = null;

            const scrollToBottom = () => {
                nextTick(() => {
                    if (terminalBody.value) {
                        const { scrollTop, scrollHeight, clientHeight } = terminalBody.value;
                        // Smart scroll: only auto-scroll if user is already near the bottom (within 150px)
                        // If they scrolled up manually, we don't force them back down.
                        const isNearBottom = scrollHeight - scrollTop - clientHeight < 150;
                        if (isNearBottom) {
                            terminalBody.value.scrollTop = terminalBody.value.scrollHeight;
                        }
                    }
                });
            };

            const startCrawl = async () => {
                if (!url.value) return;
                
                isCrawling.value = true;
                output.value = 'doogleBot@server:~# Memulai scan ' + url.value + '...\n<br>';
                abortController = new AbortController();

                try {
                    const formData = new FormData();
                    formData.append('url', url.value);

                    const response = await fetch('<?= base_url('crawler/doCrawl') ?>', {
                        method: 'POST',
                        body: formData,
                        signal: abortController.signal
                    });

                    const reader = response.body.getReader();
                    const decoder = new TextDecoder("utf-8");

                    while (true) {
                        const { done, value } = await reader.read();
                        if (done) break;
                        output.value += decoder.decode(value, {stream: true});
                        scrollToBottom();
                    }
                } catch (err) {
                    if (err.name === 'AbortError') {
                        output.value += '<br><span class="text-warning">Proses dibatalkan oleh pengguna.</span>';
                    } else {
                        output.value += '<br><span class="text-danger">Error: ' + err.message + '</span>';
                    }
                } finally {
                    isCrawling.value = false;
                    output.value += '<br><span style="color: #28a745; font-weight: bold;">[SELESAI]</span> <span style="color: #d4d4d4;">Proses Crawling ditutup</span><br>doogleBot@server:~# Menunggu perintah...\n';
                    scrollToBottom();
                }
            };

            const stopCrawl = () => {
                if (abortController) {
                    abortController.abort();
                }
            };
            
            const resetDb = async () => {
                const result = await Swal.fire({
                    title: 'Reset Database?',
                    text: "Semua data indeks gambar akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                });
                
                if(result.isConfirmed) {
                    output.value += '<br>doogleBot@server:~# Melakukan reset database...<br>';
                    scrollToBottom();
                    
                    try {
                        const res = await fetch('<?= base_url('crawler/resetDb') ?>', { method: 'POST' });
                        const data = await res.json();
                        if (data.status === 'success') {
                            output.value += '<span class="text-success">Database berhasil direset.</span><br>doogleBot@server:~# Menunggu perintah...\n';
                        } else {
                            output.value += '<span class="text-danger">Gagal mereset database.</span><br>doogleBot@server:~# Menunggu perintah...\n';
                        }
                    } catch (e) {
                        output.value += '<span class="text-danger">Error: ' + e.message + '</span><br>doogleBot@server:~# Menunggu perintah...\n';
                    }
                    scrollToBottom();
                }
            };

            return {
                url,
                isCrawling,
                output,
                terminalBody,
                startCrawl,
                stopCrawl,
                resetDb
            }
        }
    }).mount('#crawlApp');
</script>
<?= $this->endSection() ?>
