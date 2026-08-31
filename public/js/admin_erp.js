let koneksiSSE = null;
let timerInterval = null;
let startTime = null;

function disableAllButtons(disabled) {
    const btns = document.querySelectorAll('.btn:not(#btnStop)');
    btns.forEach(btn => btn.disabled = disabled);
}

function startTimer(label) {
    const timerLabel = document.getElementById('sw-label');
    const timerMin = document.getElementById('sw-min');
    const timerSec = document.getElementById('sw-sec');
    const timerMs = document.getElementById('sw-ms');
    if(timerLabel) timerLabel.innerText = label;
    startTime = Date.now();
    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        let elapsed = Date.now() - startTime;
        let ms = Math.floor((elapsed % 1000) / 10).toString().padStart(2, '0');
        let sec = Math.floor((elapsed / 1000) % 60).toString().padStart(2, '0');
        let min = Math.floor((elapsed / (1000 * 60)) % 60).toString().padStart(2, '0');
        if(timerMin) timerMin.innerText = min;
        if(timerSec) timerSec.innerText = sec;
        if(timerMs) timerMs.innerText = ms;
    }, 30);
}

function stopTimer() {
    clearInterval(timerInterval);
}

function resetTimer() {
    clearInterval(timerInterval);
    const timerMin = document.getElementById('sw-min');
    const timerSec = document.getElementById('sw-sec');
    const timerMs = document.getElementById('sw-ms');
    const timerLabel = document.getElementById('sw-label');
    if(timerMin) timerMin.innerText = '00';
    if(timerSec) timerSec.innerText = '00';
    if(timerMs) timerMs.innerText = '00';
    if(timerLabel) timerLabel.innerText = 'WAKTU PROSES';
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
            console.log("Error attempting to enable fullscreen: ", err);
        });
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
    }
}


