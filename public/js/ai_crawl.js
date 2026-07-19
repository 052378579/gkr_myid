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
