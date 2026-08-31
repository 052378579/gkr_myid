let currentPage = 1;
let currentQuery = '';
let timeoutId = null;

const searchInput = document.getElementById('searchInput');
const resultBody = document.getElementById('resultBody');
const totalCount = document.getElementById('total-count');
const btnPrev = document.getElementById('btnPrev');
const btnNext = document.getElementById('btnNext');
const loader = document.getElementById('loader');

// Responsive Placeholder
function updatePlaceholder() {
    if (window.innerWidth <= 768) {
        searchInput.placeholder = "Cari: Kode, Nama Barang";
    } else {
        searchInput.placeholder = "Cari: Kode, Nama Barang, Finishing, atau Buyer";
    }
}
window.addEventListener('resize', updatePlaceholder);
updatePlaceholder();

// Live Search Event (Debounced)
searchInput.addEventListener('input', function (e) {
    currentQuery = e.target.value.trim();
    currentPage = 1; // Reset to page 1 on new search

    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
        fetchData();
    }, 300); // 300ms delay for performance
});

function changePage(direction) {
    currentPage += direction;
    if (currentPage < 1) currentPage = 1;
    fetchData();
}

function fetchData() {
    loader.style.display = 'block';

    fetch(`/erp/api/search?q=${encodeURIComponent(currentQuery)}&page=${currentPage}`)
        .then(response => response.json())
        .then(data => {
            loader.style.display = 'none';
            if (data.status === 'success') {
                renderData(data.data);
                totalCount.innerText = data.total;

                // Handle Pagination Buttons
                btnPrev.disabled = (currentPage === 1);
                btnNext.disabled = (currentPage * data.limit) >= data.total;
            } else {
                console.error("API Error: ", data);
                resultBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-5">Error: ${data.message || 'Respons tidak valid dari server'}</td></tr>`;
            }
        })
        .catch(err => {
            loader.style.display = 'none';
            console.error("Fetch Error: ", err);
            resultBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Terjadi kesalahan koneksi.</td></tr>';
        });
}

function timeAgo(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString.replace(' ', 'T'));
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return `${diffInSeconds} detik yang lalu`;
    
    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) return `${diffInMinutes} menit lalu`;
    
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `${diffInHours} jam lalu`;
    
    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 7) return `${diffInDays} hari lalu`;
    
    if (diffInDays === 7) return `1 minggu lalu`;
    
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `update: ${day}/${month}/${year}`;
}

function renderData(items) {
    resultBody.innerHTML = '';

    if (items.length === 0) {
        resultBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-5">Tidak ada data ditemukan.</td></tr>';
        return;
    }

    items.forEach(item => {
        const tr = document.createElement('tr');

        let kodeClass = "text-primary"; // Default Biru Bootstrap
        if (item.kode_bom) {
            if (item.kode_bom.startsWith("FG-1")) {
                kodeClass = "text-warning"; 
            } else if (item.kode_bom.startsWith("FG-2")) {
                kodeClass = "text-primary"; 
            } else if (item.kode_bom.startsWith("FG-3")) {
                kodeClass = "text-success"; 
            } else if (item.kode_bom.startsWith("FG-4")) {
                kodeClass = "text-danger"; 
            }
        }

        // Menggunakan bom_name untuk printview BOM jika tersedia, kalau tidak pakai kode_bom sebagai fallback
        const namaBomLink = item.bom_name && item.bom_name !== '-' ? item.bom_name : item.kode_bom;
        
        const kodeHtml = item.kode_bom
            ? `<a href="http://103.39.49.86:82/printview?doctype=BOM&name=${encodeURIComponent(namaBomLink)}&format=BOM%20Rincian&no_letterhead=0" target="_blank" class="${kodeClass}" style="text-decoration: none; font-weight: 600;">${item.kode_bom}</a>`
            : '-';
            
        let bomNameHtml = item.bom_name && item.bom_name !== '-' ? `<div class="text-muted small text-truncate">${item.bom_name}</div>` : '';

        let namaBarang = item.item_name || '-';
        let namaAsli = item.item_name || '';
        
        let finishingHtml = item.finishing && item.finishing !== '-' ? `<div class="text-muted small text-truncate d-none d-md-block">${item.finishing}</div>` : '';
        let buyerHtmlMobile = item.buyer && item.buyer !== '-' ? `<div class="text-muted small text-truncate d-block d-md-none" title="${item.buyer}">${item.buyer}</div>` : '';

        // Format Dimensi agar bagian dalam kurung turun ke baris baru
        let dimensiText = item.dimensi || '-';
        let dimensiHtml = dimensiText.replace(/(\s*\(.*?\))/, '<br>$1');
        
        let buyerText = item.buyer || '-';
        let timeAgoHtml = '';
        if (item.buyer && item.buyer !== '-' && item.erp_modified) {
            timeAgoHtml = `<div class="text-muted small fst-italic text-truncate" style="margin-top: 2px;">${timeAgo(item.erp_modified)}</div>`;
        }

        tr.innerHTML = `
            <td class="col-kode">
                <div class="text-truncate">${kodeHtml}</div>
                ${bomNameHtml}
            </td>
            <td class="col-nama">
                <div class="text-truncate" title="${namaAsli}">${namaBarang}</div>
                ${finishingHtml}
                ${buyerHtmlMobile}
            </td>
            <td class="col-dimensi d-none d-md-table-cell">${dimensiHtml}</td>
            <td class="col-buyer d-none d-md-table-cell">
                <div class="text-truncate" title="${buyerText}">${buyerText}</div>
                ${timeAgoHtml}
            </td>
        `;
        resultBody.appendChild(tr);
    });
}

// Fetch initial data on page load
fetchData();


