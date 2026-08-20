document.addEventListener('DOMContentLoaded', function() {
    let reloadTimer;
    const duration = 300; // 5 minutes in seconds
    let elapsed = 0;
    const progressBar = document.getElementById('reloadProgressBar');
    
    function startTimer() {
        if (reloadTimer) clearInterval(reloadTimer);
        elapsed = 0;
        if(progressBar) {
            progressBar.style.display = 'block';
            progressBar.style.width = '0%';
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

    // Always start timer for log pages automatically
    startTimer();
});
