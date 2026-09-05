<script setup lang="ts">
    import AdminLayout from '@/layouts/admin/AdminLayout.vue'

    import {
        useConfirm,
    } from '@/composables/useConfirm'

    import {
        usePolling,
    } from '@/composables/game/usePolling'

    import {
        finish as finishRoomRoute,
        index as roomsIndex,
        open as openRoomRoute,
        result as roomResultRoute,
        start as startGameRoute,
        state as roomStateRoute,
    } from '@/routes/admin/rooms'

    import {
        close as closeQuestionRoute,
        next as nextQuestionRoute,
        open as openQuestionRoute,
    } from '@/routes/admin/rooms/question'

    import type {
        AdminRoomState,
        PlayRoom,
    } from '@/types/room'

    import {
        Head,
        Link,
        router,
    } from '@inertiajs/vue3'

    import {
        ref,
    } from 'vue'

    const props = defineProps<{
        room: PlayRoom
    }>()

    const {
        askConfirm,
    } = useConfirm()

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

    const actionProcessing = ref(false)

    usePolling<AdminRoomState>(
        roomStateRoute({
            room: props.room.id,
        }).url,
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

    const postAction = (
        url: string,
    ) => {
        if (actionProcessing.value) {
            return
        }

        actionProcessing.value = true

        router.post(
            url,
            {},
            {
                preserveScroll: true,

                onFinish: () => {
                    actionProcessing.value =
                        false
                },
            },
        )
    }

    const openRoom = async () => {
        if (actionProcessing.value) {
            return
        }

        const confirmed =
            await askConfirm({
                title:
                    'Buka Room?',

                description:
                    'Peserta akan mulai dapat bergabung ke lobby setelah room dibuka.',

                confirmText:
                    'Buka Room',

                cancelText:
                    'Batal',

                tone:
                    'primary',
            })

        if (!confirmed) {
            return
        }

        postAction(
            openRoomRoute({
                room: props.room.id,
            }).url,
        )
    }

    const startGame = async () => {
        if (
            participantCount.value < 2 ||
            actionProcessing.value
        ) {
            return
        }

        const confirmed =
            await askConfirm({
                title:
                    'Mulai Pertandingan?',

                description:
                    `Pertandingan akan dimulai dengan ${participantCount.value} peserta. Setelah dimulai, peserta baru tidak dapat bergabung lagi.`,

                confirmText:
                    'Mulai Game',

                cancelText:
                    'Batal',

                tone:
                    'success',
            })

        if (!confirmed) {
            return
        }

        postAction(
            startGameRoute({
                room: props.room.id,
            }).url,
        )
    }

    const openQuestion = async () => {
        if (actionProcessing.value) {
            return
        }

        const confirmed =
            await askConfirm({
                title:
                    'Buka Soal?',

                description:
                    'Soal akan langsung ditampilkan kepada seluruh peserta aktif dan mereka dapat mulai menjawab.',

                confirmText:
                    'Buka Soal',

                cancelText:
                    'Batal',

                tone:
                    'primary',
            })

        if (!confirmed) {
            return
        }

        postAction(
            openQuestionRoute({
                room: props.room.id,
            }).url,
        )
    }

    const closeQuestion = async () => {
        if (actionProcessing.value) {
            return
        }

        const confirmed =
            await askConfirm({
                title:
                    'Tutup Soal?',

                description:
                    'Jawaban akan dihitung dan peserta yang salah atau tidak menjawab akan dieliminasi. Jika tidak ada jawaban benar, seluruh peserta tetap bertahan.',

                confirmText:
                    'Tutup Soal',

                cancelText:
                    'Batal',

                tone:
                    'warning',
            })

        if (!confirmed) {
            return
        }

        postAction(
            closeQuestionRoute({
                room: props.room.id,
            }).url,
        )
    }

    const nextQuestion = () => {
        if (
            activeParticipantCount.value <= 1 ||
            actionProcessing.value
        ) {
            return
        }

        postAction(
            nextQuestionRoute({
                room: props.room.id,
            }).url,
        )
    }

    const finishGame = async () => {
        if (actionProcessing.value) {
            return
        }

        const confirmed =
            await askConfirm({
                title:
                    'Akhiri Pertandingan?',

                description:
                    'Semua peserta yang masih aktif akan ditetapkan sebagai pemenang dan pertandingan akan selesai.',

                confirmText:
                    'Akhiri Game',

                cancelText:
                    'Batal',

                tone:
                    'danger',
            })

        if (!confirmed) {
            return
        }

        postAction(
            finishRoomRoute({
                room: props.room.id,
            }).url,
        )
    }
</script>

<template>

    <Head :title="`Play - ${room.name}`" />

    <AdminLayout title="Kontrol Room">
        <template #actions>
            <Link :href="roomsIndex()"
                class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 sm:min-h-11 sm:px-4">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>

                Kembali
            </Link>
        </template>

        <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm sm:mb-6">
            <div class="flex flex-col gap-5 p-5 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex min-w-0 items-start gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
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

                <button v-if="room.status === 'running'" type="button" :disabled="actionProcessing"
                    class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-bold text-red-600 transition hover:bg-red-100 disabled:pointer-events-none disabled:opacity-50 lg:w-auto"
                    @click="finishGame">
                    Tutup Game
                </button>
            </div>
        </div>

        <div class="mb-5 grid grid-cols-2 gap-3 sm:mb-6 sm:gap-4 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <p class="text-xs font-semibold text-slate-500 sm:text-sm">
                    Jumlah Soal
                </p>

                <div class="mt-2 text-2xl font-black text-slate-950 sm:text-3xl">
                    {{ room.question_count }}
                </div>
            </div>

            <div class="rounded-2xl border border-indigo-100 bg-white p-4 shadow-sm sm:p-5">
                <p class="text-xs font-semibold text-slate-500 sm:text-sm">
                    Peserta
                </p>

                <div class="mt-2 text-2xl font-black text-indigo-600 sm:text-3xl">
                    {{ participantCount }}
                </div>
            </div>

            <div class="rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm sm:p-5">
                <p class="text-xs font-semibold text-slate-500 sm:text-sm">
                    {{
                        room.status === 'finished'
                            ? 'Pemenang'
                            : 'Aktif'
                    }}
                </p>

                <div class="mt-2 text-2xl font-black text-emerald-600 sm:text-3xl">
                    {{
                        room.status === 'finished'
                            ? winnerCount
                            : activeParticipantCount
                    }}
                </div>
            </div>

            <div class="rounded-2xl border border-red-100 bg-white p-4 shadow-sm sm:p-5">
                <p class="text-xs font-semibold text-slate-500 sm:text-sm">
                    Tereliminasi
                </p>

                <div class="mt-2 text-2xl font-black text-red-600 sm:text-3xl">
                    {{ eliminatedParticipantCount }}
                </div>
            </div>
        </div>

        <div v-if="room.status === 'locked'"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="px-5 py-10 text-center sm:px-8 sm:py-14">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="18" height="11" x="3" y="11" rx="2" />

                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                </div>

                <h3 class="mt-5 text-xl font-black text-slate-950 sm:text-2xl">
                    Room belum dibuka
                </h3>

                <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500">
                    Buka room agar peserta dapat bergabung ke pertandingan.
                </p>

                <div v-if="room.question_count === 0"
                    class="mx-auto mt-5 max-w-lg rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">
                    Room harus memiliki minimal satu soal.
                </div>

                <button type="button" :disabled="room.question_count === 0 ||
                    actionProcessing
                    "
                    class="mt-6 inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-indigo-600 px-7 py-3 font-bold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-slate-300 sm:w-auto"
                    @click="openRoom">
                    Buka Room
                </button>
            </div>
        </div>

        <div v-else-if="room.status === 'waiting'"
            class="overflow-hidden rounded-2xl border border-indigo-100 bg-white shadow-sm">
            <div class="px-5 py-8 text-center sm:px-8 sm:py-12">
                <div
                    class="inline-flex items-center gap-2 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                    <span class="h-2 w-2 rounded-full bg-emerald-500" />

                    Room Terbuka
                </div>

                <h3 class="mt-5 text-2xl font-black text-slate-950 sm:text-3xl">
                    Menunggu peserta bergabung
                </h3>

                <p class="mx-auto mt-2 max-w-lg text-sm text-slate-500">
                    Bagikan kode berikut kepada seluruh peserta.
                </p>

                <div
                    class="mx-auto mt-6 inline-flex rounded-2xl bg-slate-950 px-7 py-4 font-mono text-2xl font-black tracking-[0.18em] text-white sm:px-9 sm:text-3xl">
                    {{ room.code }}
                </div>

                <div class="mt-9">
                    <p class="text-xs font-bold uppercase tracking-[0.15em] text-slate-400">
                        Peserta Bergabung
                    </p>

                    <div class="mt-2 text-6xl font-black text-indigo-600 sm:text-7xl">
                        {{ participantCount }}
                    </div>

                    <p class="mt-1 text-sm text-slate-400">
                        peserta di lobby
                    </p>
                </div>

                <button type="button" :disabled="participantCount < 2 ||
                    actionProcessing
                    "
                    class="mt-8 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-8 py-3.5 font-bold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300 sm:w-auto"
                    @click="startGame">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8 5v14l11-7Z" />
                    </svg>

                    Mulai Game
                </button>

                <p v-if="participantCount < 2" class="mt-3 text-sm font-medium text-amber-600">
                    Minimal 2 peserta diperlukan untuk memulai pertandingan.
                </p>

                <p v-else class="mx-auto mt-4 max-w-md text-xs leading-5 text-slate-400">
                    Setelah game dimulai, peserta baru tidak dapat bergabung.
                </p>
            </div>
        </div>

        <div v-else-if="room.status === 'running'" class="space-y-5 sm:space-y-6">
            <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 sm:p-5">
                <div>
                    <div class="font-bold text-emerald-900">
                        Game Sedang Berjalan
                    </div>

                    <p class="mt-1 text-sm text-emerald-700">
                        Peserta baru tidak dapat bergabung lagi.
                    </p>
                </div>
            </div>

            <div v-if="
                room.game_phase === 'ready' &&
                room.current_question
            " class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-slate-50/60 px-5 py-5 sm:px-6">
                    <div class="text-xs font-bold uppercase tracking-[0.15em] text-indigo-600">
                        Soal Berikutnya
                    </div>

                    <div class="mt-1 text-sm text-slate-500">
                        Soal
                        {{ room.current_question.game_order }}
                        dari
                        {{ room.question_count }}
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <h3 class="text-lg font-extrabold leading-7 text-slate-950 sm:text-xl">
                        {{ room.current_question.text }}
                    </h3>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div v-for="option in room.current_question.options" :key="option.id"
                            class="flex items-center gap-3 rounded-xl border px-3.5 py-3" :class="option.is_correct
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

                            <span class="flex-1 text-sm text-slate-700">
                                {{ option.text }}
                            </span>

                            <span v-if="option.is_correct" class="text-xs font-bold text-emerald-700">
                                BENAR
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-slate-50/50 p-5 sm:p-6">
                    <p class="text-center text-sm text-slate-500">
                        Soal sudah siap. Peserta belum dapat melihat soal ini.
                    </p>

                    <button type="button" :disabled="actionProcessing"
                        class="mt-4 flex min-h-12 w-full items-center justify-center rounded-xl bg-indigo-600 px-6 py-3.5 font-bold text-white transition hover:bg-indigo-700 disabled:pointer-events-none disabled:opacity-50"
                        @click="openQuestion">
                        Buka Soal
                    </button>
                </div>
            </div>

            <div v-else-if="
                room.game_phase === 'open' &&
                room.current_question
            " class="overflow-hidden rounded-2xl border border-indigo-200 bg-white shadow-sm">
                <div class="border-b border-indigo-100 bg-indigo-50/60 px-5 py-5 sm:px-6">
                    <div class="text-xs font-bold uppercase tracking-[0.15em] text-indigo-600">
                        Soal Sedang Dibuka
                    </div>

                    <div class="mt-1 text-sm text-slate-500">
                        Soal
                        {{ room.current_question.game_order }}
                        dari
                        {{ room.question_count }}
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <h3 class="text-lg font-extrabold text-slate-950 sm:text-xl">
                        {{ room.current_question.text }}
                    </h3>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div v-for="option in room.current_question.options" :key="option.id"
                            class="flex items-center gap-3 rounded-xl border px-3.5 py-3" :class="option.is_correct
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

                            <span class="flex-1 text-sm text-slate-700">
                                {{ option.text }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4 text-center">
                            <div class="text-3xl font-black text-indigo-600">
                                {{ answeredCount }}
                            </div>

                            <div class="mt-1 text-xs text-slate-500">
                                Sudah Menjawab
                            </div>
                        </div>

                        <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 text-center">
                            <div class="text-3xl font-black text-amber-600">
                                {{ waitingAnswerCount }}
                            </div>

                            <div class="mt-1 text-xs text-slate-500">
                                Belum Menjawab
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-red-100 bg-red-50/50 p-5 sm:p-6">
                    <button type="button" :disabled="actionProcessing"
                        class="flex min-h-12 w-full items-center justify-center rounded-xl bg-red-600 px-6 py-3.5 font-bold text-white transition hover:bg-red-700 disabled:pointer-events-none disabled:opacity-50"
                        @click="closeQuestion">
                        Tutup Soal
                    </button>
                </div>
            </div>

            <div v-else-if="
                room.game_phase === 'result' &&
                room.current_question &&
                room.result
            " class="space-y-5">
                <div v-if="
                    room.result.round_result ===
                    'no_elimination'
                " class="rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6">
                    <h3 class="font-bold text-amber-900">
                        Tidak Ada Eliminasi
                    </h3>

                    <p class="mt-1 text-sm leading-6 text-amber-700">
                        Tidak ada peserta yang menjawab benar. Seluruh peserta aktif tetap bertahan.
                    </p>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 bg-slate-50/60 px-5 py-5 sm:px-6">
                        <div class="text-xs font-bold uppercase tracking-[0.15em] text-emerald-600">
                            Hasil Soal
                        </div>

                        <div class="mt-1 text-sm text-slate-500">
                            Soal
                            {{ room.current_question.game_order }}
                            dari
                            {{ room.question_count }}
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <h3 class="text-lg font-extrabold text-slate-950 sm:text-xl">
                            {{ room.current_question.text }}
                        </h3>

                        <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
                            <div class="rounded-xl bg-indigo-50 p-4 text-center">
                                <div class="text-2xl font-black text-indigo-600">
                                    {{ room.result.answered_count }}
                                </div>

                                <div class="mt-1 text-xs text-slate-500">
                                    Menjawab
                                </div>
                            </div>

                            <div class="rounded-xl bg-emerald-50 p-4 text-center">
                                <div class="text-2xl font-black text-emerald-600">
                                    {{ room.result.correct_count }}
                                </div>

                                <div class="mt-1 text-xs text-slate-500">
                                    Benar
                                </div>
                            </div>

                            <div class="rounded-xl bg-red-50 p-4 text-center">
                                <div class="text-2xl font-black text-red-600">
                                    {{ room.result.eliminated_count }}
                                </div>

                                <div class="mt-1 text-xs text-slate-500">
                                    Tereliminasi
                                </div>
                            </div>

                            <div class="rounded-xl bg-slate-100 p-4 text-center">
                                <div class="text-2xl font-black text-slate-950">
                                    {{ room.result.remaining_count }}
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
                                    {{ room.result.wrong_count }}
                                </strong>
                            </span>

                            <span>
                                Tidak menjawab:
                                <strong class="text-red-600">
                                    {{ room.result.no_answer_count }}
                                </strong>
                            </span>
                        </div>
                    </div>
                </div>

                <div v-if="
                    room.result.round_result ===
                    'normal'
                " class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-5 sm:px-6">
                        <h3 class="font-bold text-slate-900">
                            Peserta Menjawab Benar
                        </h3>
                    </div>

                    <div class="grid gap-3 p-5 sm:grid-cols-2 sm:p-6 xl:grid-cols-3">
                        <div v-for="participant in room.result.correct_participants" :key="participant.id"
                            class="flex items-center gap-3 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3">
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

                <div v-else class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm sm:p-6">
                    <h3 class="font-bold text-slate-900">
                        Semua Peserta Tetap Bertahan
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Seluruh
                        <strong>
                            {{ room.result.remaining_count }}
                            peserta aktif
                        </strong>
                        tetap dapat melanjutkan permainan.
                    </p>
                </div>

                <button v-if="
                    room.has_next_question &&
                    activeParticipantCount > 1
                " type="button" :disabled="actionProcessing"
                    class="flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 py-4 font-bold text-white transition hover:bg-indigo-700 disabled:pointer-events-none disabled:opacity-50"
                    @click="nextQuestion">
                    Soal Berikutnya

                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14" />

                        <path d="m13 6 6 6-6 6" />
                    </svg>
                </button>

                <div v-else class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-center sm:p-6">
                    <div class="font-bold text-amber-900">
                        Tidak Ada Soal Berikutnya
                    </div>

                    <p class="mx-auto mt-1 max-w-lg text-sm leading-6 text-amber-700">
                        Semua soal telah digunakan. Gunakan tombol Tutup Game untuk menyelesaikan pertandingan.
                    </p>

                    <button type="button" :disabled="actionProcessing"
                        class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl bg-red-600 px-6 py-3 font-bold text-white transition hover:bg-red-700 disabled:pointer-events-none disabled:opacity-50"
                        @click="finishGame">
                        Tutup Game
                    </button>
                </div>
            </div>
        </div>

        <div v-else-if="room.status === 'finished'"
            class="overflow-hidden rounded-2xl border border-indigo-100 bg-white shadow-sm">
            <div class="px-5 py-10 text-center sm:px-8 sm:py-14">
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="m5 12 4 4L19 6" />
                    </svg>
                </div>

                <div class="mt-5 text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">
                    Selesai
                </div>

                <h3 class="mt-2 text-2xl font-black text-slate-950 sm:text-3xl">
                    Game Telah Selesai
                </h3>

                <p class="mx-auto mt-2 max-w-lg text-sm text-slate-500">
                    Pertandingan telah selesai dan pemenang telah ditentukan.
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
                    <Link :href="roomResultRoute({
                        room: room.id,
                    })
                        "
                        class="inline-flex min-h-11 items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 font-bold text-white transition hover:bg-indigo-700">
                        Lihat Hasil Lengkap
                    </Link>

                    <Link :href="roomsIndex()"
                        class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-6 py-3 font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
                        Kembali ke Daftar Room
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>