<script setup lang="ts">
    import { usePolling } from '@/composables/game/usePolling'
    import ParticipantLayout from '@/layouts/participant/ParticipantLayout.vue'

    import {
        Head,
        router,
    } from '@inertiajs/vue3'

    const props = defineProps<{
        room: {
            name: string
            code: string
            status: string
            revision: number
        }

        participant: {
            name: string
            code: string
            status: string
        }
    }>()

    interface ParticipantState {
        revision: number

        room_status:
        | 'locked'
        | 'waiting'
        | 'running'
        | 'finished'

        game_phase:
        | 'ready'
        | 'open'
        | 'result'
        | null

        participant_status:
        | 'waiting'
        | 'active'
        | 'eliminated'
        | 'winner'

        question: unknown | null
    }

    usePolling<ParticipantState>(
        `/participant/room/${props.room.code}/state`,
        (state) => {
            if (
                state.room_status ===
                'running'
            ) {
                router.visit(
                    `/participant/room/${props.room.code}/play`,
                )
            }
        },
    )
</script>

<template>

    <Head title="Menunggu Permainan — SiOne" />

    <ParticipantLayout>
        <section class="mx-auto w-full max-w-lg">
            <div
                class="relative overflow-hidden rounded-[2rem] border border-white/80 bg-white/90 shadow-2xl shadow-slate-300/40 backdrop-blur-xl">
                <!-- Accent -->
                <div class="absolute inset-x-0 top-0 h-1 bg-indigo-600" />

                <!-- Main Waiting State -->
                <div class="px-5 pb-6 pt-8 text-center sm:px-8 sm:pb-8 sm:pt-10">
                    <!-- Connection Status -->
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-60" />

                            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500" />
                        </span>

                        Terhubung ke Room
                    </div>

                    <!-- Waiting Animation -->
                    <div class="relative mx-auto mt-7 flex h-28 w-28 items-center justify-center sm:h-32 sm:w-32"
                        aria-hidden="true">
                        <div class="absolute inset-0 animate-ping rounded-full bg-indigo-100 opacity-50" />

                        <div class="absolute inset-3 rounded-full bg-indigo-50" />

                        <div
                            class="relative flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-xl shadow-indigo-200 sm:h-18 sm:w-18">
                            <svg class="h-8 w-8 sm:h-9 sm:w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9" />

                                <path d="M12 7v5l3 2" />
                            </svg>
                        </div>
                    </div>

                    <!-- Waiting Title -->
                    <div class="mt-6">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">
                            Waiting Room
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            Menunggu permainan dimulai
                            <span class="inline-flex w-6 justify-start text-indigo-600">
                                <span class="animate-pulse">
                                    ...
                                </span>
                            </span>
                        </h1>

                        <p class="mx-auto mt-3 max-w-sm text-sm leading-6 text-slate-500 sm:text-base">
                            Kamu sudah berada di dalam room. Permainan akan
                            terbuka otomatis setelah admin memulainya.
                        </p>
                    </div>

                    <!-- Participant -->
                    <div class="mt-7 rounded-2xl border border-slate-100 bg-slate-50 p-4 text-left sm:p-5">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-indigo-600 shadow-sm">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M20 21a8 8 0 0 0-16 0" />

                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    Peserta
                                </p>

                                <p class="mt-0.5 truncate font-bold text-slate-900 sm:text-lg">
                                    {{ participant.name }}
                                </p>
                            </div>

                            <div
                                class="shrink-0 rounded-xl bg-white px-3 py-2 font-mono text-sm font-bold tracking-wider text-slate-600 shadow-sm">
                                #{{ participant.code }}
                            </div>
                        </div>
                    </div>

                    <!-- Room -->
                    <div
                        class="mt-3 flex items-center justify-between gap-4 rounded-2xl border border-slate-100 bg-white px-4 py-3.5 text-left">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Room
                            </p>

                            <p class="mt-1 truncate text-sm font-bold text-slate-900 sm:text-base">
                                {{ room.name }}
                            </p>
                        </div>

                        <div
                            class="shrink-0 rounded-lg bg-indigo-50 px-3 py-1.5 font-mono text-xs font-bold tracking-wider text-indigo-600">
                            {{ room.code }}
                        </div>
                    </div>

                    <!-- Keep Page Open -->
                    <div
                        class="mt-5 flex items-start gap-3 rounded-2xl border border-amber-100 bg-amber-50/70 px-4 py-3.5 text-left">
                        <div
                            class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-amber-600 shadow-sm">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path
                                    d="M10.3 2.9 1.8 17a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 2.9a2 2 0 0 0-3.4 0Z" />

                                <path d="M12 9v4" />
                                <path d="M12 17h.01" />
                            </svg>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-amber-900">
                                Jangan tutup halaman ini
                            </p>

                            <p class="mt-0.5 text-xs leading-5 text-amber-700">
                                Pastikan perangkat tetap terhubung ke internet
                                selama menunggu.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </ParticipantLayout>
</template>