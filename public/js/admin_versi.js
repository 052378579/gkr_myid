const { createApp, ref, computed, onMounted } = Vue;

createApp({
    setup() {
        const daftarVersi = ref([]);
        const isLoading = ref(true);
        const isSubmitting = ref(false);
        const isEdit = ref(false);
        const currentPage = ref(1);
        const itemsPerPage = ref(10);
        
        const totalPages = computed(() => {
            return Math.ceil(daftarVersi.value.length / itemsPerPage.value) || 1;
        });
        
        const paginatedVersi = computed(() => {
            const start = (currentPage.value - 1) * itemsPerPage.value;
            const end = start + itemsPerPage.value;
            return daftarVersi.value.slice(start, end);
        });

        const nextPage = () => {
            if (currentPage.value < totalPages.value) currentPage.value++;
        };

        const prevPage = () => {
            if (currentPage.value > 1) currentPage.value--;
        };

        const goToPage = (page) => {
            currentPage.value = page;
        };
        
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
                const response = await fetch(window.AppConfig.apiGetAll);
                const jsonResponse = await response.json();
                daftarVersi.value = jsonResponse.data || [];
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
            
            const url = isEdit.value ? window.AppConfig.apiUpdate : window.AppConfig.apiStore;
            
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
                        
                        const response = await fetch(window.AppConfig.apiDelete, {
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
            formatTanggal,
            paginatedVersi,
            currentPage,
            totalPages,
            nextPage,
            prevPage,
            goToPage
        };
    }
}).mount('#adminVersiApp');
