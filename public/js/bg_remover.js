const { createApp, ref } = Vue;

createApp({
    setup() {
        const selectedFile = ref(null);
        const isLoading = ref(false);
        const originalImage = ref(null);
        const resultImage = ref(null);
        const errorMessage = ref(null);

        const handleFileUpload = (event) => {
            selectedFile.value = event.target.files[0];
        };

        const uploadImage = async () => {
            if (!selectedFile.value) return;

            isLoading.value = true;
            errorMessage.value = null;
            
            const formData = new FormData();
            formData.append('image', selectedFile.value);

            try {
                const url = (window.CONFIG && window.CONFIG.BASE_URL ? window.CONFIG.BASE_URL : '/') + 'bg-remover/process';
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    originalImage.value = data.original;
                    resultImage.value = data.result;
                } else {
                    errorMessage.value = data.error;
                }
            } catch (error) {
                errorMessage.value = "Koneksi ke server terputus atau terjadi kesalahan sistem.";
            } finally {
                isLoading.value = false;
            }
        };

        return { selectedFile, isLoading, originalImage, resultImage, errorMessage, handleFileUpload, uploadImage };
    }
}).mount('#rnd');
