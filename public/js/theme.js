/**
 * theme.js
 * Skrip untuk mengelola Mode Gelap & Terang secara otomatis berdasarkan waktu
 * dan menyimpan preferensi manual pengguna menggunakan localStorage.
 */

(function () {
    const THEME_STORAGE_KEY = 'gkr_theme';
    const htmlElement = document.documentElement;

    // Fungsi untuk menetapkan tema dan memperbarui ikon
    function setTheme(theme) {
        htmlElement.setAttribute('data-bs-theme', theme);
        
        // Memperbarui ikon jika elemen tersedia
        const themeIcon = document.getElementById('themeIcon');
        if (themeIcon) {
            if (theme === 'dark') {
                themeIcon.innerHTML = '🌙 Mode Gelap'; // Ikon bulan untuk gelap
            } else {
                themeIcon.innerHTML = '☀️ Mode Terang'; // Ikon matahari untuk terang
            }
        }
    }

    // Fungsi inisialisasi awal
    function initializeTheme() {
        const storedTheme = localStorage.getItem(THEME_STORAGE_KEY);
        
        if (storedTheme) {
            // Jika pengguna sudah pernah memilih secara manual, prioritaskan
            setTheme(storedTheme);
        } else {
            // Jika belum ada, gunakan logika waktu otomatis
            const currentHour = new Date().getHours();
            
            // 06:00 - 17:59 (Terang), 18:00 - 05:59 (Gelap)
            if (currentHour >= 6 && currentHour < 18) {
                setTheme('light');
            } else {
                setTheme('dark');
            }
        }
    }

    // Eksekusi inisialisasi segera (mencegah kedipan gaya CSS)
    initializeTheme();

    // Pasang Event Listener setelah DOM sepenuhnya dimuat
    document.addEventListener('DOMContentLoaded', () => {
        // Panggil lagi untuk memperbarui teks ikon karena saat initializeTheme berjalan, DOM mungkin belum siap
        initializeTheme();

        const themeToggleBtn = document.getElementById('themeToggleBtn');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                // Terapkan perubahan
                setTheme(newTheme);
                
                // Simpan ke localStorage agar tidak ter-reset
                localStorage.setItem(THEME_STORAGE_KEY, newTheme);
            });
        }
    });
})();
