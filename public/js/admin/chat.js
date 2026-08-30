const pageConfig = document.querySelector('meta[name="page-config"]');
const apiGetLogs = pageConfig ? pageConfig.getAttribute('data-api-get-logs') : '';

const chatApp = Vue.createApp({
    data() {
        return {
            sessions: [],
            chatLogs: [],
            loadingSessions: false,
            loadingChat: false,
            search: '',
            activeNoHp: null,
            activeNamaLengkap: null,
            activeFotoProfil: null,
            searchTimeout: null,
            baseUrl: document.querySelector('meta[name="app-config"]') ? document.querySelector('meta[name="app-config"]').getAttribute('data-base-url') : ''
        }
    },
    mounted() {
        this.fetchSessions();
    },
    methods: {
        async fetchSessions() {
            this.loadingSessions = true;
            try {
                const response = await fetch(`${apiGetLogs}?action=sessions&search=${encodeURIComponent(this.search)}`);
                const result = await response.json();
                if (result.status === 'success') {
                    this.sessions = result.data;
                }
            } catch (error) {
                console.error("Error fetching sessions:", error);
            } finally {
                this.loadingSessions = false;
            }
        },
        async openChat(noHp, namaLengkap = null, fotoProfil = null) {
            this.activeNoHp = noHp;
            this.activeNamaLengkap = namaLengkap;
            this.activeFotoProfil = fotoProfil;
            this.loadingChat = true;
            this.chatLogs = [];
            try {
                const response = await fetch(`${apiGetLogs}?action=detail&no_hp=${encodeURIComponent(noHp)}`);
                const result = await response.json();
                if (result.status === 'success') {
                    this.chatLogs = result.data;
                    
                    // Scroll to bottom after render
                    setTimeout(() => {
                        this.scrollToBottom();
                    }, 100);
                }
            } catch (error) {
                console.error("Error fetching chat details:", error);
            } finally {
                this.loadingChat = false;
            }
        },
        scrollToBottom() {
            const container = this.$refs.chatScroll;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },
        async clearChat() {
            if (!this.activeNoHp) return;
            
            const result = await Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Riwayat percakapan untuk nomor ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                try {
                    const apiClearUrl = apiGetLogs + '/clear'; // apiGetLogs is /api/admin/chat
                    const response = await fetch(apiClearUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ no_hp: this.activeNoHp })
                    });
                    
                    const data = await response.json();
                    if (data.status === 'success') {
                        Swal.fire('Terhapus!', data.message, 'success');
                        this.chatLogs = [];
                        this.activeNoHp = null;
                        this.activeNamaLengkap = null;
                        this.fetchSessions();
                    } else {
                        Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                    }
                } catch (error) {
                    console.error("Error clearing chat:", error);
                    Swal.fire('Error!', 'Gagal menghubungi server.', 'error');
                }
            }
        },
        getAvatarUrl(nama, foto) {
            if (foto) {
                return this.baseUrl + 'dokumen/karyawan/' + foto;
            }
            const bg = '2B3385';
            return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(nama || 'User') + '&background=' + bg + '&color=fff';
        },
        formatTime(datetimeStr) {
            if (!datetimeStr) return '';
            const date = new Date(datetimeStr);
            const today = new Date();
            const isToday = date.getDate() === today.getDate() && date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear();
            
            const timeStr = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            if (isToday) return timeStr;
            
            const dateStr = date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: '2-digit' });
            return `${dateStr} ${timeStr}`;
        }
    },
    watch: {
        search(newVal) {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            this.searchTimeout = setTimeout(() => {
                this.fetchSessions();
            }, 500); // Debounce 500ms
        }
    }
});

chatApp.mount('#chatApp');

