import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Ranking Satu';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),

    layout: (name) => {
        // Untuk sementara halaman auth tetap menggunakan
        // layout authentication bawaan.
        if (name.startsWith('auth/')) {
            return AuthLayout;
        }

        // Admin, participant, dan halaman lainnya
        // menentukan layout masing-masing di file Vue.
        return null;
    },

    progress: {
        color: '#0094DE',
    },
});

// Set light / dark mode saat aplikasi dimuat.
initializeTheme();

// Listen flash toast dari Laravel.
initializeFlashToast();