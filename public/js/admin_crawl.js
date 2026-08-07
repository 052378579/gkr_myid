const { createApp, ref, computed, nextTick } = Vue;

createApp({
    setup() {
        const url = ref('');
        const isCrawling = ref(false);
        const output = ref('doogleBot@server:~# Menunggu perintah...\n');
        const terminalBody = ref(null);
        let abortController = null;

        // Stopwatch State
        const startTime = ref(0);
        const elapsedTime = ref(0);
        let timerInterval = null;

        const formattedTime = computed(() => {
            let totalMs = elapsedTime.value;
            let minutes = Math.floor(totalMs / 60000);
            let seconds = Math.floor((totalMs % 60000) / 1000);
            let centiseconds = Math.floor((totalMs % 1000) / 10);

            return {
                main: `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}:`,
                ms: String(centiseconds).padStart(2, '0')
            };
        });
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

            // Start Stopwatch
            elapsedTime.value = 0;
            startTime.value = Date.now();
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                elapsedTime.value = Date.now() - startTime.value;
            }, 10);

            try {
                const formData = new FormData();
                formData.append('url', url.value);

                const response = await fetch(window.AppConfig.apiDoCrawl, {
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
                if (timerInterval) clearInterval(timerInterval);
                output.value += '<br><span style="color: #28a745; font-weight: bold;">[SELESAI]</span> <span style="color: #d4d4d4;">Proses Crawling ditutup</span><br>doogleBot@server:~# Menunggu perintah...\n';
                scrollToBottom();
            }
        };

        const stopCrawl = () => {
            if (abortController) {
                abortController.abort();
            }
            if (timerInterval) clearInterval(timerInterval);
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
                    const res = await fetch(window.AppConfig.apiResetDb, { method: 'POST' });
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        output.value += '<span class="text-success">Database berhasil direset.</span><br>doogleBot@server:~# Menunggu perintah...\n';
                    } else {
                        output.value += '<span class="text-danger">Gagal mereset database: ' + (data.pesan || '') + '</span><br>doogleBot@server:~# Menunggu perintah...\n';
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
            elapsedTime,
            formattedTime,
            startCrawl,
            stopCrawl,
            resetDb
        }
    }
}).mount('#crawlApp');
