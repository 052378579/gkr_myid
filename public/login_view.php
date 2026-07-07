<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#ff0000" media="(prefers-color-scheme: dark)">

    <title><?= esc($title) ?></title>
    
    <link rel="shortcut icon" href="/icon/favicon.ico" />
    <link rel="icon" type="image/png" href="/icon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/icon/favicon.svg" />
    <link rel="apple-touch-icon" sizes="180x180" href="/icon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="GRACIA" />
    <link rel="manifest" href="/icon/site.webmanifest" />

    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://unpkg.com">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Mengadopsi signin.css murni dari Bootstrap */
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f8f9fa;
        }
        .form-signin {
            max-width: 330px;
            padding: 15px;
        }
        
        /* Penyesuaian tinggi input dan icon agar sejajar */
        .form-signin .form-control,
        .form-signin .input-group-text {
            position: relative;
            box-sizing: border-box;
            height: auto;
            padding: 10px;
            font-size: 16px;
        }
        
        .form-signin .form-control:focus {
            z-index: 2;
        }
        
        /* Kustomisasi Ikon Brand */
        .brand-icon {
            font-size: 4.5rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }

        /* Penyesuaian jarak Toast dari atas */
        .custom-toast-container {
            margin-top: 20px;
            z-index: 1080;
        }

        /* PERBAIKAN: CSS Footer Versi Aplikasi untuk diposisikan absolut di bawah */
        .footer-version { 
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center; 
            font-size: 0.85rem; 
            color: #6c757d; 
            user-select: none; 
        }
        .footer-version a {
            color: #6c757d;
            transition: color 0.2s ease;
        }
        .footer-version a:hover {
            color: #212529;
        }
    </style>
</head>
<body class="text-center">

    <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3 custom-toast-container">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="toast align-items-center text-white bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-medium text-start">
                        <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="toast align-items-center text-white bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-medium text-start">
                        <i class="fas fa-exclamation-triangle me-2"></i> <?= session()->getFlashdata('error') ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div id="rnd" class="w-100">
        <main class="form-signin w-100 m-auto">
            
            <h1 class="h3 mb-3 fw-normal"><img src="<?= base_url('Gracia_logo.png') ?>" style="width:50%;"></h1>
            <p class="text-muted mb-4">Silakan masuk menggunakan Nomor HP</p>

            <form action="<?= base_url('login/process') ?>" method="POST" @submit="onSubmit">
                <?= csrf_field() ?>
                
                <label for="no_hp" class="visually-hidden">Nomor WhatsApp / HP</label>
                
                <div class="input-group mb-2">
                    <span class="input-group-text bg-white"><i class="fas fa-phone text-muted"></i></span>
                    <input type="tel" 
                           class="form-control" 
                           id="no_hp" 
                           name="no_hp" 
                           v-model="noHp" 
                           @input="filterInput"
                           placeholder="08xxxxxxxxxx" 
                           autocomplete="tel"
                           required autofocus>
                </div>
                
                <div class="form-text text-danger mb-3 text-start small" v-if="errorMessage" style="min-height: 20px;">
                    <i class="fas fa-info-circle me-1"></i> {{ errorMessage }}
                </div>
                <div class="mb-3" v-else style="min-height: 20px;"></div>

                <button class="btn btn-dark w-100 py-2 fw-bold" type="submit" :disabled="!isFormValid">
                    <i class="fas fa-sign-in-alt me-2"></i> Masuk
                </button>
            </form>
        </main>

        <div class="footer-version" v-if="appInfo">
            &copy; <?= date('Y') ?> <a href="https://wickerkane.com" class="text-decoration-none text-primary" target="_blank" rel="noopener">PT. Gracia Kreasi Rotan</a><br>
            Dibuat oleh RND {{ appInfo }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toastElList = document.querySelectorAll('.toast');
            const toastList = [...toastElList].map(toastEl => {
                const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
                toast.show();
                return toast;
            });
        });

        const { createApp, ref, computed, onMounted } = Vue;

        createApp({
            setup() {
                const noHp = ref('');
                const appInfo = ref(''); 

                onMounted(async () => {
                    try {
                        const response = await fetch('<?= base_url('versi.json') ?>');
                        if (response.ok) {
                            const data = await response.json();
                            appInfo.value = `v.${data.version}`;
                        }
                    } catch (error) {
                        console.error('Gagal mengambil info versi aplikasi:', error);
                    }
                });

                const filterInput = () => {
                    noHp.value = noHp.value.replace(/[^0-9]/g, '');
                };

                const regexPattern = /^08[0-9]{8,13}$/;

                const isFormValid = computed(() => {
                    return regexPattern.test(noHp.value);
                });

                const errorMessage = computed(() => {
                    if (noHp.value.length === 0) return '';
                    if (!noHp.value.startsWith('08')) return 'Diawali dengan "08"';
                    if (noHp.value.length < 10) return '';
                    if (noHp.value.length > 15) return 'Maksimal 15 digit';
                    if (!isFormValid.value) return 'Format tidak valid';
                    return ''; 
                });

                const onSubmit = (event) => {
                    if (!isFormValid.value) {
                        event.preventDefault();
                    }
                };

                return {
                    noHp,
                    filterInput,
                    isFormValid,
                    errorMessage,
                    onSubmit,
                    appInfo 
                }
            }
        }).mount('#rnd');
    </script>
</body>
</html>