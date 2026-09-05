import { createInertiaApp } from '@inertiajs/vue3'

import {
    initializeTheme,
} from '@/composables/useAppearance'

import AuthLayout from '@/layouts/AuthLayout.vue'

import {
    initializeFlashToast,
} from '@/lib/flashToast'

const appName =
    import.meta.env.VITE_APP_NAME ||
    'Ranking Satu'

void createInertiaApp({
    title: (title) =>
        title
            ? `${title} - ${appName}`
            : appName,

    layout: (name) => {
        /*
        |--------------------------------------------------------------------------
        | Login
        |--------------------------------------------------------------------------
        |
        | Login SiOne mempunyai full-page layout sendiri.
        | Jangan bungkus dengan AuthLayout.
        |
        */

        if (
            name === 'auth/Login'
        ) {
            return null
        }

        /*
        |--------------------------------------------------------------------------
        | Halaman Auth Lain
        |--------------------------------------------------------------------------
        |
        | Forgot Password, Reset Password, Verify Email, dst
        | masih boleh menggunakan AuthLayout bawaan.
        |
        */

        if (
            name.startsWith(
                'auth/',
            )
        ) {
            return AuthLayout
        }

        /*
        |--------------------------------------------------------------------------
        | Application Pages
        |--------------------------------------------------------------------------
        |
        | Admin, participant, Welcome, dan halaman lainnya
        | menentukan layout masing-masing.
        |
        */

        return null
    },

    progress: {
        color: '#0094DE',
    },
})

/*
|--------------------------------------------------------------------------
| Theme
|--------------------------------------------------------------------------
*/

initializeTheme()

/*
|--------------------------------------------------------------------------
| Flash Toast
|--------------------------------------------------------------------------
*/

initializeFlashToast()