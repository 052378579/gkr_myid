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
    
    <style>
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }
        .main-container { flex-grow: 1; display: flex; overflow: hidden; padding: 0; margin: 0; }
        .terminal-wrapper { height: 100%; border-left: 5px solid #333; }
        
        .terminal-scroll {
            scrollbar-width: thin;
            scrollbar-color: #555 #1e1e1e;
        }
        .terminal-scroll::-webkit-scrollbar { width: 8px; }
        .terminal-scroll::-webkit-scrollbar-track { background: #1e1e1e; }
        .terminal-scroll::-webkit-scrollbar-thumb { background-color: #555; border-radius: 4px; }
        
        .terminal-line { margin-bottom: 3px; font-family: 'Consolas', 'Courier New', monospace; line-height: 1.4; word-wrap: break-word; }
        .prompt { color: #00ff00; font-weight: bold; }
        
        #btnReset { border-width: 2px !important; }
    </style>
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

<script>
    const { createApp, ref, computed, nextTick } = Vue;

    createApp({
        setup() {
            const url = ref('/var/www/FOTO');
            const isProcessing = ref(false);
            const logs = ref(['<span class="prompt">root@server:~#</span> Menunggu instruksi...']);
            const terminalBox = ref(null);
            
            const timerLabel = ref('WAKTU PROSES');
            const startTime = ref(0);
            const elapsedTime = ref(0);
            let timerInterval = null;

            const timeFormatted = computed(() => {
                let ms = Math.floor((elapsedTime.value % 1000) / 10);
                let sec = Math.floor((elapsedTime.value / 1000) % 60);
                let min = Math.floor((elapsedTime.value / (1000 * 60)) % 60);
                return {
                    min: min.toString().padStart(2, '0'),
                    sec: sec.toString().padStart(2, '0'),
                    ms: ms.toString().padStart(2, '0')
                };
            });

            const scrollToBottom = () => {
                nextTick(() => {
                    if (terminalBox.value) {
                        terminalBox.value.scrollTop = terminalBox.value.scrollHeight;
                    }
                });
            };

            const addLog = (msg) => {
                logs.value.push(msg);
                scrollToBottom();
            };

            const startTimer = (label) => {
                timerLabel.value = label;
                startTime.value = Date.now();
                clearInterval(timerInterval);
                timerInterval = setInterval(() => {
                    elapsedTime.value = Date.now() - startTime.value;
                }, 30);
            };

            const stopTimer = () => {
                clearInterval(timerInterval);
            };

            const resetTimer = () => {
                clearInterval(timerInterval);
                elapsedTime.value = 0;
            };

            const toggleFullscreen = () => {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.log(`Error attempting to enable fullscreen: ${err.message}`);
                    });
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                }
            };

            const hardReset = () => {
                Swal.fire({
                    title: 'PERINGATAN KRITIS',
                    heightAuto: false,
                    text: "Seluruh data indeks crawler akan DIHAPUS. Lanjutkan?",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa-solid fa-rotate"></i> Ya, Reset Database',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeReset();
                    }
                });
            };

            const executeReset = async () => {
                isProcessing.value = true;
                resetTimer();
                logs.value = ['<span class="prompt">root@server:~#</span> Mengeksekusi TRUNCATE TABLE gkr_cari...'];
                
                try {
                    const response = await fetch('/crawler/resetDb', {
                        method: 'POST'
                    });
                    const data = await response.json();
                    
                    if(data.status === 'sukses' || data.status === 'ok') {
                        addLog('<span class="text-success">[OK] Database lokal telah dikosongkan.</span>');
                        Swal.fire({
                            title: 'Berhasil', 
                            text: 'Database berhasil di-reset.', 
                            icon: 'success',
                            heightAuto: false
                        });
                    } else {
                        addLog('<span class="text-danger">[ERROR] Gagal mereset database.</span>');
                        Swal.fire({
                            title: 'Gagal', 
                            text: data.pesan || 'Gagal mereset database.', 
                            icon: 'error',
                            heightAuto: false
                        });
                    }
                } catch (err) {
                    addLog('<span class="text-danger">[ERROR] Kesalahan jaringan.</span>');
                    Swal.fire({
                        title: 'Error', 
                        text: 'Kesalahan jaringan', 
                        icon: 'error',
                        heightAuto: false
                    });
                } finally {
                    isProcessing.value = false;
                }
            };

            const startCrawling = async () => {
                if (!url.value) return;
                
                isProcessing.value = true;
                logs.value = [`<span class="prompt">root@server:~#</span> Memulai Crawling: ${url.value}...`];
                startTimer('WAKTU CRAWLING');
                
                try {
                    const formData = new FormData();
                    formData.append('url', url.value);
                    
                    const response = await fetch('/crawler/doCrawl', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.body) {
                        throw new Error('ReadableStream tidak didukung oleh browser Anda.');
                    }

                    const reader = response.body.getReader();
                    const decoder = new TextDecoder('utf-8');

                    while (true) {
                        const { done, value } = await reader.read();
                        if (done) break;
                        
                        const chunk = decoder.decode(value, { stream: true });
                        const lines = chunk.split('\n');
                        lines.forEach(line => {
                            if (line.trim()) {
                                addLog(line);
                            }
                        });
                    }
                    
                    addLog('<span class="text-success fw-bold">[SYSTEM] PROSES SELESAI.</span>');
                    Swal.fire({
                        title: 'Berhasil', 
                        text: 'Proses Crawling selesai.', 
                        icon: 'success',
                        heightAuto: false
                    });
                } catch (err) {
                    addLog(`<span class="text-danger">[ERROR] ${err.message || 'Koneksi terputus.'}</span>`);
                } finally {
                    stopTimer();
                    isProcessing.value = false;
                }
            };

            return {
                url,
                isProcessing,
                logs,
                terminalBox,
                timerLabel,
                timeFormatted,
                toggleFullscreen,
                hardReset,
                startCrawling
            };
        }
    }).mount('#crawlerApp');
</script>
</body>
</html>
