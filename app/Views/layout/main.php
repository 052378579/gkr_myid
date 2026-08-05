<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="app-config" 
        data-base-url="<?= base_url() ?>" 
        data-csrf-token="<?= csrf_token() ?>" 
        data-versi-url="<?= base_url('versi.json') ?>" 
        data-sw-url="<?= base_url('sw.js') ?>">
    <title><?= $this->renderSection('title') ?></title>
    <!-- Skrip Tema (dijalankan awal untuk menghindari FOUC) -->
    <script src="<?= base_url('js/theme.js') ?>?v=<?= time() ?>"></script>
    <!-- Bootstrap 5.3 CSS -->
    <link href="<?= base_url('vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('vendor/fontawesome/css/all.min.css') ?>">
    <!-- Base Styles -->
    <link rel="stylesheet" href="<?= base_url('css/main.css') ?>?v=<?= time() ?>">
    <?php if (strpos(uri_string(), 'admin') === 0): ?>
        <link rel="icon" type="image/x-icon" href="<?= base_url('faviconp.ico') ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    <?php endif; ?>
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-body text-body">

    <?= $this->renderSection('content') ?>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="<?= base_url('vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    
    <!-- Vue.js 3 -->
    <script src="<?= base_url('vendor/vue/vue.global.prod.js') ?>"></script>

    <!-- SweetAlert2 -->
    <script src="<?= base_url('vendor/sweetalert2/sweetalert2.all.min.js') ?>"></script>

    <!-- App Config Loader -->
    <script src="<?= base_url('js/config.js') ?>?v=<?= time() ?>"></script>

    <?= $this->renderSection('scripts') ?>
    
    <!-- Global Toast Notification -->
    <?= $this->include('components/toast') ?>
</body>
</html>
