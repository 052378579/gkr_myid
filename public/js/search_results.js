document.addEventListener("DOMContentLoaded", function() {
    const uploadAreaContainer = document.getElementById('uploadAreaContainer');
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const cameraInput = document.getElementById('cameraInput');
    const btnCamera = document.getElementById('btnCamera');
    const btnGallery = document.getElementById('btnGallery');
    const previewArea = document.getElementById('previewArea');
    const uploadPreview = document.getElementById('uploadPreview');
    const clearImageBtn = document.getElementById('clearImageBtn');
    const uploadError = document.getElementById('uploadError');
    const uploadSubmitBtn = document.getElementById('uploadSubmitBtn');
    const uploadSubmitText = document.getElementById('uploadSubmitText');
    const uploadSubmitLoading = document.getElementById('uploadSubmitLoading');
    let currentFile = null;
    let cropperInstance = null;

    if (!uploadAreaContainer) return;

    if (uploadArea) uploadArea.addEventListener('click', () => fileInput.click());
    if (btnCamera) btnCamera.addEventListener('click', () => cameraInput.click());
    if (btnGallery) btnGallery.addEventListener('click', () => fileInput.click());
    
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.backgroundColor = '#e9ecef';
    });
    uploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadArea.style.backgroundColor = '';
    });
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.style.backgroundColor = '';
        if (e.dataTransfer.files.length) processFile(e.dataTransfer.files[0]);
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) processFile(e.target.files[0]);
    });
    
    if (cameraInput) {
        cameraInput.addEventListener('change', (e) => {
            if (e.target.files.length) processFile(e.target.files[0]);
        });
    }

    function processFile(file) {
        uploadError.classList.add('d-none');
        if (!file) return;
        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            showError('Hanya format JPG, PNG, atau WEBP yang didukung.');
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            showError('Ukuran gambar maksimal 5MB.');
            return;
        }
        currentFile = file;
        uploadPreview.src = URL.createObjectURL(file);
        uploadAreaContainer.classList.add('d-none');
        previewArea.classList.remove('d-none');
        uploadSubmitBtn.classList.remove('d-none');
        
        // Inisialisasi Cropper setelah gambar tampil
        setTimeout(() => {
            if (cropperInstance) cropperInstance.destroy();
            cropperInstance = new Cropper(uploadPreview, {
                viewMode: 1,
                autoCropArea: 0.8,
                responsive: true,
            });
        }, 100);
    }

    clearImageBtn.addEventListener('click', () => {
        currentFile = null;
        fileInput.value = '';
        uploadPreview.src = '';
        uploadAreaContainer.classList.remove('d-none');
        previewArea.classList.add('d-none');
        uploadSubmitBtn.classList.add('d-none');
        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }
        uploadError.classList.add('d-none');
    });

    function showError(msg) {
        uploadError.textContent = msg;
        uploadError.classList.remove('d-none');
    }

    uploadSubmitBtn.addEventListener('click', async () => {
        if (!currentFile || !cropperInstance) return;
        uploadSubmitBtn.disabled = true;
        uploadSubmitText.classList.add('d-none');
        uploadSubmitLoading.classList.remove('d-none');
        uploadError.classList.add('d-none');

        cropperInstance.getCroppedCanvas({
            maxWidth: 1024,
            maxHeight: 1024
        }).toBlob(async (blob) => {
            if (!blob) {
                showError('Gagal memotong gambar.');
                uploadSubmitBtn.disabled = false;
                uploadSubmitText.classList.remove('d-none');
                uploadSubmitLoading.classList.add('d-none');
                return;
            }

            const formData = new FormData();
            formData.append('image', blob, currentFile.name);

            try {
                const uploadUrl = (window.SearchConfig && window.SearchConfig.apiSearchUpload) ? window.SearchConfig.apiSearchUpload : '/api/search/upload';
                const res = await fetch(uploadUrl, { method: 'POST', body: formData });
                const data = await res.json();
                if (!res.ok) {
                    const errorMsg = (data.messages && data.messages.error) || data.message || data.error || 'Terjadi kesalahan.';
                    throw new Error(errorMsg);
                }
                if (data.status === 'success' || data.status === 'sukses') {
                    let baseUrl = (window.SearchConfig && window.SearchConfig.searchUrl) ? window.SearchConfig.searchUrl : '/cari';
                    window.location.href = baseUrl + '?type=image_results';
                } else {
                    showError('Terjadi kesalahan saat memproses gambar.');
                }
            } catch (err) {
                showError(err.message);
            } finally {
                uploadSubmitBtn.disabled = false;
                uploadSubmitText.classList.remove('d-none');
                uploadSubmitLoading.classList.add('d-none');
            }
        }, 'image/jpeg', 0.9);
    });
});

/* ==========================================================================
   VUE 3 GOOGLE KNOWLEDGE PANEL REACTIVE CONTROLLER
   ========================================================================== */
document.addEventListener("DOMContentLoaded", function() {
    const knowledgeElem = document.getElementById('knowledgeApp');
    if (!knowledgeElem || typeof Vue === 'undefined') return;

    const { createApp, ref, onMounted } = Vue;

    createApp({
        setup() {
            const selectedIndex = ref(0);

            const BLANK_WHITE_SVG = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="368" height="230" viewBox="0 0 368 230"><rect width="368" height="230" fill="%23ffffff"/></svg>';

            const activeItem = ref({
                title: 'Mebel Gracia',
                description: '-',
                kodeBom: 'FG-',
                produksi: 'UNIT -',
                dirPath: 'GRACIA/2022/',
                siteUrl: '#',
                imageUrl: BLANK_WHITE_SVG,
                erpUrl: '#',
                pdfUrl: '#'
            });

            function selectKnowledgeItem(index, title, description, kodeBom, produksi, dirPath, siteUrl, imageUrl, erpUrl, pdfUrl) {
                selectedIndex.value = index;
                
                let patternBOM = /\(?\b(?:fg|Fg|FG)\s*[-_]?\s*([0-9]+)\)?/gi;
                let cleanTitle = title ? title.replace(patternBOM, '(FG-$1)') : 'Mebel Gracia';
                let cleanDesc  = description ? description.replace(patternBOM, '(FG-$1)') : cleanTitle;

                activeItem.value = {
                    title: cleanTitle,
                    description: cleanDesc,
                    kodeBom: kodeBom || 'FG-',
                    produksi: produksi || 'UNIT -',
                    dirPath: dirPath || 'GRACIA/2022/',
                    siteUrl: siteUrl,
                    imageUrl: (imageUrl && imageUrl !== 'undefined' && imageUrl !== 'null') ? imageUrl : BLANK_WHITE_SVG,
                    erpUrl: erpUrl,
                    pdfUrl: pdfUrl
                };
            }

            function handleImgError(e) {
                e.target.src = BLANK_WHITE_SVG;
            }

            function formatTitleCase(str) {
                if (!str || str === '-') return '-';
                let cleaned = str.replace(/\(?\b(?:fg|Fg|FG)\s*[-_]?\s*([0-9]+)\)?/gi, '(FG-$1)');
                return cleaned;
            }

            function formatKodeBom(bom) {
                if (!bom || bom === '-' || bom === 'FG-') return 'FG-';
                let clean = bom.toUpperCase().trim();
                if (!clean.startsWith('FG-') && clean.startsWith('FG')) {
                    clean = 'FG-' + clean.substring(2).replace(/^[-_\s]+/, '');
                }
                return clean;
            }

            function formatLihatBom(bom) {
                if (!bom || bom === '-' || bom === 'FG-') return 'BOM-FG- -001';
                let clean = formatKodeBom(bom);
                return 'BOM-' + clean + '-001';
            }

            function formatProduksi(prod) {
                if (!prod || prod === 'UNIT -' || prod === '-') return '-';
                return prod;
            }

            function isBomAvailable(bom) {
                return (bom && bom !== '-' && bom !== 'FG-');
            }

            // Inisialisasi klik item pertama secara reaktif setelah Vue dipasang
            onMounted(() => {
                const firstItem = document.querySelector('.site-result-item');
                if (firstItem) {
                    setTimeout(() => {
                        firstItem.click();
                    }, 50);
                }
            });

            return {
                selectedIndex,
                activeItem,
                selectKnowledgeItem,
                handleImgError,
                formatTitleCase,
                formatKodeBom,
                formatLihatBom,
                formatProduksi,
                isBomAvailable
            };
        }
    }).mount('#knowledgeApp');
});

