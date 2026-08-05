<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Trend Pencarian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4" id="trendApp" v-cloak>
    <h3 class="mb-4 text-center fw-bold" style="color: #2B3385;"><i class="fas fa-chart-line me-2"></i>Trend Pencarian (Top Clicks)</h3>

    <!-- Loading State -->
    <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem; color: #2B3385 !important;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div v-else>
        <!-- Gabungan (Grid / Column Chart) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold mb-3" style="color: #2B3385;">Gabungan Proporsi Top Clicks (Situs vs Gambar)</h5>
                        <div id="gabunganChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Situs & Gambar Stacked (Atas-Bawah Layout) -->
        <div class="row g-4">
            <!-- Top 10 Situs -->
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3" style="color: #2B3385;">Top 10 Situs Terbanyak Diklik</h5>
                        <div id="situsChart"></div>
                    </div>
                </div>
            </div>
            
            <!-- Top 10 Gambar -->
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3" style="color: #2B3385;">Top 10 Gambar Terbanyak Diklik</h5>
                        <div class="position-relative">
                            <div id="gambarChart"></div>
                            
                            <!-- Custom HTML Y-Axis Labels for Images -->
                            <div class="d-flex flex-column justify-content-around position-absolute" 
                                 style="top: 0; left: 0; width: 60px; height: 100%; padding-top: 15px; padding-bottom: 25px; pointer-events: none;">
                                <div v-for="(img, idx) in topImages" :key="idx" class="text-center d-flex align-items-center justify-content-center" style="flex: 1;">
                                    <img :src="img.imageUrl" :alt="img.title" 
                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; box-shadow: 0 2px 4px rgba(0,0,0,0.1); pointer-events: auto;"
                                         @error="img.imageUrl = '<?= base_url('Gracia_logo.png') ?>'"
                                         :title="img.title">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?= base_url('css/trend.css') ?>?v=<?= time() ?>">
<?= $this->section('styles') ?>
<meta name="page-config" 
    data-api-url="<?= base_url('api/trend') ?>"
    data-logo-url="<?= base_url('Gracia_logo.png') ?>">
<?= $this->endSection() ?>
<script src="<?= base_url('js/trend.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>
