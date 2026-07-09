const { createApp, ref } = Vue;

createApp({
    setup() {
        const query = ref('');

        const search = () => {
            if(query.value.trim() !== '') {
                window.location.href = window.AppConfig.searchUrl + '?q=' + encodeURIComponent(query.value);
            }
        };

        return {
            query,
            search
        }
    }
}).mount('#app');
