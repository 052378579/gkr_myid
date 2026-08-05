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
                                <img src="${img.imageUrl}" alt="Thumbnail" class="img-fluid rounded mb-2" style="max-height: 120px; object-fit: cover; width: 100%;" onerror="this.onerror=null; this.src='${window.AppConfig.logoUrl}';">
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
                const response = await fetch(window.AppConfig.apiUrl);
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
