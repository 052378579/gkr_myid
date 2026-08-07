<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Changelog - Gracia<?= $this->endSection() ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('css/admin_versi.css') ?>?v=<?= ASSET_VERSION ?>">

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

<div class="changelog-container">
    <div class="header-area">
        
        <h1 class="header-title">Catatan perubahan</h1>
    </div>

    <div class="column-headers">
        <div class="col-version">Versi</div>
        <div class="col-description">Deskripsi</div>
    </div>

    <?php if (empty($changelog)): ?>
        <div class="text-center py-5 text-muted">Belum ada data riwayat rilis versi.</div>
    <?php else: ?>
        <?php foreach ($changelog as $index => $item): ?>
            <div class="version-item">
                <div class="version-meta">
                    <div class="version-number"><?= esc($item['versi']) ?></div>
                    <div class="version-date"><?= esc($item['tanggal_rilis_formatted']) ?></div>
                </div>
                
                <div class="version-card">
                    <div class="card-header-flex">
                        <h3 class="card-title"><?= esc($item['judul']) ?></h3>
                        <p class="card-desc"><?= esc($item['deskripsi']) ?></p>
                    </div>
                    
                    <div class="accordion-custom" id="accordion_<?= $index ?>">
                        <!-- Improvements -->
                        <div class="accordion-item-custom">
                            <button class="accordion-button-custom collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_imp_<?= $index ?>" aria-expanded="false" aria-controls="collapse_imp_<?= $index ?>">
                                <span>Improvements (<?= count($item['improvements'] ?? []) ?>)</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div id="collapse_imp_<?= $index ?>" class="collapse" data-bs-parent="#accordion_<?= $index ?>">
                                <div class="accordion-body-custom">
                                    <?php if (empty($item['improvements'])): ?>
                                        <span class="text-muted fst-italic">No improvements in this version.</span>
                                    <?php else: ?>
                                        <ul>
                                            <?php foreach ($item['improvements'] as $imp): ?>
                                                <li><?= esc($imp) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Fixes -->
                        <div class="accordion-item-custom">
                            <button class="accordion-button-custom collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_fix_<?= $index ?>" aria-expanded="false" aria-controls="collapse_fix_<?= $index ?>">
                                <span>Fixes (<?= count($item['fixes'] ?? []) ?>)</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div id="collapse_fix_<?= $index ?>" class="collapse" data-bs-parent="#accordion_<?= $index ?>">
                                <div class="accordion-body-custom">
                                    <?php if (empty($item['fixes'])): ?>
                                        <span class="text-muted fst-italic">No fixes in this version.</span>
                                    <?php else: ?>
                                        <ul>
                                            <?php foreach ($item['fixes'] as $fix): ?>
                                                <li><?= esc($fix) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Patches -->
                        <div class="accordion-item-custom">
                            <button class="accordion-button-custom collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_patch_<?= $index ?>" aria-expanded="false" aria-controls="collapse_patch_<?= $index ?>">
                                <span>Patches (<?= count($item['patches'] ?? []) ?>)</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div id="collapse_patch_<?= $index ?>" class="collapse" data-bs-parent="#accordion_<?= $index ?>">
                                <div class="accordion-body-custom">
                                    <?php if (empty($item['patches'])): ?>
                                        <span class="text-muted fst-italic">No patches in this version.</span>
                                    <?php else: ?>
                                        <ul>
                                            <?php foreach ($item['patches'] as $patch): ?>
                                                <li><?= esc($patch) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="pagination-container d-flex justify-content-center mt-5">
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm shadow-sm">
                <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $currentPage - 1 ?>" tabindex="-1" aria-disabled="true">
                        <i class="fas fa-chevron-left me-1"></i> Sebelumnya
                    </a>
                </li>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
                <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $currentPage + 1 ?>">
                        Selanjutnya <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>
