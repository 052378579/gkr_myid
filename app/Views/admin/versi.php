<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Manajemen Versi<?= $this->endSection() ?>

<?= $this->section('content') ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white glassmorphism fixed-top shadow-sm" style="background: rgba(255, 255, 255, 0.95) !important; backdrop-filter: blur(10px);">
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
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Versi</th>
                            <th>Tanggal Rilis</th>
                            <th>Judul</th>
                            <th>Highlights</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in daftarVersi" :key="item.id">
                            <td class="ps-4 fw-bold">{{ item.versi }}</td>
                            <td>{{ formatTanggal(item.tanggal_rilis) }}</td>
                            <td>{{ item.judul }}</td>
                            <td>
                                <span class="badge bg-primary me-1 rounded-pill" v-if="item.improvements.length > 0">{{ item.improvements.length }} Imprv</span>
                                <span class="badge bg-danger me-1 rounded-pill" v-if="item.fixes.length > 0">{{ item.fixes.length }} Fixes</span>
                                <span class="badge bg-success rounded-pill" v-if="item.patches.length > 0">{{ item.patches.length }} Patch</span>
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

<?= $this->section('scripts') ?>
<script>
    const { createApp, ref, onMounted } = Vue;

    createApp({
        setup() {
            const daftarVersi = ref([]);
            const isLoading = ref(true);
            const isSubmitting = ref(false);
            const isEdit = ref(false);
            
            const form = ref({
                id: null,
                versi: '',
                tanggal_rilis: '',
                judul: '',
                deskripsi: '',
                improvements: [],
                fixes: [],
                patches: []
            });

            let modalInstance = null;

            const fetchVersi = async () => {
                isLoading.value = true;
                try {
                    const response = await fetch('<?= base_url('admin/versi/getAll') ?>');
                    daftarVersi.value = await response.json();
                } catch (error) {
                    console.error("Error fetching data:", error);
                    Swal.fire('Error', 'Gagal memuat data versi.', 'error');
                }
                isLoading.value = false;
            };

            const resetForm = () => {
                form.value = {
                    id: null,
                    versi: '',
                    tanggal_rilis: new Date().toISOString().split('T')[0],
                    judul: '',
                    deskripsi: '',
                    improvements: [],
                    fixes: [],
                    patches: []
                };
            };

            const tambahVersi = () => {
                isEdit.value = false;
                resetForm();
                if(!modalInstance) modalInstance = new bootstrap.Modal(document.getElementById('versiModal'));
                modalInstance.show();
            };

            const editVersi = (item) => {
                isEdit.value = true;
                // Deep copy to avoid reactive mutation before save
                form.value = JSON.parse(JSON.stringify(item));
                if(!modalInstance) modalInstance = new bootstrap.Modal(document.getElementById('versiModal'));
                modalInstance.show();
            };

            const addListItem = (type) => {
                form.value[type].push('');
            };

            const removeListItem = (type, index) => {
                form.value[type].splice(index, 1);
            };

            const simpanVersi = async () => {
                isSubmitting.value = true;
                
                // Filter out empty strings from arrays
                form.value.improvements = form.value.improvements.filter(i => i.trim() !== '');
                form.value.fixes = form.value.fixes.filter(i => i.trim() !== '');
                form.value.patches = form.value.patches.filter(i => i.trim() !== '');
                
                const url = isEdit.value ? '<?= base_url('admin/versi/update') ?>' : '<?= base_url('admin/versi/store') ?>';
                
                const formData = new URLSearchParams();
                if(isEdit.value) formData.append('id', form.value.id);
                formData.append('versi', form.value.versi);
                formData.append('tanggal_rilis', form.value.tanggal_rilis);
                formData.append('judul', form.value.judul);
                formData.append('deskripsi', form.value.deskripsi);
                
                form.value.improvements.forEach(item => formData.append('improvements[]', item));
                form.value.fixes.forEach(item => formData.append('fixes[]', item));
                form.value.patches.forEach(item => formData.append('patches[]', item));

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
                        fetchVersi();
                    } else {
                        Swal.fire('Gagal', result.message, 'error');
                    }
                } catch (error) {
                    console.error("Error saving data:", error);
                    Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                }
                
                isSubmitting.value = false;
            };

            const hapusVersi = (id) => {
                Swal.fire({
                    title: 'Hapus Versi?',
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
                            formData.append('id', id);
                            
                            const response = await fetch('<?= base_url('admin/versi/delete') ?>', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: formData
                            });
                            
                            const res = await response.json();
                            if (res.status === 'success') {
                                Swal.fire('Terhapus!', res.message, 'success');
                                fetchVersi();
                            } else {
                                Swal.fire('Gagal!', res.message, 'error');
                            }
                        } catch (e) {
                            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                        }
                    }
                });
            };

            const formatTanggal = (dateStr) => {
                if (!dateStr) return '';
                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                return new Date(dateStr).toLocaleDateString('id-ID', options);
            };

            onMounted(() => {
                fetchVersi();
            });

            return {
                daftarVersi,
                isLoading,
                isSubmitting,
                isEdit,
                form,
                tambahVersi,
                editVersi,
                hapusVersi,
                simpanVersi,
                addListItem,
                removeListItem,
                formatTanggal
            };
        }
    }).mount('#adminVersiApp');
</script>
<?= $this->endSection() ?>
