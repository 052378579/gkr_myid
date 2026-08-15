<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>GRACIA - ERP Engine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .prompt { color: #85c46c; }
        .terminal-line { margin-bottom: 4px; border-bottom: 1px solid #333; padding-bottom: 2px; }
        .terminal-scroll { scrollbar-width: thin; scrollbar-color: #495057 #1e1e1e; }
        .terminal-scroll::-webkit-scrollbar { width: 8px; }
        .terminal-scroll::-webkit-scrollbar-track { background: #1e1e1e; }
        .terminal-scroll::-webkit-scrollbar-thumb { background-color: #495057; border-radius: 4px; }
    </style>
</head>
<body>

<div class="container d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="row g-0 w-100 shadow-lg rounded-4 overflow-hidden" style="max-width: 1200px; height: 75vh;">
        <!-- Left Side: Controls -->
        <div class="col-md-5 bg-body d-flex flex-column align-items-center justify-content-center p-5 text-center">
            <!-- Logo -->
            <img src="/Gracia_logo.png" alt="GRACIA Logo" style="max-height: 55px;" class="mb-1">
            
            <h4 class="mt-2 fw-bold mb-1">ERP Data Engine</h4>
            <p class="text-muted small mb-4">Mesin Crawling & Ekstraksi Data ERPNext</p>
            
            <a href="/admin" class="btn btn-outline-secondary btn-sm rounded-pill mb-4 px-4">Kembali ke Admin</a>
            
            <!-- Form Container -->
            <div class="w-100 text-start" style="max-width: 400px;">
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">Masukan Kode BOM</label>
                    <input type="text" class="form-control" id="inputPrefix" value="FG-">
                    <div class="form-text mt-1" style="font-size: 0.75rem;">Misalnya: FG- atau FG-1</div>
                </div>

                <button id="btnCrawl" type="button" class="btn btn-primary rounded-pill w-100 py-2 fw-bold mb-2" onclick="jalankanTerminal('crawl')">
                    <i class="fa-solid fa-cloud-arrow-down me-1"></i> Mulai Crawl (Tarik Data)
                </button>
                
                <button id="btnReset" type="button" class="btn btn-outline-danger rounded-pill w-100 py-2 fw-bold mb-3" onclick="hardReset()">
                    <i class="fa-solid fa-rotate me-1"></i> HARD RESET & CRAWL ULANG
                </button>
                
                <button id="btnStop" type="button" class="btn btn-danger rounded-pill w-100 py-2 fw-bold mt-3" onclick="hentikanTerminal()" style="display:none;">
                    Batalkan
                </button>

                <!-- Stopwatch UI -->
                <div class="mt-4 text-center">
                    <div class="font-monospace fw-bold text-primary d-flex justify-content-center align-items-baseline" style="font-size: 2.2rem; letter-spacing: 2px;">
                        <span id="sw-min">00</span>:<span id="sw-sec">00</span><sup style="font-size: 1.2rem; margin-left: 1px;" id="sw-ms">00</sup>
                    </div>
                    <div class="text-muted small fw-medium text-uppercase mt-1" style="font-size: 0.75rem; letter-spacing: 3px;" id="sw-label">WAKTU PROSES</div>
                </div>
            </div>
        </div>

        <!-- Right Side: Terminal -->
        <div class="col-md-7 bg-dark text-light d-flex flex-column terminal-wrapper" style="background-color: #1e1e1e !important;">
            <div class="p-4 font-monospace small flex-grow-1 terminal-scroll" style="overflow-y: auto; color: #a9b7c6; font-size: 14px;" id="terminalBox">
                <div class="terminal-line"><span class="prompt">root@server:~#</span> Menunggu instruksi...</div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button Ekstraksi -->
<button class="btn btn-warning shadow-lg position-fixed bottom-0 end-0 m-4 d-flex align-items-center justify-content-center" id="btnEkstrak" title="Mulai Ekstrak (Belah Kolom)" onclick="jalankanTerminal('ekstrak')" style="width: 60px; height: 60px; border-radius: 12px; z-index: 1050; border: 2px solid #212529; transition: all 0.2s;">
    <i class="fa-solid fa-table fs-4"></i>
</button>

<script>
    let koneksiSSE = null;
    
    // Timer Variables
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

    function stopTimer() {
        clearInterval(timerInterval);
    }

    function resetTimer() {
        clearInterval(timerInterval);
        document.getElementById('sw-min').innerText = '00';
        document.getElementById('sw-sec').innerText = '00';
        document.getElementById('sw-ms').innerText = '00';
    }

    function jalankanTerminal(mode) {
        const terminalBox = document.getElementById('terminalBox');
        const btnCrawl = document.getElementById('btnCrawl');
        const btnEkstrak = document.getElementById('btnEkstrak');
        const btnReset = document.getElementById('btnReset');
        const btnStop = document.getElementById('btnStop');
        const inputPrefix = document.getElementById('inputPrefix').value;

        terminalBox.innerHTML = '<div class="terminal-line"><span class="prompt">root@server:~#</span> Memulai koneksi Python 3.11.2 (' + mode + ')...</div>';
        
        btnCrawl.disabled = true;
        btnEkstrak.disabled = true;
        btnReset.disabled = true;
        btnStop.style.display = 'block';

        if (mode === 'crawl') {
            startTimer('WAKTU CRAWLING');
        } else {
            startTimer('WAKTU EKSTRAKSI');
        }

        if (koneksiSSE) { koneksiSSE.close(); }

        let urlAPI = '';
        if (mode === 'crawl') {
            urlAPI = '/admin/erp/crawl?prefix=' + encodeURIComponent(inputPrefix);
        } else {
            urlAPI = '/admin/erp/ekstrak';
        }

        koneksiSSE = new EventSource(urlAPI);

        koneksiSSE.onmessage = function(event) {
            if (event.data === '[EOF]') {
                terminalBox.innerHTML += '<div class="terminal-line text-success"><b>[SYSTEM] PROSES SELESAI.</b></div>';
                koneksiSSE.close();
                stopTimer();
                btnCrawl.disabled = false;
                btnEkstrak.disabled = false;
                btnReset.disabled = false;
                btnStop.style.display = 'none';
            } else {
                terminalBox.innerHTML += '<div class="terminal-line">' + event.data + '</div>';
            }
            terminalBox.scrollTop = terminalBox.scrollHeight;
        };

        koneksiSSE.onerror = function(event) {
            terminalBox.innerHTML += '<div class="terminal-line text-danger">[ERROR] Koneksi streaming terputus.</div>';
            koneksiSSE.close();
            stopTimer();
            btnCrawl.disabled = false;
            btnEkstrak.disabled = false;
            btnReset.disabled = false;
            btnStop.style.display = 'none';
        };
    }

    function hentikanTerminal() {
        if (koneksiSSE) {
            koneksiSSE.close();
            stopTimer();
            const terminalBox = document.getElementById('terminalBox');
            terminalBox.innerHTML += '<div class="terminal-line text-warning"><b>[SYSTEM] PROSES DIHENTIKAN PAKSA OLEH PENGGUNA.</b></div>';
            terminalBox.scrollTop = terminalBox.scrollHeight;
            
            document.getElementById('btnCrawl').disabled = false;
            document.getElementById('btnEkstrak').disabled = false;
            document.getElementById('btnReset').disabled = false;
            document.getElementById('btnStop').style.display = 'none';
        }
    }
    
    function hardReset() {
        if(confirm("PERINGATAN: Ini akan menghapus SELURUH data ERP yang ada di database lokal Anda. Apakah Anda yakin ingin melanjutkan dan memulai Crawl dari awal?")) {
            resetTimer();
            const terminalBox = document.getElementById('terminalBox');
            terminalBox.innerHTML = '<div class="terminal-line"><span class="prompt">root@server:~#</span> Mengeksekusi TRUNCATE TABLE gkr_erp...</div>';
            
            fetch('/admin/erp/reset_db', { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'ok') {
                        terminalBox.innerHTML += '<div class="terminal-line text-success">[OK] Database lokal telah dikosongkan. Memulai Crawl baru...</div>';
                        jalankanTerminal('crawl');
                    } else {
                        terminalBox.innerHTML += '<div class="terminal-line text-danger">[ERROR] Gagal mereset database.</div>';
                    }
                })
                .catch(err => {
                    terminalBox.innerHTML += '<div class="terminal-line text-danger">[ERROR] Terjadi kesalahan jaringan saat mereset DB.</div>';
                });
        }
    }
</script>

</body>
</html>