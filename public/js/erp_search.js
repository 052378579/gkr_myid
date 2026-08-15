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
        searchInput.placeholder = "Cari: Kode, Nama Barang, Material, Warna, Anyam, atau Fabric";
    }
}
window.addEventListener('resize', updatePlaceholder);
updatePlaceholder();

// Live Search Event (Debounced)
searchInput.addEventListener('input', function(e) {
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
            }
        })
        .catch(err => {
            loader.style.display = 'none';
            console.error("Fetch Error: ", err);
            resultBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Terjadi kesalahan koneksi.</td></tr>';
        });
}

function renderData(items) {
    resultBody.innerHTML = '';
    
    if (items.length === 0) {
        resultBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-5">Tidak ada data ditemukan.</td></tr>';
        return;
    }

    items.forEach(item => {
        const tr = document.createElement('tr');
        
        // Cek isi weaving dan fabric
        const weavingHtml = item.weaving ? `<span class="col-weaving">[W: ${item.weaving}]</span>` : '';
        const fabricHtml = item.fabric ? `<span class="col-fabric">[F: ${item.fabric}]</span>` : '';
        
        // Cek kata 'estimasi' atau 'cancel' (case-insensitive) di semua kolom data
        const rowDataString = Object.values(item).join(' ').toLowerCase();
        if (rowDataString.includes('estimasi') || rowDataString.includes('cancel')) {
            tr.classList.add('row-estimasi');
        }
        
        let kodeColor = "#1e3a8a"; // Default Biru Gracia
        if (item.kode_bom) {
            if (item.kode_bom.startsWith("FG-1")) {
                kodeColor = "#fd7e14"; // Orange Bootstrap
            } else if (item.kode_bom.startsWith("FG-2")) {
                kodeColor = "#0d6efd"; // Primary Bootstrap
            } else if (item.kode_bom.startsWith("FG-3")) {
                kodeColor = "#198754"; // Success Bootstrap
            } else if (item.kode_bom.startsWith("FG-4")) {
                kodeColor = "#dc3545"; // Danger Bootstrap
            }
        }
        
        const kodeHtml = item.kode_bom 
            ? `<a href="http://103.39.49.86:82/desk#Form/Item/${item.kode_bom}" target="_blank" style="text-decoration: none; color: ${kodeColor}; font-weight: 600;">${item.kode_bom}</a>` 
            : '-';
        
        let materialHtml = item.material ? item.material.replace(/FRAME/gi, '<strong>$&</strong>').replace(/\+/g, '<strong>+</strong>').replace(/estimasi/gi, '<strong>$&</strong>') : '-';
        // Format Dimensi agar bagian dalam kurung turun ke baris baru
        let dimensiText = item.dimensi || '-';
        let dimensiHtml = dimensiText.replace(/(\s*\(.*?\))/, '<br>$1');

        
        tr.innerHTML = `
            <td class="col-kode">${kodeHtml}</td>
            <td class="col-nama">${item.item_name || '-'}</td>
            <td class="col-dimensi d-none d-md-table-cell" style="min-width: 150px;">${dimensiHtml}</td>
            <td class="col-material d-none d-md-table-cell">
                ${materialHtml} 
                ${weavingHtml} 
                ${fabricHtml}
            </td>
        `;
        resultBody.appendChild(tr);
    });
}

// Fetch initial data on page load
fetchData();
