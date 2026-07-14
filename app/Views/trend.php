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

<style>
    [v-cloak] { display: none; }
    /* Padding left for the custom Y axis thumbnails */
    #gambarChart .apexcharts-yaxis {
        transform: translateX(50px);
    }
    #gambarChart .apexcharts-grid,
    #gambarChart .apexcharts-series,
    #gambarChart .apexcharts-xaxis {
        transform: translateX(40px);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    const { createApp, ref, onMounted } = Vue;

    createApp({
        setup() {
            const loading = ref(true);
            const topSites = ref([]);
            const topImages = ref([]);
            const combined = ref([]);

            const initCharts = () => {
                // Konfigurasi Warna
                const primaryColor = '#2B3385';
                const secondaryColor = '#4a54c4';
                
                // 1. Chart Gabungan (Grid / Column)
                // Memisahkan data site dan image untuk grouped bar chart
                let siteData = [];
                let imageData = [];
                let categories = [];
                
                // Urutkan combined berdasarkan clicks DESC
                combined.value.forEach((item, idx) => {
                    categories.push(`Rank ${idx+1}`);
                    if (item.tipe === 'site') {
                        siteData.push(item.clicks);
                        imageData.push(0);
                    } else {
                        siteData.push(0);
                        imageData.push(item.clicks);
                    }
                });

                new ApexCharts(document.querySelector("#gabunganChart"), {
                    series: [{
                        name: 'Situs',
                        data: siteData
                    }, {
                        name: 'Gambar',
                        data: imageData
                    }],
                    chart: {
                        type: 'area',
                        height: 350,
                        toolbar: { show: false },
                        fontFamily: 'inherit'
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2 },
                    xaxis: { categories: categories },
                    yaxis: { title: { text: 'Jumlah Klik' } },
                    fill: { 
                        type: 'gradient', 
                        gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.3, stops: [0, 90, 100] } 
                    },
                    colors: [primaryColor, '#00E396'],
                    tooltip: {
                        y: { formatter: function (val) { return val + " klik" } }
                    }
                }).render();

                // 2. Chart Situs (Horizontal Bar)
                new ApexCharts(document.querySelector("#situsChart"), {
                    series: [{
                        name: 'Klik',
                        data: topSites.value.map(s => s.clicks)
                    }],
                    chart: {
                        type: 'bar',
                        height: 400,
                        toolbar: { show: false },
                        fontFamily: 'inherit'
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 4,
                            barHeight: '70%',
                        }
                    },
                    colors: [primaryColor],
                    dataLabels: { enabled: true },
                    xaxis: { categories: topSites.value.map(s => s.title.length > 30 ? s.title.substring(0, 30) + '...' : s.title) },
                    tooltip: {
                        theme: 'light',
                        y: { title: { formatter: function() { return 'Klik: ' } } }
                    }
                }).render();

                // 3. Chart Gambar (Horizontal Bar dengan Margin Kiri untuk Thumbnail)
                new ApexCharts(document.querySelector("#gambarChart"), {
                    series: [{
                        name: 'Klik',
                        data: topImages.value.map(img => img.clicks)
                    }],
                    chart: {
                        type: 'bar',
                        height: 500,
                        toolbar: { show: false },
                        fontFamily: 'inherit'
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 4,
                            barHeight: '60%',
                        }
                    },
                    colors: [secondaryColor],
                    dataLabels: { enabled: true },
                    xaxis: { categories: topImages.value.map(img => img.title.length > 20 ? img.title.substring(0, 20) + '...' : img.title) },
                    grid: {
                        padding: { left: 50 } // Memberi ruang untuk absolute div thumbnail
                    },
                    tooltip: {
                        theme: 'light',
                        custom: function({series, seriesIndex, dataPointIndex, w}) {
                            const img = topImages.value[dataPointIndex];
                            return `
                                <div class="p-2 text-center" style="max-width: 200px;">
                                    <img src="${img.imageUrl}" alt="Thumbnail" class="img-fluid rounded mb-2" style="max-height: 120px; object-fit: cover; width: 100%;" onerror="this.onerror=null; this.src='<?= base_url('Gracia_logo.png') ?>';">
                                    <div class="fw-bold text-truncate" style="color: ${primaryColor}; font-size: 0.9rem;">${img.title}</div>
                                    <div class="text-muted small">${series[seriesIndex][dataPointIndex]} klik</div>
                                </div>
                            `;
                        }
                    }
                }).render();
            };

            onMounted(async () => {
                try {
                    const response = await fetch('/api/trend');
                    const json = await response.json();
                    topSites.value = json.topSites;
                    topImages.value = json.topImages;
                    combined.value = json.combined;
                    
                    setTimeout(() => {
                        initCharts();
                    }, 100);
                } catch (error) {
                    console.error("Gagal memuat data trend:", error);
                } finally {
                    loading.value = false;
                }
            });

            return {
                loading, topSites, topImages, combined
            };
        }
    }).mount('#trendApp');
</script>
<?= $this->endSection() ?>
