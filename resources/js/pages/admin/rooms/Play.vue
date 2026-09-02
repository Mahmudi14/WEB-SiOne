<script setup lang="ts">
    import { usePolling } from '@/composables/game/usePolling'
    import AdminLayout from '@/layouts/admin/AdminLayout.vue'

    import type {
        AdminRoomState,
        PlayRoom,
    } from '@/types/room'

    import {
        Head,
        Link,
        router,
    } from '@inertiajs/vue3'

    import { ref } from 'vue'

    const props = defineProps<{
        room: PlayRoom
    }>()

    /*
    |--------------------------------------------------------------------------
    | Counters
    |--------------------------------------------------------------------------
    */

    const participantCount = ref(
        props.room.participant_count,
    )

    const activeParticipantCount = ref(
        props.room.active_participant_count,
    )

    const eliminatedParticipantCount = ref(
        props.room.eliminated_participant_count,
    )

    const winnerCount = ref(
        props.room.winner_count,
    )

    const answeredCount = ref(
        props.room.answered_count,
    )

    const waitingAnswerCount = ref(
        props.room.waiting_answer_count,
    )

    /*
    |--------------------------------------------------------------------------
    | Polling
    |--------------------------------------------------------------------------
    */

    usePolling<AdminRoomState>(
        `/admin/rooms/${props.room.id}/state`,
        (state) => {
            participantCount.value =
                state.participant_count

            activeParticipantCount.value =
                state.active_participant_count

            eliminatedParticipantCount.value =
                state.eliminated_participant_count

            winnerCount.value =
                state.winner_count

            answeredCount.value =
                state.answered_count

            waitingAnswerCount.value =
                state.waiting_answer_count
        },
    )

    /*
    |--------------------------------------------------------------------------
    | Open Room
    |--------------------------------------------------------------------------
    */

    const openRoom = () => {
        const confirmed = window.confirm(
            'Buka room ini agar peserta dapat bergabung?',
        )

        if (!confirmed) {
            return
        }

        router.post(
            `/admin/rooms/${props.room.id}/open`,
            {},
            {
                preserveScroll: true,
            },
        )
    }

    /*
    |--------------------------------------------------------------------------
    | Start Game
    |--------------------------------------------------------------------------
    */

    const startGame = () => {
        const confirmed = window.confirm(
            `Mulai game dengan ${participantCount.value} peserta? Setelah game dimulai, peserta baru tidak dapat bergabung.`,
        )

        if (!confirmed) {
            return
        }

        router.post(
            `/admin/rooms/${props.room.id}/start`,
            {},
            {
                preserveScroll: true,
            },
        )
    }

    /*
    |--------------------------------------------------------------------------
    | Open Question
    |--------------------------------------------------------------------------
    */

    const openQuestion = () => {
        const confirmed = window.confirm(
            'Buka soal ini kepada seluruh peserta aktif?',
        )

        if (!confirmed) {
            return
        }

        router.post(
            `/admin/rooms/${props.room.id}/question/open`,
            {},
            {
                preserveScroll: true,
            },
        )
    }

    /*
    |--------------------------------------------------------------------------
    | Close Question
    |--------------------------------------------------------------------------
    */

    const closeQuestion = () => {
        const confirmed = window.confirm(
            'Tutup soal sekarang? Jika ada minimal satu peserta menjawab benar, peserta yang salah atau tidak menjawab akan tereliminasi. Jika tidak ada yang menjawab benar, tidak ada eliminasi.',
        )

        if (!confirmed) {
            return
        }

        router.post(
            `/admin/rooms/${props.room.id}/question/close`,
            {},
            {
                preserveScroll: true,
            },
        )
    }

    /*
    |--------------------------------------------------------------------------
    | Next Question
    |--------------------------------------------------------------------------
    */

    const nextQuestion = () => {
        router.post(
            `/admin/rooms/${props.room.id}/question/next`,
            {},
            {
                preserveScroll: true,
            },
        )
    }

    /*
    |--------------------------------------------------------------------------
    | Finish Game
    |--------------------------------------------------------------------------
    */

    const finishGame = () => {
        const confirmed = window.confirm(
            'Tutup game sekarang? Semua peserta yang masih aktif akan menjadi pemenang.',
        )

        if (!confirmed) {
            return
        }

        router.post(
            `/admin/rooms/${props.room.id}/finish`,
            {},
            {
                preserveScroll: true,
            },
        )
    }
</script>

<template>

    <Head :title="`Play - ${room.name}`" />

    <AdminLayout title="Kontrol Room">
        <!-- =====================================================
        TOP RIGHT ACTION
        ====================================================== -->

        <template #actions>
            <Link href="/admin/rooms"
                class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 sm:min-h-11 sm:px-4">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m15 18-6-6 6-6" />
                </svg>

                Kembali
            </Link>
        </template>

        <!-- =====================================================
        ROOM HEADER
        ====================================================== -->

        <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm sm:mb-6">
            <div class="flex flex-col gap-5 p-5 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex min-w-0 items-start gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 21v-7" />
                            <path d="M4 10V3" />
                            <path d="M12 21v-9" />
                            <path d="M12 8V3" />
                            <path d="M20 21v-5" />
                            <path d="M20 12V3" />
                            <path d="M1 14h6" />
                            <path d="M9 8h6" />
                            <path d="M17 16h6" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-600">
                            Mode Permainan
                        </p>

                        <h2 class="mt-1 break-words text-xl font-black tracking-tight text-slate-950 sm:text-2xl">
                            {{ room.name }}
                        </h2>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span
                                class="rounded-lg bg-slate-100 px-2.5 py-1.5 font-mono text-xs font-bold tracking-wider text-slate-600">
                                {{ room.code }}
                            </span>

                            <span v-if="room.status === 'locked'"
                                class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400" />

                                Terkunci
                            </span>

                            <span v-else-if="room.status === 'waiting'"
                                class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">
                                <span class="relative flex h-1.5 w-1.5">
                                    <span
                                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-40" />

                                    <span class="relative h-1.5 w-1.5 rounded-full bg-amber-500" />
                                </span>

                                Menunggu
                            </span>

                            <span v-else-if="room.status === 'running'"
                                class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">
                                <span class="relative flex h-1.5 w-1.5">
                                    <span
                                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-40" />

                                    <span class="relative h-1.5 w-1.5 rounded-full bg-emerald-500" />
                                </span>

                                Berjalan
                            </span>

                            <span v-else
                                class="inline-flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />

                                Selesai
                            </span>
                        </div>
                    </div>
                </div>

                <button v-if="room.status === 'running'" type="button"
                    class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-bold text-red-600 transition hover:bg-red-100 lg:w-auto"
                    @click="finishGame">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="14" height="14" x="5" y="5" rx="2" />
                    </svg>

                    Tutup Game
                </button>
            </div>
        </div>

        <!-- =====================================================
        STATISTICS
        ====================================================== -->

        <div class="mb-5 grid grid-cols-2 gap-3 sm:mb-6 sm:gap-4 xl:grid-cols-4">
            <!-- Questions -->
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 sm:text-sm">
                            Jumlah Soal
                        </p>

                        <div class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            {{ room.question_count }}
                        </div>
                    </div>

                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-500 sm:h-10 sm:w-10">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10" />

                            <path d="M9.1 9a3 3 0 1 1 5.83 1c0 2-3 2-3 4" />

                            <path d="M12 17h.01" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Participants -->
            <div class="rounded-2xl border border-indigo-100 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 sm:text-sm">
                            Peserta
                        </p>

                        <div class="mt-2 text-2xl font-black tracking-tight text-indigo-600 sm:text-3xl">
                            {{ participantCount }}
                        </div>
                    </div>

                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 sm:h-10 sm:w-10">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />

                            <circle cx="9" cy="7" r="4" />

                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active -->
            <div class="rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 sm:text-sm">
                            {{
                                room.status === 'finished'
                                    ? 'Pemenang'
                                    : 'Aktif'
                            }}
                        </p>

                        <div class="mt-2 text-2xl font-black tracking-tight text-emerald-600 sm:text-3xl">
                            {{
                                room.status === 'finished'
                                    ? winnerCount
                                    : activeParticipantCount
                            }}
                        </div>
                    </div>

                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 sm:h-10 sm:w-10">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m5 12 4 4L19 6" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Eliminated -->
            <div class="rounded-2xl border border-red-100 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 sm:text-sm">
                            Tereliminasi
                        </p>

                        <div class="mt-2 text-2xl font-black tracking-tight text-red-600 sm:text-3xl">
                            {{ eliminatedParticipantCount }}
                        </div>
                    </div>

                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-600 sm:h-10 sm:w-10">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- =====================================================
        LOCKED
        ====================================================== -->

        <div v-if="room.status === 'locked'"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="px-5 py-10 text-center sm:px-8 sm:py-14">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="18" height="11" x="3" y="11" rx="2" />

                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                </div>

                <p class="mt-5 text-xs font-bold uppercase tracking-[0.16em] text-slate-400">
                    Room Terkunci
                </p>

                <h3 class="mt-2 text-xl font-black tracking-tight text-slate-950 sm:text-2xl">
                    Room belum dibuka
                </h3>

                <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500">
                    Buka room agar peserta dapat memasukkan kode room dan
                    bergabung ke pertandingan.
                </p>

                <div v-if="room.question_count === 0"
                    class="mx-auto mt-5 max-w-lg rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-700">
                    Room harus memiliki minimal satu soal sebelum dapat
                    dibuka.
                </div>

                <button type="button" :disabled="room.question_count === 0"
                    class="mt-6 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-7 py-3 font-bold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none sm:w-auto"
                    @click="openRoom">
                    Buka Room

                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14" />
                        <path d="m13 6 6 6-6 6" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- =====================================================
        WAITING
        ====================================================== -->

        <div v-else-if="room.status === 'waiting'"
            class="overflow-hidden rounded-2xl border border-indigo-100 bg-white shadow-sm">
            <div class="px-5 py-8 text-center sm:px-8 sm:py-12">
                <div
                    class="inline-flex items-center gap-2 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-40" />

                        <span class="relative h-2 w-2 rounded-full bg-emerald-500" />
                    </span>

                    Room Terbuka
                </div>

                <h3 class="mt-5 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                    Menunggu peserta bergabung
                </h3>

                <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500">
                    Bagikan kode berikut kepada seluruh peserta.
                </p>

                <!-- Code -->
                <div
                    class="mx-auto mt-6 inline-flex rounded-2xl bg-slate-950 px-7 py-4 font-mono text-2xl font-black tracking-[0.18em] text-white shadow-lg sm:px-9 sm:text-3xl">
                    {{ room.code }}
                </div>

                <!-- Counter -->
                <div class="mt-9">
                    <p class="text-xs font-bold uppercase tracking-[0.15em] text-slate-400">
                        Peserta Bergabung
                    </p>

                    <div class="mt-2 text-6xl font-black tracking-tight text-indigo-600 sm:text-7xl">
                        {{ participantCount }}
                    </div>

                    <p class="mt-1 text-sm text-slate-400">
                        peserta di lobby
                    </p>
                </div>

                <button type="button" :disabled="participantCount === 0"
                    class="mt-8 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-8 py-3.5 font-bold text-white shadow-sm shadow-emerald-200 transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none sm:w-auto"
                    @click="startGame">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M8 5v14l11-7Z" />
                    </svg>

                    Mulai Game
                </button>

                <p v-if="participantCount === 0" class="mt-3 text-xs text-slate-400">
                    Tunggu minimal satu peserta bergabung.
                </p>

                <p v-else class="mx-auto mt-4 max-w-md text-xs leading-5 text-slate-400">
                    Setelah game dimulai, peserta baru tidak dapat bergabung.
                </p>
            </div>
        </div>

        <!-- =====================================================
        RUNNING
        ====================================================== -->

        <div v-else-if="room.status === 'running'" class="space-y-5 sm:space-y-6">
            <!-- Running notification -->
            <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 sm:p-5">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm">
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-40" />

                        <span class="relative h-2.5 w-2.5 rounded-full bg-emerald-500" />
                    </span>
                </div>

                <div>
                    <div class="font-bold text-emerald-900">
                        Game Sedang Berjalan
                    </div>

                    <p class="mt-1 text-sm leading-6 text-emerald-700">
                        Peserta baru tidak dapat bergabung lagi.
                    </p>
                </div>
            </div>

            <!-- =================================================
            READY
            ================================================== -->

            <div v-if="
                room.game_phase === 'ready' &&
                room.current_question
            " class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-slate-50/60 px-5 py-5 sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-[0.15em] text-indigo-600">
                                Soal Berikutnya
                            </div>

                            <div class="mt-1 text-sm text-slate-500">
                                Soal
                                {{
                                    room.current_question
                                        .game_order
                                }}
                                dari
                                {{ room.question_count }}
                            </div>
                        </div>

                        <span
                            class="inline-flex w-fit rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">
                            Belum Dibuka
                        </span>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <h3 class="text-lg font-extrabold leading-7 text-slate-950 sm:text-xl sm:leading-8">
                        {{ room.current_question.text }}
                    </h3>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div v-for="
option in
                                    room.current_question.options
                            " :key="option.id" class="flex min-w-0 items-center gap-3 rounded-xl border px-3.5 py-3"
                            :class="option.is_correct
                                    ? 'border-emerald-200 bg-emerald-50'
                                    : 'border-slate-200 bg-slate-50'
                                ">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-sm font-black"
                                :class="option.is_correct
                                        ? 'bg-emerald-600 text-white'
                                        : 'bg-white text-slate-600'
                                    ">
                                {{ option.label }}
                            </div>

                            <span class="min-w-0 flex-1 break-words text-sm leading-6 text-slate-700">
                                {{ option.text }}
                            </span>

                            <span v-if="option.is_correct" class="shrink-0 text-xs font-bold text-emerald-700">
                                BENAR
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-slate-50/50 p-5 sm:p-6">
                    <p class="text-center text-sm text-slate-500">
                        Soal sudah siap. Peserta belum dapat melihat soal ini.
                    </p>

                    <button type="button"
                        class="mt-4 flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-3.5 font-bold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700"
                        @click="openQuestion">
                        Buka Soal
                    </button>
                </div>
            </div>

            <!-- =================================================
            OPEN
            ================================================== -->

            <div v-else-if="
                room.game_phase === 'open' &&
                room.current_question
            " class="overflow-hidden rounded-2xl border border-indigo-200 bg-white shadow-sm">
                <div class="border-b border-indigo-100 bg-indigo-50/60 px-5 py-5 sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div
                                class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.15em] text-indigo-600">
                                <span class="relative flex h-2 w-2">
                                    <span
                                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-indigo-400 opacity-40" />

                                    <span class="relative h-2 w-2 rounded-full bg-indigo-600" />
                                </span>

                                Soal Sedang Dibuka
                            </div>

                            <div class="mt-1 text-sm text-slate-500">
                                Soal
                                {{
                                    room.current_question
                                        .game_order
                                }}
                                dari
                                {{ room.question_count }}
                            </div>
                        </div>

                        <span
                            class="inline-flex w-fit rounded-full border border-indigo-200 bg-white px-3 py-1.5 text-xs font-bold text-indigo-600">
                            Sedang Dijawab
                        </span>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <h3 class="text-lg font-extrabold leading-7 text-slate-950 sm:text-xl sm:leading-8">
                        {{ room.current_question.text }}
                    </h3>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div v-for="
option in
                                    room.current_question.options
                            " :key="option.id" class="flex min-w-0 items-center gap-3 rounded-xl border px-3.5 py-3"
                            :class="option.is_correct
                                    ? 'border-emerald-200 bg-emerald-50'
                                    : 'border-slate-200 bg-slate-50'
                                ">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-sm font-black"
                                :class="option.is_correct
                                        ? 'bg-emerald-600 text-white'
                                        : 'bg-white text-slate-600'
                                    ">
                                {{ option.label }}
                            </div>

                            <span class="min-w-0 flex-1 break-words text-sm leading-6 text-slate-700">
                                {{ option.text }}
                            </span>
                        </div>
                    </div>

                    <!-- Answer counters -->
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4 text-center sm:p-5">
                            <div class="text-3xl font-black text-indigo-600 sm:text-4xl">
                                {{ answeredCount }}
                            </div>

                            <div class="mt-1 text-xs font-semibold text-slate-500 sm:text-sm">
                                Sudah Menjawab
                            </div>
                        </div>

                        <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 text-center sm:p-5">
                            <div class="text-3xl font-black text-amber-600 sm:text-4xl">
                                {{ waitingAnswerCount }}
                            </div>

                            <div class="mt-1 text-xs font-semibold text-slate-500 sm:text-sm">
                                Belum Menjawab
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-red-100 bg-red-50/50 p-5 sm:p-6">
                    <button type="button"
                        class="flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-6 py-3.5 font-bold text-white transition hover:bg-red-700"
                        @click="closeQuestion">
                        Tutup Soal
                    </button>
                </div>
            </div>

            <!-- =================================================
            RESULT
            ================================================== -->

            <div v-else-if="
                room.game_phase === 'result' &&
                room.current_question &&
                room.result
            " class="space-y-5">
                <!-- No elimination -->
                <div v-if="
                    room.result.round_result ===
                    'no_elimination'
                " class="rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-amber-600 shadow-sm">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 8v4" />
                                <path d="M12 16h.01" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-bold text-amber-900">
                                Tidak Ada Eliminasi
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-amber-700">
                                Tidak ada peserta yang menjawab benar pada
                                soal ini. Seluruh peserta aktif tetap bertahan.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 bg-slate-50/60 px-5 py-5 sm:px-6">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-xs font-bold uppercase tracking-[0.15em] text-emerald-600">
                                    Hasil Soal
                                </div>

                                <div class="mt-1 text-sm text-slate-500">
                                    Soal
                                    {{
                                        room.current_question
                                            .game_order
                                    }}
                                    dari
                                    {{ room.question_count }}
                                </div>
                            </div>

                            <span
                                class="inline-flex w-fit rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                                Selesai
                            </span>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <h3 class="text-lg font-extrabold leading-7 text-slate-950 sm:text-xl sm:leading-8">
                            {{ room.current_question.text }}
                        </h3>

                        <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
                            <div class="rounded-xl bg-indigo-50 p-4 text-center">
                                <div class="text-2xl font-black text-indigo-600 sm:text-3xl">
                                    {{
                                        room.result
                                            .answered_count
                                    }}
                                </div>

                                <div class="mt-1 text-xs text-slate-500">
                                    Menjawab
                                </div>
                            </div>

                            <div class="rounded-xl bg-emerald-50 p-4 text-center">
                                <div class="text-2xl font-black text-emerald-600 sm:text-3xl">
                                    {{
                                        room.result
                                            .correct_count
                                    }}
                                </div>

                                <div class="mt-1 text-xs text-slate-500">
                                    Benar
                                </div>
                            </div>

                            <div class="rounded-xl bg-red-50 p-4 text-center">
                                <div class="text-2xl font-black text-red-600 sm:text-3xl">
                                    {{
                                        room.result
                                            .eliminated_count
                                    }}
                                </div>

                                <div class="mt-1 text-xs text-slate-500">
                                    Tereliminasi
                                </div>
                            </div>

                            <div class="rounded-xl bg-slate-100 p-4 text-center">
                                <div class="text-2xl font-black text-slate-950 sm:text-3xl">
                                    {{
                                        room.result
                                            .remaining_count
                                    }}
                                </div>

                                <div class="mt-1 text-xs text-slate-500">
                                    Tersisa
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm text-slate-500">
                            <span>
                                Salah:

                                <strong class="text-red-600">
                                    {{
                                        room.result
                                            .wrong_count
                                    }}
                                </strong>
                            </span>

                            <span>
                                Tidak menjawab:

                                <strong class="text-red-600">
                                    {{
                                        room.result
                                            .no_answer_count
                                    }}
                                </strong>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Correct Participants -->
                <div v-if="
                    room.result.round_result ===
                    'normal'
                " class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-5 sm:px-6">
                        <h3 class="font-bold text-slate-900">
                            Peserta Menjawab Benar
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Peserta berikut masih bertahan dalam permainan.
                        </p>
                    </div>

                    <div v-if="
                        room.result
                            .correct_participants
                            .length === 0
                    " class="p-6 text-center text-sm text-slate-500">
                        Tidak ada peserta yang menjawab benar.
                    </div>

                    <div v-else class="grid gap-3 p-5 sm:grid-cols-2 sm:p-6 xl:grid-cols-3">
                        <div v-for="
participant in
                                    room.result
                                        .correct_participants
                            " :key="participant.id"
                            class="flex items-center gap-3 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-600 shadow-sm">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m5 12 4 4L19 6" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <div class="truncate font-semibold text-emerald-900">
                                    {{ participant.name }}
                                </div>

                                <div class="mt-0.5 font-mono text-xs text-emerald-600">
                                    #{{ participant.code }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- All Survive -->
                <div v-else-if="
                    room.result.round_result ===
                    'no_elimination'
                " class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m5 12 4 4L19 6" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-bold text-slate-900">
                                Semua Peserta Tetap Bertahan
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Seluruh
                                <strong class="text-slate-900">
                                    {{
                                        room.result
                                            .remaining_count
                                    }}
                                    peserta aktif
                                </strong>
                                tetap dapat melanjutkan permainan.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Next -->
                <button v-if="room.has_next_question" type="button"
                    class="flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-4 font-bold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700"
                    @click="nextQuestion">
                    Soal Berikutnya

                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14" />
                        <path d="m13 6 6 6-6 6" />
                    </svg>
                </button>

                <!-- Last -->
                <div v-else class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-center sm:p-6">
                    <div class="font-bold text-amber-900">
                        Tidak Ada Soal Berikutnya
                    </div>

                    <p class="mx-auto mt-1 max-w-lg text-sm leading-6 text-amber-700">
                        Semua soal telah digunakan. Gunakan tombol Tutup Game
                        untuk menyelesaikan pertandingan.
                    </p>

                    <button type="button"
                        class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl bg-red-600 px-6 py-3 font-bold text-white transition hover:bg-red-700"
                        @click="finishGame">
                        Tutup Game
                    </button>
                </div>
            </div>

            <!-- =================================================
            FALLBACK
            ================================================== -->

            <div v-else class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50">
                    <span class="relative flex h-3 w-3">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-indigo-400 opacity-40" />

                        <span class="relative h-3 w-3 rounded-full bg-indigo-600" />
                    </span>
                </div>

                <div class="mt-4 font-semibold text-slate-900">
                    Memuat state permainan...
                </div>
            </div>
        </div>

        <!-- =====================================================
        FINISHED
        ====================================================== -->

        <div v-else-if="room.status === 'finished'"
            class="overflow-hidden rounded-2xl border border-indigo-100 bg-white shadow-sm">
            <div class="px-5 py-10 text-center sm:px-8 sm:py-14">
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="m5 12 4 4L19 6" />
                    </svg>
                </div>

                <div class="mt-5 text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">
                    Selesai
                </div>

                <h3 class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                    Game Telah Selesai
                </h3>

                <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500">
                    Pertandingan pada room ini telah selesai.
                </p>

                <div class="mx-auto mt-7 grid max-w-2xl grid-cols-3 gap-3">
                    <div class="rounded-xl bg-indigo-50 p-3 sm:p-4">
                        <div class="text-2xl font-black text-indigo-600 sm:text-3xl">
                            {{ participantCount }}
                        </div>

                        <div class="mt-1 text-[10px] text-slate-500 sm:text-xs">
                            Peserta
                        </div>
                    </div>

                    <div class="rounded-xl bg-emerald-50 p-3 sm:p-4">
                        <div class="text-2xl font-black text-emerald-600 sm:text-3xl">
                            {{ winnerCount }}
                        </div>

                        <div class="mt-1 text-[10px] text-slate-500 sm:text-xs">
                            Pemenang
                        </div>
                    </div>

                    <div class="rounded-xl bg-red-50 p-3 sm:p-4">
                        <div class="text-2xl font-black text-red-600 sm:text-3xl">
                            {{ eliminatedParticipantCount }}
                        </div>

                        <div class="mt-1 text-[10px] text-slate-500 sm:text-xs">
                            Eliminasi
                        </div>
                    </div>
                </div>

                <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
                    <Link :href="`/admin/rooms/${room.id}/result`"
                        class="inline-flex min-h-11 items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 font-bold text-white transition hover:bg-indigo-700">
                        Lihat Hasil Lengkap
                    </Link>

                    <Link href="/admin/rooms"
                        class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-6 py-3 font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
                        Kembali ke Daftar Room
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>