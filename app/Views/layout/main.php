<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <!-- Skrip Tema (dijalankan awal untuk menghindari FOUC) -->
    <script src="<?= base_url('js/theme.js') ?>"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?></title>
    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Vue.js 3 CDN (Versi Produksi) -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?= $this->renderSection('scripts') ?>
    
    <!-- Global Toast Notification -->
    <?= $this->include('components/toast') ?>
</body>
</html>
