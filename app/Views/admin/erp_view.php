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

<script>
    let koneksiSSE = null;
    let timerInterval = null;
    let startTime = 0;

    function startTimer(label) {
        document.getElementById('sw-label').innerText = label;
        startTime = Date.now();
        clearInterval(timerInterval);
        
        timerInterval = setInterval(() => {
            let elapsedTime = Date.now() - startTime;
            let ms = Math.floor((elapsedTime % 1000) / 10);
            let sec = Math.floor((elapsedTime / 1000) % 60);
            let min = Math.floor((elapsedTime / (1000 * 60)) % 60);
            
            document.getElementById('sw-min').innerText = min.toString().padStart(2, '0');
            document.getElementById('sw-sec').innerText = sec.toString().padStart(2, '0');
            document.getElementById('sw-ms').innerText = ms.toString().padStart(2, '0');
        }, 30);
    }

    function stopTimer() { clearInterval(timerInterval); }
    function resetTimer() {
        clearInterval(timerInterval);
        document.getElementById('sw-min').innerText = '00';
        document.getElementById('sw-sec').innerText = '00';
        document.getElementById('sw-ms').innerText = '00';
    }

    function disableAllButtons(disabled) {
        document.getElementById('btnReset').disabled = disabled;
        document.getElementById('btnEkstrak').disabled = disabled;
        document.getElementById('btnLanjutan').disabled = disabled;
    }

    function jalankanTerminal(mode) {
        const terminalBox = document.getElementById('terminalBox');
        const btnStop = document.getElementById('btnStop');

        terminalBox.innerHTML = '<div class="terminal-line"><span class="prompt">root@server:~#</span> Memulai Crawling Masif (' + mode + ')...</div>';
        
        disableAllButtons(true);
        btnStop.style.display = 'block';
        startTimer('WAKTU CRAWLING');

        if (koneksiSSE) { koneksiSSE.close(); }
        let urlAPI = '/admin/erp/crawl'; // Tidak pakai prefix lagi
        koneksiSSE = new EventSource(urlAPI);

        koneksiSSE.onmessage = function(event) {
            if (event.data === '[EOF]') {
                terminalBox.innerHTML += '<div class="terminal-line text-success"><b>[SYSTEM] PROSES SELESAI.</b></div>';
                koneksiSSE.close(); stopTimer(); disableAllButtons(false); btnStop.style.display = 'none';
            } else {
                terminalBox.innerHTML += '<div class="terminal-line">' + event.data + '</div>';
            }
            terminalBox.scrollTop = terminalBox.scrollHeight;
        };

        koneksiSSE.onerror = function(event) {
            terminalBox.innerHTML += '<div class="terminal-line text-danger">[ERROR] Koneksi streaming terputus.</div>';
            koneksiSSE.close(); stopTimer(); disableAllButtons(false); btnStop.style.display = 'none';
        };
    }

    function jalankanEkstrak(mode) {
        Swal.fire({
            title: 'PERINGATAN',
            heightAuto: false,
            text: "Ini akan mengekstrak detail (dimensi, load, harga) untuk baris yang belum lengkap. Lanjutkan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa-solid fa-bolt"></i> Ya, Ekstrak Data',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const terminalBox = document.getElementById('terminalBox');
                const btnStop = document.getElementById('btnStop');

                terminalBox.innerHTML = '<div class="terminal-line"><span class="prompt">root@server:~#</span> Memulai proses ekstraksi...</div>';
                disableAllButtons(true); btnStop.style.display = 'block';
                startTimer('WAKTU EKSTRAKSI');

                if (koneksiSSE) { koneksiSSE.close(); }
                koneksiSSE = new EventSource('/admin/erp/' + mode);

                koneksiSSE.onmessage = function(event) {
                    if (event.data === '[EOF]') {
                        terminalBox.innerHTML += '<div class="terminal-line text-success"><b>[SYSTEM] PROSES SELESAI.</b></div>';
                        koneksiSSE.close(); stopTimer(); disableAllButtons(false); btnStop.style.display = 'none';
                    } else {
                        terminalBox.innerHTML += '<div class="terminal-line">' + event.data + '</div>';
                    }
                    terminalBox.scrollTop = terminalBox.scrollHeight;
                };

                koneksiSSE.onerror = function(event) {
                    terminalBox.innerHTML += '<div class="terminal-line text-danger">[ERROR] Koneksi streaming terputus.</div>';
                    koneksiSSE.close(); stopTimer(); disableAllButtons(false); btnStop.style.display = 'none';
                };
            }
        });
    }

    function jalankanLanjutan() {
        Swal.fire({
            title: 'SINKRONISASI INKREMENTAL',
            heightAuto: false,
            text: "Sistem hanya akan menarik data yang diperbarui di pabrik sejak sinkronisasi terakhir. Lanjutkan?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa-solid fa-forward-step"></i> Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const terminalBox = document.getElementById('terminalBox');
                const btnStop = document.getElementById('btnStop');

                terminalBox.innerHTML = '<div class="terminal-line"><span class="prompt">root@server:~#</span> Memulai Sinkronisasi Inkremental...</div>';
                disableAllButtons(true); btnStop.style.display = 'block';
                startTimer('WAKTU SINKRONISASI');

                if (koneksiSSE) { koneksiSSE.close(); }
                koneksiSSE = new EventSource('/admin/erp/lanjutan');

                koneksiSSE.onmessage = function(event) {
                    if (event.data === '[EOF]') {
                        terminalBox.innerHTML += '<div class="terminal-line text-success"><b>[SYSTEM] SINKRONISASI SELESAI.</b></div>';
                        koneksiSSE.close(); stopTimer(); disableAllButtons(false); btnStop.style.display = 'none';
                    } else {
                        terminalBox.innerHTML += '<div class="terminal-line">' + event.data + '</div>';
                    }
                    terminalBox.scrollTop = terminalBox.scrollHeight;
                };

                koneksiSSE.onerror = function(event) {
                    terminalBox.innerHTML += '<div class="terminal-line text-danger">[ERROR] Koneksi streaming terputus.</div>';
                    koneksiSSE.close(); stopTimer(); disableAllButtons(false); btnStop.style.display = 'none';
                };
            }
        });
    }

    function hentikanTerminal() {
        if (koneksiSSE) {
            koneksiSSE.close(); stopTimer();
            const terminalBox = document.getElementById('terminalBox');
            terminalBox.innerHTML += '<div class="terminal-line text-warning"><b>[SYSTEM] PROSES DIHENTIKAN PAKSA.</b></div>';
            terminalBox.scrollTop = terminalBox.scrollHeight;
            disableAllButtons(false); document.getElementById('btnStop').style.display = 'none';
        }
    }
    
    function hardReset() {
        Swal.fire({
            title: 'PERINGATAN KRITIS',
            heightAuto: false,
            text: "Seluruh data lokal akan DIHAPUS (TRUNCATE) dan ditarik ulang dari awal. Lanjutkan?",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa-solid fa-rotate"></i> Ya, Reset & Crawl',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                resetTimer();
                const terminalBox = document.getElementById('terminalBox');
                terminalBox.innerHTML = '<div class="terminal-line"><span class="prompt">root@server:~#</span> Mengeksekusi TRUNCATE TABLE gkr_erp...</div>';
                
                fetch('/admin/erp/reset_db', { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if(data.status === 'ok') {
                            terminalBox.innerHTML += '<div class="terminal-line text-success">[OK] Database lokal telah dikosongkan. Memulai Crawl...</div>';
                            jalankanTerminal('crawl');
                        } else {
                            terminalBox.innerHTML += '<div class="terminal-line text-danger">[ERROR] Gagal mereset database.</div>';
                        }
                    }).catch(err => {
                        terminalBox.innerHTML += '<div class="terminal-line text-danger">[ERROR] Kesalahan jaringan.</div>';
                    });
            }
        });
    }

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
</body>
</html>
