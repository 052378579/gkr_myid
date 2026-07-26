<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('title') ?>Manajemen Doodle<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="gkr">
    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-header border-0 d-flex justify-content-between align-items-center pt-4 pb-2">
            <h5 class="mb-0 fw-bold">Doodle</h5>
            <button class="btn btn-primary shadow-sm rounded-pill px-3" @click="bukaModalDoodle"><i class="fas fa-plus me-1"></i> Doodle Baru</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="">
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
                            <input type="file" class="form-control" ref="doodleFileInput" accept=".png,.webp,.gif" @change="onDoodleFileChange" :required="!isEditDoodle">
                            <small class="text-muted d-block mt-1"><i class="fas fa-info-circle me-1"></i>Gunakan gambar transparan (PNG/WEBP) agar menyatu dengan Mode Gelap.</small>
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
<script src="<?= base_url('js/admin_doodle.js') ?>?v=<?= time() ?>"></script>
<?= $this->endSection() ?>
