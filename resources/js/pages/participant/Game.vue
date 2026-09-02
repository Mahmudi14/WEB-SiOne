<script setup lang="ts">
    import { usePolling } from '@/composables/game/usePolling'
    import ParticipantLayout from '@/layouts/participant/ParticipantLayout.vue'

    import {
        Head,
        Link,
        useForm,
    } from '@inertiajs/vue3'

    import { ref } from 'vue'

    type OptionLabel =
        | 'A'
        | 'B'
        | 'C'
        | 'D'

    type RoomStatus =
        | 'locked'
        | 'waiting'
        | 'running'
        | 'finished'

    type GamePhase =
        | 'ready'
        | 'open'
        | 'result'
        | null

    type ParticipantStatus =
        | 'waiting'
        | 'active'
        | 'eliminated'
        | 'winner'

    type AnswerResult =
        | 'correct'
        | 'wrong'
        | 'no_answer'
        | null

    type RoundResult =
        | 'normal'
        | 'no_elimination'
        | null

    interface GameQuestionOption {
        id: number
        label: OptionLabel
        text: string
    }

    interface GameQuestion {
        id: number
        number: number
        text: string
        options: GameQuestionOption[]
    }

    interface GameState {
        revision: number

        room_status: RoomStatus

        game_phase: GamePhase

        participant_status:
        ParticipantStatus

        question:
        | GameQuestion
        | null

        answer: {
            submitted: boolean

            selected_option_id:
            | number
            | null

            result: AnswerResult
        }

        round_result:
        RoundResult
    }

    const props = defineProps<{
        room: {
            name: string
            code: string
            status: RoomStatus
            game_phase: GamePhase
            revision: number
        }

        participant: {
            name: string
            code: string
            status: ParticipantStatus
        }
    }>()

    /*
    |--------------------------------------------------------------------------
    | State
    |--------------------------------------------------------------------------
    */

    const roomStatus =
        ref<RoomStatus>(
            props.room.status,
        )

    const phase =
        ref<GamePhase>(
            props.room.game_phase,
        )

    const participantStatus =
        ref<ParticipantStatus>(
            props.participant.status,
        )

    const question =
        ref<GameQuestion | null>(
            null,
        )

    const selectedOptionId =
        ref<number | null>(
            null,
        )

    const answerSubmitted =
        ref(false)

    const answerResult =
        ref<AnswerResult>(
            null,
        )

    const roundResult =
        ref<RoundResult>(
            null,
        )

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    const answerForm = useForm<{
        question_option_id:
        | number
        | null
    }>({
        question_option_id: null,
    })

    /*
    |--------------------------------------------------------------------------
    | Select Option
    |--------------------------------------------------------------------------
    */

    const selectOption = (
        optionId: number,
    ) => {
        if (
            answerSubmitted.value ||
            answerForm.processing
        ) {
            return
        }

        selectedOptionId.value =
            optionId

        answerForm.clearErrors()
    }

    /*
    |--------------------------------------------------------------------------
    | Submit
    |--------------------------------------------------------------------------
    */

    const submitAnswer = () => {
        if (
            selectedOptionId.value ===
            null ||
            answerSubmitted.value ||
            answerForm.processing
        ) {
            return
        }

        answerForm.question_option_id =
            selectedOptionId.value

        answerForm.post(
            `/participant/room/${props.room.code}/answer`,
            {
                preserveScroll: true,

                onSuccess: () => {
                    answerSubmitted.value =
                        true

                    answerForm.clearErrors()
                },
            },
        )
    }

    /*
    |--------------------------------------------------------------------------
    | Polling
    |--------------------------------------------------------------------------
    */

    usePolling<GameState>(
        `/participant/room/${props.room.code}/state`,
        (state) => {
            roomStatus.value =
                state.room_status

            phase.value =
                state.game_phase

            participantStatus.value =
                state.participant_status

            /*
             * Jika server mengirim soal baru,
             * reset jawaban lokal soal sebelumnya.
             */
            if (
                state.question &&
                question.value?.id !==
                state.question.id
            ) {
                selectedOptionId.value =
                    null

                answerSubmitted.value =
                    false

                answerResult.value =
                    null

                roundResult.value =
                    null

                answerForm.reset()

                answerForm.clearErrors()
            }

            question.value =
                state.question

            /*
             * Server adalah sumber status submit.
             */
            answerSubmitted.value =
                state.answer.submitted

            /*
             * Jangan menimpa pilihan lokal
             * sebelum jawaban benar-benar terkirim.
             */
            if (
                state.answer.submitted
            ) {
                selectedOptionId.value =
                    state.answer
                        .selected_option_id
            }

            answerResult.value =
                state.answer.result

            roundResult.value =
                state.round_result
        },
    )
</script>

<template>

    <Head title="Permainan — SiOne" />

    <ParticipantLayout>
        <!-- =====================================================
        WINNER
        ====================================================== -->

        <div v-if="
            roomStatus === 'finished' &&
            participantStatus === 'winner'
        "
            class="fixed inset-0 z-50 flex min-h-svh items-center justify-center overflow-y-auto bg-slate-950 p-5 text-white sm:p-8">
            <!-- Decoration -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
                <div class="absolute -left-32 -top-32 h-80 w-80 rounded-full bg-indigo-500/20 blur-3xl" />

                <div class="absolute -bottom-32 -right-32 h-80 w-80 rounded-full bg-violet-500/20 blur-3xl" />
            </div>

            <div class="relative mx-auto w-full max-w-xl text-center">
                <div
                    class="mx-auto flex h-24 w-24 items-center justify-center rounded-[2rem] bg-amber-400 text-slate-950 shadow-2xl shadow-amber-500/20 sm:h-28 sm:w-28">
                    <svg class="h-12 w-12 sm:h-14 sm:w-14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M8 21h8" />

                        <path d="M12 17v4" />

                        <path d="M7 4h10v4a5 5 0 0 1-10 0V4Z" />

                        <path d="M7 6H4v2a4 4 0 0 0 4 4" />

                        <path d="M17 6h3v2a4 4 0 0 1-4 4" />
                    </svg>
                </div>

                <p class="mt-7 text-xs font-bold uppercase tracking-[0.25em] text-amber-300">
                    Permainan Selesai
                </p>

                <h1 class="mt-3 text-5xl font-black tracking-tight sm:text-7xl">
                    Selamat!
                </h1>

                <p class="mx-auto mt-4 max-w-md text-base leading-7 text-slate-300 sm:text-lg">
                    Kamu berhasil bertahan hingga akhir dan menjadi
                    pemenang SiOne.
                </p>

                <!-- Winner Identity -->
                <div
                    class="mx-auto mt-8 flex max-w-sm items-center gap-4 rounded-2xl border border-white/10 bg-white/5 p-4 text-left backdrop-blur">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/10">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="7" r="4" />

                            <path d="M20 21a8 8 0 0 0-16 0" />
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="truncate font-bold">
                            {{ participant.name }}
                        </div>

                        <div class="mt-1 font-mono text-sm text-slate-400">
                            #{{ participant.code }}
                        </div>
                    </div>

                    <div class="rounded-xl bg-amber-400/10 px-3 py-1.5 text-xs font-bold text-amber-300">
                        WINNER
                    </div>
                </div>

                <Link href="/"
                    class="mt-8 inline-flex min-h-12 items-center justify-center rounded-xl bg-white px-6 py-3 font-bold text-slate-950 transition hover:bg-slate-100">
                    Kembali ke Halaman Awal
                </Link>
            </div>
        </div>

        <!-- =====================================================
        ELIMINATED
        ====================================================== -->

        <div v-else-if="
            participantStatus ===
            'eliminated'
        "
            class="fixed inset-0 z-50 flex min-h-svh items-center justify-center overflow-y-auto bg-red-600 p-5 text-white sm:p-8">
            <div class="mx-auto w-full max-w-lg text-center">
                <div
                    class="mx-auto flex h-24 w-24 items-center justify-center rounded-[2rem] bg-white/15 sm:h-28 sm:w-28">
                    <svg class="h-12 w-12 sm:h-14 sm:w-14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </div>

                <p class="mt-7 text-xs font-bold uppercase tracking-[0.22em] text-white/70">
                    Hasil Ronde
                </p>

                <h1 class="mt-3 text-4xl font-black tracking-tight sm:text-6xl">
                    {{
                        answerResult ===
                            'no_answer'
                            ? 'Tidak Menjawab'
                            : 'Jawaban Salah'
                    }}
                </h1>

                <div class="mt-4 text-lg font-black uppercase tracking-[0.15em] sm:text-xl">
                    Tereliminasi
                </div>

                <p class="mx-auto mt-4 max-w-md text-sm leading-6 text-white/75 sm:text-base">
                    Perjalananmu di pertandingan ini telah berakhir.
                    Terima kasih sudah mengikuti SiOne.
                </p>

                <Link href="/"
                    class="mt-8 inline-flex min-h-12 items-center justify-center rounded-xl bg-white px-6 py-3 font-bold text-red-600 transition hover:bg-red-50">
                    Kembali ke Halaman Awal
                </Link>
            </div>
        </div>

        <!-- =====================================================
        CORRECT
        ====================================================== -->

        <div v-else-if="
            phase === 'result' &&
            answerResult === 'correct'
        "
            class="fixed inset-0 z-50 flex min-h-svh items-center justify-center overflow-y-auto bg-emerald-600 p-5 text-white sm:p-8">
            <div class="mx-auto w-full max-w-lg text-center">
                <div
                    class="mx-auto flex h-24 w-24 items-center justify-center rounded-[2rem] bg-white/15 sm:h-28 sm:w-28">
                    <svg class="h-12 w-12 sm:h-14 sm:w-14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m5 12 4 4L19 6" />
                    </svg>
                </div>

                <p class="mt-7 text-xs font-bold uppercase tracking-[0.22em] text-white/70">
                    Hasil Ronde
                </p>

                <h1 class="mt-3 text-5xl font-black tracking-tight sm:text-7xl">
                    Benar!
                </h1>

                <p class="mt-4 text-lg font-bold text-white/90 sm:text-xl">
                    Kamu tetap bertahan.
                </p>

                <div
                    class="mx-auto mt-8 inline-flex items-center gap-3 rounded-2xl bg-white/10 px-5 py-3.5 text-sm font-semibold">
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white opacity-50" />

                        <span class="relative h-2.5 w-2.5 rounded-full bg-white" />
                    </span>

                    Menunggu soal berikutnya
                </div>
            </div>
        </div>

        <!-- =====================================================
        NO ELIMINATION
        ====================================================== -->

        <div v-else-if="
            phase === 'result' &&
            roundResult ===
            'no_elimination'
        "
            class="fixed inset-0 z-50 flex min-h-svh items-center justify-center overflow-y-auto bg-amber-500 p-5 text-slate-950 sm:p-8">
            <div class="mx-auto w-full max-w-xl text-center">
                <div
                    class="mx-auto flex h-24 w-24 items-center justify-center rounded-[2rem] bg-white/25 sm:h-28 sm:w-28">
                    <svg class="h-11 w-11 sm:h-13 sm:w-13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10" />

                        <path d="M12 8v5" />
                        <path d="M12 17h.01" />
                    </svg>
                </div>

                <p class="mt-7 text-xs font-bold uppercase tracking-[0.22em] text-slate-900/60">
                    Hasil Ronde
                </p>

                <h1 class="mt-3 text-4xl font-black tracking-tight sm:text-6xl">
                    Tidak Ada Eliminasi
                </h1>

                <p v-if="
                    answerResult ===
                    'wrong'
                " class="mt-5 text-lg font-bold">
                    Jawabanmu belum tepat.
                </p>

                <p v-else-if="
                    answerResult ===
                    'no_answer'
                " class="mt-5 text-lg font-bold">
                    Kamu tidak mengirim jawaban.
                </p>

                <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-900/70 sm:text-base">
                    Tidak ada peserta yang menjawab benar pada soal ini,
                    sehingga seluruh peserta tetap bertahan.
                </p>

                <div
                    class="mx-auto mt-8 inline-flex items-center gap-3 rounded-2xl bg-white/25 px-5 py-3.5 text-sm font-semibold">
                    <span class="h-2.5 w-2.5 animate-pulse rounded-full bg-slate-950" />

                    Menunggu soal berikutnya
                </div>
            </div>
        </div>

        <!-- =====================================================
        NORMAL GAME
        ====================================================== -->

        <div v-else class="mx-auto w-full max-w-3xl">
            <!-- Participant Bar -->
            <div
                class="mb-4 flex items-center gap-3 rounded-2xl border border-white/80 bg-white/90 p-3.5 shadow-sm backdrop-blur sm:p-4">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="7" r="4" />

                        <path d="M20 21a8 8 0 0 0-16 0" />
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="truncate text-sm font-bold text-slate-900 sm:text-base">
                        {{ participant.name }}
                    </div>

                    <div class="mt-0.5 font-mono text-xs text-slate-400">
                        #{{ participant.code }}
                    </div>
                </div>

                <div class="text-right">
                    <div class="hidden text-[10px] font-semibold uppercase tracking-wider text-slate-400 sm:block">
                        Room
                    </div>

                    <div
                        class="mt-0.5 rounded-lg bg-slate-100 px-2.5 py-1.5 font-mono text-xs font-bold tracking-wider text-slate-600">
                        {{ room.code }}
                    </div>
                </div>
            </div>

            <!-- =================================================
            READY
            ================================================== -->

            <div v-if="
                phase === 'ready' &&
                participantStatus ===
                'active'
            "
                class="overflow-hidden rounded-[1.75rem] border border-white/80 bg-white/90 p-6 text-center shadow-xl shadow-slate-200/50 backdrop-blur sm:p-10">
                <div class="relative mx-auto flex h-24 w-24 items-center justify-center" aria-hidden="true">
                    <div class="absolute inset-0 animate-ping rounded-full bg-indigo-100 opacity-50" />

                    <div class="absolute inset-3 rounded-full bg-indigo-50" />

                    <div
                        class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v4" />
                            <path d="m16.2 7.8 2.9-2.9" />
                            <path d="M18 12h4" />
                            <path d="m16.2 16.2 2.9 2.9" />
                            <path d="M12 18v4" />
                            <path d="m4.9 19.1 2.9-2.9" />
                            <path d="M2 12h4" />
                            <path d="m4.9 4.9 2.9 2.9" />
                        </svg>
                    </div>
                </div>

                <p class="mt-5 text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">
                    Siap untuk soal berikutnya
                </p>

                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">
                    Bersiap!
                </h1>

                <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-500 sm:text-base">
                    Tetap fokus. Soal akan tampil otomatis ketika admin
                    membuka ronde berikutnya.
                </p>
            </div>

            <!-- =================================================
            OPEN QUESTION
            ================================================== -->

            <div v-else-if="
                phase === 'open' &&
                question &&
                participantStatus ===
                'active'
            "
                class="overflow-hidden rounded-[1.75rem] border border-white/80 bg-white/95 shadow-xl shadow-slate-200/50 backdrop-blur">
                <!-- Question Header -->
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-7">
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                            Pertanyaan
                        </p>

                        <div class="mt-0.5 text-sm font-extrabold text-indigo-600">
                            Soal {{ question.number }}
                        </div>
                    </div>

                    <div v-if="
                        !answerSubmitted
                    "
                        class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-50" />

                            <span class="relative h-2 w-2 rounded-full bg-emerald-500" />
                        </span>

                        Dibuka
                    </div>

                    <div v-else
                        class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-600">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m5 12 4 4L19 6" />
                        </svg>

                        Terjawab
                    </div>
                </div>

                <!-- Question -->
                <div class="px-5 pt-6 sm:px-7 sm:pt-7">
                    <h1 class="text-xl font-extrabold leading-relaxed text-slate-950 sm:text-2xl lg:text-[1.75rem]">
                        {{ question.text }}
                    </h1>

                    <p v-if="
                        !answerSubmitted
                    " class="mt-2 text-sm leading-6 text-slate-500">
                        Pilih satu jawaban, kemudian tekan tombol kirim.
                    </p>
                </div>

                <!-- Options -->
                <div class="space-y-3 px-5 pb-2 pt-6 sm:px-7 sm:pt-7">
                    <button v-for="
option in
                                question.options
                        " :key="option.id" type="button" :disabled="answerSubmitted ||
                            answerForm.processing
                            "
                        class="group flex min-h-16 w-full items-center gap-3 rounded-2xl border p-3.5 text-left transition duration-200 sm:gap-4 sm:p-4"
                        :class="[
                            selectedOptionId ===
                                option.id
                                ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-100'
                                : 'border-slate-200 bg-slate-50/70 hover:border-indigo-300 hover:bg-indigo-50/50',

                            answerSubmitted &&
                                selectedOptionId !==
                                option.id
                                ? 'cursor-not-allowed opacity-45'
                                : '',
                        ]" @click="
                            selectOption(
                                option.id,
                            )
                            ">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border text-base font-black transition sm:h-12 sm:w-12"
                            :class="selectedOptionId ===
                                    option.id
                                    ? 'border-indigo-600 bg-indigo-600 text-white'
                                    : 'border-slate-200 bg-white text-indigo-600'
                                ">
                            {{ option.label }}
                        </div>

                        <div
                            class="min-w-0 flex-1 break-words text-sm font-semibold leading-6 text-slate-800 sm:text-base">
                            {{ option.text }}
                        </div>

                        <div v-if="
                            selectedOptionId ===
                            option.id
                        "
                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m5 12 4 4L19 6" />
                            </svg>
                        </div>
                    </button>
                </div>

                <!-- Error -->
                <p v-if="
                    answerForm.errors
                        .question_option_id
                "
                    class="mx-5 mt-4 rounded-xl bg-red-50 px-4 py-3 text-center text-sm font-medium text-red-600 sm:mx-7">
                    {{
                        answerForm.errors
                            .question_option_id
                    }}
                </p>

                <!-- Submit -->
                <div v-if="
                    !answerSubmitted
                " class="mt-5 border-t border-slate-100 bg-slate-50/50 px-5 py-5 sm:px-7 sm:py-6">
                    <button type="button" :disabled="selectedOptionId ===
                        null ||
                        answerForm.processing
                        "
                        class="group flex min-h-14 w-full items-center justify-center gap-2.5 rounded-2xl bg-indigo-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-indigo-200/70 transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-xl disabled:pointer-events-none disabled:translate-y-0 disabled:bg-slate-300 disabled:shadow-none"
                        @click="submitAnswer">
                        <svg v-if="
                            answerForm.processing
                        " class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />

                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z" />
                        </svg>

                        {{
                            answerForm.processing
                                ? 'Mengirim Jawaban...'
                                : selectedOptionId ===
                                    null
                                    ? 'Pilih Jawaban'
                                    : 'Kirim Jawaban'
                        }}

                        <svg v-if="
                            !answerForm.processing &&
                            selectedOptionId !==
                            null
                        " class="h-5 w-5 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14" />
                            <path d="m13 6 6 6-6 6" />
                        </svg>
                    </button>

                    <p class="mt-3 text-center text-xs leading-5 text-slate-400">
                        Pastikan pilihanmu benar. Jawaban tidak dapat
                        diubah setelah dikirim.
                    </p>
                </div>

                <!-- Submitted -->
                <div v-else class="mt-5 border-t border-indigo-100 bg-indigo-50/70 px-5 py-6 text-center sm:px-7">
                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m5 12 4 4L19 6" />
                        </svg>
                    </div>

                    <div class="mt-3 font-extrabold text-slate-900">
                        Jawaban Terkirim
                    </div>

                    <p class="mx-auto mt-1 max-w-sm text-sm leading-6 text-slate-500">
                        Jawabanmu sudah dikunci. Tunggu admin
                        mengumumkan hasil ronde.
                    </p>
                </div>
            </div>

            <!-- =================================================
            FINISHED NON-WINNER
            ================================================== -->

            <div v-else-if="
                roomStatus ===
                'finished'
            "
                class="rounded-[1.75rem] border border-white/80 bg-white/90 p-7 text-center shadow-xl shadow-slate-200/50 sm:p-10">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M8 2v4" />

                        <path d="M16 2v4" />

                        <rect width="18" height="18" x="3" y="4" rx="2" />

                        <path d="M3 10h18" />
                        <path d="m9 16 2 2 4-4" />
                    </svg>
                </div>

                <h1 class="mt-5 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                    Permainan Selesai
                </h1>

                <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                    Terima kasih telah mengikuti pertandingan SiOne.
                </p>

                <Link href="/"
                    class="mt-6 inline-flex min-h-12 items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 font-bold text-white transition hover:bg-indigo-700">
                    Kembali ke Halaman Awal
                </Link>
            </div>

            <!-- =================================================
            FALLBACK
            ================================================== -->

            <div v-else
                class="rounded-[1.75rem] border border-white/80 bg-white/90 p-8 text-center shadow-xl shadow-slate-200/50">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50">
                    <span class="relative flex h-3 w-3">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-indigo-400 opacity-50" />

                        <span class="relative h-3 w-3 rounded-full bg-indigo-600" />
                    </span>
                </div>

                <h2 class="mt-4 font-bold text-slate-900">
                    Memuat permainan...
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Tunggu sebentar, status permainan sedang diperbarui.
                </p>
            </div>
        </div>
    </ParticipantLayout>
</template>