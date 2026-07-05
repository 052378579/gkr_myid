<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Crawler Terminal<?= $this->endSection() ?>

<?= $this->section('content') ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark glassmorphism fixed-top shadow-sm" style="background: rgba(33, 37, 41, 0.9) !important;">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= base_url('admin') ?>">Doogle Crawler</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="<?= base_url('/') ?>">Ke Beranda</a>
            <a class="nav-link" href="<?= base_url('admin') ?>">Admin Panel</a>
        </div>
    </div>
</nav>

<div class="container mt-5 pt-5" id="crawlApp">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Mulai Crawling</h5>
                    <form @submit.prevent="startCrawl">
                        <div class="mb-3">
                            <label class="form-label">URL Target</label>
                            <input type="url" v-model="url" class="form-control" placeholder="https://example.com" required :disabled="isCrawling">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill" :disabled="isCrawling">
                            <span v-if="isCrawling" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            {{ isCrawling ? 'Crawling...' : 'Mulai Scan' }}
                        </button>
                    </form>
                    <button v-if="isCrawling" @click="stopCrawl" class="btn btn-danger w-100 rounded-pill mt-3">Batalkan</button>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm rounded-4 border-0 bg-dark text-light" style="min-height: 500px;">
                <div class="card-header bg-dark border-bottom border-secondary d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-terminal me-2"></i>Live Output Terminal</span>
                    <button @click="output = ''" class="btn btn-sm btn-outline-light">Clear</button>
                </div>
                <div class="card-body p-3 font-monospace small" style="overflow-y: auto; height: 500px;" ref="terminalBody">
                    <div v-html="output"></div>
                </div>
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
            const output = ref('Menunggu perintah...\n');
            const terminalBody = ref(null);
            let abortController = null;

            const scrollToBottom = () => {
                nextTick(() => {
                    if (terminalBody.value) {
                        terminalBody.value.scrollTop = terminalBody.value.scrollHeight;
                    }
                });
            };

            const startCrawl = async () => {
                if (!url.value) return;
                
                isCrawling.value = true;
                output.value = '';
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
                    scrollToBottom();
                }
            };

            const stopCrawl = () => {
                if (abortController) {
                    abortController.abort();
                }
            };

            return {
                url,
                isCrawling,
                output,
                terminalBody,
                startCrawl,
                stopCrawl
            }
        }
    }).mount('#crawlApp');
</script>
<?= $this->endSection() ?>
