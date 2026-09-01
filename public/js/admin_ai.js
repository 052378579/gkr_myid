const { createApp, ref, computed, nextTick } = Vue;

createApp({
    setup() {
        const url = ref('');
        const isCrawling = ref(false);
        const isJanitorRunning = ref(false);
        const output = ref('<span style="color: #6c757d;">Menunggu perintah sinkronisasi...</span><br>');
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
                main: `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`,
                ms: String(centiseconds).padStart(2, '0')
            };
        });

        const scrollToBottom = () => {
            nextTick(() => {
                if (terminalBody.value) {
                    terminalBody.value.scrollTop = terminalBody.value.scrollHeight;
                }
            });
        };

        const startCrawl = async (mode = 'sync') => {
            if (isCrawling.value) return;

            isCrawling.value = true;
            let modeText = mode === 'reset' ? 'HARD RESET' : 'INKREMENTAL';
            output.value = `<span style="color: #4db8ff;">[START]</span> Memulai sinkronisasi AI Trainer (${modeText})...<br>`;
            scrollToBottom();

            abortController = new AbortController();

            // Start Stopwatch
            elapsedTime.value = 0;
            startTime.value = Date.now();
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                elapsedTime.value = Date.now() - startTime.value;
            }, 10);

            try {
                const response = await fetch(window.AppConfig.apiDoCrawl + '?mode=' + mode, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'url=' + encodeURIComponent(url.value || '/var/www/FOTO'),
                    signal: abortController.signal
                });

                if (!response.body) {
                    throw new Error('ReadableStream tidak didukung.');
                }

                const reader = response.body.getReader();
                const decoder = new TextDecoder('utf-8');

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    const chunk = decoder.decode(value, { stream: true });
                    const lines = chunk.split('\n');
                    
                    lines.forEach(line => {
                        if (line.trim() !== '') {
                            // Parse ANSI Escape Codes to HTML
                            let formattedLine = line
                                .replace(/\x1b\[0m/g, '</span>')
                                .replace(/\x1b\[91m/g, '<span style="color: #ff6b6b;">')
                                .replace(/\x1b\[92m/g, '<span style="color: #51cf66;">')
                                .replace(/\x1b\[93m/g, '<span style="color: #fcc419;">')
                                .replace(/\x1b\[96m/g, '<span style="color: #3bc9db;">')
                                .replace(/\x1b\[1m/g, '<span style="font-weight: bold;">');
                            
                            output.value += `${formattedLine}<br>`;
                        }
                    });
                    
                    scrollToBottom();
                }

                output.value += '<br><span style="color: #28a745;">[SELESAI]</span> Proses sinkronisasi telah tuntas.<br>';
                scrollToBottom();

            } catch (error) {
                if (error.name === 'AbortError') {
                    output.value += '<br><span style="color: #ffc107;">[DIBATALKAN]</span> Anda telah menghentikan tampilan streaming.<br>';
                } else {
                    output.value += `<br><span style="color: #dc3545;">[ERROR]</span> ${error.message}<br>`;
                }
                scrollToBottom();
            } finally {
                isCrawling.value = false;
                abortController = null;
                if (timerInterval) clearInterval(timerInterval);
            }
        };

        const startJanitor = async () => {
            if (isCrawling.value || isJanitorRunning.value) return;

            isCrawling.value = true;
            isJanitorRunning.value = true;
            output.value = `<span style="color: #4db8ff;">[START]</span> Menjalankan Janitor (Pembersih Sinkronisasi)...<br>`;
            scrollToBottom();

            abortController = new AbortController();

            try {
                const response = await fetch(window.AppConfig.apiDoJanitor, {
                    method: 'POST',
                    signal: abortController.signal
                });

                if (!response.body) {
                    throw new Error('ReadableStream tidak didukung.');
                }

                const reader = response.body.getReader();
                const decoder = new TextDecoder('utf-8');

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    const chunk = decoder.decode(value, { stream: true });
                    const lines = chunk.split('\n');
                    
                    lines.forEach(line => {
                        if (line.trim() !== '') {
                            output.value += `${line}<br>`;
                        }
                    });
                    
                    scrollToBottom();
                }

            } catch (error) {
                if (error.name === 'AbortError') {
                    output.value += '<br><span style="color: #ffc107;">[DIBATALKAN]</span> Anda telah menghentikan proses Janitor.<br>';
                } else {
                    output.value += `<br><span style="color: #dc3545;">[ERROR]</span> ${error.message}<br>`;
                }
                scrollToBottom();
            } finally {
                isCrawling.value = false;
                isJanitorRunning.value = false;
                abortController = null;
            }
        };

        const stopCrawl = () => {
            if (abortController) {
                abortController.abort();
            }
            if (timerInterval) clearInterval(timerInterval);
        };

        return {
            isCrawling,
            output,
            terminalBody,
            elapsedTime,
            formattedTime,
            url,
            startCrawl,
            stopCrawl,
            isJanitorRunning,
            startJanitor
        };
    }
}).mount('#crawlApp');
// Toggle Fullscreen Function
window.toggleFullscreen = function() {
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
