<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('title') ?>Log Aktivitas<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">   
    <div class="d-flex justify-content-between align-items-center mb-4">
        <ul class="nav nav-underline gap-1" id="logTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-4" id="cari-tab" data-bs-toggle="tab" data-bs-target="#cari-tab-pane" type="button" role="tab" aria-controls="cari-tab-pane" aria-selected="true" style="border-bottom-width: 3px; color: #2B3385 !important;">Log Cari</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4" id="user-tab" data-bs-toggle="tab" data-bs-target="#user-tab-pane" type="button" role="tab" aria-controls="user-tab-pane" aria-selected="false" style="border-bottom-width: 3px; color: #2B3385 !important;">Log User</button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="logTabContent">
        <!-- Tabel Log User -->
        <div class="tab-pane fade" id="user-tab-pane" role="tabpanel" aria-labelledby="user-tab" tabindex="0">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-header border-0 d-flex justify-content-between align-items-center pt-4 pb-2">
                    <h5 class="mb-0 fw-bold">Riwayat Akses Pengguna</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="">
                                <tr>
                                    <th class="ps-4">Pengguna</th>
                                    <th>Aktivitas</th>
                                    <th>Waktu</th>
                                    <th>Alamat IP</th>
                                    <th class="pe-4">User Agent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logUser)): ?>
                                    <?php foreach ($logUser as $log): ?>
                                        <tr>
                                            <td class="ps-4 text-nowrap"><?= esc($log['nama_lengkap'] ?? 'Tidak Diketahui (ID: '.$log['id_user'].')') ?></td>
                                            <td>
                                                <?php if ($log['aktivitas'] === 'masuk'): ?>
                                                    <span class="badge bg-success rounded-pill">Masuk</span>
                                                <?php elseif ($log['aktivitas'] === 'keluar'): ?>
                                                    <span class="badge bg-secondary rounded-pill">Keluar</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger rounded-pill">Gagal Masuk</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-nowrap"><?= date('d/m/Y H:i:s', strtotime($log['waktu'])) ?></td>
                                            <td><?= esc(in_array($log['alamat_ip'], ['127.0.0.1', '::1', 'localhost']) ? ($serverIP ?? '10.147.17.40') : $log['alamat_ip']) ?></td>
                                            <td class="pe-4" title="<?= esc($log['agen_pengguna']) ?>">
                                                <?php 
                                                    $isMobile = preg_match('/Mobile|Android|BlackBerry|iPhone|Windows Phone/i', $log['agen_pengguna']);
                                                    echo $isMobile ? '<i class="fas fa-mobile-alt me-1"></i> Mobile' : '<i class="fas fa-desktop me-1"></i> Desktop';
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat aktivitas pengguna.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer border-top-0 d-flex justify-content-between align-items-center py-3">
                    <?php if (isset($pagerUserCount) && $pagerUserCount > 1): ?>
                        <span class="text-muted small">Halaman <?= $pagerUserCurrent ?> dari <?= $pagerUserCount ?></span>
                        <div class="btn-group">
                            <a href="<?= $pagerUserCurrent > 1 ? $pagerUser->getPageURI($pagerUserCurrent - 1, 'logUser') : '#' ?>" class="btn btn-sm btn-outline-secondary <?= $pagerUserCurrent > 1 ? '' : 'disabled' ?>">Sebelumnya</a>
                            <a href="<?= $pagerUserCurrent < $pagerUserCount ? $pagerUser->getPageURI($pagerUserCurrent + 1, 'logUser') : '#' ?>" class="btn btn-sm btn-outline-secondary <?= $pagerUserCurrent < $pagerUserCount ? '' : 'disabled' ?>">Berikutnya</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tabel Log Cari -->
        <div class="tab-pane fade show active" id="cari-tab-pane" role="tabpanel" aria-labelledby="cari-tab" tabindex="0">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-header border-0 d-flex justify-content-between align-items-center pt-4 pb-2">
                    <h5 class="mb-0 fw-bold">Riwayat Pencarian</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="">
                                <tr>
                                    <th class="ps-4">Pengguna</th>
                                    <th>Kata Kunci / Input</th>
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
                                                    <span class="badge bg-secondary rounded-pill"><i class="fas fa-desktop me-1"></i> Web</span>
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
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/admin_log.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>
