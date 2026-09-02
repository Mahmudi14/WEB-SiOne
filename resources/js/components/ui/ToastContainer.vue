<script setup lang="ts">
    import {
        computed,
        watch,
    } from 'vue'

    import {
        usePage,
    } from '@inertiajs/vue3'

    import {
        useToast,
    } from '@/composables/useToast'

    const {
        state,
        removeToast,
        success,
        error,
    } = useToast()

    const page = usePage()

    const flash = computed(
        () =>
            page.props.flash as
            | {
                success?: string | null
                error?: string | null
            }
            | undefined,
    )

    /*
    |--------------------------------------------------------------------------
    | Flash Success
    |--------------------------------------------------------------------------
    */

    watch(
        () => flash.value?.success,
        (message) => {
            if (!message) {
                return
            }

            success(message)
        },
        {
            immediate: true,
        },
    )

    /*
    |--------------------------------------------------------------------------
    | Flash Error
    |--------------------------------------------------------------------------
    */

    watch(
        () => flash.value?.error,
        (message) => {
            if (!message) {
                return
            }

            error(message)
        },
        {
            immediate: true,
        },
    )

    /*
    |--------------------------------------------------------------------------
    | Style
    |--------------------------------------------------------------------------
    */

    const containerClass = (
        tone: string,
    ) => {
        switch (tone) {
            case 'success':
                return 'border-emerald-200 bg-white'

            case 'error':
                return 'border-red-200 bg-white'

            case 'warning':
                return 'border-amber-200 bg-white'

            default:
                return 'border-blue-200 bg-white'
        }
    }

    const iconClass = (
        tone: string,
    ) => {
        switch (tone) {
            case 'success':
                return 'bg-emerald-50 text-emerald-600'

            case 'error':
                return 'bg-red-50 text-red-600'

            case 'warning':
                return 'bg-amber-50 text-amber-600'

            default:
                return 'bg-blue-50 text-bps-blue'
        }
    }
</script>

<template>
    <Teleport to="body">
        <div
            class="pointer-events-none fixed inset-x-0 top-4 z-[300] flex flex-col items-center gap-2 px-4 sm:left-auto sm:right-5 sm:top-5 sm:w-[380px] sm:px-0">
            <TransitionGroup enter-active-class="transition duration-300 ease-out"
                enter-from-class="translate-y-[-12px] opacity-0 sm:translate-x-6 sm:translate-y-0"
                enter-to-class="translate-x-0 translate-y-0 opacity-100"
                leave-active-class="transition duration-200 ease-in" leave-from-class="translate-x-0 opacity-100"
                leave-to-class="translate-y-[-8px] opacity-0 sm:translate-x-6 sm:translate-y-0"
                move-class="transition duration-200">
                <div v-for="toast in state.items" :key="toast.id"
                    class="pointer-events-auto flex w-full items-start gap-3 rounded-2xl border p-3.5 shadow-xl shadow-slate-950/10"
                    :class="containerClass(
                        toast.tone,
                    )
                        " role="status">
                    <!-- Icon -->
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl" :class="iconClass(
                        toast.tone,
                    )
                        ">
                        <!-- Success -->
                        <svg v-if="
                            toast.tone ===
                            'success'
                        " class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5" />
                        </svg>

                        <!-- Error -->
                        <svg v-else-if="
                            toast.tone ===
                            'error'
                        " class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />

                            <path d="m15 9-6 6" />

                            <path d="m9 9 6 6" />
                        </svg>

                        <!-- Warning -->
                        <svg v-else-if="
                            toast.tone ===
                            'warning'
                        " class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21.73 18-8-14a2 2 0 0 0-3.46 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />

                            <path d="M12 9v4" />

                            <path d="M12 17h.01" />
                        </svg>

                        <!-- Info -->
                        <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />

                            <path d="M12 16v-4" />

                            <path d="M12 8h.01" />
                        </svg>
                    </div>

                    <!-- Content -->
                    <div class="min-w-0 flex-1 pt-0.5">
                        <div class="text-sm font-bold text-slate-900">
                            {{
                                toast.tone ===
                                    'success'
                                    ? 'Berhasil'
                                    : toast.tone ===
                                        'error'
                                        ? 'Gagal'
                                        : toast.tone ===
                                            'warning'
                                            ? 'Perhatian'
                                            : 'Informasi'
                            }}
                        </div>

                        <p class="mt-0.5 break-words text-sm leading-5 text-slate-500">
                            {{ toast.message }}
                        </p>
                    </div>

                    <!-- Close -->
                    <button type="button"
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Tutup notifikasi" @click="
                            removeToast(
                                toast.id,
                            )
                            ">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18" />

                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>