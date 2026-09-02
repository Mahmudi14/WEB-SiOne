<script setup lang="ts">
    import AdminLayout from '@/layouts/admin/AdminLayout.vue'

    import type {
        EditableRoom,
        RoomFormData,
        RoomStatus,
    } from '@/types/room'

    import type {
        Question,
        QuestionFormData,
        QuestionOptionLabel,
    } from '@/types/question'

    import {
        Head,
        Link,
        router,
        useForm,
    } from '@inertiajs/vue3'

    import {
        computed,
        ref,
    } from 'vue'

    const props = defineProps<{
        room: EditableRoom
    }>()

    /*
    |--------------------------------------------------------------------------
    | Questions
    |--------------------------------------------------------------------------
    */

    const questions = computed(
        () => props.room.questions ?? [],
    )

    /*
    |--------------------------------------------------------------------------
    | Tabs
    |--------------------------------------------------------------------------
    */

    type Tab =
        | 'information'
        | 'questions'

    const activeTab =
        ref<Tab>('information')

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    const statusClass = (
        status: RoomStatus,
    ) => {
        switch (status) {
            case 'locked':
                return 'border-slate-200 bg-slate-100 text-slate-600'

            case 'waiting':
                return 'border-amber-200 bg-amber-50 text-amber-700'

            case 'running':
                return 'border-emerald-200 bg-emerald-50 text-emerald-700'

            case 'finished':
                return 'border-indigo-200 bg-indigo-50 text-indigo-700'
        }
    }

    const statusDotClass = (
        status: RoomStatus,
    ) => {
        switch (status) {
            case 'locked':
                return 'bg-slate-400'

            case 'waiting':
                return 'bg-amber-500'

            case 'running':
                return 'bg-emerald-500'

            case 'finished':
                return 'bg-indigo-500'
        }
    }

    const playLabel = (
        status: RoomStatus,
    ) => {
        switch (status) {
            case 'locked':
                return 'Play'

            case 'waiting':
                return 'Lobby'

            case 'running':
                return 'Kontrol'

            case 'finished':
                return 'Hasil'
        }
    }

    const playUrl = computed(() => {
        if (
            props.room.status ===
            'finished'
        ) {
            return `/admin/rooms/${props.room.id}/result`
        }

        return `/admin/rooms/${props.room.id}/play`
    })

    /*
    |--------------------------------------------------------------------------
    | Option Labels
    |--------------------------------------------------------------------------
    */

    const optionLabels:
        QuestionOptionLabel[] = [
            'A',
            'B',
            'C',
            'D',
        ]

    const getOptionLabel = (
        index: number,
    ): QuestionOptionLabel => {
        return optionLabels[index] ?? 'A'
    }

    /*
    |--------------------------------------------------------------------------
    | Room Form
    |--------------------------------------------------------------------------
    */

    const roomForm =
        useForm<RoomFormData>({
            name:
                props.room.name,

            code:
                props.room.code,

            description:
                props.room.description ?? '',

            shuffle_questions:
                props.room.shuffle_questions,
        })

    const updateRoom = () => {
        if (!props.room.can_edit) {
            return
        }

        roomForm.put(
            `/admin/rooms/${props.room.id}`,
            {
                preserveScroll: true,
            },
        )
    }

    /*
    |--------------------------------------------------------------------------
    | Question Form
    |--------------------------------------------------------------------------
    */

    const createEmptyQuestion =
        (): QuestionFormData => ({
            text: '',

            options: [
                {
                    text: '',
                    is_correct: true,
                },
                {
                    text: '',
                    is_correct: false,
                },
            ],
        })

    const questionForm =
        useForm<QuestionFormData>(
            createEmptyQuestion(),
        )

    const editingQuestionId =
        ref<number | null>(null)

    const questionModalOpen =
        ref(false)

    const openCreateQuestionModal = () => {
        if (
            !props.room.can_edit_questions
        ) {
            return
        }

        resetQuestionForm()

        questionModalOpen.value =
            true
    }

    /*
    |--------------------------------------------------------------------------
    | Add Option
    |--------------------------------------------------------------------------
    */

    const addOption = () => {
        if (
            !props.room.can_edit_questions
        ) {
            return
        }

        if (
            questionForm.options.length >= 4
        ) {
            return
        }

        questionForm.options.push({
            text: '',
            is_correct: false,
        })
    }

    /*
    |--------------------------------------------------------------------------
    | Remove Option
    |--------------------------------------------------------------------------
    */

    const removeOption = (
        index: number,
    ) => {
        if (
            !props.room.can_edit_questions
        ) {
            return
        }

        if (
            questionForm.options.length <= 2
        ) {
            return
        }

        const wasCorrect =
            questionForm.options[index]
                ?.is_correct ?? false

        questionForm.options.splice(
            index,
            1,
        )

        if (wasCorrect) {
            questionForm.options.forEach(
                (
                    option,
                    optionIndex,
                ) => {
                    option.is_correct =
                        optionIndex === 0
                },
            )
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Correct Option
    |--------------------------------------------------------------------------
    */

    const setCorrectOption = (
        index: number,
    ) => {
        if (
            !props.room.can_edit_questions
        ) {
            return
        }

        questionForm.options.forEach(
            (
                option,
                optionIndex,
            ) => {
                option.is_correct =
                    optionIndex === index
            },
        )
    }

    /*
    |--------------------------------------------------------------------------
    | Error Helper
    |--------------------------------------------------------------------------
    */

    const optionError = (
        index: number,
    ): string | undefined => {
        const errors =
            questionForm.errors as Record<
                string,
                string
            >

        return errors[
            `options.${index}.text`
        ]
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Question Form
    |--------------------------------------------------------------------------
    */

    const resetQuestionForm = () => {
        editingQuestionId.value = null

        questionForm.clearErrors()

        questionForm.text = ''

        questionForm.options = [
            {
                text: '',
                is_correct: true,
            },
            {
                text: '',
                is_correct: false,
            },
        ]
    }

    const closeQuestionModal = () => {
        questionModalOpen.value =
            false

        resetQuestionForm()
    }

    /*
    |--------------------------------------------------------------------------
    | Submit Question
    |--------------------------------------------------------------------------
    */

    const submitQuestion = () => {
        if (
            !props.room.can_edit_questions
        ) {
            return
        }

        if (
            editingQuestionId.value !==
            null
        ) {
            questionForm.put(
                `/admin/rooms/${props.room.id}/questions/${editingQuestionId.value}`,
                {
                    preserveScroll: true,

                    onSuccess: () => {
                        closeQuestionModal()
                    },
                },
            )

            return
        }

        questionForm.post(
            `/admin/rooms/${props.room.id}/questions`,
            {
                preserveScroll: true,

                onSuccess: () => {
                    closeQuestionModal()
                },
            },
        )
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Question
    |--------------------------------------------------------------------------
    */

    const editQuestion = (
        question: Question,
    ) => {
        if (
            !props.room.can_edit_questions
        ) {
            return
        }

        editingQuestionId.value =
            question.id

        questionForm.clearErrors()

        questionForm.text =
            question.text

        questionForm.options =
            question.options.map(
                (option) => ({
                    text:
                        option.text,

                    is_correct:
                        option.is_correct,
                }),
            )

        questionModalOpen.value =
            true
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Question
    |--------------------------------------------------------------------------
    */

    const deleteQuestion = (
        question: Question,
    ) => {
        if (
            !props.room.can_edit_questions
        ) {
            return
        }

        const confirmed =
            window.confirm(
                `Hapus soal nomor ${question.position}?`,
            )

        if (!confirmed) {
            return
        }

        router.delete(
            `/admin/rooms/${props.room.id}/questions/${question.id}`,
            {
                preserveScroll: true,

                onSuccess: () => {
                    if (
                        editingQuestionId.value ===
                        question.id
                    ) {
                        resetQuestionForm()
                    }
                },
            },
        )
    }
</script>

<template>

    <Head :title="room.can_edit
        ? `Edit ${room.name} — SiOne`
        : `Lihat ${room.name} — SiOne`
        " />

    <AdminLayout :title="room.can_edit
        ? 'Edit Room'
        : 'Lihat Room'
        ">
        <!-- =====================================================
        TOP RIGHT ACTION
        ====================================================== -->

        <template #actions>
            <Link href="/admin/rooms"
                class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus-visible:ring-2 focus-visible:ring-indigo-500/30 sm:min-h-11 sm:px-4">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m15 18-6-6 6-6" />
                </svg>

                Kembali
            </Link>
        </template>

        <!-- =====================================================
        ROOM SUMMARY
        ====================================================== -->

        <div class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm sm:mb-6">
            <div class="flex flex-col gap-5 p-5 sm:p-6 md:flex-row md:items-center md:justify-between">
                <div class="flex min-w-0 items-start gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect width="18" height="18" x="3" y="3" rx="2" />

                            <path d="M7 8h10" />
                            <path d="M7 12h10" />
                            <path d="M7 16h6" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">
                            Room
                        </p>

                        <h2 class="mt-1 break-words text-xl font-extrabold tracking-tight text-slate-950 sm:text-2xl">
                            {{ room.name }}
                        </h2>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span
                                class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1.5 font-mono text-xs font-bold tracking-wider text-slate-600">
                                {{ room.code }}
                            </span>

                            <span
                                class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-bold"
                                :class="statusClass(
                                    room.status,
                                )
                                    ">
                                <span class="h-1.5 w-1.5 rounded-full" :class="statusDotClass(
                                    room.status,
                                )
                                    " />

                                {{ room.status_label }}
                            </span>

                            <span
                                class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-500">
                                {{ questions.length }}
                                soal
                            </span>
                        </div>
                    </div>
                </div>

                <Link :href="playUrl"
                    class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700 focus-visible:ring-2 focus-visible:ring-indigo-500/40 focus-visible:ring-offset-2 md:w-auto">
                    <svg v-if="
                        room.status !==
                        'finished'
                    " class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polygon points="5 3 19 12 5 21 5 3" />
                    </svg>

                    <svg v-else class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 3v18h18" />
                        <path d="m7 16 4-4 4 3 5-7" />
                    </svg>

                    {{ playLabel(room.status) }}
                </Link>
            </div>
        </div>

        <!-- =====================================================
        LOCK WARNING
        ====================================================== -->

        <div v-if="!room.can_edit" class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:mb-6 sm:p-5">
            <div class="flex items-start gap-3 sm:gap-4">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-600 shadow-sm">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="18" height="11" x="3" y="11" rx="2" />

                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                </div>

                <div>
                    <h3 class="font-bold text-amber-900">
                        Konfigurasi Room Dikunci
                    </h3>

                    <p class="mt-1 max-w-3xl text-sm leading-6 text-amber-700">
                        Room sudah dibuka atau pertandingan telah selesai.
                        Informasi room dan soal tetap dapat dilihat, tetapi
                        tidak dapat diubah.
                    </p>
                </div>
            </div>
        </div>

        <!-- =====================================================
        TABS
        ====================================================== -->

        <div class="mb-5 overflow-x-auto sm:mb-6">
            <div class="flex min-w-max gap-1 rounded-xl border border-slate-200 bg-white p-1 shadow-sm">
                <button type="button"
                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition"
                    :class="activeTab ===
                        'information'
                        ? 'bg-indigo-600 text-white shadow-sm'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        " @click="
                            activeTab =
                            'information'
                            ">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10" />

                        <path d="M12 16v-4" />
                        <path d="M12 8h.01" />
                    </svg>

                    Informasi Room
                </button>

                <button type="button"
                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition"
                    :class="activeTab ===
                        'questions'
                        ? 'bg-indigo-600 text-white shadow-sm'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        " @click="
                            activeTab =
                            'questions'
                            ">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M9.1 9a3 3 0 1 1 5.83 1c0 2-3 2-3 4" />

                        <path d="M12 17h.01" />
                        <circle cx="12" cy="12" r="10" />
                    </svg>

                    Soal

                    <span class="rounded-full px-2 py-0.5 text-xs" :class="activeTab ===
                        'questions'
                        ? 'bg-white/15 text-white'
                        : 'bg-slate-100 text-slate-500'
                        ">
                        {{ questions.length }}
                    </span>
                </button>
            </div>
        </div>

        <!-- =====================================================
        INFORMATION
        ====================================================== -->

        <div v-if="
            activeTab ===
            'information'
        " class="w-full">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <!-- Header -->
                <div class="border-b border-slate-100 bg-slate-50/60 px-5 py-5 sm:px-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-bold text-slate-900">
                                Informasi Room
                            </h3>

                            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                                Atur informasi dasar dan perilaku room
                                pertandingan.
                            </p>
                        </div>
                    </div>
                </div>

                <form class="space-y-6 p-5 sm:p-6" @submit.prevent="
                    updateRoom
                ">
                    <!-- Name -->
                    <div>
                        <label for="room-name" class="mb-2 block text-sm font-semibold text-slate-700">
                            Nama Room
                        </label>

                        <input id="room-name" v-model="roomForm.name" type="text" :disabled="!room.can_edit
                            " :aria-invalid="roomForm.errors.name
                                ? 'true'
                                : 'false'
                                "
                            class="min-h-12 w-full rounded-xl border bg-white px-4 text-sm text-slate-900 outline-none transition focus:ring-4 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-500"
                            :class="roomForm.errors.name
                                ? 'border-red-300 focus:border-red-500 focus:ring-red-100'
                                : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100'
                                " />

                        <p v-if="
                            roomForm.errors
                                .name
                        " class="mt-2 text-sm font-medium text-red-600">
                            {{
                                roomForm.errors
                                    .name
                            }}
                        </p>
                    </div>

                    <!-- Code -->
                    <div>
                        <label for="room-code" class="mb-2 block text-sm font-semibold text-slate-700">
                            Kode Room
                        </label>

                        <input id="room-code" v-model="roomForm.code" type="text" maxlength="20" autocomplete="off"
                            autocapitalize="characters" spellcheck="false" :disabled="!room.can_edit
                                " :aria-invalid="roomForm.errors.code
                                    ? 'true'
                                    : 'false'
                                    "
                            class="min-h-12 w-full rounded-xl border bg-white px-4 font-mono text-sm font-bold uppercase tracking-wider text-slate-900 outline-none transition focus:ring-4 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-500"
                            :class="roomForm.errors.code
                                ? 'border-red-300 focus:border-red-500 focus:ring-red-100'
                                : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100'
                                " />

                        <p v-if="
                            !roomForm.errors.code
                        " class="mt-2 text-xs leading-5 text-slate-500">
                            Kode digunakan peserta untuk menemukan dan masuk
                            ke room.
                        </p>

                        <p v-if="
                            roomForm.errors
                                .code
                        " class="mt-2 text-sm font-medium text-red-600">
                            {{
                                roomForm.errors
                                    .code
                            }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="room-description" class="mb-2 block text-sm font-semibold text-slate-700">
                            Deskripsi

                            <span class="ml-1 font-normal text-slate-400">
                                Opsional
                            </span>
                        </label>

                        <textarea id="room-description" v-model="roomForm.description
                            " rows="4" :disabled="!room.can_edit
                                " :aria-invalid="roomForm.errors.description
                                    ? 'true'
                                    : 'false'
                                    "
                            class="w-full resize-none rounded-xl border bg-white px-4 py-3 text-sm leading-6 text-slate-900 outline-none transition focus:ring-4 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-500"
                            :class="roomForm.errors.description
                                ? 'border-red-300 focus:border-red-500 focus:ring-red-100'
                                : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100'
                                " />

                        <p v-if="
                            roomForm.errors
                                .description
                        " class="mt-2 text-sm font-medium text-red-600">
                            {{
                                roomForm.errors
                                    .description
                            }}
                        </p>
                    </div>

                    <!-- Shuffle -->
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex min-w-0 items-start gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        aria-hidden="true">
                                        <path d="M16 3h5v5" />
                                        <path d="M4 20 21 3" />
                                        <path d="M21 16v5h-5" />
                                        <path d="m15 15 6 6" />
                                        <path d="M4 4 9 9" />
                                    </svg>
                                </div>

                                <div>
                                    <div class="font-semibold text-slate-900">
                                        Acak Soal
                                    </div>

                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Urutan soal diacak satu kali ketika
                                        permainan dimulai.
                                    </p>
                                </div>
                            </div>

                            <button type="button" role="switch" :aria-checked="roomForm.shuffle_questions
                                " :disabled="!room.can_edit
                                    "
                                class="relative mt-1 h-7 w-12 shrink-0 rounded-full transition duration-200 focus-visible:ring-2 focus-visible:ring-indigo-500/40 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :class="roomForm.shuffle_questions
                                    ? 'bg-indigo-600'
                                    : 'bg-slate-300'
                                    " @click="
                                        roomForm.shuffle_questions =
                                        !roomForm.shuffle_questions
                                        ">
                                <span
                                    class="absolute top-1 h-5 w-5 rounded-full bg-white shadow-sm transition-all duration-200"
                                    :class="roomForm.shuffle_questions
                                        ? 'left-6'
                                        : 'left-1'
                                        " />
                            </button>
                        </div>

                        <div class="mt-4 flex items-center gap-2 border-t border-slate-200/80 pt-4 text-xs font-semibold"
                            :class="roomForm.shuffle_questions
                                ? 'text-emerald-600'
                                : 'text-slate-400'
                                ">
                            <span class="h-2 w-2 rounded-full" :class="roomForm.shuffle_questions
                                ? 'bg-emerald-500'
                                : 'bg-slate-300'
                                " />

                            {{
                                roomForm.shuffle_questions
                                    ? 'Pengacakan soal aktif'
                                    : 'Soal mengikuti urutan yang dibuat'
                            }}
                        </div>
                    </div>

                    <!-- Submit -->
                    <div v-if="
                        room.can_edit
                    " class="flex justify-end border-t border-slate-100 pt-6">
                        <button type="submit" :disabled="roomForm.processing
                            "
                            class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700 focus-visible:ring-2 focus-visible:ring-indigo-500/40 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:bg-slate-300 disabled:shadow-none sm:w-auto">
                            <svg v-if="
                                roomForm.processing
                            " class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />

                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z" />
                            </svg>

                            {{
                                roomForm.processing
                                    ? 'Menyimpan...'
                                    : 'Simpan Perubahan'
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- =====================================================
        QUESTIONS
        ====================================================== -->

        <div v-else class="w-full">
            <!-- Question Lock -->
            <div v-if="
                !room.can_edit_questions
            " class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:mb-6 sm:p-5">
                <div class="flex items-start gap-3">
                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-amber-600 shadow-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" />

                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </div>

                    <div>
                        <div class="font-bold text-amber-900">
                            Soal Terkunci
                        </div>

                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            Soal hanya dapat diubah ketika room masih
                            berstatus Terkunci. Daftar soal tetap dapat
                            dilihat.
                        </p>
                    </div>
                </div>
            </div>



            <!-- =================================================
            QUESTION LIST
            ================================================== -->

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <!-- Header -->
                <div
                    class="flex flex-col gap-4 border-b border-slate-100 bg-slate-50/60 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <h3 class="font-bold text-slate-900">
                            Daftar Soal
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            {{
                                questions.length === 0
                                    ? 'Belum ada soal.'
                                    : `${questions.length} soal tersedia.`
                            }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="rounded-xl bg-indigo-50 px-3 py-2 text-sm font-bold text-indigo-600">
                            {{ questions.length }}
                            Soal
                        </div>

                        <button v-if="
                            room.can_edit_questions
                        " type="button"
                            class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700"
                            @click="
                                openCreateQuestionModal
                            ">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 5v14" />
                                <path d="M5 12h14" />
                            </svg>

                            Tambah Soal
                        </button>
                    </div>
                </div>

                <!-- Empty -->
                <div v-if="
                    questions.length === 0
                " class="px-5 py-14 text-center sm:px-6 sm:py-16">
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10" />

                            <path d="M9.1 9a3 3 0 1 1 5.83 1c0 2-3 2-3 4" />

                            <path d="M12 17h.01" />
                        </svg>
                    </div>

                    <h4 class="mt-4 font-bold text-slate-900">
                        Belum ada soal
                    </h4>

                    <p class="mx-auto mt-1 max-w-sm text-sm leading-6 text-slate-500">
                        {{
                            room.can_edit_questions
                                ? 'Tambahkan soal pertama menggunakan form di atas.'
                                : 'Room ini tidak memiliki soal.'
                        }}
                    </p>
                </div>

                <!-- Questions -->
                <div v-else class="divide-y divide-slate-100">
                    <article v-for="
question in
    questions
                        " :key="question.id
                            " class="p-4 sm:p-6">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <!-- Number -->
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-sm font-black text-white shadow-sm shadow-indigo-200 sm:h-11 sm:w-11">
                                {{
                                    question.position
                                }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p
                                    class="break-words text-sm font-semibold leading-6 text-slate-900 sm:text-base sm:leading-7">
                                    {{
                                        question.text
                                    }}
                                </p>

                                <!-- Options -->
                                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                                    <div v-for="
option in
    question.options
                                        " :key="option.id
                                            "
                                        class="flex min-w-0 items-start gap-2.5 rounded-xl border px-3 py-3 text-sm"
                                        :class="option.is_correct
                                            ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                                            : 'border-slate-200 bg-slate-50 text-slate-600'
                                            ">
                                        <span
                                            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg font-bold"
                                            :class="option.is_correct
                                                ? 'bg-emerald-600 text-white'
                                                : 'bg-white text-slate-500'
                                                ">
                                            {{
                                                option.label
                                            }}
                                        </span>

                                        <span class="min-w-0 flex-1 break-words leading-6">
                                            {{
                                                option.text
                                            }}
                                        </span>

                                        <svg v-if="
                                            option.is_correct
                                        " class="mt-1 h-4 w-4 shrink-0 text-emerald-600" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                            stroke-linejoin="round" aria-label="Jawaban benar">
                                            <path d="m5 12 4 4L19 6" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div v-if="
                                    room.can_edit_questions
                                " class="mt-4 flex flex-wrap justify-end gap-2">
                                    <button type="button"
                                        class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
                                        @click="
                                            editQuestion(
                                                question,
                                            )
                                            ">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            aria-hidden="true">
                                            <path d="M12 20h9" />

                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                        </svg>

                                        Edit
                                    </button>

                                    <button type="button"
                                        class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-3.5 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                                        @click="
                                            deleteQuestion(
                                                question,
                                            )
                                            ">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            aria-hidden="true">
                                            <path d="M3 6h18" />

                                            <path d="M8 6V4h8v2" />

                                            <path d="M19 6l-1 14H6L5 6" />
                                        </svg>

                                        Hapus
                                    </button>
                                </div>

                                <div v-else class="mt-4 flex justify-end">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-500">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="M2.06 12.35a1 1 0 0 1 0-.7C3.7 7.6 7.6 5 12 5c4.4 0 8.3 2.6 9.94 6.65a1 1 0 0 1 0 .7C20.3 16.4 16.4 19 12 19c-4.4 0-8.3-2.6-9.94-6.65" />

                                            <circle cx="12" cy="12" r="3" />
                                        </svg>

                                        Mode lihat
                                    </span>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
        <!-- =====================================================
QUESTION MODAL
====================================================== -->

        <Teleport to="body">
            <Transition enter-active-class="transition-opacity duration-200" enter-from-class="opacity-0"
                enter-to-class="opacity-100" leave-active-class="transition-opacity duration-150"
                leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="
                    questionModalOpen
                " class="fixed inset-0 z-[100] bg-slate-950/50 backdrop-blur-[2px]" @click="
                closeQuestionModal
            " />
            </Transition>

            <Transition enter-active-class="transition duration-200 ease-out"
                enter-from-class="translate-y-4 opacity-0 sm:scale-95 sm:translate-y-0"
                enter-to-class="translate-y-0 opacity-100 sm:scale-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="translate-y-0 opacity-100 sm:scale-100"
                leave-to-class="translate-y-4 opacity-0 sm:scale-95 sm:translate-y-0">
                <div v-if="
                    questionModalOpen
                " class="fixed inset-0 z-[110] flex items-end justify-center sm:items-center sm:p-5">
                    <div class="flex max-h-[94svh] w-full flex-col overflow-hidden rounded-t-[1.75rem] bg-white shadow-2xl sm:max-w-3xl sm:rounded-[1.75rem]"
                        @click.stop>
                        <!-- Modal Header -->
                        <div
                            class="flex shrink-0 items-start justify-between gap-4 border-b border-slate-100 px-5 py-5 sm:px-6">
                            <div class="flex min-w-0 items-start gap-3">
                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        aria-hidden="true">
                                        <path d="M12 20h9" />

                                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <h3 class="text-lg font-extrabold text-slate-950">
                                        {{
                                            editingQuestionId
                                                ? 'Edit Soal'
                                                : 'Tambah Soal'
                                        }}
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Buat pertanyaan dengan minimal 2 dan maksimal
                                        4 pilihan jawaban.
                                    </p>
                                </div>
                            </div>

                            <button type="button"
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                                aria-label="Tutup modal" @click="
                                    closeQuestionModal
                                ">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M18 6 6 18" />
                                    <path d="m6 6 12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <form id="question-form" class="flex-1 space-y-6 overflow-y-auto px-5 py-5 sm:px-6 sm:py-6"
                            @submit.prevent="
                                submitQuestion
                            ">
                            <!-- Question -->
                            <div>
                                <label for="question-text" class="mb-2 block text-sm font-semibold text-slate-700">
                                    Pertanyaan
                                </label>

                                <textarea id="question-text" v-model="questionForm.text
                                    " rows="4" placeholder="Masukkan pertanyaan..." autofocus
                                    class="w-full resize-none rounded-xl border border-slate-300 px-4 py-3 text-sm leading-6 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" />

                                <p v-if="
                                    questionForm.errors
                                        .text
                                " class="mt-2 text-sm font-medium text-red-600">
                                    {{
                                        questionForm.errors
                                            .text
                                    }}
                                </p>
                            </div>

                            <!-- Options -->
                            <div>
                                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-700">
                                            Pilihan Jawaban
                                        </div>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            Tekan huruf A–D untuk menentukan jawaban
                                            benar.
                                        </p>
                                    </div>

                                    <button v-if="
                                        questionForm
                                            .options
                                            .length < 4
                                    " type="button"
                                        class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3.5 py-2 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-100 sm:w-auto"
                                        @click="
                                            addOption
                                        ">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            aria-hidden="true">
                                            <path d="M12 5v14" />
                                            <path d="M5 12h14" />
                                        </svg>

                                        Tambah Pilihan
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <div v-for="(
option,
                                        index
                                ) in questionForm.options" :key="index">
                                        <div
                                            class="grid grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-2 sm:gap-3">
                                            <!-- Correct Label -->
                                            <button type="button"
                                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border text-sm font-black transition"
                                                :class="option.is_correct
                                                        ? 'border-emerald-600 bg-emerald-600 text-white shadow-sm shadow-emerald-200'
                                                        : 'border-slate-300 bg-white text-slate-600 hover:border-emerald-400 hover:text-emerald-600'
                                                    " :aria-label="`Jadikan pilihan ${getOptionLabel(index)} sebagai jawaban benar`
                                            " @click="
                                            setCorrectOption(
                                                index,
                                            )
                                            ">
                                                {{
                                                    getOptionLabel(
                                                        index,
                                                    )
                                                }}
                                            </button>

                                            <!-- Option Text -->
                                            <input v-model="option.text
                                                " type="text" :placeholder="`Pilihan ${getOptionLabel(index)}`
                                            " class="h-12 min-w-0 w-full rounded-xl border border-slate-300 px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" />

                                            <!-- Remove -->
                                            <button v-if="
                                                questionForm
                                                    .options
                                                    .length > 2
                                            " type="button"
                                                class="flex h-12 w-12 items-center justify-center rounded-xl border border-red-200 bg-white text-red-500 transition hover:bg-red-50 hover:text-red-600"
                                                aria-label="Hapus pilihan" @click="
                                                    removeOption(
                                                        index,
                                                    )
                                                    ">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M3 6h18" />
                                                    <path d="M8 6V4h8v2" />
                                                    <path d="M19 6l-1 14H6L5 6" />
                                                </svg>
                                            </button>
                                        </div>

                                        <p v-if="
                                            optionError(
                                                index,
                                            )
                                        " class="ml-14 mt-2 text-sm font-medium text-red-600">
                                            {{
                                                optionError(
                                                    index,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <p v-if="
                                    questionForm.errors
                                        .options
                                " class="mt-3 text-sm font-medium text-red-600">
                                    {{
                                        questionForm.errors
                                            .options
                                    }}
                                </p>

                                <!-- Correct Hint -->
                                <div
                                    class="mt-4 flex items-start gap-2 rounded-xl bg-emerald-50 px-3.5 py-3 text-xs leading-5 text-emerald-700">
                                    <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                        stroke-linejoin="round" aria-hidden="true">
                                        <path d="m20 6-11 11-5-5" />
                                    </svg>

                                    Pilihan berwarna hijau merupakan jawaban benar.
                                </div>
                            </div>
                        </form>

                        <!-- Modal Footer -->
                        <div
                            class="flex shrink-0 flex-col-reverse gap-2 border-t border-slate-100 bg-slate-50/70 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                            <button type="button"
                                class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 sm:w-auto"
                                @click="
                                    closeQuestionModal
                                ">
                                Batal
                            </button>

                            <button type="submit" form="question-form" :disabled="questionForm.processing
                                " class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700 disabled:pointer-events-none disabled:bg-slate-300 disabled:shadow-none sm:w-auto">
                                <svg v-if="
                                    questionForm.processing
                                " class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />

                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z" />
                                </svg>

                                {{
                                    questionForm.processing
                                        ? 'Menyimpan...'
                                        : editingQuestionId
                                            ? 'Simpan Perubahan'
                                            : 'Tambah Soal'
                                }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AdminLayout>
</template>