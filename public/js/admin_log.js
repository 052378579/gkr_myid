document.addEventListener('DOMContentLoaded', function() {
    let reloadTimer;
    const duration = 300; // 5 minutes in seconds
    let elapsed = 0;
    const progressBar = document.getElementById('reloadProgressBar');
    
    // Helper function to manage timer
    function startTimer() {
        if (reloadTimer) clearInterval(reloadTimer);
        elapsed = 0;
        if(progressBar) {
            progressBar.style.display = 'block';
            progressBar.style.width = '0%';
            // Force reflow
            void progressBar.offsetWidth;
        }
        
        reloadTimer = setInterval(() => {
            elapsed++;
            const percentage = (elapsed / duration) * 100;
            if(progressBar) {
                progressBar.style.width = percentage + '%';
            }
            
            if (elapsed >= duration) {
                clearInterval(reloadTimer);
                window.location.reload();
            }
        }, 1000);
    }

    function stopTimer() {
        if (reloadTimer) clearInterval(reloadTimer);
        elapsed = 0;
        if(progressBar) {
            progressBar.style.display = 'none';
            progressBar.style.width = '0%';
        }
    }

    // Initialize Bootstrap Tabs event listeners
    const logTabEl = document.getElementById('logTab');
    if (logTabEl) {
        logTabEl.addEventListener('shown.bs.tab', function (event) {
            const activeTabId = event.target.getAttribute('id');
            if (activeTabId === 'cari-tab') {
                startTimer();
            } else {
                stopTimer();
            }
        });
        
        // Initial check in case 'Log Cari' is active on load (though usually Log User is default)
        const activeTab = logTabEl.querySelector('.nav-link.active');
        if (activeTab && activeTab.id === 'cari-tab') {
            startTimer();
        }
    }
});
