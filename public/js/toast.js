document.addEventListener('DOMContentLoaded', () => {
    const toastEl = document.getElementById('globalToast');
    if (toastEl) {
        if (typeof bootstrap !== 'undefined') {
            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
        } else {
            // Beri waktu 500ms jika script bootstrap dimuat belakangan (defer/async)
            setTimeout(() => {
                if (typeof bootstrap !== 'undefined') {
                    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
                    toast.show();
                }
            }, 500);
        }
    }
});
