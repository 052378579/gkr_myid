<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Pengaturan Karyawan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white glassmorphism fixed-top shadow-sm" style="background: rgba(255, 255, 255, 0.95) !important; backdrop-filter: blur(10px);">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('admin') ?>">
            <img src="<?= base_url('Gracia_logo.png') ?>" alt="Gracia" style="height: 30px; width: auto;">
        </a>
        <div class="navbar-nav ms-auto fw-medium">
            <a class="nav-link" href="<?= base_url('admin') ?>" style="color: #2B3385 !important;"><i class="fas fa-arrow-left me-1"></i> Panel Admin</a>
        </div>
    </div>
</nav>

<div class="container mt-5 pt-5 pb-5" id="adminKaryawanApp">   
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold" style="color: #2B3385;">Manajemen Karyawan (Pengguna)</h4>
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
                <i class="fas fa-plus me-1"></i> Tambah Karyawan
            </button>
        </div>
    </div>

    <!-- Tabel Karyawan -->
    <div class="card shadow-sm rounded-4 border-0 mb-5">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nama Lengkap</th>
                            <th>No. HP</th>
                            <th>Divisi</th>
                            <th>Akses Terakhir</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in paginatedKaryawan" :key="item.id_user">
                            <td class="ps-4 fw-bold">{{ item.nama_lengkap }}</td>
                            <td>{{ item.no_hp }}</td>
                            <td><span class="badge bg-secondary rounded-pill">{{ item.divisi }}</span></td>
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
        <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center py-3" v-if="totalPages > 1">
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
                            <input type="text" class="form-control" v-model="form.divisi" placeholder="Cth: IT, Marketing, dll" required>
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
    const { createApp, ref, computed, onMounted } = Vue;

    createApp({
        setup() {
            const daftarKaryawan = ref([]);
            const perPage = ref(10);
            const currentPage = ref(1);

            const paginatedKaryawan = computed(() => {
                const start = (currentPage.value - 1) * perPage.value;
                return daftarKaryawan.value.slice(start, start + perPage.value);
            });
            
            const totalPages = computed(() => Math.ceil(daftarKaryawan.value.length / perPage.value) || 1);

            const isLoading = ref(true);
            const isSubmitting = ref(false);
            const isEdit = ref(false);
            
            const form = ref({
                id_user: null,
                nama_lengkap: '',
                no_hp: '',
                divisi: ''
            });

            let modalInstance = null;

            const fetchKaryawan = async () => {
                isLoading.value = true;
                try {
                    const response = await fetch('<?= base_url('admin/karyawan/getAll') ?>');
                    daftarKaryawan.value = await response.json();
                } catch (error) {
                    console.error("Error fetching data:", error);
                    Swal.fire('Error', 'Gagal memuat data karyawan.', 'error');
                }
                isLoading.value = false;
            };

            const resetForm = () => {
                form.value = {
                    id_user: null,
                    nama_lengkap: '',
                    no_hp: '',
                    divisi: ''
                };
            };

            const tambahKaryawan = () => {
                isEdit.value = false;
                resetForm();
                if(!modalInstance) modalInstance = new bootstrap.Modal(document.getElementById('karyawanModal'));
                modalInstance.show();
            };

            const editKaryawan = (item) => {
                isEdit.value = true;
                form.value = JSON.parse(JSON.stringify(item));
                if(!modalInstance) modalInstance = new bootstrap.Modal(document.getElementById('karyawanModal'));
                modalInstance.show();
            };

            const simpanKaryawan = async () => {
                isSubmitting.value = true;
                const url = isEdit.value ? '<?= base_url('admin/karyawan/update') ?>' : '<?= base_url('admin/karyawan/store') ?>';
                
                const formData = new URLSearchParams();
                if(isEdit.value) formData.append('id_user', form.value.id_user);
                formData.append('nama_lengkap', form.value.nama_lengkap);
                formData.append('no_hp', form.value.no_hp);
                formData.append('divisi', form.value.divisi);

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.status === 'success') {
                        modalInstance.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: result.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        fetchKaryawan();
                    } else {
                        let errorMsg = result.message;
                        if(result.errors) {
                            errorMsg += '\n' + Object.values(result.errors).join('\n');
                        }
                        Swal.fire('Gagal', errorMsg, 'error');
                    }
                } catch (error) {
                    console.error("Error saving data:", error);
                    Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                }
                
                isSubmitting.value = false;
            };

            const hapusKaryawan = (id_user) => {
                Swal.fire({
                    title: 'Hapus Data Karyawan?',
                    text: "Data ini tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const formData = new URLSearchParams();
                            formData.append('id_user', id_user);
                            
                            const response = await fetch('<?= base_url('admin/karyawan/delete') ?>', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: formData
                            });
                            
                            const res = await response.json();
                            if (res.status === 'success') {
                                modalInstance.hide();
                                Swal.fire('Terhapus!', res.message, 'success');
                                fetchKaryawan();
                            } else {
                                Swal.fire('Gagal!', res.message, 'error');
                            }
                        } catch (e) {
                            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                        }
                    }
                });
            };

            onMounted(() => {
                fetchKaryawan();
            });

            return {
                daftarKaryawan,
                perPage,
                currentPage,
                paginatedKaryawan,
                totalPages,
                isLoading,
                isSubmitting,
                isEdit,
                form,
                tambahKaryawan,
                editKaryawan,
                hapusKaryawan,
                simpanKaryawan
            };
        }
    }).mount('#adminKaryawanApp');
</script>
<?= $this->endSection() ?>
