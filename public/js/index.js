const { createApp, ref } = Vue;

createApp({
    setup() {
        const query = ref('');
        const uploadFile = ref(null);
        const uploadPreviewUrl = ref(null);
        const uploadError = ref(null);
        const isUploading = ref(false);
        let cropperInstance = null;

        const suggestions = ref([]);
        const showSuggestions = ref(false);
        const activeIndex = ref(-1);
        let debounceTimer = null;

        const fetchSuggestions = () => {
            clearTimeout(debounceTimer);
            if (query.value.trim() === '') {
                suggestions.value = [];
                showSuggestions.value = false;
                return;
            }
            debounceTimer = setTimeout(async () => {
                try {
                    const res = await fetch('/api/autocomplete?q=' + encodeURIComponent(query.value));
                    if(res.ok) {
                        const data = await res.json();
                        suggestions.value = data;
                        showSuggestions.value = true;
                        activeIndex.value = -1;
                    }
                } catch(e) {
                    console.error("Autocomplete error:", e);
                }
            }, 300);
        };

        const navigateDown = () => {
            if (showSuggestions.value && suggestions.value.length > 0) {
                if (activeIndex.value < suggestions.value.length - 1) {
                    activeIndex.value++;
                }
            }
        };

        const navigateUp = () => {
            if (showSuggestions.value && suggestions.value.length > 0) {
                if (activeIndex.value > 0) {
                    activeIndex.value--;
                }
            }
        };

        const selectCurrentSuggestion = () => {
            if (showSuggestions.value && activeIndex.value >= 0 && activeIndex.value < suggestions.value.length) {
                selectSuggestion(suggestions.value[activeIndex.value]);
            } else {
                search();
            }
        };

        const selectSuggestion = (item) => {
            query.value = item;
            showSuggestions.value = false;
            search();
        };

        const handleBlur = () => {
            setTimeout(() => {
                showSuggestions.value = false;
            }, 150);
        };

        const search = () => {
            if(query.value.trim() !== '') {
                window.location.href = window.AppConfig.searchUrl + '?q=' + encodeURIComponent(query.value);
            }
        };

        const handleFileSelect = (event) => {
            const file = event.target.files[0];
            processFile(file);
        };

        const handleDrop = (event) => {
            const file = event.dataTransfer.files[0];
            processFile(file);
        };

        const processFile = (file) => {
            uploadError.value = null;
            if (!file) return;
            
            const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                uploadError.value = 'Hanya format JPG, PNG, atau WEBP yang didukung.';
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) {
                uploadError.value = 'Ukuran gambar maksimal 5MB.';
                return;
            }

            uploadFile.value = file;
            uploadPreviewUrl.value = URL.createObjectURL(file);
            
            // Inisialisasi Cropper setelah gambar di-render oleh Vue
            setTimeout(() => {
                const imgElement = document.getElementById('vueUploadPreview');
                if (imgElement) {
                    if (cropperInstance) cropperInstance.destroy();
                    cropperInstance = new Cropper(imgElement, {
                        viewMode: 1,
                        autoCropArea: 0.8,
                        responsive: true,
                    });
                }
            }, 100);
        };

        const clearImage = () => {
            uploadFile.value = null;
            if (uploadPreviewUrl.value) {
                URL.revokeObjectURL(uploadPreviewUrl.value);
                uploadPreviewUrl.value = null;
            }
            if (cropperInstance) {
                cropperInstance.destroy();
                cropperInstance = null;
            }
            uploadError.value = null;
        };

        const uploadAndSearch = async () => {
            if (!uploadFile.value || !cropperInstance) return;
            
            isUploading.value = true;
            uploadError.value = null;
            
            // Ambil gambar yang sudah di-crop dari browser (Canvas)
            cropperInstance.getCroppedCanvas({
                maxWidth: 1024,
                maxHeight: 1024
            }).toBlob(async (blob) => {
                if (!blob) {
                    uploadError.value = "Gagal memotong gambar.";
                    isUploading.value = false;
                    return;
                }
                
                const formData = new FormData();
                // Kirim blob dengan nama file aslinya
                formData.append('image', blob, uploadFile.value.name);
                
                try {
                    const response = await fetch('/api/search/upload', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        const errorMsg = (data.messages && data.messages.error) || data.message || data.error || 'Terjadi kesalahan internal pada server.';
                        throw new Error(errorMsg);
                    }
                    
                    if (data.status === 'success' || data.status === 'sukses') {
                        window.location.href = window.AppConfig.searchUrl + '?type=image_results';
                    } else {
                        uploadError.value = 'Terjadi kesalahan saat memproses gambar.';
                    }
                    
                } catch (err) {
                    uploadError.value = err.message;
                }
            }, 'image/jpeg', 0.9);
        };

        const handleFocus = () => {
            if (query.value.trim() !== '' && suggestions.value.length > 0) {
                showSuggestions.value = true;
            } else {
                showSuggestions.value = false;
            }
        };

        return {
            query,
            suggestions,
            showSuggestions,
            activeIndex,
            fetchSuggestions,
            navigateDown,
            navigateUp,
            selectCurrentSuggestion,
            selectSuggestion,
            handleFocus,
            handleBlur,
            search,
            uploadFile,
            uploadPreviewUrl,
            uploadError,
            isUploading,
            handleFileSelect,
            handleDrop,
            clearImage,
            uploadAndSearch
        }
    }
}).mount('#app');

// Melacak pergerakan mouse KHUSUS untuk efek Spotlight Aura pada tombol Cari
document.addEventListener('mousemove', (e) => {
    document.querySelectorAll('.google-ai-container-spotlight').forEach(container => {
        const rect = container.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        container.style.setProperty('--mouse-x', `${x}px`);
        container.style.setProperty('--mouse-y', `${y}px`);
    });
});

