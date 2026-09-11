<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('title') ?>Kamus NLP<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* CSS Khusus untuk memotong kosa kata agar maksimal 2 baris */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        word-break: break-word;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0" id="adminKamusApp">   
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold" style="color: #2B3385;">Kamus NLP (Natural Language Processing)</h4>
    </div>

    <!-- Tampilan Utama: Tabel Kamus -->
    <div class="card shadow-sm rounded-4 border-0 mb-5">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 1%; white-space: nowrap;">Kategori</th>
                            <th>Kosakata / Kata Kunci</th>
                            <th class="text-end pe-4" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in kamusList" :key="item.id">
                            <td class="ps-4 fw-bold text-primary">{{ item.kategori }}</td>
                            <td>
                                <div class="line-clamp-2 text-muted" style="font-family: monospace; font-size: 0.9em;">
                                    {{ item.kata_kunci }}
                                </div>
                            </td>
                            <td class="pe-4 text-end text-nowrap">
                                <button class="btn btn-sm btn-primary rounded-pill px-3" @click="bukaModal(item)">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                            </td>
                        </tr>
                        <tr v-if="kamusList.length === 0">
                            <td colspan="3" class="text-center py-4 text-muted">Belum ada data kamus.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kamus -->
    <div class="modal fade" id="kamusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <form @submit.prevent="simpanModal">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Edit Kategori: {{ formEdit.kategori }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kata Kunci / Kosakata (Pisahkan dengan koma) <span class="text-danger">*</span></label>
                            <textarea class="form-control" rows="8" v-model="formEdit.kata_kunci" style="font-family: monospace;" required></textarea>
                            <small class="text-muted d-block mt-2"><i class="fas fa-info-circle me-1"></i>Setiap kata kunci akan otomatis dibersihkan dari spasi berlebih dan diubah ke huruf besar (UPPERCASE).</small>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" :disabled="isSubmitting">
                            <i v-if="isSubmitting" class="fas fa-spinner fa-spin me-1"></i>
                            <i v-else class="fas fa-save me-1"></i> Simpan
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
            const kamusList = ref(<?= json_encode($kamusData) ?>);
            const formEdit = ref({ id: null, kategori: '', kata_kunci: '' });
            const isSubmitting = ref(false);
            let modalInstance = null;

            onMounted(() => {
                const modalEl = document.getElementById('kamusModal');
                if (modalEl) {
                    modalInstance = new bootstrap.Modal(modalEl);
                }
            });

            const bukaModal = (item) => {
                formEdit.value = { 
                    id: item.id, 
                    kategori: item.kategori, 
                    kata_kunci: item.kata_kunci 
                };
                if (modalInstance) {
                    modalInstance.show();
                }
            };

            const tutupModal = () => {
                if (modalInstance) {
                    modalInstance.hide();
                }
            };

            const simpanModal = async () => {
                isSubmitting.value = true;
                
                try {
                    const response = await fetch('<?= base_url('admin/kamus/update') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            id: formEdit.value.id,
                            kata_kunci: formEdit.value.kata_kunci
                        })
                    });

                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: result.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            alert(result.message);
                        }
                        
                        // Perbarui data di list utama
                        const index = kamusList.value.findIndex(k => k.id === formEdit.value.id);
                        if (index !== -1) {
                            kamusList.value[index].kata_kunci = result.data.kata_kunci;
                        }
                        
                        tutupModal();
                    } else {
                        throw new Error(result.message || 'Gagal menyimpan');
                    }
                } catch (error) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: error.message
                        });
                    } else {
                        alert(error.message);
                    }
                } finally {
                    isSubmitting.value = false;
                }
            };

            return {
                kamusList,
                formEdit,
                isSubmitting,
                bukaModal,
                simpanModal
            };
        }
    }).mount('#adminKamusApp');
</script>
<?= $this->endSection() ?>
