<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Awan Kata (Tren Pencarian)<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/awan_kata.css') ?>?v=<?= time() ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">
    <div class="mt-4 mb-2 ps-4">
        <a href="<?= base_url('/') ?>">
            <img src="<?= base_url('Gracia_logo.png') ?>" alt="Gracia Logo" style="height: 38px;" class="mb-1">
        </a>
        <h6 class="text-muted fw-medium mb-1" style="letter-spacing: 0.5px;">Awan Kata (Tren Pencarian)</h6>
        <!-- Bilah Kemajuan Auto-Refresh -->
        <div class="progress" style="height: 4px; width: 220px; background-color: #e9ecef; border-radius: 4px;">
            <div id="refreshProgressBar" class="progress-bar" role="progressbar" style="width: 100%; background-color: #2B3385; transition: width 1s linear;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    <div class="px-3 text-center">
        <?php if (empty($wordList)): ?>
            <div class="py-5 text-muted">Belum ada data pencarian teks yang cukup untuk membuat awan kata.</div>
        <?php else: ?>
            <!-- Wadah HTML untuk WordCloud2.js (Mode Span DOM) -->
            <div id="canvas-container" style="width: 100%; height: 600px; display: flex; justify-content: center; align-items: center;">
                <div id="wordCloudCanvas" style="width: 100%; height: 100%; position: relative;"></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="fixed-bottom py-3 w-100" style="background-color: transparent; color: #70757a; font-size: 0.9rem;">
    <div class="text-center w-100 fw-medium">
        RND &copy; <?= date('Y') ?>
    </div>
    <div class="position-absolute" style="right: 20px; top: 50%; transform: translateY(-50%);">
        <span class="text-muted fw-medium"><?= esc($version) ?></span>
    </div>
</footer>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (!empty($wordList)): ?>
<script>
    window.wordListData = <?= json_encode($wordList) ?>;
    window.AppConfig = window.AppConfig || {};
    window.AppConfig.searchUrl = '<?= url_to('Search::index') ?>';
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.2.2/wordcloud2.min.js"></script>
<script src="<?= base_url('js/awan_kata.js') ?>?v=<?= time() ?>"></script>
<?php endif; ?>
<?= $this->endSection() ?>
