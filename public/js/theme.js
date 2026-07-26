/**
 * theme.js
 * Skrip untuk mengelola Mode Gelap & Terang secara otomatis berdasarkan preferensi OS,
 * waktu (fallback), dan menyimpan preferensi manual pengguna menggunakan localStorage.
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
                themeIcon.innerHTML = '🌙';
            } else {
                themeIcon.innerHTML = '☀️';
            }
        }
    }

    // Fungsi untuk mendapatkan tema otomatis berdasarkan Sistem OS atau Waktu
    function getAutomaticTheme() {
        // Prioritas 2: Cek Preferensi OS (System Dark Mode)
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
            return 'light';
        }

        // Prioritas 3 (Fallback): Waktu Otomatis (06:00 - 17:59 Terang)
        const currentHour = new Date().getHours();
        if (currentHour >= 6 && currentHour < 18) {
            return 'light';
        } else {
            return 'dark';
        }
    }

    // Fungsi inisialisasi awal
    function initializeTheme() {
        const storedTheme = localStorage.getItem(THEME_STORAGE_KEY);
        
        if (storedTheme) {
            // Prioritas 1: Jika pengguna sudah pernah memilih secara manual
            setTheme(storedTheme);
        } else {
            // Prioritas 2 & 3: Gunakan OS System Preference / Fallback Waktu
            setTheme(getAutomaticTheme());
        }
    }

    // Eksekusi inisialisasi segera (mencegah kedipan gaya CSS / FOUC)
    initializeTheme();

    // Pantau perubahan dari OS secara Real-time
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            // Hanya ganti secara realtime jika user BELUM pernah mengatur manual di localStorage
            if (!localStorage.getItem(THEME_STORAGE_KEY)) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });
    }

    // Pasang Event Listener setelah DOM sepenuhnya dimuat
    document.addEventListener('DOMContentLoaded', () => {
        // Panggil lagi untuk memperbarui ikon yang baru selesai dirender
        initializeTheme();

        const themeToggleBtn = document.getElementById('themeToggleBtn');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                // Terapkan perubahan
                setTheme(newTheme);
                
                // Simpan ke localStorage (Mengaktifkan Prioritas 1)
                localStorage.setItem(THEME_STORAGE_KEY, newTheme);
            });
        }
    });
})();
