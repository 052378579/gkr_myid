const { createApp, ref, onMounted, nextTick } = Vue;

createApp({
    setup() {
        const messages = ref([]);
        const sessions = ref([]);
        const activeSessionId = ref('main');
        const newMessage = ref('');
        const isLoading = ref(false);
        const chatContainer = ref(null);

        const scrollToBottom = () => {
            nextTick(() => {
                if (chatContainer.value) {
                    chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
                }
            });
        };

        const fetchSessions = async () => {
            try {
                const response = await axios.get('/api/ai/sessions');
                if (response.data && !response.data.error) {
                    sessions.value = response.data;
                }
            } catch (error) {
                console.error("Gagal mengambil daftar sesi:", error);
            }
        };

        const fetchMessages = async () => {
            try {
                const response = await axios.get(`/api/ai/messages?session_id=${activeSessionId.value}`);
                if (response.data && !response.data.error) {
                    messages.value = response.data;
                    scrollToBottom();
                } else if (response.data && response.data.error) {
                    messages.value = [];
                }
            } catch (error) {
                console.error("Gagal mengambil riwayat pesan:", error);
                messages.value = [];
            }
        };

        const selectSession = async (id) => {
            activeSessionId.value = id;
            await fetchMessages();
        };

        const sendMessage = async () => {
            const text = newMessage.value.trim();
            if (!text) return;

            // Optimistic UI update
            const tempId = Date.now();
            messages.value.push({
                id: tempId,
                sender: 'user',
                message: text,
                source: 'web',
                created_at: new Date().toISOString()
            });
            
            newMessage.value = '';
            isLoading.value = true;
            scrollToBottom();

            try {
                const formData = new FormData();
                formData.append('message', text);
                formData.append('session_id', activeSessionId.value);

                const response = await axios.post('/api/ai/chat', formData);
                
                if (response.data && response.data.status === 'success') {
                    // Refresh data for safety or just push bot response
                    await fetchMessages();
                }
            } catch (error) {
                console.error("Error mengirim pesan:", error);
                messages.value.push({
                    id: Date.now() + 1,
                    sender: 'bot',
                    message: "Terjadi kesalahan saat menghubungi server. Silakan coba lagi.",
                    created_at: new Date().toISOString()
                });
            } finally {
                isLoading.value = false;
                scrollToBottom();
            }
        };

        const renderMarkdown = (text) => {
            if (!text) return '';
            return marked.parse(text, { breaks: true });
        };

        const formatTime = (datetimeString) => {
            if (!datetimeString) return '';
            const d = new Date(datetimeString);
            return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        };

        const createNewChat = async () => {
            try {
                const response = await axios.post('/api/ai/sessions');
                if (response.data && response.data.status === 'success') {
                    const newId = response.data.session.id;
                    await fetchSessions();
                    activeSessionId.value = newId;
                    messages.value = [];
                }
            } catch (error) {
                console.error("Gagal membuat sesi baru:", error);
            }
        };

        const deleteSession = async (id) => {
            if (!confirm('Apakah Anda yakin ingin menghapus sesi ini?')) return;
            try {
                const response = await axios.delete(`/api/ai/sessions/${id}`);
                if (response.data && response.data.status === 'success') {
                    if (activeSessionId.value === id) {
                        activeSessionId.value = 'main';
                        await fetchMessages();
                    }
                    await fetchSessions();
                }
            } catch (error) {
                console.error("Gagal menghapus sesi:", error);
            }
        };

        onMounted(() => {
            fetchSessions();
            fetchMessages();
            
            // Initialize Fancybox for dynamic content
            if (typeof Fancybox !== 'undefined') {
                Fancybox.bind("[data-fancybox]", {
                    // Custom options if needed
                });
            }
        });

        return {
            messages,
            sessions,
            activeSessionId,
            newMessage,
            isLoading,
            chatContainer,
            sendMessage,
            renderMarkdown,
            formatTime,
            createNewChat,
            selectSession,
            deleteSession
        };
    }
}).mount('#app');
