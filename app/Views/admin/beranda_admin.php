<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('title') ?>Mesin Pencari<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .img-thumbnail-link {
        display: inline-block;
        transition: transform 0.2s ease-in-out;
    }
    .img-thumbnail-link:hover {
        transform: scale(1.08);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="gkr">   
    <!-- Tabel Utama Manajemen Mesin Pencari -->
    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-header border-0 d-flex justify-content-between align-items-center pt-4 pb-2">
            <h5 class="mb-0 fw-bold" style="color: #2B3385;">Manajemen Mesin Pencari</h5>
            <div class="d-flex gap-3 align-items-center">
                <div class="d-flex align-items-center">
                    <label class="me-2 text-muted small mb-0">Tampilkan:</label>
                    <select class="form-select form-select-sm rounded-pill w-auto" v-model.number="perPage" @change="currentPageImages=1">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="input-group" style="max-width: 280px;">
                    <span class="input-group-text border-end-0 bg-body"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0 shadow-none" placeholder="Cari barang atau URL..." v-model="searchImage">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-tertiary">
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
                                <a v-if="img.imageUrl" :href="img.imageUrl" target="_blank" class="img-thumbnail-link" title="Buka Gambar">
                                    <img :src="img.imageUrl" style="width:75px;aspect-ratio:16/9;object-fit:cover;" class="rounded-3 shadow-sm border">
                                </a>
                                <span v-else class="badge bg-secondary">No Image</span>
                            </td>
                            <td class="fw-medium">{{ img.judul || img.title || img.alt }}</td>
                            <td>
                                <a :href="img.siteUrl || img.url" target="_blank" class="text-truncate d-inline-block text-primary text-decoration-none" style="max-width:220px;">
                                    {{ img.siteUrl || img.url }}
                                </a>
                            </td>
                            <td><span class="badge bg-secondary rounded-pill">{{ img.klik !== undefined ? img.klik : img.clicks }}</span></td>
                            <td>
                                <span v-if="(img.rusak !== undefined ? img.rusak : img.broken) == 1" class="badge bg-danger rounded-pill px-3">Broken</span>
                                <span v-else class="badge bg-success rounded-pill px-3">OK</span>
                            </td>
                            <td class="pe-4 text-end text-nowrap">
                                <button class="btn btn-sm btn-primary rounded-pill px-3" @click="openModalEditImage(img)"><i class="fas fa-edit me-1"></i>Edit</button>
                            </td>
                        </tr>
                        <tr v-if="paginatedImages.length === 0">
                            <td colspan="6" class="text-center py-5 text-muted">Tidak ada data ditemukan dalam mesin pencari.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer border-top-0 d-flex justify-content-between align-items-center py-3" v-if="totalImagePages > 1">
                <span class="text-muted small">Halaman {{ currentPageImages }} dari {{ totalImagePages }}</span>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-secondary" :disabled="currentPageImages === 1" @click="currentPageImages--">Sebelumnya</button>
                    <button class="btn btn-sm btn-outline-secondary" :disabled="currentPageImages === totalImagePages" @click="currentPageImages++">Berikutnya</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Image -->
    <div class="modal fade" id="modalEditImage" tabindex="-1" aria-labelledby="modalEditImageLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h4 class="modal-title fw-bold text-secondary w-100 text-center" id="modalEditImageLabel">Edit Data Mesin Pencari</h4>
                    <button type="button" class="btn-close shadow-none position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <!-- Kolom Kiri: Data Teks & Klasifikasi -->
                        <div class="col-md-6 border-end pe-4">
                            <div class="mb-3">
                                <label class="form-label text-muted mb-1">Nama Barang / Judul</label>
                                <input type="text" class="form-control rounded-3" v-model="formEditImage.title">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted mb-1">Teks Alternatif (Alt)</label>
                                <input type="text" class="form-control rounded-3" v-model="formEditImage.alt">
                            </div>
                            
                            <!-- Dropdown Material & Warna Bersebelahan -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1">Material</label>
                                    <select class="form-select rounded-3" v-model="selectedMaterialImage">
                                        <option value="">-- Pilih Material --</option>
                                        <option v-for="mat in uniqueMaterials" :key="mat" :value="mat">{{ mat }}</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1">Warna</label>
                                    <select class="form-select rounded-3" v-model="selectedWarnaImage">
                                        <option value="">-- Pilih Warna --</option>
                                        <option v-for="war in uniqueWarna" :key="war" :value="war">{{ war }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted mb-1">Kata Kunci</label>
                                <input type="text" class="form-control rounded-3" v-model="formEditImage.keywords">
                            </div>

                            <!-- Klik & Status Bersebelahan -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1">Klik</label>
                                    <input type="number" class="form-control rounded-3" v-model="formEditImage.clicks">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1">Status</label>
                                    <select class="form-select rounded-3" v-model="formEditImage.broken">
                                        <option value="0">Aktif (0)</option>
                                        <option value="1">Rusak (1)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kolom Kanan: Visual & URL Terkunci -->
                        <div class="col-md-6 ps-4">
                            <!-- Pratinjau Gambar 8:5 Anti-Terpotong dengan Tautan Aktif -->
                            <div class="mb-3 text-center bg-light rounded-3 d-flex align-items-center justify-content-center overflow-hidden border position-relative" style="aspect-ratio: 8/5; max-height: 250px;">
                                <a v-if="formEditImage.imageUrl" :href="formEditImage.imageUrl" target="_blank" class="w-100 h-100 d-block" title="Klik untuk membuka gambar berukuran penuh">
                                    <img :src="formEditImage.imageUrl" alt="Preview Gambar" class="w-100 h-100" style="object-fit: contain;">
                                </a>
                                <span v-else class="text-muted small">Pratinjau tidak tersedia</span>
                            </div>
                            
                            <!-- URL Terkunci -->
                            <div class="mb-3">
                                <label class="form-label text-muted mb-1">URL Situs Induk</label>
                                <input type="text" class="form-control rounded-3 bg-body-secondary text-muted" v-model="formEditImage.siteUrl" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted mb-1">URL Gambar (Source)</label>
                                <input type="text" class="form-control rounded-3 bg-body-secondary text-muted" v-model="formEditImage.imageUrl" disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Area Tombol Aksi Modal (Hapus di Kiri, OK/Batal di Kanan) -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <div>
                            <button type="button" class="btn btn-danger px-4 shadow-sm rounded-3" @click="deleteImage(formEditImage.id)"><i class="fas fa-trash me-1"></i>Hapus</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary px-4 shadow-sm rounded-3 me-2" @click="simpanEditImage"><i class="fas fa-check me-1"></i>OK</button>
                            <button type="button" class="btn btn-secondary px-4 shadow-sm rounded-3" data-bs-dismiss="modal">Batal</button>
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
        apiStoreImage: '<?= base_url('api/storeImage') ?>'
    };
</script>
<script src="<?= base_url('js/admin_beranda.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>
