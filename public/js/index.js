const { createApp, ref } = Vue;

createApp({
    setup() {
        const query = ref('');
        const uploadFile = ref(null);
        const uploadPreviewUrl = ref(null);
        const uploadError = ref(null);
        const isUploading = ref(false);

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
        };

        const clearImage = () => {
            uploadFile.value = null;
            if (uploadPreviewUrl.value) {
                URL.revokeObjectURL(uploadPreviewUrl.value);
                uploadPreviewUrl.value = null;
            }
            uploadError.value = null;
        };

        const uploadAndSearch = async () => {
            if (!uploadFile.value) return;
            
            isUploading.value = true;
            uploadError.value = null;
            
            const formData = new FormData();
            formData.append('image', uploadFile.value);
            
            try {
                // Asumsi base_url('/api/search/upload') digunakan jika tidak ada config khusus.
                // Mengambil root domain (atau subfolder jika ada) dari window.location.origin
                // Sebaiknya sediakan uploadUrl di AppConfig jika mungkin, tapi kita bisa pakai fetch API relatif.
                const response = await fetch('/api/search/upload', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    const errorMsg = (data.messages && data.messages.error) || data.message || data.error || 'Terjadi kesalahan internal pada server.';
                    throw new Error(errorMsg);
                }
                
                if (data.status === 'success') {
                    // Redirect ke hasil pencarian gambar, hash sudah disimpan di session backend
                    window.location.href = window.AppConfig.searchUrl + '?type=image_results';
                } else {
                    uploadError.value = 'Terjadi kesalahan saat memproses gambar.';
                }
                
            } catch (err) {
                uploadError.value = err.message;
            } finally {
                isUploading.value = false;
            }
        };

        return {
            query,
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

