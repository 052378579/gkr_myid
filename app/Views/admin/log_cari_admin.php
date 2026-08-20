<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('title') ?>Log Pencarian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">   
    <div class="card shadow-sm rounded-4 border-0 mt-3">
        <div class="card-header border-0 d-flex justify-content-between align-items-center pt-4 pb-2">
            <h5 class="mb-0 fw-bold">Riwayat Pencarian</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="">
                        <tr>
                            <th class="ps-4">Pengguna</th>
                            <th>Kata Kunci</th>
                            <th>Tipe</th>
                            <th>Hasil</th>
                            <th>Sumber</th>
                            <th>Waktu</th>
                            <th class="pe-4">Alamat IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logCari)): ?>
                            <?php foreach ($logCari as $log): ?>
                                <tr>
                                    <td class="ps-4"><?= esc($log['nama_lengkap'] ?? 'Tamu / Anonim') ?></td>
                                    <td><span class="fw-medium"><?= esc($log['kata_kunci']) ?></span></td>
                                    <td>
                                        <?php if (strpos(strtolower($log['tipe_pencarian']), 'gambar') !== false): ?>
                                            <span class="badge bg-primary rounded-pill"><i class="fas fa-image me-1"></i> <?= esc(ucwords($log['tipe_pencarian'])) ?></span>
                                        <?php elseif ($log['tipe_pencarian'] === 'situs'): ?>
                                            <span class="badge bg-info rounded-pill"><i class="fas fa-globe me-1"></i> Situs</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill"><i class="fas fa-font me-1"></i> Teks</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark rounded-pill border"><?= esc($log['jumlah_hasil']) ?></span></td>
                                    <td>
                                        <?php if (isset($log['source']) && strtolower($log['source']) === 'telegram'): ?>
                                            <span class="badge bg-primary rounded-pill" style="background-color: #0088cc !important;"><i class="fab fa-telegram-plane me-1"></i> Telegram</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill"><i class="fas fa-globe me-1"></i> Web</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-nowrap"><?= date('d/m/Y H:i:s', strtotime($log['waktu'])) ?></td>
                                    <td class="pe-4"><?= esc(in_array($log['alamat_ip'], ['127.0.0.1', '::1', 'localhost']) ? ($serverIP ?? '10.147.17.40') : $log['alamat_ip']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat pencarian.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer border-top-0 d-flex justify-content-between align-items-center py-3">
            <?php if (isset($pagerCariCount) && $pagerCariCount > 1): ?>
                <span class="text-muted small">Halaman <?= $pagerCariCurrent ?> dari <?= $pagerCariCount ?></span>
                <div class="btn-group">
                    <a href="<?= $pagerCariCurrent > 1 ? $pagerCari->getPageURI($pagerCariCurrent - 1, 'logCari') : '#' ?>" class="btn btn-sm btn-outline-secondary <?= $pagerCariCurrent > 1 ? '' : 'disabled' ?>">Sebelumnya</a>
                    <a href="<?= $pagerCariCurrent < $pagerCariCount ? $pagerCari->getPageURI($pagerCariCurrent + 1, 'logCari') : '#' ?>" class="btn btn-sm btn-outline-secondary <?= $pagerCariCurrent < $pagerCariCount ? '' : 'disabled' ?>">Berikutnya</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/admin_log.js') ?>?v=<?= ASSET_VERSION ?>"></script>
<?= $this->endSection() ?>
