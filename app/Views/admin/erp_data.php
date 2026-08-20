<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('title') ?>Data ERP<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="gkr">   
    <!-- Tabel Utama Manajemen ERP -->
    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-header border-0 d-flex justify-content-between align-items-center pt-4 pb-2">
            <h5 class="mb-0 fw-bold" style="color: #2B3385;">
                <span class="d-none d-md-inline">Manajemen Data ERP</span>
                <span class="d-inline d-md-none">Data ERP</span>
            </h5>
            <form method="get" action="/admin/erp/data" class="d-flex gap-2 gap-md-3 align-items-center m-0 flex-nowrap">
                <div class="d-none d-md-flex align-items-center">
                    <label class="me-2 text-muted small mb-0 d-none d-md-inline-block">Tampilkan:</label>
                    <select name="perPage" class="form-select form-select-sm rounded-pill w-auto" onchange="this.form.submit()">
                        <option value="10" <?= (isset($perPage) && $perPage == 10) ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= (isset($perPage) && $perPage == 25) ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= (isset($perPage) && $perPage == 50) ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= (isset($perPage) && $perPage == 100) ? 'selected' : '' ?>>100</option>
                    </select>
                </div>
                <div class="input-group input-group-sm shadow-sm" style="max-width: 250px; min-width: 140px; border-radius: 50rem; overflow: hidden;">
                    <span class="input-group-text border-end-0 bg-body" style="border-top-left-radius: 50rem; border-bottom-left-radius: 50rem;"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0 shadow-none" placeholder="Cari Kode BOM..." value="<?= esc($search ?? '') ?>">
                    <button type="submit" class="d-none"></button>
                </div>
                <a href="/admin/erp" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm text-nowrap"><i class="fas fa-terminal me-1"></i> Mesin</a>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-tertiary">
                        <tr>
                            <th class="ps-4">Kode BOM</th>
                            <th>Nama Barang</th>
                            <th>Dimensi</th>
                            <th>Material</th>
                            <th>Weaving</th>
                            <th>Fabric</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($erpData)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Tidak ada data ERP ditemukan.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($erpData as $row): ?>
                            <tr>
                                <td class="ps-4"><span class="badge border font-monospace text-dark bg-light"><?= esc($row['kode_bom']) ?></span></td>
                                <td class="fw-medium text-truncate" style="max-width:200px;"><?= esc($row['item_name'] ?? '-') ?></td>
                                <td><?= esc($row['dimensi'] ?? '-') ?></td>
                                <td class="text-truncate" style="max-width:150px;"><?= esc($row['material'] ?? '-') ?></td>
                                <td><?= esc($row['weaving'] ?? '-') ?></td>
                                <td><?= esc($row['fabric'] ?? '-') ?></td>
                                <td class="pe-4 text-end text-nowrap">
                                    <button class="btn btn-sm btn-primary rounded-pill px-3"><i class="fas fa-edit me-1 d-none d-md-inline-block"></i>Edit</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer border-top-0 d-flex justify-content-between align-items-center py-3">
                <span class="text-muted small">Halaman <?= $pagerCurrent ?> dari <?= $pagerCount ?></span>
                <div class="btn-group">
                    <?= $pager->links('erpData', 'admin_pager') ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
