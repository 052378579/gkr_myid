let erpDataJson = [];
document.addEventListener('DOMContentLoaded', () => {
    const dataScript = document.getElementById('erp-data-json');
    if (dataScript) {
        erpDataJson = JSON.parse(dataScript.textContent);
    }
});

function togglePrice(index) {
    const dataDiv = document.getElementById('price-' + index);
    const hiddenDiv = document.getElementById('hidden-' + index);
    const icon = document.getElementById('icon-' + index);
    
    if (dataDiv.style.display === 'none') {
        dataDiv.style.display = 'block';
        hiddenDiv.style.display = 'none';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
        icon.classList.replace('text-secondary', 'text-primary');
    } else {
        dataDiv.style.display = 'none';
        hiddenDiv.style.display = 'block';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
        icon.classList.replace('text-primary', 'text-secondary');
    }
}

function toggleModalMinPrice() {
    let el = document.getElementById('mdl-minprice');
    let val = document.getElementById('mdl-minprice-val').value;
    let icon = document.getElementById('mdl-minprice-icon');
    if (el.innerText === '***') {
        el.innerText = '$' + val;
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    } else {
        el.innerText = '***';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    }
}

function toggleModalSugPrice() {
    let el = document.getElementById('mdl-sugprice');
    let val = document.getElementById('mdl-sugprice-val').value;
    let icon = document.getElementById('mdl-sugprice-icon');
    if (el.innerText === '***') {
        el.innerText = '$' + val;
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    } else {
        el.innerText = '***';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    }
}

function showDetail(index) {
    const data = erpDataJson[index];
    if(!data) return;
    
    let kbom = data.kode_bom || '-';
    let mdlKode = document.getElementById('mdl-kode');
    mdlKode.innerText = kbom;
    mdlKode.className = 'badge fs-6 font-monospace shadow-sm';
    if (kbom.indexOf('FG-1') === 0) {
        mdlKode.classList.add('bg-warning', 'text-dark');
    } else if (kbom.indexOf('FG-2') === 0) {
        mdlKode.classList.add('bg-primary', 'text-white');
    } else if (kbom.indexOf('FG-3') === 0) {
        mdlKode.classList.add('bg-success', 'text-white');
    } else if (kbom.indexOf('FG-4') === 0) {
        mdlKode.classList.add('bg-danger', 'text-white');
    } else {
        mdlKode.classList.add('bg-light', 'text-dark', 'border');
    }
    document.getElementById('mdl-nama').innerText = data.item_name || '-';
    document.getElementById('mdl-dimensi').innerText = data.dimensi || '-';
    document.getElementById('mdl-finishing').innerText = data.finishing || '-';
    document.getElementById('mdl-packing').innerText = data.packing || '-';
    
    document.getElementById('mdl-buyer').innerText = data.buyer || '-';
    document.getElementById('mdl-load40').innerText = data.load_40 || '-';
    document.getElementById('mdl-load40hc').innerText = data.load_40_hc || '-';
    document.getElementById('mdl-tanggal').innerText = data.erp_modified || '-';
    
    let minp = parseFloat(data.minimum_selling_price || 0).toFixed(2);
    let sugp = parseFloat(data.suggested_selling_price || 0).toFixed(2);
    
    document.getElementById('mdl-minprice-val').value = minp;
    document.getElementById('mdl-sugprice-val').value = sugp;
    
    document.getElementById('mdl-minprice').innerText = '***';
    document.getElementById('mdl-sugprice').innerText = '***';
    document.getElementById('mdl-minprice-icon').className = 'fas fa-eye-slash text-success';
    document.getElementById('mdl-sugprice-icon').className = 'fas fa-eye-slash text-primary';
    
    document.getElementById('mdl-sync').innerText = data.updated_at || data.created_at || '-';
    
    var myModal = new bootstrap.Modal(document.getElementById('bomModal'), {
      keyboard: true
    });
    myModal.show();
}

window.togglePrice = togglePrice;
window.toggleModalMinPrice = toggleModalMinPrice;
window.toggleModalSugPrice = toggleModalSugPrice;
window.showDetail = showDetail;
