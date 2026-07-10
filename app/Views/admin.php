<?= $this->extend('layout/main') ?>

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
            <li class="nav-item">
                <button class="nav-link px-0" style="border-bottom-width: 3px; color: #2B3385 !important;" :class="{active: currentTab === 'doodle'}" @click="currentTab = 'doodle'">Doodle</button>
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
                                <img :src="img.imageUrl.replace('https://foto.gkr.my.id/', '<?= base_url('dokumen/foto/') ?>')" style="width:50px;height:50px;object-fit:cover;" class="rounded-3 shadow-sm">
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

    <!-- Tabel Doodle -->
    <div v-if="currentTab === 'doodle'" class="card shadow-sm rounded-4 border-0">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2">
            <h5 class="mb-0 fw-bold">Manajemen Doodle</h5>
            <button class="btn btn-primary shadow-sm rounded-pill px-3" @click="bukaModalDoodle"><i class="fas fa-plus me-1"></i> Doodle Baru</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Event</th>
                            <th>Gambar</th>
                            <th>Tgl Mulai</th>
                            <th>Tgl Selesai</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in doodles" :key="item.id_doodle">
                            <td class="ps-4 fw-bold">{{ item.event }}</td>
                            <td>
                                <img :src="'<?= base_url('dokumen/doodle/') ?>' + item.gambar" class="rounded-3 shadow-sm" style="height: 50px; object-fit: cover;" alt="Doodle">
                            </td>
                            <td>{{ formatTanggal(item.tgl_mulai) }}</td>
                            <td>{{ formatTanggal(item.tgl_selesai) }}</td>
                            <td>
                                <span v-if="item.status === 'aktif'" class="badge bg-success rounded-pill">Aktif</span>
                                <span v-else class="badge bg-secondary rounded-pill">Tidak Aktif</span>
                            </td>
                            <td class="pe-4 text-end text-nowrap">
                                <button class="btn btn-sm btn-primary rounded-pill px-3 me-1" @click="editDoodle(item)">Edit</button>
                                <button class="btn btn-sm btn-danger rounded-pill px-3" @click="deleteDoodle(item.id_doodle)">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="doodles.length === 0">
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data Doodle.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Doodle -->
    <div class="modal fade" id="doodleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow rounded-4">
                <form @submit.prevent="simpanDoodle">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">{{ isEditDoodle ? 'Edit Doodle' : 'Tambah Doodle' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Event <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="doodleForm.event" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Tgl Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" v-model="doodleForm.tgl_mulai" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Tgl Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" v-model="doodleForm.tgl_selesai" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" v-model="doodleForm.status" required>
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Gambar {{ !isEditDoodle ? '<span class="text-danger">*</span>' : '' }}</label>
                            <input type="file" class="form-control" ref="doodleFileInput" accept=".jpg,.jpeg,.png,.webp,.gif" @change="onDoodleFileChange" :required="!isEditDoodle">
                        </div>
                        <div class="text-center mt-3" v-if="doodlePreview">
                            <img :src="doodlePreview" style="max-height: 120px; object-fit: contain;" class="rounded shadow-sm">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" :disabled="isSubmittingDoodle">
                            <i v-if="isSubmittingDoodle" class="fas fa-spinner fa-spin me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="mt-5 py-4 border-top w-100" style="background-color: #f8f9fa;">
    <div class="container text-center text-muted small">
        Dikembangkan oleh <span style="color: #2B3385;" class="fw-bold">RND</span> &copy; <?= date('Y') ?> &bull; <a href="<?= base_url('admin/versi') ?>" class="text-decoration-none text-muted" style="transition: color 0.2s;" onmouseover="this.style.color='#2B3385'" onmouseout="this.style.color='inherit'"><?= esc($version) ?></a>
    </div>
</footer>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.AppConfig = {
        apiGetSites: '<?= base_url('api/getSites') ?>',
        apiGetImages: '<?= base_url('api/getImages') ?>',
        apiDeleteSite: '<?= base_url('api/deleteSite/') ?>',
        apiDeleteImage: '<?= base_url('api/deleteImage/') ?>',
        apiUpdateSite: '<?= base_url('api/updateSite/') ?>',
        apiUpdateImage: '<?= base_url('api/updateImage/') ?>',
        urlDokumenDoodle: '<?= base_url('dokumen/doodle/') ?>',
        apiStoreDoodle: '<?= base_url('doodle/store') ?>',
        apiUpdateDoodle: '<?= base_url('doodle/update') ?>',
        apiDeleteDoodle: '<?= base_url('doodle/delete') ?>',
        apiGetAllDoodle: '<?= base_url('doodle/getAll') ?>'
    };
</script>
<script src="<?= base_url('js/admin.js') ?>"></script>
<?= $this->endSection() ?>