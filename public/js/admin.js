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
        const doodles = ref([]);
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
            const res = await fetch(window.AppConfig.apiGetSites);
            const json = await res.json();
            sites.value = json.data;
        };

        const loadImages = async () => {
            const res = await fetch(window.AppConfig.apiGetImages);
            const json = await res.json();
            images.value = json.data;
        };

        const deleteSite = async (id) => {
            if(confirm('Yakin hapus situs ini?')) {
                await fetch(window.AppConfig.apiDeleteSite + id, {method: 'POST'});
                loadSites();
            }
        };

        const deleteImage = async (id) => {
            if(confirm('Yakin hapus gambar ini?')) {
                await fetch(window.AppConfig.apiDeleteImage + id, {method: 'POST'});
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
                
                await fetch(window.AppConfig.apiUpdateSite + site.id, {
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
                
                await fetch(window.AppConfig.apiUpdateImage + img.id, {
                    method: 'POST',
                    body: formData
                });
                loadImages();
                Swal.fire('Berhasil!', 'Data gambar telah diubah.', 'success');
            }
        };

        // --- DOODLE LOGIC ---
        const doodleModal = ref(null);
        const doodleFileInput = ref(null);
        const doodleForm = ref({ id_doodle: '', event: '', tgl_mulai: '', tgl_selesai: '', status: 'aktif' });
        const doodleGambarFile = ref(null);
        const doodlePreview = ref('');
        const isEditDoodle = ref(false);
        const isSubmittingDoodle = ref(false);

        const formatTanggal = (tgl) => {
            const date = new Date(tgl);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        };

        const loadDoodles = async () => {
            const res = await fetch(window.AppConfig.apiGetAllDoodle);
            const json = await res.json();
            if (json.status === 'success') {
                doodles.value = json.data;
            }
        };

        const onDoodleFileChange = (e) => {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2097152) {
                    Swal.fire('Error', 'Ukuran gambar maksimal 2MB', 'error');
                    doodleFileInput.value.value = '';
                    doodlePreview.value = isEditDoodle.value ? window.AppConfig.urlDokumenDoodle + doodleForm.value.gambarLama : '';
                    return;
                }
                doodleGambarFile.value = file;
                doodlePreview.value = URL.createObjectURL(file);
            }
        };

        const bukaModalDoodle = () => {
            isEditDoodle.value = false;
            doodleForm.value = { id_doodle: '', event: '', tgl_mulai: '', tgl_selesai: '', status: 'aktif' };
            doodleGambarFile.value = null;
            doodlePreview.value = '';
            if(doodleFileInput.value) doodleFileInput.value.value = '';
            doodleModal.value.show();
        };

        const editDoodle = (item) => {
            isEditDoodle.value = true;
            doodleForm.value = { ...item };
            doodleForm.value.gambarLama = item.gambar;
            doodleGambarFile.value = null;
            doodlePreview.value = window.AppConfig.urlDokumenDoodle + item.gambar;
            if(doodleFileInput.value) doodleFileInput.value.value = '';
            doodleModal.value.show();
        };

        const simpanDoodle = async () => {
            isSubmittingDoodle.value = true;
            const formData = new FormData();
            formData.append('event', doodleForm.value.event);
            formData.append('tgl_mulai', doodleForm.value.tgl_mulai);
            formData.append('tgl_selesai', doodleForm.value.tgl_selesai);
            formData.append('status', doodleForm.value.status);
            
            if (doodleGambarFile.value) {
                formData.append('gambar', doodleGambarFile.value);
            }
            
            let endpoint = window.AppConfig.apiStoreDoodle;
            if (isEditDoodle.value) {
                endpoint = window.AppConfig.apiUpdateDoodle;
                formData.append('id_doodle', doodleForm.value.id_doodle);
            }

            try {
                const response = await fetch(endpoint, { method: 'POST', body: formData });
                const res = await response.json();
                if (response.ok) {
                    Swal.fire('Berhasil', res.message, 'success');
                    doodleModal.value.hide();
                    loadDoodles();
                } else {
                    Swal.fire('Gagal', res.message || 'Gagal menyimpan', 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
            } finally {
                isSubmittingDoodle.value = false;
            }
        };

        const deleteDoodle = async (id) => {
            if(confirm("Yakin hapus doodle ini?")) {
                const formData = new FormData();
                formData.append('id_doodle', id);
                const res = await fetch(window.AppConfig.apiDeleteDoodle, { method: 'POST', body: formData });
                if (res.ok) {
                    loadDoodles();
                }
            }
        };

        onMounted(() => {
            doodleModal.value = new bootstrap.Modal(document.getElementById('doodleModal'));
            loadSites();
            loadImages();
            loadDoodles();
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
            editImage,
            // Doodle
            doodles,
            doodleForm,
            isEditDoodle,
            isSubmittingDoodle,
            doodleFileInput,
            doodlePreview,
            formatTanggal,
            onDoodleFileChange,
            bukaModalDoodle,
            editDoodle,
            simpanDoodle,
            deleteDoodle
        }
    }
}).mount('#gkr');
