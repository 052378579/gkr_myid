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
                    console.error('Error attempting to enable fullscreen:', err);
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
            logs.value = ['<span class="prompt">root@server:~#</span> Memulai Crawling: ...'];
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
                addLog('<span class="text-danger">[ERROR] ' + (err.message || 'Koneksi terputus.') + '</span>');
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
