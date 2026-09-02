<script setup lang="ts">
    import {
        computed,
        nextTick,
        onBeforeUnmount,
        onMounted,
        ref,
        watch,
    } from 'vue'

    import {
        useConfirm,
    } from '@/composables/useConfirm'

    const {
        state,
        confirm,
        cancel,
    } = useConfirm()

    const cancelButton =
        ref<HTMLButtonElement | null>(null)

    /*
    |--------------------------------------------------------------------------
    | Tone
    |--------------------------------------------------------------------------
    */

    const iconContainerClass =
        computed(() => {
            switch (state.tone) {
                case 'danger':
                    return 'bg-red-50 text-bps-red'

                case 'warning':
                    return 'bg-orange-50 text-bps-orange'

                case 'success':
                    return 'bg-green-50 text-bps-green'

                default:
                    return 'bg-blue-50 text-bps-blue'
            }
        })

    const confirmButtonClass =
        computed(() => {
            switch (state.tone) {
                case 'danger':
                    return 'bg-bps-red hover:bg-red-700 focus-visible:ring-red-500/30'

                case 'warning':
                    return 'bg-bps-orange hover:bg-orange-600 focus-visible:ring-orange-500/30'

                case 'success':
                    return 'bg-bps-green hover:bg-green-700 focus-visible:ring-green-500/30'

                default:
                    return 'bg-bps-blue hover:bg-blue-600 focus-visible:ring-blue-500/30'
            }
        })

    /*
    |--------------------------------------------------------------------------
    | Focus
    |--------------------------------------------------------------------------
    */

    watch(
        () => state.open,
        async (open) => {
            if (!open) {
                return
            }

            await nextTick()

            cancelButton.value?.focus()
        },
    )

    /*
    |--------------------------------------------------------------------------
    | Keyboard
    |--------------------------------------------------------------------------
    */

    const handleKeydown = (
        event: KeyboardEvent,
    ) => {
        if (!state.open) {
            return
        }

        if (
            event.key ===
            'Escape'
        ) {
            cancel()
        }
    }

    onMounted(() => {
        window.addEventListener(
            'keydown',
            handleKeydown,
        )
    })

    onBeforeUnmount(() => {
        window.removeEventListener(
            'keydown',
            handleKeydown,
        )
    })
</script>

<template>
    <Teleport to="body">
        <!-- ================================================
        BACKDROP
        ================================================= -->

        <Transition enter-active-class="transition-opacity duration-200" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="state.open" class="fixed inset-0 z-[200] bg-slate-950/50 backdrop-blur-[2px]" @click="cancel" />
        </Transition>

        <!-- ================================================
        DIALOG
        ================================================= -->

        <Transition enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-4 scale-95 opacity-0" enter-to-class="translate-y-0 scale-100 opacity-100"
            leave-active-class="transition duration-150 ease-in" leave-from-class="translate-y-0 scale-100 opacity-100"
            leave-to-class="translate-y-4 scale-95 opacity-0">
            <div v-if="state.open"
                class="pointer-events-none fixed inset-0 z-[210] flex items-end justify-center p-0 sm:items-center sm:p-5">
                <div role="alertdialog" aria-modal="true" :aria-label="state.title"
                    class="pointer-events-auto w-full overflow-hidden rounded-t-[1.75rem] border border-slate-200 bg-white shadow-2xl sm:max-w-md sm:rounded-[1.75rem]"
                    @click.stop>
                    <!-- ========================================
                    CONTENT
                    ========================================= -->

                    <div class="px-5 pb-5 pt-6 sm:px-6 sm:pb-6 sm:pt-7">
                        <!-- Icon -->
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl" :class="iconContainerClass
                            ">
                            <!-- Danger -->
                            <svg v-if="
                                state.tone ===
                                'danger'
                            " class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18" />

                                <path d="M8 6V4h8v2" />

                                <path d="M19 6l-1 14H6L5 6" />

                                <path d="M10 11v5" />

                                <path d="M14 11v5" />
                            </svg>

                            <!-- Warning -->
                            <svg v-else-if="
                                state.tone ===
                                'warning'
                            " class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m21.73 18-8-14a2 2 0 0 0-3.46 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />

                                <path d="M12 9v4" />

                                <path d="M12 17h.01" />
                            </svg>

                            <!-- Success -->
                            <svg v-else-if="
                                state.tone ===
                                'success'
                            " class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5" />
                            </svg>

                            <!-- Primary -->
                            <svg v-else class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />

                                <path d="M12 16v-4" />

                                <path d="M12 8h.01" />
                            </svg>
                        </div>

                        <!-- Title -->
                        <h2 class="mt-5 text-lg font-extrabold tracking-tight text-slate-950 sm:text-xl">
                            {{ state.title }}
                        </h2>

                        <!-- Description -->
                        <p v-if="
                            state.description
                        " class="mt-2 text-sm leading-6 text-slate-500">
                            {{
                                state.description
                            }}
                        </p>
                    </div>

                    <!-- ========================================
                    ACTIONS
                    ========================================= -->

                    <div
                        class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-slate-50/70 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                        <button ref="cancelButton" type="button"
                            class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400/30 sm:w-auto"
                            @click="cancel">
                            {{ state.cancelText }}
                        </button>

                        <button type="button"
                            class="inline-flex min-h-11 w-full items-center justify-center rounded-xl px-5 py-2.5 text-sm font-bold text-white shadow-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 sm:w-auto"
                            :class="confirmButtonClass
                                " @click="confirm">
                            {{ state.confirmText }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>