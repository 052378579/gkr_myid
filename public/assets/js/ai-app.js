const { createApp, ref, onMounted, nextTick } = Vue;

createApp({
    setup() {
        const messages = ref([]);
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

        const fetchMessages = async () => {
            try {
                const response = await axios.get('/api/ai/messages');
                if (response.data && !response.data.error) {
                    messages.value = response.data;
                    scrollToBottom();
                }
            } catch (error) {
                console.error("Gagal mengambil riwayat pesan:", error);
            }
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

        const createNewChat = () => {
            // For now, since unified session uses 1 history based on phone number
            alert("Sesi baru. (Untuk skenario Unified Session, ini bisa membersihkan layar cache lokal saja).");
        };

        onMounted(() => {
            fetchMessages();
        });

        return {
            messages,
            newMessage,
            isLoading,
            chatContainer,
            sendMessage,
            renderMarkdown,
            formatTime,
            createNewChat
        };
    }
}).mount('#app');
