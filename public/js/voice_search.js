/**
 * Mesin Pencari Gracia - Voice Search Engine (Bahasa Indonesia id-ID)
 * Menggunakan Web Speech API Native & Visualizer Audio Wave
 */

document.addEventListener('DOMContentLoaded', () => {
    initVoiceSearch();
});

function initVoiceSearch() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    
    // Periksa dukungan peramban
    if (!SpeechRecognition) {
        console.warn('[Voice Search] Web Speech API tidak didukung di browser ini.');
        const btnVoiceList = document.querySelectorAll('.btn-voice-search');
        btnVoiceList.forEach(btn => {
            btn.style.display = 'none';
        });
        return;
    }

    // Suntikkan Modal Popup Voice Wave Overlay ke dalam DOM jika belum ada
    if (!document.getElementById('voiceSearchModal')) {
        injectVoiceSearchModal();
    }

    const voiceModalEl = document.getElementById('voiceSearchModal');
    const voiceModal = new bootstrap.Modal(voiceModalEl);
    const waveText = document.getElementById('voiceWaveText');
    const waveTranscript = document.getElementById('voiceWaveTranscript');
    const btnStopVoice = document.getElementById('btnStopVoice');

    let recognition = null;
    let activeInput = null;
    let activeForm = null;

    // Pasang Event Listener ke seluruh tombol dengan kelas .btn-voice-search
    document.querySelectorAll('.btn-voice-search').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Cari input teks pencarian terdekat
            const parentGroup = btn.closest('.input-group') || btn.closest('form');
            if (parentGroup) {
                activeInput = parentGroup.querySelector('input[type="text"], input[name="q"], input[type="search"]');
                activeForm = btn.closest('form');
            }

            startRecognition();
        });
    });

    function startRecognition() {
        try {
            recognition = new SpeechRecognition();
            recognition.lang = 'id-ID';
            recognition.interimResults = true;
            recognition.maxAlternatives = 1;

            recognition.onstart = () => {
                waveText.textContent = 'Mendengarkan... Bicara sekarang';
                waveTranscript.textContent = '...';
                voiceModal.show();
            };

            recognition.onresult = (event) => {
                let currentTranscript = '';
                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    currentTranscript += event.results[i][0].transcript;
                }
                
                if (currentTranscript.trim() !== '') {
                    waveTranscript.textContent = `"${currentTranscript}"`;
                    if (activeInput) {
                        activeInput.value = currentTranscript;
                        // Memicu event input Vue 3
                        activeInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
            };

            recognition.onerror = (event) => {
                console.error('[Voice Search Error]:', event.error);
                if (event.error === 'not-allowed') {
                    waveText.textContent = 'Izin Mikrofon Ditolak';
                    waveTranscript.textContent = 'Silakan izinkan akses mikrofon di browser Anda.';
                } else if (event.error === 'no-speech') {
                    waveText.textContent = 'Tidak Ada Suara Terdeteksi';
                    waveTranscript.textContent = 'Silakan coba bicara kembali.';
                } else {
                    waveText.textContent = 'Terjadi Kesalahan Kesalahan';
                    waveTranscript.textContent = event.error;
                }
                setTimeout(() => {
                    voiceModal.hide();
                }, 2000);
            };

            recognition.onend = () => {
                waveText.textContent = 'Selesai mendengarkan';
                setTimeout(() => {
                    voiceModal.hide();
                    // Jika ada teks pencarian valid, otomatis submit form atau jalankan pencarian
                    if (activeInput && activeInput.value.trim() !== '') {
                        const rawQuery = activeInput.value.trim();
                        // Format spasi menjadi '+' persis seperti standar form HTML (application/x-www-form-urlencoded)
                        const queryVal = encodeURIComponent(rawQuery).replace(/%20/g, '+');
                        
                        // Membaca tab aktif saat ini dari URL, dengan default 'images' untuk pencarian visual katalog
                        const urlParams = new URLSearchParams(window.location.search);
                        const currentType = urlParams.get('type') || 'images';
                        
                        const searchTarget = (window.AppConfig && window.AppConfig.searchUrl) ? window.AppConfig.searchUrl : 'cari';
                        window.location.href = `${searchTarget}?q=${queryVal}&type=${currentType}`;
                    }
                }, 800);
            };

            recognition.start();

        } catch (err) {
            console.error('[Voice Search Exception]:', err);
        }
    }

    if (btnStopVoice) {
        btnStopVoice.addEventListener('click', () => {
            if (recognition) {
                recognition.stop();
            }
            voiceModal.hide();
        });
    }
}

function injectVoiceSearchModal() {
    const modalHTML = `
    <div class="modal fade" id="voiceSearchModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px); color: #fff;">
                <div class="modal-body text-center p-4">
                    <div class="voice-wave-wrapper mb-3">
                        <div class="voice-wave-circle">
                            <i class="fa-solid fa-microphone text-white fs-1"></i>
                        </div>
                        <div class="voice-wave-ring ring-1"></div>
                        <div class="voice-wave-ring ring-2"></div>
                        <div class="voice-wave-ring ring-3"></div>
                    </div>
                    <h5 class="fw-bold mb-2 text-white" id="voiceWaveText">Mendengarkan...</h5>
                    <p class="text-info fs-6 fw-semibold fst-italic mb-4" id="voiceWaveTranscript" style="min-height: 24px;">"..."</p>
                    <button type="button" class="btn btn-outline-light rounded-pill px-4 py-2 btn-sm" id="btnStopVoice">
                        <i class="fa-solid fa-xmark me-1"></i> Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}
