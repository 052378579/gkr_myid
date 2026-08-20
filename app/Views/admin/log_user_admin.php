<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('title') ?>Log User<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">   
    <div class="card shadow-sm rounded-4 border-0 mt-3">
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/admin_log.js') ?>?v=<?= ASSET_VERSION ?>"></script>
<?= $this->endSection() ?>
