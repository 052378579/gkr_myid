const { createApp, ref, computed, onMounted } = Vue;

createApp({
    setup() {
        const noHp = ref('');
        const appInfo = ref(''); 

        onMounted(async () => {
            try {
                const response = await fetch(window.AppConfig.versiUrl);
                if (response.ok) {
                    const data = await response.json();
                    appInfo.value = `v.${data.version}`;
                }
            } catch (error) {
                console.error('Gagal mengambil info versi aplikasi:', error);
            }
        });

        const filterInput = () => {
            noHp.value = noHp.value.replace(/[^0-9]/g, '');
        };

        const regexPattern = /^08[0-9]{8,13}$/;

        const isFormValid = computed(() => {
            return regexPattern.test(noHp.value);
        });

        const errorMessage = computed(() => {
            if (noHp.value.length === 0) return '';
            if (!noHp.value.startsWith('08')) return 'Diawali dengan "08"';
            if (noHp.value.length < 10) return '';
            if (noHp.value.length > 15) return 'Maksimal 15 digit';
            if (!isFormValid.value) return 'Format tidak valid';
            return ''; 
        });

        const onSubmit = (event) => {
            if (!isFormValid.value) {
                event.preventDefault();
            }
        };

        return {
            noHp,
            filterInput,
            isFormValid,
            errorMessage,
            onSubmit,
            appInfo 
        }
    }
}).mount('#rnd');

if ('serviceWorker' in navigator && window.AppConfig && window.AppConfig.swUrl) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register(window.AppConfig.swUrl)
            .then((reg) => {
                // Berhasil (Silent fallback)
            })
            .catch((err) => {
                console.error('PWA Service Worker gagal didaftarkan: ', err);
            });
    });
}
