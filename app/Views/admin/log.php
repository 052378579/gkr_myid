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
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2">
                    <h5 class="mb-0 fw-bold">Riwayat Akses Pengguna</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
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
                                            <td><?= esc($log['alamat_ip'] === '::1' ? '127.0.0.1' : $log['alamat_ip']) ?></td>
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
            </div>
        </div>

        <!-- Tabel Log Cari -->
        <div class="tab-pane fade show active" id="cari-tab-pane" role="tabpanel" aria-labelledby="cari-tab" tabindex="0">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2">
                    <h5 class="mb-0 fw-bold">Riwayat Pencarian</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Pengguna</th>
                                    <th>Kata Kunci / Input</th>
                                    <th>Tipe</th>
                                    <th>Hasil</th>
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
                                                <?php if ($log['tipe_pencarian'] === 'gambar'): ?>
                                                    <span class="badge bg-primary rounded-pill"><i class="fas fa-image me-1"></i> Gambar</span>
                                                <?php elseif ($log['tipe_pencarian'] === 'situs'): ?>
                                                    <span class="badge bg-info rounded-pill"><i class="fas fa-globe me-1"></i> Situs</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary rounded-pill"><i class="fas fa-font me-1"></i> Teks</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge bg-light text-dark rounded-pill border"><?= esc($log['jumlah_hasil']) ?></span></td>
                                            <td class="text-nowrap"><?= date('d/m/Y H:i:s', strtotime($log['waktu'])) ?></td>
                                            <td class="pe-4"><?= esc($log['alamat_ip'] === '::1' ? '127.0.0.1' : $log['alamat_ip']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat pencarian.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let reloadTimer;
    const duration = 300; // 5 minutes in seconds
    let elapsed = 0;
    const progressBar = document.getElementById('reloadProgressBar');
    
    // Helper function to manage timer
    function startTimer() {
        if (reloadTimer) clearInterval(reloadTimer);
        elapsed = 0;
        progressBar.style.display = 'block';
        progressBar.style.width = '0%';
        // Force reflow
        void progressBar.offsetWidth;
        
        reloadTimer = setInterval(() => {
            elapsed++;
            const percentage = (elapsed / duration) * 100;
            progressBar.style.width = percentage + '%';
            
            if (elapsed >= duration) {
                clearInterval(reloadTimer);
                window.location.reload();
            }
        }, 1000);
    }

    function stopTimer() {
        if (reloadTimer) clearInterval(reloadTimer);
        elapsed = 0;
        progressBar.style.display = 'none';
        progressBar.style.width = '0%';
    }

    // Initialize Bootstrap Tabs event listeners
    const logTabEl = document.getElementById('logTab');
    if (logTabEl) {
        logTabEl.addEventListener('shown.bs.tab', function (event) {
            const activeTabId = event.target.getAttribute('id');
            if (activeTabId === 'cari-tab') {
                startTimer();
            } else {
                stopTimer();
            }
        });
        
        // Initial check in case 'Log Cari' is active on load (though usually Log User is default)
        const activeTab = logTabEl.querySelector('.nav-link.active');
        if (activeTab && activeTab.id === 'cari-tab') {
            startTimer();
        }
    }
});
</script>
<?= $this->endSection() ?>
