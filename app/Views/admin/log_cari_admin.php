<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('title') ?>Log Pencarian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style type="text/css">
@media print { 
    .no-print, .sidebar, header, .card-footer, nav { display: none !important; } 
    .card { border: none !important; box-shadow: none !important; } 
    body { background-color: #fff !important; }
}
</style>
<div class="container-fluid px-0">   
    <div class="card shadow-sm rounded-4 border-0 mt-3">
        <div class="card-header border-0 d-flex flex-wrap justify-content-between align-items-center pt-4 pb-2 gap-2">
            <h5 class="mb-0 fw-bold">Riwayat Pencarian</h5>
            
            <div class="d-flex align-items-center gap-2 no-print">
                <form method="GET" class="d-flex gap-2 align-items-center m-0">
                    <select name="tipe" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Semua Tipe</option>
                        <option value="gambar" <?= (isset($filterTipe) && $filterTipe === 'gambar') ? 'selected' : '' ?>>Gambar</option>
                        <option value="situs" <?= (isset($filterTipe) && $filterTipe === 'situs') ? 'selected' : '' ?>>Situs</option>
                        <option value="teks" <?= (isset($filterTipe) && $filterTipe === 'teks') ? 'selected' : '' ?>>Teks</option>
                    </select>
                    
                    <select name="sumber" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Semua Sumber</option>
                        <option value="Web" <?= (isset($filterSumber) && $filterSumber === 'Web') ? 'selected' : '' ?>>Web</option>
                        <option value="WhatsApp" <?= (isset($filterSumber) && $filterSumber === 'WhatsApp') ? 'selected' : '' ?>>WhatsApp</option>
                        <option value="Telegram" <?= (isset($filterSumber) && $filterSumber === 'Telegram') ? 'selected' : '' ?>>Telegram</option>
                        <option value="Web AI" <?= (isset($filterSumber) && $filterSumber === 'Web AI') ? 'selected' : '' ?>>Web AI</option>
                    </select>
                    
                    <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </form>

                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" type="button" id="dropdownExport" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-download"></i> Ekspor
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="dropdownExport">
                        <li>
                            <a class="dropdown-item" href="<?= base_url('admin/log/cari/export?format=csv' . (!empty($filterTipe) ? '&tipe=' . $filterTipe : '') . (!empty($filterSumber) ? '&sumber=' . urlencode($filterSumber) : '')) ?>">
                                <i class="fas fa-file-csv me-2 text-success"></i> Unduh CSV
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('admin/log/cari/export?format=excel' . (!empty($filterTipe) ? '&tipe=' . $filterTipe : '') . (!empty($filterSumber) ? '&sumber=' . urlencode($filterSumber) : '')) ?>">
                                <i class="fas fa-file-excel me-2 text-success"></i> Unduh Excel
                            </a>
                        </li>
                    </ul>
                </div>
                
                <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" onclick="window.print()">
                    <i class="fas fa-print"></i> Cetak
                </button>
            </div>
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
                                    <td><span class="fw-medium text-nowrap"><?= $log['kata_kunci'] === 'UPLOADED_IMAGE' ? '<span class="text-secondary"><i class="fas fa-camera me-1"></i> Foto</span>' : esc($log['kata_kunci']) ?></span></td>
                                    <td>
                                        <?php 
                                        $tipe = strtolower($log['tipe_pencarian']);
                                        if ($log['kata_kunci'] === 'UPLOADED_IMAGE'): ?>
                                            <span class="badge bg-success rounded-pill"><i class="fas fa-camera me-1"></i> AI Vision</span>
                                        <?php elseif (strpos($tipe, 'gambar') !== false): ?>
                                            <span class="badge bg-primary rounded-pill"><i class="fas fa-image me-1"></i> Gambar</span>
                                        <?php elseif ($tipe === 'situs'): ?>
                                            <span class="badge bg-info rounded-pill"><i class="fas fa-globe me-1"></i> Situs</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill"><i class="fas fa-font me-1"></i> Teks</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark rounded-pill border"><?= esc($log['jumlah_hasil']) ?></span></td>
                                    <td>
                                        <?php 
                                        $sumber = isset($log['source']) ? strtolower($log['source']) : 'web';
                                        if ($sumber === 'telegram'): ?>
                                            <span class="badge rounded-pill" style="background-color: #0088cc; color: white;"><i class="fab fa-telegram-plane me-1"></i> Telegram</span>
                                        <?php elseif ($sumber === 'whatsapp'): ?>
                                            <span class="badge rounded-pill" style="background-color: #25D366; color: white;"><i class="fab fa-whatsapp me-1"></i> WhatsApp</span>
                                        <?php elseif (strpos($sumber, 'web ai') !== false || $sumber === 'web_ai'): ?>
                                            <span class="badge bg-dark rounded-pill text-white"><i class="fas fa-robot me-1"></i> Web AI</span>
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
