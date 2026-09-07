<script setup lang="ts">
    import {
        router,
        usePage,
    } from '@inertiajs/vue3'

    import {
        nextTick,
        onBeforeUnmount,
        onMounted,
        ref,
    } from 'vue'

    interface PageProps {
        errors?: Record<
            string,
            string
        >

        flash?: {
            error?: string | null
        }

        [key: string]: unknown
    }

    interface ErrorEventDetail {
        title?: string
        message?: string
    }

    const page =
        usePage<PageProps>()

    const open =
        ref(false)

    const title =
        ref('Tidak Dapat Melanjutkan')

    const message =
        ref('')

    const showError = (
        errorMessage: string,
        errorTitle =
            'Tidak Dapat Melanjutkan',
    ) => {
        const normalizedMessage =
            errorMessage.trim()

        if (!normalizedMessage) {
            return
        }

        title.value =
            errorTitle

        message.value =
            normalizedMessage

        open.value =
            true
    }

    const showPageError = () => {
        const errors =
            page.props.errors ?? {}

        const firstError =
            Object.values(
                errors,
            ).find(
                (value) =>
                    typeof value ===
                    'string' &&
                    value.trim() !== '',
            )

        if (firstError) {
            showError(
                firstError,
            )

            return
        }

        const flashError =
            page.props.flash
                ?.error

        if (
            typeof flashError ===
            'string' &&
            flashError.trim() !== ''
        ) {
            showError(
                flashError,
            )
        }
    }

    const handleGlobalError = (
        event: Event,
    ) => {
        const customEvent =
            event as CustomEvent<ErrorEventDetail>

        if (
            !customEvent.detail
                ?.message
        ) {
            return
        }

        showError(
            customEvent
                .detail
                .message,
            customEvent
                .detail
                .title ??
            'Tidak Dapat Melanjutkan',
        )
    }

    const close = () => {
        open.value =
            false
    }

    const handleKeydown = (
        event: KeyboardEvent,
    ) => {
        if (
            event.key ===
            'Escape' &&
            open.value
        ) {
            close()
        }
    }

    let removeFinishListener:
        | (() => void)
        | null = null

    onMounted(() => {
        showPageError()

        removeFinishListener =
            router.on(
                'finish',
                () => {
                    void nextTick()
                        .then(
                            showPageError,
                        )
                },
            )

        window.addEventListener(
            'sione:error',
            handleGlobalError,
        )

        window.addEventListener(
            'keydown',
            handleKeydown,
        )
    })

    onBeforeUnmount(() => {
        removeFinishListener?.()

        window.removeEventListener(
            'sione:error',
            handleGlobalError,
        )

        window.removeEventListener(
            'keydown',
            handleKeydown,
        )
    })
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="open"
                class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm"
                role="dialog" aria-modal="true" @click.self="close">
                <div
                    class="w-full max-w-sm rounded-[1.75rem] border border-white/80 bg-white p-6 text-center shadow-2xl sm:p-7">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />

                            <path d="M12 8v4" />

                            <path d="M12 16h.01" />
                        </svg>
                    </div>

                    <p class="mt-5 text-xs font-bold uppercase tracking-[0.16em] text-red-500">
                        SiOne
                    </p>

                    <h2 class="mt-2 text-xl font-black tracking-tight text-slate-950">
                        {{ title }}
                    </h2>

                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        {{ message }}
                    </p>

                    <button type="button"
                        class="mt-6 min-h-12 w-full rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-indigo-100"
                        @click="close">
                        Mengerti
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>