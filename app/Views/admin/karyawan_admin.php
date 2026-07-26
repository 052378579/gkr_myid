<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('title') ?>Pengaturan Karyawan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0" id="adminKaryawanApp">   
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold" style="color: #2B3385;">Manajemen Karyawan</h4>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center">
                <label class="me-2 text-muted small">Tampilkan:</label>
                <select class="form-select form-select-sm rounded-pill w-auto" v-model.number="perPage" @change="currentPage = 1">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            <button class="btn btn-primary rounded-pill px-4" @click="tambahKaryawan">
                <i class="fas fa-plus me-1"></i> Tambah
            </button>
        </div>
    </div>

    <!-- Tabel Karyawan -->
    <div class="card shadow-sm rounded-4 border-0 mb-5">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="">
                        <tr>
                            <th class="ps-4">Nama Lengkap</th>
                            <th>No. HP</th>
                            <th>Divisi</th>
                            <th>Status</th>
                            <th>Akses Terakhir</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in paginatedKaryawan" :key="item.id_user">
                            <td class="ps-4 fw-bold">{{ item.nama_lengkap }}</td>
                            <td>{{ item.no_hp }}</td>
                            <td>
                                <span :class="getDivisiBadgeClass(item.divisi)" :style="getDivisiBadgeStyle(item.divisi)">
                                    {{ item.divisi }}
                                </span>
                            </td>
                            <td>
                                <span :class="['badge rounded-pill', {'bg-success': item.status === 'aktif', 'bg-warning text-dark': item.status === 'pending', 'bg-danger': item.status === 'suspend'}]">
                                    {{ item.status ? item.status.charAt(0).toUpperCase() + item.status.slice(1) : '' }}
                                </span>
                            </td>
                            <td>
                                <small v-if="item.last_ip" class="text-muted d-block"><i class="fas fa-network-wired me-1"></i>{{ item.last_ip }}</small>
                                <span v-else class="text-muted small">-</span>
                            </td>
                            <td class="pe-4 text-end text-nowrap">
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" @click="editKaryawan(item)">Edit</button>
                            </td>
                        </tr>
                        <tr v-if="daftarKaryawan.length === 0 && !isLoading">
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data karyawan.</td>
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
        <div class="card-footer border-top-0 d-flex justify-content-between align-items-center py-3" v-if="totalPages > 1">
            <span class="text-muted small">Halaman {{ currentPage }} dari {{ totalPages }}</span>
            <div class="btn-group">
                <button class="btn btn-sm btn-outline-secondary" :disabled="currentPage === 1" @click="currentPage--">Sebelumnya</button>
                <button class="btn btn-sm btn-outline-secondary" :disabled="currentPage === totalPages" @click="currentPage++">Berikutnya</button>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="karyawanModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow rounded-4">
                <form @submit.prevent="simpanKaryawan">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" style="color: #2B3385;">{{ isEdit ? 'Edit Karyawan' : 'Tambah Karyawan Baru' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 pt-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.nama_lengkap" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor HP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.no_hp" placeholder="Cth: 08123456789" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Divisi <span class="text-danger">*</span></label>
                            <select class="form-select" v-model="form.divisi" required>
                                <option value="" disabled>-- Divisi --</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Produksi 1">Produksi 1</option>
                                <option value="Produksi 2">Produksi 2</option>
                                <option value="Produksi 4">Produksi 4</option>
                                <option value="RND">RND</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" v-model="form.status" required>
                                <option value="pending">Pending</option>
                                <option value="aktif">Aktif</option>
                                <option value="suspend">Suspend</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-between">
                        <!-- Tombol Hapus (Kiri) -->
                        <div>
                            <button v-if="isEdit" type="button" class="btn btn-outline-danger rounded-pill px-4" @click="hapusKaryawan(form.id_user)">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </div>
                        <!-- Tombol Simpan & Batal (Kanan) -->
                        <div>
                            <button type="button" class="btn btn-light rounded-pill px-4 me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4" :disabled="isSubmitting">
                                <i v-if="isSubmitting" class="fas fa-spinner fa-spin me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.KaryawanConfig = {
        apiGetAll: '<?= base_url('admin/karyawan/getAll') ?>',
        apiStore: '<?= base_url('admin/karyawan/store') ?>',
        apiUpdate: '<?= base_url('admin/karyawan/update') ?>',
        apiDelete: '<?= base_url('admin/karyawan/delete') ?>'
    };
</script>
<script src="<?= base_url('js/admin_karyawan.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>
