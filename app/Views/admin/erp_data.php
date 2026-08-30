<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('title') ?>Data ERP<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="gkr">   
    <!-- Tabel Utama Manajemen ERP -->
    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-header border-0 d-flex justify-content-between align-items-center pt-4 pb-2">
            <h5 class="mb-0 fw-bold" style="color: #2B3385;">
                <span class="d-none d-md-inline">Pusat Data ERP</span>
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
                            <th>Finishing</th>
                            <th>Buyer</th>
                            <th class="text-center">Load (40/HC)</th>
                            <th class="pe-4">Harga (USD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($erpData)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Tidak ada data ERP ditemukan.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($erpData as $index => $row): ?>
                            <tr>
                                <?php
                                    $kbom = esc($row['kode_bom']);
                                    $kbom_class = "bg-light text-dark border";
                                    if (strpos($kbom, "FG-1") === 0) {
                                        $kbom_class = "bg-warning text-dark";
                                    } elseif (strpos($kbom, "FG-2") === 0) {
                                        $kbom_class = "bg-primary text-white";
                                    } elseif (strpos($kbom, "FG-3") === 0) {
                                        $kbom_class = "bg-success text-white";
                                    } elseif (strpos($kbom, "FG-4") === 0) {
                                        $kbom_class = "bg-danger text-white";
                                    }
                                ?>
                                <td class="ps-4">
                                    <span class="badge font-monospace <?= $kbom_class ?>" style="cursor:pointer; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1" onclick="showDetail(<?= $index ?>)" title="Klik untuk lihat detail">
                                        <?= $kbom ?>
                                    </span>
                                </td>
                                <td class="fw-medium text-truncate" style="max-width:200px;" title="<?= esc($row['item_name'] ?? '-') ?>"><?= esc($row['item_name'] ?? '-') ?></td>
                                <td><?= esc($row['dimensi'] ?? '-') ?></td>
                                <td class="text-truncate" style="max-width:150px;" title="<?= esc($row['finishing'] ?? '-') ?>"><?= esc($row['finishing'] ?? '-') ?></td>
                                <td><?= esc($row['buyer'] ?? '-') ?></td>
                                <td class="text-center fw-medium text-muted">
                                    <?= esc($row['load_40'] ?? '-') ?> <span class="fw-light mx-1">/</span> <?= esc($row['load_40_hc'] ?? '-') ?>
                                </td>
                                <td class="pe-4 text-nowrap">
                                    <?php 
                                        $min = number_format((float)($row['minimum_selling_price'] ?? 0), 2);
                                        $saran = number_format((float)($row['suggested_selling_price'] ?? 0), 2);
                                    ?>
                                    <div class="d-flex justify-content-start align-items-center gap-2" style="min-width: 150px;">
                                        <button class="btn btn-sm btn-light border rounded-circle flex-shrink-0" style="width: 30px; height: 30px; padding: 0;" onclick="togglePrice(<?= $index ?>)" title="Toggle Privasi Harga">
                                            <i class="fas fa-eye-slash text-secondary" id="icon-<?= $index ?>"></i>
                                        </button>
                                        <div class="text-muted small text-start price-data" id="price-<?= $index ?>" style="display: none;">
                                            <div>Min: <span class="text-success fw-bold">$<?= $min ?></span></div>
                                            <div>Saran: <span class="text-success fw-bold">$<?= $saran ?></span></div>
                                        </div>
                                        <div class="text-muted small text-start price-hidden" id="hidden-<?= $index ?>">
                                            <div>Min: <span class="fw-bold">****</span></div>
                                            <div>Saran: <span class="fw-bold">****</span></div>
                                        </div>
                                    </div>
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

<!-- Modal Box Detail BOM -->
<div class="modal fade" id="bomModal" tabindex="-1" aria-labelledby="bomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold d-flex align-items-center flex-wrap gap-2" id="bomModalLabel" style="color: #2B3385;">
                    <div><i class="fas fa-box-open me-2 text-primary"></i>Kode BOM</div>
                    <span id="mdl-kode" class="badge fs-6 font-monospace shadow-sm"></span>
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                
                <!-- Tabular 2 Kolom -->
                <div class="row g-4">
                    <div class="col-md-6">

                        <table class="table table-sm table-borderless">
                            <tr>
                                <td colspan="2" class="pb-3">
                                    <div class="text-muted small mb-1">Nama Barang</div>
                                    <div id="mdl-nama" class="fw-bold text-dark text-break fs-6" style="line-height: 1.3;"></div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Dimensi ( w x d x h )</td>
                                <td id="mdl-dimensi" class="fw-medium text-dark"></td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Finishing</td>
                                <td id="mdl-finishing" class="fw-medium text-dark"></td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Packing</td>
                                <td id="mdl-packing" class="fw-medium text-dark"></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted small w-50">Buyer</td>
                                <td id="mdl-buyer" class="fw-medium text-dark"></td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Load (40)</td>
                                <td id="mdl-load40" class="fw-medium text-dark"></td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Load (40 HC)</td>
                                <td id="mdl-load40hc" class="fw-medium text-dark"></td>
                            </tr>
                            <tr>
                                <td class="text-muted small">Tanggal BOM</td>
                                <td id="mdl-tanggal" class="fw-medium text-dark"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-2">
                    <div class="col-12">
                        <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Harga (USD)</h6>
                        <div class="d-flex gap-4">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3 flex-fill text-center border border-success border-opacity-25 position-relative">
                                <button class="btn btn-sm btn-link position-absolute top-0 end-0 mt-2 me-2 shadow-none p-0" onclick="toggleModalMinPrice()">
                                    <i class="fas fa-eye-slash text-success" id="mdl-minprice-icon"></i>
                                </button>
                                <div class="text-success small fw-bold mb-1">MINIMUM SELLING PRICE</div>
                                <div class="fs-4 text-success fw-bolder" id="mdl-minprice">***</div>
                                <input type="hidden" id="mdl-minprice-val" value="">
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3 flex-fill text-center border border-primary border-opacity-25 position-relative">
                                <button class="btn btn-sm btn-link position-absolute top-0 end-0 mt-2 me-2 shadow-none p-0" onclick="toggleModalSugPrice()">
                                    <i class="fas fa-eye-slash text-primary" id="mdl-sugprice-icon"></i>
                                </button>
                                <div class="text-primary small fw-bold mb-1">SUGGESTED SELLING PRICE</div>
                                <div class="fs-4 text-primary fw-bolder" id="mdl-sugprice">***</div>
                                <input type="hidden" id="mdl-sugprice-val" value="">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end text-muted" style="font-size: 0.7rem;">
                    Sinkronisasi Terakhir: <span id="mdl-sync"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="erp-data-json"><?= json_encode($erpData) ?></script>`n<script src="<?= base_url('js/admin_erp_data.js') ?>?v=<?= ASSET_VERSION ?>"></script>
<?= $this->endSection() ?>

