<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('title') ?>Dashboard KPI<?= $this->endSection() ?>



<?= $this->section('content') ?>
<div id="gkrDashboard">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #2B3385;">
                <i class="fas fa-chart-line me-2"></i>Dashboard
            </h4>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" @click="loadKpiData" :disabled="isLoading">
                <i class="fas fa-sync-alt me-1" :class="{'fa-spin': isLoading}"></i> Perbarui Data
            </button>
        </div>
    </div>

    <!-- Row 4 Cards KPI -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Barang Terindeks -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm rounded-4 kpi-card p-3 bg-body">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon bg-primary-subtle text-primary me-3">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Total Barang</div>
                        <h3 class="fw-bold mb-0" style="color: #2B3385;">{{ kpi.totalItems }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Klik Terakumulasi -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm rounded-4 kpi-card p-3 bg-body">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon bg-success-subtle text-success me-3">
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Total Klik</div>
                        <h3 class="fw-bold mb-0 text-success">{{ formatNumber(kpi.totalKlik) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Top Produk #1 -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm rounded-4 kpi-card p-3 bg-body">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon bg-warning-subtle text-warning me-3">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-medium">Produk Populer #1</div>
                        <h6 class="fw-bold mb-0 text-truncate" style="color: #2B3385;" :title="kpi.topProduct">
                            {{ kpi.topProduct }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Total Users (Karyawan) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm rounded-4 kpi-card p-3 bg-body">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon bg-info-subtle text-info me-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Total Users</div>
                        <h3 class="fw-bold mb-0 text-info">{{ kpi.totalUsers }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row Visualisasi ApexCharts & Tabel Detail Top 10 -->
    <div class="row g-4">
        <!-- Kolom Grafik Bar Charts ApexCharts -->
        <div class="col-12 col-lg-7 col-xl-8">
            <div class="card shadow-sm rounded-4 border-0 p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0" style="color: #2B3385;">
                        <i class="fas fa-chart-bar me-2"></i>10 Barang Sering Dicari
                    </h5>
                </div>
                <div class="chart-container">
                    <div id="apexTopChart"></div>
                </div>
            </div>
        </div>

        <!-- Kolom Rincian Ringkas Top 10 Barang -->
        <div class="col-12 col-lg-5 col-xl-4">
            <div class="card shadow-sm rounded-4 border-0 p-3 h-100">
                <div class="card-header border-0 bg-transparent pt-2 pb-3">
                    <h5 class="fw-bold mb-0" style="color: #2B3385;">
                        <i class="fas fa-list-ol me-2"></i>10 Barang Baru
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div v-for="(item, index) in kpi.top10" :key="item.id" class="list-group-item d-flex align-items-center border-0 px-0 py-2">
                            <span class="badge rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                  :class="index === 0 ? 'bg-warning text-dark' : index === 1 ? 'bg-secondary text-white' : index === 2 ? 'bg-dark text-white' : 'bg-light text-muted border'"
                                  style="width: 28px; height: 28px; font-size: 0.8rem;">
                                {{ index + 1 }}
                            </span>
                            <div class="me-2 overflow-hidden flex-grow-1">
                                <a :href="item.siteUrl || item.url" target="_blank" class="fw-medium text-body text-decoration-none d-block text-truncate small" :title="item.judul">
                                    {{ item.judul }}
                                </a>
                            </div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill small">
                                {{ item.klik }} Klik
                            </span>
                        </div>
                        <div v-if="kpi.top10.length === 0" class="text-center py-4 text-muted small">
                            Belum ada data popularitas barang.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- ApexCharts JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<?= $this->section('styles') ?>
<meta name="page-config" data-api-get-top-searched="<?= base_url('api/getTopSearched') ?>">
<?= $this->endSection() ?>
<script src="<?= base_url('js/admin_dashboard.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>
