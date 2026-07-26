<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/profile.css') ?>?v=<?= time() ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<nav class="navbar navbar-expand-lg sticky-top" style="background: rgba(43, 51, 133, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold text-white" href="<?= base_url('/') ?>">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Beranda
        </a>
        <button class="btn text-white border-0 d-flex align-items-center" id="themeToggleBtn" type="button" style="font-weight: 500;">
            <span id="themeIcon">☀️ Mode Terang</span>
        </button>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            

            <div class="profile-glass p-4 p-md-5">
                <h3 class="fw-bold brand-color mb-4 text-center">Profil Karyawan</h3>
                
                <form action="<?= base_url('profile/update') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="text-center mb-4">
                        <?php 
                            $avatarUrl = '';
                            if (!empty($user['foto_profil'])) {
                                $avatarUrl = base_url('dokumen/karyawan/' . $user['foto_profil']);
                            } else {
                                $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($user['nama_lengkap']) . "&background=2B3385&color=fff";
                            }
                        ?>
                        <img id="avatar-img" src="<?= $avatarUrl ?>" alt="Foto Profil" class="avatar-preview mb-2">
                        
                        <div class="upload-btn-wrapper d-block">
                            <button class="btn btn-sm btn-light rounded-pill shadow-sm border"><i class="fas fa-camera"></i> Ubah Foto</button>
                            <input type="file" name="foto_profil" id="foto_profil" accept=".jpg,.jpeg,.png" onchange="previewImage(this);" />
                        </div>
                        <div class="form-text mt-2 small">Format JPG/PNG, Maks. 2MB</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-medium text-secondary">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control form-control-custom" value="<?= esc($user['nama_lengkap']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-medium text-secondary">Divisi</label>
                            <select name="divisi" class="form-control form-control-custom" required>
                                <option value="" disabled>Pilih Divisi</option>
                                <option value="Marketing" <?= $user['divisi'] == 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                                <option value="Produksi 1" <?= $user['divisi'] == 'Produksi 1' ? 'selected' : '' ?>>Produksi 1</option>
                                <option value="Produksi 2" <?= $user['divisi'] == 'Produksi 2' ? 'selected' : '' ?>>Produksi 2</option>
                                <option value="Produksi 4" <?= $user['divisi'] == 'Produksi 4' ? 'selected' : '' ?>>Produksi 4</option>
                                <option value="RND" <?= $user['divisi'] == 'RND' ? 'selected' : '' ?>>RND</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-medium text-secondary">Nomor HP</label>
                            <input type="tel" name="no_hp" class="form-control form-control-custom" value="<?= esc($user['no_hp']) ?>" disabled>
                            <div class="form-text text-danger" style="font-size: 0.75rem; white-space: nowrap;"><i class="fas fa-info-circle"></i> Hubungi Admin untuk ubah Nomor HP</div>
                        </div>
                    </div>

                    <!--<div class="mb-4">
                        <label class="form-label small fw-medium text-secondary">Kode Akses / Sandi</label>
                        <input type="password" name="access_token" class="form-control form-control-custom" placeholder="Isi untuk mengubah sandi" value="">
                        <div class="form-text small">Kosongkan jika tidak ingin merubah sandi.</div>
                    </div>

                    <hr class="mb-4 opacity-25">-->

                    <button type="submit" class="btn btn-brand w-100 rounded-pill py-2 fw-bold">
                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top">
                    <a href="<?= base_url('logout') ?>" class="text-danger text-decoration-none small fw-medium">
                        <i class="fas fa-sign-out-alt me-1"></i> Keluar / Logout
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/profile.js') ?>"></script>
<?= $this->endSection() ?>
