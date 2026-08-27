<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BG Remover</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <style>
        .preview-box { height: 350px; object-fit: contain; width: 100%; }
        .bg-checker { 
            background-image: 
                linear-gradient(45deg, #ddd 25%, transparent 25%), 
                linear-gradient(-45deg, #ddd 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #ddd 75%), 
                linear-gradient(-45deg, transparent 75%, #ddd 75%); 
            background-size: 20px 20px; 
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px; 
            background-color: #fff;
        }
    </style>
</head>
<body class="bg-light pb-5">
    <div id="rnd" class="container pt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="text-center mb-4 fw-bold text-primary">Hapus Background Foto</h2>
                        
                        <form @submit.prevent="uploadImage" class="mb-4">
                            <div class="mb-3">
                                <input type="file" class="form-control form-control-lg" @change="handleFileUpload" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold" :disabled="isLoading">
                                <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
                                {{ isLoading ? 'AI Sedang Memproses...' : 'Mulai Proses' }}
                            </button>
                        </form>

                        <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>

                        <div v-if="resultImage" class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <h5 class="text-muted text-center">Foto Asli</h5>
                                <img :src="originalImage" class="img-thumbnail preview-box shadow-sm bg-secondary-subtle">
                            </div>
                            <div class="col-md-6 mb-3 text-center">
                                <h5 class="text-success text-center fw-bold">Hasil Transparan</h5>
                                <img :src="resultImage" class="img-thumbnail preview-box shadow-sm bg-checker mb-3">
                                <!-- Tombol Download -->
                                <a :href="resultImage" download="hasil-ai-transparent.png" class="btn btn-success w-100 fw-bold shadow-sm">Unduh ⬇️</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
                        const response = await fetch('<?= base_url('bg-remover/process') ?>', {
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
    </script>
</body>
</html>