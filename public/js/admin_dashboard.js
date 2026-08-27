const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const isLoading = ref(false);
        const kpi = ref({
            totalItems: 0,
            totalKlik: 0,
            totalBroken: 0,
            totalUsers: 0,
            topProduct: '-',
            top10: []
        });

        let chartInstance = null;

        const formatNumber = (num) => {
            if (num == null) return '0';
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        };

        const renderApexChart = (top10Data) => {
            const categories = top10Data.map(item => {
                const title = item.judul || item.title || 'Tanpa Nama';
                return title.length > 25 ? title.substring(0, 25) + '...' : title;
            });
            const seriesData = top10Data.map(item => parseInt(item.klik || item.clicks || 0));

            const options = {
                series: [{
                    name: 'Jumlah Klik',
                    data: seriesData
                }],
                chart: {
                    type: 'bar',
                    height: 380,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '55%',
                        borderRadius: 6,
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                colors: ['#2B3385'],
                dataLabels: {
                    enabled: true,
                    textAnchor: 'start',
                    style: {
                        colors: ['#2B3385'],
                        fontSize: '12px',
                        fontWeight: 'bold'
                    },
                    formatter: function (val) {
                        return val + ' Klik';
                    },
                    offsetX: 8
                },
                stroke: {
                    width: 0
                },
                xaxis: {
                    categories: categories,
                    labels: {
                        style: {
                            colors: '#6c757d',
                            fontSize: '11px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#2B3385',
                            fontWeight: '600',
                            fontSize: '12px'
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 3
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function (val) {
                            return val + ' Klik Terakumulasi';
                        }
                    }
                }
            };

            const chartEl = document.querySelector("#apexTopChart");
            if (chartEl) {
                if (chartInstance) {
                    try {
                        chartInstance.destroy();
                    } catch (e) {}
                    chartInstance = null;
                }
                chartInstance = new ApexCharts(chartEl, options);
                chartInstance.render();
            }
        };

        const loadKpiData = async () => {
            isLoading.value = true;
            try {
                const res = await fetch(window.AppConfig.apiGetTopSearched);
                const json = await res.json();
                if (json.status === 'sukses' || json.status === 'success') {
                    kpi.value = json.data;
                    renderApexChart(json.data.top10 || []);
                }
            } catch (e) {
                console.error("Gagal menarik data KPI:", e);
            } finally {
                isLoading.value = false;
            }
        };

        onMounted(() => {
            loadKpiData();
        });

        return {
            isLoading,
            kpi,
            formatNumber,
            loadKpiData
        };
    }
}).mount('#gkrDashboard');
