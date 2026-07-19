document.addEventListener("DOMContentLoaded", function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const previewArea = document.getElementById('previewArea');
    const uploadPreview = document.getElementById('uploadPreview');
    const clearImageBtn = document.getElementById('clearImageBtn');
    const uploadError = document.getElementById('uploadError');
    const uploadSubmitBtn = document.getElementById('uploadSubmitBtn');
    const uploadSubmitText = document.getElementById('uploadSubmitText');
    const uploadSubmitLoading = document.getElementById('uploadSubmitLoading');
    let currentFile = null;
    let cropperInstance = null;

    if (!uploadArea) return;

    uploadArea.addEventListener('click', () => fileInput.click());
    
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
        uploadArea.classList.add('d-none');
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
        uploadArea.classList.remove('d-none');
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
                if (data.status === 'success') {
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
