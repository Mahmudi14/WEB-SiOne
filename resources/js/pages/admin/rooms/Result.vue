<script setup lang="ts">
    import AdminLayout from '@/layouts/admin/AdminLayout.vue'

    import type {
        EliminatedParticipant,
        ResultParticipant,
        ResultQuestion,
        ResultRoom,
        ResultSummary,
    } from '@/types/result'

    import {
        Head,
        Link,
        router,
    } from '@inertiajs/vue3'

    const props = defineProps<{
        room: ResultRoom

        summary: ResultSummary

        winners: ResultParticipant[]

        eliminated_participants:
        EliminatedParticipant[]

        questions: ResultQuestion[]
    }>()

    /*
    |--------------------------------------------------------------------------
    | Duplicate
    |--------------------------------------------------------------------------
    */

    const duplicateRoom = () => {
        const confirmed =
            window.confirm(
                `Duplikat room "${props.room.name}" beserta seluruh soalnya?`,
            )

        if (!confirmed) {
            return
        }

        router.post(
            `/admin/rooms/${props.room.id}/duplicate`,
        )
    }
</script>

<template>

    <Head :title="`Hasil - ${room.name}`" />

    <AdminLayout title="Hasil Pertandingan">
        <!-- =====================================================
        HEADER
        ====================================================== -->

        <div
            class="mb-6 flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-bps-blue">
                    Hasil Pertandingan
                </div>

                <h1 class="mt-1 text-2xl font-black text-bps-navy">
                    {{ room.name }}
                </h1>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="rounded-lg bg-slate-100 px-3 py-1.5 font-mono text-sm font-bold text-slate-700">
                        {{ room.code }}
                    </span>

                    <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-bps-blue">
                        SELESAI
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <button type="button"
                    class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-bps-blue transition hover:bg-blue-100"
                    @click="duplicateRoom">
                    Duplikat Room
                </button>

                <Link href="/admin/rooms"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Kembali
                </Link>
            </div>
        </div>

        <!-- =====================================================
        SUMMARY
        ====================================================== -->

        <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <!-- Participants -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <div class="text-sm text-slate-500">
                    Total Peserta
                </div>

                <div class="mt-2 text-3xl font-black text-bps-blue">
                    {{
                        summary.participant_count
                    }}
                </div>
            </div>

            <!-- Winner -->
            <div class="rounded-2xl border border-green-200 bg-green-50 p-5">
                <div class="text-sm text-green-700">
                    Pemenang
                </div>

                <div class="mt-2 text-3xl font-black text-bps-green">
                    {{
                        summary.winner_count
                    }}
                </div>
            </div>

            <!-- Eliminated -->
            <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
                <div class="text-sm text-red-700">
                    Tereliminasi
                </div>

                <div class="mt-2 text-3xl font-black text-red-600">
                    {{
                        summary.eliminated_count
                    }}
                </div>
            </div>

            <!-- Questions -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <div class="text-sm text-slate-500">
                    Soal Digunakan
                </div>

                <div class="mt-2 text-3xl font-black text-bps-navy">
                    {{
                        summary.questions_used
                    }}
                </div>
            </div>
        </div>

        <!-- =====================================================
        WINNERS
        ====================================================== -->

        <div class="mb-6 rounded-2xl border border-green-200 bg-white">
            <div class="border-b border-green-100 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-100 text-xl font-black text-bps-green">
                        ★
                    </div>

                    <div>
                        <h2 class="font-bold text-bps-navy">
                            Pemenang
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Peserta yang bertahan
                            sampai pertandingan
                            ditutup.
                        </p>
                    </div>
                </div>
            </div>

            <!-- No Winner -->
            <div v-if="
                winners.length === 0
            " class="px-6 py-10 text-center">
                <div class="text-sm font-semibold text-slate-700">
                    Tidak ada pemenang
                </div>
            </div>

            <!-- Winner List -->
            <div v-else class="grid gap-3 p-6 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="(
participant,
                            index
                    ) in winners" :key="participant.id" class="rounded-xl border border-green-200 bg-green-50 p-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-bps-green font-black text-white">
                            {{ index + 1 }}
                        </div>

                        <div>
                            <div class="font-bold text-green-900">
                                {{
                                    participant.name
                                }}
                            </div>

                            <div class="mt-0.5 font-mono text-xs text-green-700">
                                #{{
                                    participant.code
                                }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =====================================================
        QUESTION HISTORY
        ====================================================== -->

        <div class="mb-6 rounded-2xl border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 class="font-bold text-bps-navy">
                    Riwayat Soal
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Statistik perjalanan peserta
                    pada setiap soal.
                </p>
            </div>

            <!-- No Question -->
            <div v-if="
                questions.length === 0
            " class="px-6 py-12 text-center text-sm text-slate-500">
                Tidak ada soal yang selesai
                dimainkan.
            </div>

            <!-- History -->
            <div v-else class="divide-y divide-slate-100">
                <div v-for="
question in questions
                    " :key="question.id" class="p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <!-- Question -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-bps-navy text-sm font-black text-white">
                                    {{
                                        question.number
                                    }}
                                </div>

                                <div class="font-semibold leading-relaxed text-slate-900">
                                    {{
                                        question.text
                                    }}
                                </div>
                            </div>

                            <!-- No elimination -->
                            <div v-if="
                                question.round_result ===
                                'no_elimination'
                            " class="mt-4 rounded-xl border border-orange-200 bg-orange-50 px-4 py-3">
                                <div class="text-sm font-bold text-orange-800">
                                    Tidak Ada Eliminasi
                                </div>

                                <div class="mt-1 text-xs text-orange-700">
                                    Tidak ada peserta
                                    menjawab benar sehingga
                                    seluruh peserta tetap
                                    bertahan.
                                </div>
                            </div>
                        </div>

                        <!-- Flow -->
                        <div class="flex shrink-0 items-center gap-3 rounded-xl bg-slate-50 px-5 py-3">
                            <div class="text-center">
                                <div class="text-xl font-black text-bps-blue">
                                    {{
                                        question.participants_before
                                    }}
                                </div>

                                <div class="text-[11px] text-slate-400">
                                    Sebelum
                                </div>
                            </div>

                            <div class="text-slate-300">
                                →
                            </div>

                            <div class="text-center">
                                <div class="text-xl font-black text-bps-green">
                                    {{
                                        question.remaining_count
                                    }}
                                </div>

                                <div class="text-[11px] text-slate-400">
                                    Bertahan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="mt-5 grid gap-2 sm:grid-cols-2 lg:grid-cols-5">
                        <div class="rounded-lg bg-blue-50 px-3 py-3 text-center">
                            <div class="font-bold text-bps-blue">
                                {{
                                    question.answered_count
                                }}
                            </div>

                            <div class="text-xs text-slate-500">
                                Menjawab
                            </div>
                        </div>

                        <div class="rounded-lg bg-green-50 px-3 py-3 text-center">
                            <div class="font-bold text-bps-green">
                                {{
                                    question.correct_count
                                }}
                            </div>

                            <div class="text-xs text-slate-500">
                                Benar
                            </div>
                        </div>

                        <div class="rounded-lg bg-red-50 px-3 py-3 text-center">
                            <div class="font-bold text-red-600">
                                {{
                                    question.wrong_count
                                }}
                            </div>

                            <div class="text-xs text-slate-500">
                                Salah
                            </div>
                        </div>

                        <div class="rounded-lg bg-orange-50 px-3 py-3 text-center">
                            <div class="font-bold text-bps-orange">
                                {{
                                    question.no_answer_count
                                }}
                            </div>

                            <div class="text-xs text-slate-500">
                                Tidak Jawab
                            </div>
                        </div>

                        <div class="rounded-lg bg-slate-100 px-3 py-3 text-center">
                            <div class="font-bold text-slate-700">
                                {{
                                    question.eliminated_count
                                }}
                            </div>

                            <div class="text-xs text-slate-500">
                                Eliminasi
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =====================================================
        ELIMINATED PARTICIPANTS
        ====================================================== -->

        <div class="rounded-2xl border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 class="font-bold text-bps-navy">
                    Peserta Tereliminasi
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Riwayat peserta yang gugur
                    selama pertandingan.
                </p>
            </div>

            <div v-if="
                eliminated_participants.length ===
                0
            " class="px-6 py-10 text-center text-sm text-slate-500">
                Tidak ada peserta tereliminasi.
            </div>

            <div v-else class="divide-y divide-slate-100">
                <div v-for="
participant in
                            eliminated_participants
                    " :key="participant.id"
                    class="flex flex-col gap-2 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="font-semibold text-slate-900">
                            {{
                                participant.name
                            }}
                        </div>

                        <div class="mt-0.5 font-mono text-xs text-slate-400">
                            #{{
                                participant.code
                            }}
                        </div>
                    </div>

                    <div v-if="
                        participant.eliminated_at_question
                    " class="text-sm text-red-600">
                        Gugur di soal
                        <strong>
                            {{
                                participant
                                    .eliminated_at_question
                                    .number
                            }}
                        </strong>
                    </div>

                    <div v-else class="text-sm text-slate-400">
                        -
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>