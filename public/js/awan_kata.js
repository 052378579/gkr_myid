document.addEventListener('DOMContentLoaded', function () {
    const wordList = window.wordListData || [];
    const canvas = document.getElementById('wordCloudCanvas');

    if (!canvas || !wordList.length) return;

    // Fungsi warna gradasi Biru Dongker (Gracia Theme)
    function getGraciaColor() {
        const colors = ['#2B3385', '#3a44a1', '#1c2260', '#4c57c2', '#5e6acc', '#131742'];
        return colors[Math.floor(Math.random() * colors.length)];
    }

    // Opsi konfigurasi WordCloud
    const searchUrl = window.AppConfig?.searchUrl || '/cari';
    const options = {
        list: wordList,
        gridSize: Math.round(16 * (canvas.clientWidth || 1024) / 1024) || 16,
        weightFactor: function (size) {
            return Math.log(size + 1) * 15 + 15;
        },
        fontFamily: 'Inter, system-ui, -apple-system, sans-serif',
        color: getGraciaColor,
        rotateRatio: 0.3,
        rotationSteps: 2,
        backgroundColor: 'transparent',
        shape: 'circle',
        ellipticity: 1,
        wait: 40,
        click: function (item) {
            window.location.href = searchUrl + "?q=" + encodeURIComponent(item[0]) + "&type=sites";
        },
        hover: function (item, dimension, event) {
            if (item) {
                canvas.style.cursor = 'pointer';
            } else {
                canvas.style.cursor = 'default';
            }
        }
    };

    // Render Awan Kata
    if (typeof WordCloud === 'function') {
        WordCloud(canvas, options);
    }

    // Efek Bernapas saat rendering selesai
    canvas.addEventListener('wordcloudstop', function (e) {
        const words = canvas.querySelectorAll('span');
        words.forEach(function (wordSpan) {
            wordSpan.style.cursor = 'pointer';
            const duration = (Math.random() * 4 + 2).toFixed(2);
            const delay = (Math.random() * 5).toFixed(2);
            wordSpan.style.animation = `randomFade ${duration}s infinite alternate ease-in-out ${delay}s`;
        });
    });

    // Responsif
    let resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            if (typeof WordCloud === 'function') {
                WordCloud(canvas, options);
            }
        }, 200);
    });

    // Fitur Auto-Refresh 5 Menit (300 Detik)
    const totalSeconds = 300;
    let currentSeconds = totalSeconds;
    const progressBar = document.getElementById('refreshProgressBar');

    if (progressBar) {
        const refreshInterval = setInterval(function () {
            currentSeconds--;
            const percentage = (currentSeconds / totalSeconds) * 100;
            progressBar.style.width = percentage + '%';
            progressBar.setAttribute('aria-valuenow', percentage);

            if (currentSeconds <= 0) {
                clearInterval(refreshInterval);
                window.location.reload();
            }
        }, 1000);
    }
});
