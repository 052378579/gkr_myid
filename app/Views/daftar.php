<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#ff0000" media="(prefers-color-scheme: dark)">
<link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('apple-touch-icon.png') ?>" />
<meta name="apple-mobile-web-app-title" content="GRACIA" />
<link rel="manifest" href="<?= base_url('site.webmanifest') ?>" />
<link rel="stylesheet" href="<?= base_url('css/login.css') ?>?v=<?= time() ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="rnd" class="w-100">
    <main class="form-signin w-100 m-auto">
        
        <h1 class="h3 mb-3 fw-normal"><img src="<?= base_url('Gracia_logo.png') ?>" style="width:75%;"></h1>
        <p class="text-muted mb-4">Pendaftaran Akun Baru</p>

        <form action="<?= base_url('daftar/process') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="input-group mb-3">
                <span class="input-group-text bg-white"><i class="fas fa-user text-muted"></i></span>
                <input type="text" class="form-control" name="nama_lengkap" placeholder="Nama Lengkap" required autofocus>
            </div>
            
            <div class="input-group mb-3">
                <span class="input-group-text bg-white"><i class="fas fa-phone text-muted"></i></span>
                <input type="tel" class="form-control" name="no_hp" placeholder="08xxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
            </div>

            <div class="input-group mb-4">
                <span class="input-group-text bg-white"><i class="fas fa-building text-muted"></i></span>
                <select class="form-select" name="divisi" required style="color: #6c757d;">
                    <option value="" disabled selected>Pilih Divisi...</option>
                    <option value="Marketing" style="color: #212529;">Marketing</option>
                    <option value="Produksi 1" style="color: #212529;">Produksi 1</option>
                    <option value="Produksi 2" style="color: #212529;">Produksi 2</option>
                    <option value="Produksi 4" style="color: #212529;">Produksi 4</option>
                </select>
            </div>
            
            <button class="btn btn-gracia w-100 py-2 fw-bold mb-3" type="submit">Daftar</button>
            
            <a href="<?= base_url('login') ?>" class="text-decoration-none small text-muted">ke Halaman Login</a>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/daftar.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>
