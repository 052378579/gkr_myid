<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BG Remover</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link rel="stylesheet" href="<?= base_url('css/bg_remover.css') ?>?v=<?= ASSET_VERSION ?>">
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

    <script src="<?= base_url('js/bg_remover.js') ?>?v=<?= ASSET_VERSION ?>"></script>
</body>
</html>
