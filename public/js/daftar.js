document.addEventListener('DOMContentLoaded', function () {
    const divisiSelect = document.querySelector('select[name="divisi"]');
    if (divisiSelect) {
        divisiSelect.addEventListener('change', function () {
            this.style.color = '#212529';
        });
    }
});
