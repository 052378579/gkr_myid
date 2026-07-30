<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Awan Kata (Tren Pencarian)<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Animasi CSS Fade-In untuk memunculkan kanvas perlahan */
    #canvas-container {
        animation: fadeIn 1.5s ease-in-out forwards;
        opacity: 0;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    /* Animasi kelap-kelip berkelanjutan pada setiap kata (Twinkling) */
    @keyframes randomFade {
        0% { opacity: 0.15; }
        100% { opacity: 1; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">
    <div class="mt-4 mb-2 ps-4">
        <a href="<?= base_url('/') ?>">
            <img src="<?= base_url('Gracia_logo.png') ?>" alt="Gracia Logo" style="height: 38px;" class="mb-1">
        </a>
        <h6 class="text-muted fw-medium mb-1" style="letter-spacing: 0.5px;">Awan Kata (Tren Pencarian)</h6>
        <!-- Bilah Kemajuan Auto-Refresh -->
        <div class="progress" style="height: 4px; width: 220px; background-color: #e9ecef; border-radius: 4px;">
            <div id="refreshProgressBar" class="progress-bar" role="progressbar" style="width: 100%; background-color: #2B3385; transition: width 1s linear;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    <div class="px-3 text-center">
        <?php if (empty($wordList)): ?>
            <div class="py-5 text-muted">Belum ada data pencarian teks yang cukup untuk membuat awan kata.</div>
        <?php else: ?>
            <!-- Wadah HTML untuk WordCloud2.js (Mode Span DOM) -->
            <div id="canvas-container" style="width: 100%; height: 600px; display: flex; justify-content: center; align-items: center;">
                <div id="wordCloudCanvas" style="width: 100%; height: 100%; position: relative;"></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="fixed-bottom py-3 w-100" style="background-color: transparent; color: #70757a; font-size: 0.9rem;">
    <div class="text-center w-100 fw-medium">
        RND &copy; <?= date('Y') ?>
    </div>
    <div class="position-absolute" style="right: 20px; top: 50%; transform: translateY(-50%);">
        <span class="text-muted fw-medium"><?= esc($version) ?></span>
    </div>
</footer>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (!empty($wordList)): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.2.2/wordcloud2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const wordList = <?= json_encode($wordList) ?>;
    
    // Siapkan Canvas
    const container = document.getElementById('canvas-container');
    const canvas = document.getElementById('wordCloudCanvas');
    
    // Sesuaikan ukuran dengan wadah responsif
    // Karena menggunakan DOM/div, tidak butuh properti width/height kanvas statis
    // namun wordcloud2 js dapat membaca ukuran aslinya.

    // Fungsi warna gradasi Biru Dongker (Gracia Theme)
    function getGraciaColor() {
        const colors = ['#2B3385', '#3a44a1', '#1c2260', '#4c57c2', '#5e6acc', '#131742'];
        return colors[Math.floor(Math.random() * colors.length)];
    }

    // Opsi konfigurasi WordCloud
    const options = {
        list: wordList,
        // Gunakan clientWidth karena DOM element 'div' tidak memiliki atribut 'width'
        gridSize: Math.round(16 * (canvas.clientWidth || 1024) / 1024) || 16,
        weightFactor: function (size) {
            // Skala bobot logaritmik agar kata yang sangat sering tidak mendominasi berlebihan
            return Math.log(size + 1) * 15 + 15;
        },
        fontFamily: 'Inter, system-ui, -apple-system, sans-serif',
        color: getGraciaColor,
        rotateRatio: 0.3,
        rotationSteps: 2,
        backgroundColor: 'transparent',
        shape: 'circle',
        ellipticity: 1,
        wait: 40, // Animasi sekuensial (pop-in delay per kata)
        click: function(item) {
            // Arahkan pengguna ke halaman pencarian dengan kata yang diklik
            window.location.href = "<?= url_to('Search::index') ?>?q=" + encodeURIComponent(item[0]) + "&type=sites";
        },
        hover: function(item, dimension, event) {
            // Ubah kursor menjadi telunjuk (pointer) saat menyorot kata
            if (item) {
                canvas.style.cursor = 'pointer';
            } else {
                canvas.style.cursor = 'default';
            }
        }
    };

    // Render Awan Kata (ke dalam tag div, menghasilkan span)
    WordCloud(canvas, options);
    
    // Memberikan Nyawa (Efek Bernapas) saat rendering perakitan selesai!
    canvas.addEventListener('wordcloudstop', function (e) {
        const words = canvas.querySelectorAll('span');
        
        words.forEach(function(wordSpan) {
            // Pastikan kata bisa di-klik dan terasa interaktif
            wordSpan.style.cursor = 'pointer';
            
            // Random durasi nafas: 2 detik s/d 6 detik
            const duration = (Math.random() * 4 + 2).toFixed(2);
            // Random penundaan mulai: 0 detik s/d 5 detik
            const delay = (Math.random() * 5).toFixed(2);
            
            // Menyematkan nafas kehidupan
            wordSpan.style.animation = `randomFade ${duration}s infinite alternate ease-in-out ${delay}s`;
        });
    });
    
    // Responsif: Gambar ulang saat jendela diubah ukurannya
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            WordCloud(canvas, options);
        }, 200);
    });
    
    // Fitur Auto-Refresh 5 Menit (300 Detik)
    const totalSeconds = 300;
    let currentSeconds = totalSeconds;
    const progressBar = document.getElementById('refreshProgressBar');
    
    const refreshInterval = setInterval(function() {
        currentSeconds--;
        
        // Kalkulasi persentase lebar bilah
        const percentage = (currentSeconds / totalSeconds) * 100;
        progressBar.style.width = percentage + '%';
        progressBar.setAttribute('aria-valuenow', percentage);
        
        // Refresh halaman saat timer mencapai 0
        if (currentSeconds <= 0) {
            clearInterval(refreshInterval);
            window.location.reload();
        }
    }, 1000);
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>
