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
            divisi: '',
            status: 'aktif'
        });

        const getDivisiBadgeStyle = (divisi) => {
            switch (divisi) {
                case 'Produksi 1': return { backgroundColor: '#FFA500', color: '#000000' };
                case 'Produksi 2': return { backgroundColor: '#0000FF', color: '#ffffff' };
                case 'Produksi 4': return { backgroundColor: '#ff0000', color: '#ffffff' };
                default: return {}; 
            }
        };

        const getDivisiBadgeClass = (divisi) => {
            if (['Produksi 1', 'Produksi 2', 'Produksi 4'].includes(divisi)) {
                return 'badge rounded-pill';
            }
            return 'badge bg-secondary rounded-pill';
        };

        let modalInstance = null;

        const fetchKaryawan = async () => {
            isLoading.value = true;
            try {
                const response = await fetch(window.AppConfig.apiGetAll);
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
                divisi: '',
                status: 'aktif'
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
            const url = isEdit.value ? window.AppConfig.apiUpdate : window.AppConfig.apiStore;
            
            const formData = new URLSearchParams();
            if(isEdit.value) formData.append('id_user', form.value.id_user);
            formData.append('nama_lengkap', form.value.nama_lengkap);
            formData.append('no_hp', form.value.no_hp);
            formData.append('divisi', form.value.divisi);
            formData.append('status', form.value.status);

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
                        
                        const response = await fetch(window.AppConfig.apiDelete, {
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
            simpanKaryawan,
            getDivisiBadgeStyle,
            getDivisiBadgeClass
        };
    }
}).mount('#adminKaryawanApp');
