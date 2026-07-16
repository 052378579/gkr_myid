const { createApp, ref, nextTick } = Vue;

createApp({
    setup() {
        const isCrawling = ref(false);
        const output = ref('<span style="color: #6c757d;">Menunggu perintah sinkronisasi...</span><br>');
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
            if (isCrawling.value) return;

            isCrawling.value = true;
            output.value = '<span style="color: #4db8ff;">[START]</span> Memulai sinkronisasi AI Trainer...<br>';
            scrollToBottom();

            abortController = new AbortController();

            try {
                const response = await fetch(window.AppConfig.apiDoCrawl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
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
                            // Mewarnai teks khusus
                            let formattedLine = line;
                            if (line.includes('BERHASIL') || line.includes('SUCCESS') || line.includes('OTOMATISASI')) {
                                formattedLine = `<span style="color: #28a745;">${line}</span>`;
                            } else if (line.includes('ERROR') || line.includes('Gagal')) {
                                formattedLine = `<span style="color: #dc3545;">${line}</span>`;
                            } else if (line.includes('[1/3]') || line.includes('[2/3]') || line.includes('[3/3]')) {
                                formattedLine = `<span style="color: #ffc107;">${line}</span>`;
                            }
                            
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
            }
        };

        const stopCrawl = () => {
            if (abortController) {
                abortController.abort();
            }
        };

        return {
            isCrawling,
            output,
            terminalBody,
            startCrawl,
            stopCrawl
        };
    }
}).mount('#crawlApp');
