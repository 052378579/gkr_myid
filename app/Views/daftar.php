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

    <link href="<?= base_url('vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('vendor/fontawesome/css/all.min.css') ?>">

    <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
    <style>
        .form-label {
            text-align: left;
            display: block;
            font-weight: 600;
            color: #4a4a4a;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border-radius: 0.375rem;
            padding: 0.6rem 0.75rem;
            border: 1px solid #ced4da;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(43, 51, 133, 0.25);
            border-color: #2b3385;
        }
    </style>
</head>
<body class="text-center">

    <!-- Global Toast Notification -->
    <?= $this->include('components/toast') ?>

    <div id="rnd" class="w-100">
        <main class="form-signin w-100 m-auto" style="max-width: 400px;">
            
            <h1 class="h3 mb-3 fw-normal"><img src="<?= base_url('Gracia_logo.png') ?>" style="width:60%;"></h1>
            <p class="text-muted mb-4">Silakan isi formulir untuk mendaftar</p>

            <form action="<?= base_url('daftar/process') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="mb-3 text-start">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap') ?>" placeholder="" required autofocus>
                </div>
                
                <div class="mb-3 text-start">
                    <label class="form-label">Nomor HP <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" id="no_hp" name="no_hp" value="<?= old('no_hp') ?>" placeholder="08xxxxxxxxxx" required>
                </div>
                
                <div class="mb-4 text-start">
                    <label class="form-label">Divisi <span class="text-danger">*</span></label>
                    <select class="form-select" id="divisi" name="divisi" required>
                        <option value="" disabled <?= old('divisi') == '' ? 'selected' : '' ?>></option>
                        <option value="Marketing" <?= old('divisi') == 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                        <option value="Produksi 1" <?= old('divisi') == 'Produksi 1' ? 'selected' : '' ?>>Produksi 1</option>
                        <option value="Produksi 2" <?= old('divisi') == 'Produksi 2' ? 'selected' : '' ?>>Produksi 2</option>
                        <option value="Produksi 4" <?= old('divisi') == 'Produksi 4' ? 'selected' : '' ?>>Produksi 4</option>
                        <option value="RND" <?= old('divisi') == 'RND' ? 'selected' : '' ?>>RND</option>
                    </select>
                </div>

                <button class="btn btn-gracia w-100 py-2 fw-bold mb-3" type="submit">Daftar</button>
                
                <a href="<?= base_url('login') ?>" class="text-decoration-none small" style="color: #2b3385;">Ke <b>Halaman Login</b></a>
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

    <script src="<?= base_url('vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
