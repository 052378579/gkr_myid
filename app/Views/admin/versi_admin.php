<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Manajemen Versi<?= $this->endSection() ?>

<?= $this->section('content') ?>
<nav class="navbar navbar-expand-lg navbar-light glassmorphism fixed-top shadow-sm" style="background: rgba(255, 255, 255, 0.95) !important; backdrop-filter: blur(10px);">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('admin') ?>">
            <img src="<?= base_url('Gracia_logo.png') ?>" alt="Gracia" style="height: 30px; width: auto;">
        </a>
        <div class="navbar-nav ms-auto fw-medium">
            <a class="nav-link" href="<?= base_url('admin') ?>" style="color: #2B3385 !important;"><i class="fas fa-arrow-left me-1"></i> Admin</a>
            <a class="nav-link" href="<?= base_url('versi') ?>" target="_blank" style="color: #2B3385 !important;">Changelog</a>
        </div>
    </div>
</nav>

<div class="container mt-5 pt-5 pb-5" id="adminVersiApp">   
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold" style="color: #2B3385;">Manajemen Versi</h4>
        <button class="btn btn-primary rounded-pill px-4" @click="tambahVersi">
            <i class="fas fa-plus me-1"></i> Versi Baru
        </button>
    </div>

    <!-- Tabel Versi -->
    <div class="card shadow-sm rounded-4 border-0 mb-5">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="">
                        <tr>
                            <th class="ps-4">Versi</th>
                            <th>Tanggal Rilis</th>
                            <th>Judul</th>
                            <th>Highlights</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in paginatedVersi" :key="item.id">
                            <td class="ps-4 fw-bold">{{ item.versi }}</td>
                            <td>{{ formatTanggal(item.tanggal_rilis) }}</td>
                            <td>{{ item.judul }}</td>
                            <td>
                                <span class="badge bg-primary me-1 rounded-pill" v-if="item.improvements && item.improvements.length > 0">{{ item.improvements.length }} Imprv</span>
                                <span class="badge bg-danger me-1 rounded-pill" v-if="item.fixes && item.fixes.length > 0">{{ item.fixes.length }} Fixes</span>
                                <span class="badge bg-success rounded-pill" v-if="item.patches && item.patches.length > 0">{{ item.patches.length }} Patch</span>
                            </td>
                            <td class="pe-4 text-end text-nowrap">
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" @click="editVersi(item)">Edit</button>
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3" @click="hapusVersi(item.id)">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="daftarVersi.length === 0 && !isLoading">
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data riwayat versi.</td>
                        </tr>
                        <tr v-if="isLoading">
                            <td colspan="5" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginasi -->
    <nav aria-label="Navigasi Halaman" v-if="totalPages > 1" class="mb-5">
        <ul class="pagination justify-content-center">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <button class="page-link shadow-sm border-0" @click="prevPage" :disabled="currentPage === 1" style="border-radius: 20px 0 0 20px;">
                    <i class="fas fa-chevron-left me-1"></i> Sebelumnya
                </button>
            </li>
            <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }">
                <button class="page-link shadow-sm border-0" @click="goToPage(page)">{{ page }}</button>
            </li>
            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <button class="page-link shadow-sm border-0" @click="nextPage" :disabled="currentPage === totalPages" style="border-radius: 0 20px 20px 0;">
                    Selanjutnya <i class="fas fa-chevron-right ms-1"></i>
                </button>
            </li>
        </ul>
    </nav>

    <!-- Modal Form -->
    <div class="modal fade" id="versiModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <form @submit.prevent="simpanVersi">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" style="color: #2B3385;">{{ isEdit ? 'Edit Versi' : 'Tambah Versi Baru' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 pt-4">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nomor Versi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="form.versi" placeholder="Cth: 1.2.0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Rilis <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" v-model="form.tanggal_rilis" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Rilis <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.judul" placeholder="Cth: Optimasi Performa & Fitur Baru" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Deskripsi Singkat</label>
                            <textarea class="form-control" v-model="form.deskripsi" rows="2" placeholder="Ringkasan pembaruan pada versi ini..."></textarea>
                        </div>

                        <!-- Dynamic Lists -->
                        <div class="card bg-light border-0 rounded-4 mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-primary">Improvements (Peningkatan)</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-0" @click="addListItem('improvements')">+ Tambah</button>
                                </div>
                                <div v-for="(item, index) in form.improvements" :key="'imp'+index" class="d-flex mb-2">
                                    <input type="text" class="form-control form-control-sm me-2" v-model="form.improvements[index]" placeholder="Cth: Mendukung dark mode...">
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="removeListItem('improvements', index)"><i class="fas fa-times"></i></button>
                                </div>
                                <div v-if="form.improvements.length === 0" class="text-muted small italic">Tidak ada catatan improvement.</div>
                            </div>
                        </div>

                        <div class="card bg-light border-0 rounded-4 mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-danger">Fixes (Perbaikan Bug)</h6>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill py-0" @click="addListItem('fixes')">+ Tambah</button>
                                </div>
                                <div v-for="(item, index) in form.fixes" :key="'fix'+index" class="d-flex mb-2">
                                    <input type="text" class="form-control form-control-sm me-2" v-model="form.fixes[index]" placeholder="Cth: Mengatasi error pada login...">
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="removeListItem('fixes', index)"><i class="fas fa-times"></i></button>
                                </div>
                                <div v-if="form.fixes.length === 0" class="text-muted small italic">Tidak ada catatan fixes.</div>
                            </div>
                        </div>

                        <div class="card bg-light border-0 rounded-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-success">Patches (Penambalan Minor)</h6>
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill py-0" @click="addListItem('patches')">+ Tambah</button>
                                </div>
                                <div v-for="(item, index) in form.patches" :key="'patch'+index" class="d-flex mb-2">
                                    <input type="text" class="form-control form-control-sm me-2" v-model="form.patches[index]" placeholder="Cth: Penyesuaian jarak margin...">
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="removeListItem('patches', index)"><i class="fas fa-times"></i></button>
                                </div>
                                <div v-if="form.patches.length === 0" class="text-muted small italic">Tidak ada catatan patches.</div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5" :disabled="isSubmitting">
                            <i v-if="isSubmitting" class="fas fa-spinner fa-spin me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<meta name="page-config" 
    data-api-get-all="<?= base_url('admin/versi/getAll') ?>"
    data-api-store="<?= base_url('admin/versi/store') ?>"
    data-api-update="<?= base_url('admin/versi/update') ?>"
    data-api-delete="<?= base_url('admin/versi/delete') ?>">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/admin_versi.js') ?>?v=<?= ASSET_VERSION ?>"></script>
<?= $this->endSection() ?>
