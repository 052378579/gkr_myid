<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Changelog - Gracia<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    body {
        background-color: #ffffff;
        color: #202124;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .changelog-container {
        max-width: 900px;
        margin: 60px auto;
        padding: 20px;
    }
    
    /* Header Area */
    .header-area {
        display: flex;
        align-items: center;
        margin-bottom: 50px;
    }
    .header-logo {
        height: 40px;
        width: auto;
        margin-right: 15px;
    }
    .header-title {
        font-size: 2.2rem;
        font-weight: 500;
        margin: 0;
        letter-spacing: -0.5px;
    }
    
    /* Table Headers */
    .column-headers {
        display: flex;
        padding-bottom: 15px;
        margin-bottom: 30px;
        font-size: 0.9rem;
        color: #5f6368;
        font-weight: 500;
    }
    .col-version {
        width: 25%;
        padding-right: 20px;
    }
    .col-description {
        width: 75%;
    }

    /* Version Item Row */
    .version-item {
        display: flex;
        margin-bottom: 40px;
    }
    
    /* Left Column: Version & Date */
    .version-meta {
        width: 25%;
        padding-right: 20px;
        padding-top: 5px;
    }
    .version-number {
        font-size: 0.95rem;
        color: #5f6368;
        margin-bottom: 2px;
    }
    .version-date {
        font-size: 0.85rem;
        color: #5f6368;
    }

    /* Right Column: Card */
    .version-card {
        width: 75%;
        background-color: #f8f9fa;
        border-radius: 16px;
        padding: 25px 30px;
    }
    
    /* Card Header: Title & Desc */
    .card-header-flex {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }
    .card-title {
        flex: 1;
        font-size: 1.1rem;
        font-weight: 500;
        color: #202124;
        margin: 0;
        line-height: 1.4;
    }
    .card-desc {
        flex: 1.2;
        font-size: 0.9rem;
        color: #5f6368;
        margin: 0;
        line-height: 1.5;
    }

    /* Accordion Customization */
    .accordion-custom {
        margin-top: 20px;
    }
    .accordion-item-custom {
        border: none;
        border-bottom: 1px solid #ebebeb;
        background: transparent;
        margin-bottom: 0;
    }
    .accordion-item-custom:last-child {
        border-bottom: none;
    }
    .accordion-button-custom {
        width: 100%;
        text-align: left;
        background: transparent;
        border: none;
        padding: 12px 0;
        font-size: 0.85rem;
        color: #5f6368;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: none !important;
    }
    .accordion-button-custom:focus {
        outline: none;
    }
    .accordion-button-custom i {
        font-size: 0.7rem;
        color: #9aa0a6;
        transition: transform 0.2s ease-in-out;
    }
    .accordion-button-custom[aria-expanded="true"] i {
        transform: rotate(180deg);
    }
    .accordion-body-custom {
        padding: 5px 0 15px 0;
        font-size: 0.85rem;
        color: #5f6368;
    }
    .accordion-body-custom ul {
        margin: 0;
        padding-left: 20px;
    }
    .accordion-body-custom li {
        margin-bottom: 6px;
    }
    .accordion-body-custom li:last-child {
        margin-bottom: 0;
    }
</style>

<nav class="navbar navbar-expand-lg sticky-top" style="background: rgba(43, 51, 133, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold text-white" href="<?= base_url('/') ?>">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Beranda
        </a>
    </div>
</nav>

<div class="changelog-container">
    <div class="header-area">
        
        <h1 class="header-title">Changelog</h1>
    </div>

    <div class="column-headers">
        <div class="col-version">Version</div>
        <div class="col-description">Description</div>
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
</div>

<?= $this->endSection() ?>
