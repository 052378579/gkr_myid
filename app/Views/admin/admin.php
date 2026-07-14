<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('title') ?>Mesin Pencari<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="gkr">   
    <div class="d-flex justify-content-between align-items-center mb-4">
        <ul class="nav nav-underline gap-1">
            <li class="nav-item">
                <button class="nav-link px-4" style="border-bottom-width: 3px; color: #2B3385 !important;" :class="{active: currentTab === 'sites'}" @click="currentTab = 'sites'">Situs</button>
            </li>
            <li class="nav-item">
                <button class="nav-link px-4" style="border-bottom-width: 3px; color: #2B3385 !important;" :class="{active: currentTab === 'images'}" @click="currentTab = 'images'">Gambar</button>
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
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2">
            <h5 class="mb-0 fw-bold">Manajemen Situs</h5>
            <div class="input-group" style="max-width: 250px;">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0 ps-0 shadow-none" placeholder="Cari situs..." v-model="searchSite">
            </div>
        </div>
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
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2">
            <h5 class="mb-0 fw-bold">Manajemen Gambar</h5>
            <div class="input-group" style="max-width: 250px;">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0 ps-0 shadow-none" placeholder="Cari gambar..." v-model="searchImage">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Gambar</th>
                            <th>Nama Barang</th>
                            <th>URL</th>
                            <th>Klik</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="img in paginatedImages" :key="img.id">
                            <td class="ps-4">
                                <img :src="img.imageUrl" style="width:75px;aspect-ratio:16/9;object-fit:cover;" class="rounded-3 shadow-sm">
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

    <!-- Modal Edit Site -->
    <div class="modal fade" id="modalEditSite" tabindex="-1" aria-labelledby="modalEditSiteLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h4 class="modal-title fw-bold text-secondary w-100 text-center" id="modalEditSiteLabel">Edit Situs</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6 border-end pe-4">
                            <div class="mb-3">
                                <label class="form-label text-muted mb-1">Judul</label>
                                <input type="text" class="form-control rounded-3" v-model="formEditSite.title">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted mb-1">URL</label>
                                <input type="text" class="form-control rounded-3 bg-secondary text-white" v-model="formEditSite.url" readonly disabled style="opacity: 0.6;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted mb-1">Deskripsi</label>
                                <textarea class="form-control rounded-3" v-model="formEditSite.description" rows="5"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted mb-1">Klik</label>
                                <input type="number" class="form-control rounded-3" v-model="formEditSite.clicks">
                            </div>
                            
                            <div class="mt-4">
                                <button type="button" class="btn btn-danger shadow-sm px-4 rounded-3" @click="deleteSite(formEditSite.id); $refs.closeModalBtn.click()">Hapus</button>
                                <button type="button" class="d-none" data-bs-dismiss="modal" ref="closeModalBtn"></button>
                            </div>
                        </div>
                        
                        <!-- Kolom Kanan -->
                        <div class="col-md-6 ps-4">
                            <div class="mb-3">
                                <label class="form-label text-muted mb-1">Kata Kunci</label>
                                <input type="text" class="form-control rounded-3" v-model="formEditSite.keywords">
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1">Kata Kunci (Material)</label>
                                    <select class="form-select rounded-3" v-model="selectedMaterial" size="10">
                                        <option value="">-- Pilih Material --</option>
                                        <option v-for="mat in uniqueMaterials" :key="mat" :value="mat">{{ mat }}</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1">Kata Kunci (Warna)</label>
                                    <select class="form-select rounded-3" v-model="selectedWarna" size="10">
                                        <option value="">-- Pilih Warna --</option>
                                        <option v-for="warn in uniqueWarna" :key="warn" :value="warn">{{ warn }}</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-5">
                                <button type="button" class="btn btn-primary px-4 shadow-sm rounded-3 me-2" @click="simpanEditSite">OK</button>
                                <button type="button" class="btn btn-secondary px-4 shadow-sm rounded-3" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.AppConfig = {
        apiGetSites: '<?= base_url('api/getSites') ?>',
        apiGetImages: '<?= base_url('api/getImages') ?>',
        apiGetMaterials: '<?= base_url('api/getMaterials') ?>',
        apiDeleteSite: '<?= base_url('api/deleteSite/') ?>',
        apiDeleteImage: '<?= base_url('api/deleteImage/') ?>',
        apiUpdateSite: '<?= base_url('api/updateSite/') ?>',
        apiUpdateImage: '<?= base_url('api/updateImage/') ?>',
        apiStoreSite: '<?= base_url('api/storeSite') ?>',
        apiStoreImage: '<?= base_url('api/storeImage') ?>',
        urlDokumenDoodle: '<?= base_url('dokumen/doodle/') ?>',
        apiStoreDoodle: '<?= base_url('doodle/store') ?>',
        apiUpdateDoodle: '<?= base_url('doodle/update') ?>',
        apiDeleteDoodle: '<?= base_url('doodle/delete') ?>',
        apiGetAllDoodle: '<?= base_url('doodle/getAll') ?>'
    };
</script>
<script src="<?= base_url('js/admin.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>