(function() {
    window.AppConfig = window.AppConfig || {};
    
    // Ambil konfigurasi global
    const globalMeta = document.querySelector('meta[name="app-config"]');
    if (globalMeta) {
        Object.assign(window.AppConfig, globalMeta.dataset);
    }
    
    // Ambil konfigurasi halaman spesifik (bisa diletakkan dimana saja di HTML)
    const pageMeta = document.querySelector('meta[name="page-config"]');
    if (pageMeta) {
        Object.assign(window.AppConfig, pageMeta.dataset);
    }
})();
