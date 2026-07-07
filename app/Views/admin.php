<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Admin<?= $this->endSection() ?>

<?= $this->section('content') ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white glassmorphism fixed-top shadow-sm" style="background: rgba(255, 255, 255, 0.95) !important; backdrop-filter: blur(10px);">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('admin') ?>">
            <img src="<?= base_url('assets/images/Gracia_logo.png') ?>" alt="Gracia" style="height: 30px; width: auto;">
        </a>
        <div class="navbar-nav ms-auto fw-medium">
            <a class="nav-link" href="<?= base_url('/') ?>" style="color: #2B3385 !important;">Ke Beranda</a>
            <a class="nav-link" href="<?= base_url('crawl') ?>" style="color: #2B3385 !important;">Crawler</a>
        </div>
    </div>
</nav>

<div class="container mt-5 pt-5" id="gkr">   
    <div class="d-flex justify-content-between align-items-center mb-4">
        <ul class="nav nav-underline gap-3">
            <li class="nav-item">
                <button class="nav-link px-0" style="border-bottom-width: 3px; color: #2B3385 !important;" :class="{active: currentTab === 'sites'}" @click="currentTab = 'sites'">Daftar Situs</button>
            </li>
            <li class="nav-item">
                <button class="nav-link px-0" style="border-bottom-width: 3px; color: #2B3385 !important;" :class="{active: currentTab === 'images'}" @click="currentTab = 'images'">Daftar Gambar</button>
            </li>
        </ul>
        <div class="d-flex align-items-center">
            <label class="me-2 text-muted small">Tampilkan:</label>
            <select class="form-select form-select-sm rounded-pill w-auto" v-model.number="perPage" @change="currentPageSites=1; currentPageImages=1">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <!-- Tabel Situs -->
    <div v-if="currentTab === 'sites'" class="card shadow-sm rounded-4 border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Judul</th>
                            <th>URL</th>
                            <th>Klik</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="site in paginatedSites" :key="site.id">
                            <td class="ps-4">{{ site.id }}</td>
                            <td>{{ site.title }}</td>
                            <td><a :href="site.url" target="_blank" class="text-truncate d-inline-block" style="max-width:200px;">{{ site.url }}</a></td>
                            <td><span class="badge bg-secondary rounded-pill">{{ site.clicks }}</span></td>
                            <td class="pe-4 text-end text-nowrap">
                                <button class="btn btn-sm btn-primary rounded-pill px-3 me-1" @click="editSite(site)">Edit</button>
                                <button class="btn btn-sm btn-danger rounded-pill px-3" @click="deleteSite(site.id)">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="paginatedSites.length === 0">
                            <td colspan="5" class="text-center py-4 text-muted">Tidak ada data situs.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center py-3" v-if="totalSitePages > 1">
                <span class="text-muted small">Halaman {{ currentPageSites }} dari {{ totalSitePages }}</span>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-secondary" :disabled="currentPageSites === 1" @click="currentPageSites--">Sebelumnya</button>
                    <button class="btn btn-sm btn-outline-secondary" :disabled="currentPageSites === totalSitePages" @click="currentPageSites++">Berikutnya</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Gambar -->
    <div v-if="currentTab === 'images'" class="card shadow-sm rounded-4 border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Thumb</th>
                            <th>Judul / Alt</th>
                            <th>Site URL</th>
                            <th>Klik</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="img in paginatedImages" :key="img.id">
                            <td class="ps-4">
                                <img :src="img.imageUrl" style="width:50px;height:50px;object-fit:cover;" class="rounded-3 shadow-sm">
                            </td>
                            <td>{{ img.title || img.alt }}</td>
                            <td><a :href="img.siteUrl" target="_blank" class="text-truncate d-inline-block" style="max-width:150px;">{{ img.siteUrl }}</a></td>
                            <td><span class="badge bg-secondary rounded-pill">{{ img.clicks }}</span></td>
                            <td>
                                <span v-if="img.broken == 1" class="badge bg-danger rounded-pill">Broken</span>
                                <span v-else class="badge bg-success rounded-pill">OK</span>
                            </td>
                            <td class="pe-4 text-end text-nowrap">
                                <button class="btn btn-sm btn-primary rounded-pill px-3 me-1" @click="editImage(img)">Edit</button>
                                <button class="btn btn-sm btn-danger rounded-pill px-3" @click="deleteImage(img.id)">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="paginatedImages.length === 0">
                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada data gambar.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center py-3" v-if="totalImagePages > 1">
                <span class="text-muted small">Halaman {{ currentPageImages }} dari {{ totalImagePages }}</span>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-secondary" :disabled="currentPageImages === 1" @click="currentPageImages--">Sebelumnya</button>
                    <button class="btn btn-sm btn-outline-secondary" :disabled="currentPageImages === totalImagePages" @click="currentPageImages++">Berikutnya</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const escapeHtml = (unsafe) => {
        if (unsafe == null) return '';
        return (unsafe + '').replace(/[&<"'>]/g, function (m) {
            switch (m) {
                case '&': return '&amp;';
                case '<': return '&lt;';
                case '>': return '&gt;';
                case '"': return '&quot;';
                case "'": return '&#039;';
            }
        });
    };

    const { createApp, ref, computed, onMounted } = Vue;

    createApp({
        setup() {
            const currentTab = ref('sites');
            const sites = ref([]);
            const images = ref([]);
            const perPage = ref(10);
            const currentPageSites = ref(1);
            const currentPageImages = ref(1);

            const paginatedSites = computed(() => {
                const start = (currentPageSites.value - 1) * perPage.value;
                return sites.value.slice(start, start + perPage.value);
            });
            const totalSitePages = computed(() => Math.ceil(sites.value.length / perPage.value) || 1);

            const paginatedImages = computed(() => {
                const start = (currentPageImages.value - 1) * perPage.value;
                return images.value.slice(start, start + perPage.value);
            });
            const totalImagePages = computed(() => Math.ceil(images.value.length / perPage.value) || 1);

            const loadSites = async () => {
                const res = await fetch('<?= base_url('api/getSites') ?>');
                const json = await res.json();
                sites.value = json.data;
            };

            const loadImages = async () => {
                const res = await fetch('<?= base_url('api/getImages') ?>');
                const json = await res.json();
                images.value = json.data;
            };

            const deleteSite = async (id) => {
                if(confirm('Yakin hapus situs ini?')) {
                    await fetch('<?= base_url('api/deleteSite/') ?>' + id, {method: 'POST'});
                    loadSites();
                }
            };

            const deleteImage = async (id) => {
                if(confirm('Yakin hapus gambar ini?')) {
                    await fetch('<?= base_url('api/deleteImage/') ?>' + id, {method: 'POST'});
                    loadImages();
                }
            };

            const editSite = async (site) => {
                const { value: formValues } = await Swal.fire({
                    title: 'Edit Situs',
                    html:
                        '<div class="mb-3 text-start"><label class="form-label">Judul</label><input id="swal-s1" class="form-control" value="' + escapeHtml(site.title) + '"></div>' +
                        '<div class="mb-3 text-start"><label class="form-label">URL</label><input id="swal-s2" class="form-control" value="' + escapeHtml(site.url) + '"></div>' +
                        '<div class="mb-3 text-start"><label class="form-label">Deskripsi</label><textarea id="swal-s3" class="form-control">' + escapeHtml(site.description) + '</textarea></div>' +
                        '<div class="mb-3 text-start"><label class="form-label">Kata Kunci</label><input id="swal-s4" class="form-control" value="' + escapeHtml(site.keywords) + '"></div>' +
                        '<div class="mb-3 text-start"><label class="form-label">Klik</label><input type="number" id="swal-s5" class="form-control" value="' + (site.clicks || '0') + '"></div>',
                    focusConfirm: false,
                    showCancelButton: true,
                    width: '600px',
                    preConfirm: () => {
                        return {
                            title: document.getElementById('swal-s1').value,
                            url: document.getElementById('swal-s2').value,
                            description: document.getElementById('swal-s3').value,
                            keywords: document.getElementById('swal-s4').value,
                            clicks: document.getElementById('swal-s5').value
                        }
                    }
                });
                
                if (formValues) {
                    const formData = new FormData();
                    formData.append('title', formValues.title);
                    formData.append('url', formValues.url);
                    formData.append('description', formValues.description);
                    formData.append('keywords', formValues.keywords);
                    formData.append('clicks', formValues.clicks);
                    
                    await fetch('<?= base_url('api/updateSite/') ?>' + site.id, {
                        method: 'POST',
                        body: formData
                    });
                    loadSites();
                    Swal.fire('Berhasil!', 'Data situs telah diubah.', 'success');
                }
            };

            const editImage = async (img) => {
                const { value: formValues } = await Swal.fire({
                    title: 'Edit Gambar',
                    html:
                        '<div class="mb-3 text-start"><label class="form-label">Judul</label><input id="swal-i1" class="form-control" value="' + escapeHtml(img.title) + '"></div>' +
                        '<div class="mb-3 text-start"><label class="form-label">Alt (Alternatif)</label><input id="swal-i2" class="form-control" value="' + escapeHtml(img.alt) + '"></div>' +
                        '<div class="mb-3 text-start"><label class="form-label">URL Gambar (Source)</label><input id="swal-i3" class="form-control" value="' + escapeHtml(img.imageUrl) + '"></div>' +
                        '<div class="mb-3 text-start"><label class="form-label">URL Situs Induk</label><input id="swal-i4" class="form-control" value="' + escapeHtml(img.siteUrl) + '"></div>' +
                        '<div class="mb-3 text-start"><label class="form-label">Klik</label><input type="number" id="swal-i5" class="form-control" value="' + (img.clicks || '0') + '"></div>' +
                        '<div class="mb-3 text-start"><label class="form-label">Status (0=Aktif, 1=Rusak)</label><input type="number" id="swal-i6" class="form-control" value="' + (img.broken || '0') + '" min="0" max="1"></div>',
                    focusConfirm: false,
                    showCancelButton: true,
                    width: '600px',
                    preConfirm: () => {
                        return {
                            title: document.getElementById('swal-i1').value,
                            alt: document.getElementById('swal-i2').value,
                            imageUrl: document.getElementById('swal-i3').value,
                            siteUrl: document.getElementById('swal-i4').value,
                            clicks: document.getElementById('swal-i5').value,
                            broken: document.getElementById('swal-i6').value
                        }
                    }
                });
                
                if (formValues) {
                    const formData = new FormData();
                    formData.append('title', formValues.title);
                    formData.append('alt', formValues.alt);
                    formData.append('imageUrl', formValues.imageUrl);
                    formData.append('siteUrl', formValues.siteUrl);
                    formData.append('clicks', formValues.clicks);
                    formData.append('broken', formValues.broken);
                    
                    await fetch('<?= base_url('api/updateImage/') ?>' + img.id, {
                        method: 'POST',
                        body: formData
                    });
                    loadImages();
                    Swal.fire('Berhasil!', 'Data gambar telah diubah.', 'success');
                }
            };

            onMounted(() => {
                loadSites();
                loadImages();
            });

            return {
                currentTab,
                sites,
                images,
                perPage,
                currentPageSites,
                currentPageImages,
                paginatedSites,
                totalSitePages,
                paginatedImages,
                totalImagePages,
                deleteSite,
                deleteImage,
                editSite,
                editImage
            }
        }
    }).mount('#gkr');
</script>
<?= $this->endSection() ?>