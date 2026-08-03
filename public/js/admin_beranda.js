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

const { createApp, ref, computed, watch, onMounted } = Vue;

createApp({
    setup() {
        const currentTab = ref('images');
        const sites = ref([]);
        const images = ref([]);
        const doodles = ref([]);
        const perPage = ref(10);
        const currentPageSites = ref(1);
        const currentPageImages = ref(1);
        const searchSite = ref('');
        const searchImage = ref('');

        // Modal Edit Site
        const materials = ref([]);
        const formEditSite = ref({ id: '', title: '', url: '', description: '', keywords: '', clicks: '' });
        const modalEditSiteInstance = ref(null);
        const selectedMaterial = ref('');
        const selectedWarna = ref('');
        const selectedMaterialImage = ref('');
        const selectedWarnaImage = ref('');

        // Modal Edit Image
        const formEditImage = ref({ id: '', title: '', alt: '', imageUrl: '', siteUrl: '', clicks: 0, broken: 0 });
        const modalEditImageInstance = ref(null);

        watch([selectedMaterial, selectedWarna], ([newMat, newWarna]) => {
            if (newMat || newWarna) {
                let kw = formEditSite.value.keywords || '';
                // Pecah teks kata kunci menjadi array dan bersihkan spasi
                let kwArray = kw.split(',').map(s => s.trim()).filter(s => s !== '');
                
                // Tambahkan Material jika belum ada di dalam Kata Kunci
                if (newMat && !kwArray.includes(newMat)) {
                    kwArray.push(newMat);
                }
                
                // Tambahkan Warna jika belum ada di dalam Kata Kunci
                if (newWarna && !kwArray.includes(newWarna)) {
                    kwArray.push(newWarna);
                }
                
                // Gabungkan kembali menjadi teks biasa dengan pemisah koma
                formEditSite.value.keywords = kwArray.join(', ');
            }
        });

        watch([selectedMaterialImage, selectedWarnaImage], ([newMat, newWarna]) => {
            if (newMat || newWarna) {
                let kw = formEditImage.value.keywords || '';
                let kwArray = kw.split(',').map(s => s.trim()).filter(s => s !== '');
                
                if (newMat && !kwArray.includes(newMat)) {
                    kwArray.push(newMat);
                }
                if (newWarna && !kwArray.includes(newWarna)) {
                    kwArray.push(newWarna);
                }
                
                formEditImage.value.keywords = kwArray.join(', ');
            }
        });

        const uniqueMaterials = computed(() => {
            const mats = materials.value.map(m => m.material);
            return [...new Set(mats)];
        });
        const uniqueWarna = computed(() => {
            let source = materials.value;
            // Jika material dipilih, saring daftar warna sesuai material tersebut
            if (selectedMaterial.value) {
                source = materials.value.filter(m => m.material === selectedMaterial.value);
            }
            const warns = source.map(m => m.warna);
            return [...new Set(warns)];
        });

        watch(searchSite, () => currentPageSites.value = 1);
        watch(searchImage, () => currentPageImages.value = 1);

        const previewSitusUrl = computed(() => {
            const url = formEditSite.value.url;
            if (!url) return '';
            
            // Konversi format URL galeri menjadi direct image URL
            const match = url.match(/\?([^#]+)#pid=(.+)$/);
            if (match) {
                return `https://foto.gkr.my.id/${match[1]}/${match[2]}`;
            }
            
            // Jika format aslinya sudah berupa gambar langsung
            if (url.match(/\.(jpeg|jpg|gif|png|webp)$/i)) {
                return url;
            }
            
            return ''; // Kosongkan preview jika bukan gambar
        });

        const filteredSites = computed(() => {
            if (!searchSite.value) return sites.value;
            const term = searchSite.value.toLowerCase();
            return sites.value.filter(site => site.title && site.title.toLowerCase().includes(term));
        });

        const paginatedSites = computed(() => {
            const start = (currentPageSites.value - 1) * perPage.value;
            return filteredSites.value.slice(start, start + perPage.value);
        });
        const totalSitePages = computed(() => Math.ceil(filteredSites.value.length / perPage.value) || 1);

        const filteredImages = computed(() => {
            if (!searchImage.value) return images.value;
            const term = searchImage.value.toLowerCase();
            return images.value.filter(img => {
                const titleMatch = img.title && img.title.toLowerCase().includes(term);
                const altMatch = img.alt && img.alt.toLowerCase().includes(term);
                return titleMatch || altMatch;
            });
        });

        const paginatedImages = computed(() => {
            const start = (currentPageImages.value - 1) * perPage.value;
            return filteredImages.value.slice(start, start + perPage.value);
        });
        const totalImagePages = computed(() => Math.ceil(filteredImages.value.length / perPage.value) || 1);

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

        const loadMaterials = async () => {
            try {
                const res = await fetch(window.AppConfig.apiGetMaterials);
                const json = await res.json();
                if (json.status === 'sukses' || json.status === 'success') {
                    materials.value = json.data;
                }
            } catch (e) {
                console.error('Failed to load materials', e);
            }
        };

        const deleteSite = async (id) => {
            const result = await Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Situs ini akan dihapus dari sistem!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                try {
                    await fetch(window.AppConfig.apiDeleteSite + id, { method: 'POST' });
                    if (modalEditSiteInstance.value) {
                        modalEditSiteInstance.value.hide();
                    }
                    loadSites();
                    Swal.fire('Terhapus!', 'Data situs telah berhasil dihapus.', 'success');
                } catch (e) {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus situs.', 'error');
                }
            }
        };

        const deleteImage = async (id) => {
            const result = await Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Gambar ini akan dihapus dari sistem!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                try {
                    await fetch(window.AppConfig.apiDeleteImage + id, {method: 'POST'});
                    if (modalEditImageInstance.value) {
                        modalEditImageInstance.value.hide();
                    }
                    loadImages();
                    Swal.fire('Terhapus!', 'Data gambar telah berhasil dihapus.', 'success');
                } catch (e) {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus gambar.', 'error');
                }
            }
        };

        const tambahSite = async () => {
            const { value: formValues } = await Swal.fire({
                title: 'Tambah Situs',
                html:
                    '<div class="mb-3 text-start"><label class="form-label">Judul</label><input id="swal-s1" class="form-control" placeholder="Masukkan judul situs"></div>' +
                    '<div class="mb-3 text-start"><label class="form-label">URL</label><input id="swal-s2" class="form-control" placeholder="https://example.com"></div>' +
                    '<div class="mb-3 text-start"><label class="form-label">Deskripsi</label><textarea id="swal-s3" class="form-control" placeholder="Deskripsi singkat"></textarea></div>' +
                    '<div class="mb-3 text-start"><label class="form-label">Kata Kunci</label><input id="swal-s4" class="form-control" placeholder="kata1, kata2"></div>',
                focusConfirm: false,
                showCancelButton: true,
                width: '600px',
                preConfirm: () => {
                    const title = document.getElementById('swal-s1').value;
                    const url = document.getElementById('swal-s2').value;
                    if (!title || !url) {
                        Swal.showValidationMessage('Judul dan URL wajib diisi!');
                        return false;
                    }
                    return {
                        title: title,
                        url: url,
                        description: document.getElementById('swal-s3').value,
                        keywords: document.getElementById('swal-s4').value
                    }
                }
            });
            
            if (formValues) {
                const formData = new FormData();
                formData.append('title', formValues.title);
                formData.append('url', formValues.url);
                formData.append('description', formValues.description);
                formData.append('keywords', formValues.keywords);
                
                try {
                    const res = await fetch(window.AppConfig.apiStoreSite, {
                        method: 'POST',
                        body: formData
                    });
                    const json = await res.json();
                    if (json.status === 'sukses') {
                        loadSites();
                        Swal.fire('Berhasil!', 'Situs baru berhasil ditambahkan.', 'success');
                    } else {
                        Swal.fire('Gagal!', 'Terjadi kesalahan atau URL sudah ada.', 'error');
                    }
                } catch (e) {
                    Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
                }
            }
        };

        const tambahImage = async () => {
            const { value: formValues } = await Swal.fire({
                title: 'Tambah Gambar',
                html:
                    '<div class="mb-3 text-start"><label class="form-label">Judul/Nama Gambar</label><input id="swal-i1" class="form-control" placeholder="Judul gambar"></div>' +
                    '<div class="mb-3 text-start"><label class="form-label">Alt (Alternatif)</label><input id="swal-i2" class="form-control" placeholder="Teks alternatif"></div>' +
                    '<div class="mb-3 text-start"><label class="form-label">URL Gambar (Source)</label><input id="swal-i3" class="form-control" placeholder="https://example.com/image.jpg"></div>' +
                    '<div class="mb-3 text-start"><label class="form-label">URL Situs Induk</label><input id="swal-i4" class="form-control" placeholder="https://example.com"></div>',
                focusConfirm: false,
                showCancelButton: true,
                width: '600px',
                preConfirm: () => {
                    const imageUrl = document.getElementById('swal-i3').value;
                    const siteUrl = document.getElementById('swal-i4').value;
                    if (!imageUrl || !siteUrl) {
                        Swal.showValidationMessage('URL Gambar dan URL Situs Induk wajib diisi!');
                        return false;
                    }
                    return {
                        title: document.getElementById('swal-i1').value,
                        alt: document.getElementById('swal-i2').value,
                        imageUrl: imageUrl,
                        siteUrl: siteUrl
                    }
                }
            });
            
            if (formValues) {
                const formData = new FormData();
                formData.append('title', formValues.title);
                formData.append('alt', formValues.alt);
                formData.append('imageUrl', formValues.imageUrl);
                formData.append('siteUrl', formValues.siteUrl);
                
                try {
                    const res = await fetch(window.AppConfig.apiStoreImage, {
                        method: 'POST',
                        body: formData
                    });
                    const json = await res.json();
                    if (json.status === 'sukses') {
                        loadImages();
                        Swal.fire('Berhasil!', 'Gambar baru berhasil ditambahkan.', 'success');
                    } else {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat menambahkan gambar.', 'error');
                    }
                } catch (e) {
                    Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
                }
            }
        };

        const editSite = (site) => {
            formEditSite.value = { ...site };
            selectedMaterial.value = ''; // Reset Material
            selectedWarna.value = '';    // Reset Warna
            const el = document.getElementById('modalEditSite');
            if(el) {
                modalEditSiteInstance.value = new bootstrap.Modal(el);
                modalEditSiteInstance.value.show();
            }
        };

        const simpanEditSite = async () => {
            const formData = new FormData();
            formData.append('title', formEditSite.value.title);
            formData.append('url', formEditSite.value.url);
            formData.append('description', formEditSite.value.description || '');
            formData.append('keywords', formEditSite.value.keywords || '');
            formData.append('clicks', formEditSite.value.clicks || 0);
            
            try {
                const res = await fetch(window.AppConfig.apiUpdateSite + formEditSite.value.id, {
                    method: 'POST',
                    body: formData
                });
                const json = await res.json();
                
                if (json.status === 'sukses') {
                    if(modalEditSiteInstance.value) modalEditSiteInstance.value.hide();
                    loadSites();
                    Swal.fire('Berhasil!', 'Data situs telah diubah.', 'success');
                } else {
                    Swal.fire('Gagal!', json.pesan || 'Gagal menyimpan perubahan.', 'error');
                }
            } catch (e) {
                Swal.fire('Error sistem!', 'Terjadi kesalahan jaringan atau server.', 'error');
            }
        };

        const openModalEditImage = (img) => {
            formEditImage.value = { ...img };
            selectedMaterialImage.value = ''; // Reset Material Gambar
            selectedWarnaImage.value = '';    // Reset Warna Gambar
            const el = document.getElementById('modalEditImage');
            if(el) {
                modalEditImageInstance.value = new bootstrap.Modal(el);
                modalEditImageInstance.value.show();
            }
        };

        const simpanEditImage = async () => {
            const formData = new FormData();
            formData.append('id', formEditImage.value.id || '');
            formData.append('title', formEditImage.value.title || '');
            formData.append('alt', formEditImage.value.alt || '');
            formData.append('description', formEditImage.value.description || '');
            formData.append('url', formEditImage.value.url || '');
            formData.append('imageUrl', formEditImage.value.imageUrl || '');
            formData.append('siteUrl', formEditImage.value.siteUrl || '');
            formData.append('keywords', formEditImage.value.keywords || '');
            formData.append('kode_bom', formEditImage.value.kode_bom || '');
            formData.append('clicks', formEditImage.value.clicks || 0);
            formData.append('broken', formEditImage.value.broken || 0);
            
            try {
                await fetch(window.AppConfig.apiUpdateImage + formEditImage.value.id, {
                    method: 'POST',
                    body: formData
                });
                if(modalEditImageInstance.value) modalEditImageInstance.value.hide();
                loadImages();
                Swal.fire('Berhasil!', 'Data mesin pencari telah berhasil diperbarui.', 'success');
            } catch (e) {
                Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
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
            const modalEl = document.getElementById('doodleModal');
            if (modalEl) {
                doodleModal.value = new bootstrap.Modal(modalEl);
            }
            loadSites();
            loadImages();
            loadMaterials();
            loadDoodles();
        });

        return {
            currentTab,
            sites,
            images,
            perPage,
            currentPageSites,
            currentPageImages,
            searchSite,
            searchImage,
            paginatedSites,
            totalSitePages,
            paginatedImages,
            totalImagePages,
            deleteSite,
            deleteImage,
            tambahSite,
            tambahImage,
            editSite,
            simpanEditSite,
            formEditSite,
            previewSitusUrl,
            openModalEditImage,
            simpanEditImage,
            formEditImage,
            materials,
            uniqueMaterials,
            uniqueWarna,
            selectedMaterial,
            selectedWarna,
            selectedMaterialImage,
            selectedWarnaImage,
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
