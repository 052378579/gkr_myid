<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#ff0000" media="(prefers-color-scheme: dark)">
    <title><?= esc($title) ?></title>

    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>" />
    <link rel="icon" type="image/png" sizes="96x96" href="<?= base_url('favicon-96x96.png') ?>" />
    <link rel="alternate icon" href="<?= base_url('favicon.ico') ?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('apple-touch-icon.png') ?>" />
    <meta name="apple-mobile-web-app-title" content="GRACIA" />
    <link rel="manifest" href="<?= base_url('site.webmanifest') ?>" />

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com" crossorigin>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
</head>
<body class="text-center">

    <!-- Global Toast Notification -->
    <?= $this->include('components/toast') ?>

    <div id="rnd" class="w-100">
        <main class="form-signin w-100 m-auto">
            
            <h1 class="h3 mb-3 fw-normal"><img src="<?= base_url('Gracia_logo.png') ?>" style="width:75%;"></h1>
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

                <button class="btn btn-gracia w-100 py-2 fw-bold" type="submit" :disabled="!isFormValid">Masuk</button>
            </form>
        </main>
    </div>

    <footer class="fixed-bottom py-3 w-100" style="background-color: transparent; color: #70757a; font-size: 0.9rem;">
        <div class="text-center w-100 fw-medium">
            RND &copy; <?= date('Y') ?>
        </div>
        <div class="position-absolute" style="right: 20px; top: 50%; transform: translateY(-50%);">
            <span class="text-muted fw-medium"><?= esc($version) ?></span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    
    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          navigator.serviceWorker.register('<?= base_url('sw.js') ?>')
            .then((reg) => {
              console.log('PWA Service Worker berhasil didaftarkan scope-nya pada: ', reg.scope);
            })
            .catch((err) => {
              console.error('PWA Service Worker gagal didaftarkan: ', err);
            });
        });
      }
    </script>
    <script>
        window.AppConfig = {
            versiUrl: '<?= base_url('versi.json') ?>'
        };
    </script>
    <script src="<?= base_url('js/login.js') ?>"></script>
</body>
</html>