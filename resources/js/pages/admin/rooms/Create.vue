<script setup lang="ts">
    import AdminLayout from '@/layouts/admin/AdminLayout.vue'
    import type { RoomFormData } from '@/types/room'

    import {
        Head,
        Link,
        useForm,
    } from '@inertiajs/vue3'

    const form = useForm<RoomFormData>({
        name: '',
        code: '',
        description: '',
        shuffle_questions: false,
    })

    const submit = () => {
        form.post('/admin/rooms')
    }
</script>

<template>

    <Head title="Buat Room — SiOne" />

    <AdminLayout title="Buat Room">
        <template #actions>
            <Link href="/admin/rooms"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus-visible:ring-2 focus-visible:ring-indigo-500/30">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m15 18-6-6 6-6" />
                </svg>

                Kembali
            </Link>
        </template>

        <div class="w-full">
            <!-- Intro -->
            <div class="mb-5 sm:mb-6">
                <h2 class="text-base font-bold text-slate-900 sm:text-lg">
                    Informasi Room
                </h2>

                <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                    Atur informasi dasar room sebelum menambahkan soal dan
                    menjalankan pertandingan.
                </p>
            </div>

            <!-- Card -->
            <div class="overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-sm">
                <!-- Card Header -->
                <div class="border-b border-slate-100 bg-slate-50/60 px-5 py-5 sm:px-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect width="18" height="18" x="3" y="3" rx="2" />

                                <path d="M7 8h10" />
                                <path d="M7 12h10" />
                                <path d="M7 16h6" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-bold text-slate-900">
                                Detail Room
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Informasi ini akan digunakan untuk
                                mengidentifikasi room pada sisi admin dan
                                peserta.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form class="space-y-6 p-5 sm:p-6" @submit.prevent="submit">
                    <!-- Name -->
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">
                            Nama Room
                        </label>

                        <input id="name" v-model="form.name" type="text" autocomplete="off"
                            placeholder="Contoh: Ranking Satu Final" :aria-invalid="form.errors.name
                                ? 'true'
                                : 'false'
                                "
                            class="min-h-12 w-full rounded-xl border bg-white px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-4"
                            :class="form.errors.name
                                ? 'border-red-300 focus:border-red-500 focus:ring-red-100'
                                : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100'
                                " />

                        <p v-if="form.errors.name" class="mt-2 text-sm font-medium text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Code -->
                    <div>
                        <label for="code" class="mb-2 block text-sm font-semibold text-slate-700">
                            Kode Room
                        </label>

                        <input id="code" v-model="form.code" type="text" autocomplete="off" autocapitalize="characters"
                            spellcheck="false" placeholder="Contoh: SIONE01" :aria-invalid="form.errors.code
                                ? 'true'
                                : 'false'
                                "
                            class="min-h-12 w-full rounded-xl border bg-white px-4 font-mono text-sm font-bold uppercase tracking-wider text-slate-900 outline-none transition placeholder:font-sans placeholder:font-normal placeholder:normal-case placeholder:tracking-normal placeholder:text-slate-400 focus:ring-4"
                            :class="form.errors.code
                                ? 'border-red-300 focus:border-red-500 focus:ring-red-100'
                                : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100'
                                " />

                        <p v-if="!form.errors.code" class="mt-2 text-xs leading-5 text-slate-500">
                            Kode ini digunakan peserta untuk menemukan dan
                            masuk ke room.
                        </p>

                        <p v-if="form.errors.code" class="mt-2 text-sm font-medium text-red-600">
                            {{ form.errors.code }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">
                            Deskripsi
                            <span class="ml-1 font-normal text-slate-400">
                                Opsional
                            </span>
                        </label>

                        <textarea id="description" v-model="form.description" rows="4"
                            placeholder="Tambahkan keterangan singkat mengenai room..." :aria-invalid="form.errors.description
                                ? 'true'
                                : 'false'
                                "
                            class="w-full resize-none rounded-xl border bg-white px-4 py-3 text-sm leading-6 text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-4"
                            :class="form.errors.description
                                ? 'border-red-300 focus:border-red-500 focus:ring-red-100'
                                : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100'
                                " />

                        <p v-if="form.errors.description" class="mt-2 text-sm font-medium text-red-600">
                            {{ form.errors.description }}
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

                                <div class="min-w-0">
                                    <div class="font-semibold text-slate-900">
                                        Acak Soal
                                    </div>

                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Urutan soal akan diacak ketika
                                        permainan dimulai.
                                    </p>
                                </div>
                            </div>

                            <!-- Toggle -->
                            <button type="button" role="switch" :aria-checked="form.shuffle_questions
                                "
                                class="relative mt-1 h-7 w-12 shrink-0 rounded-full transition duration-200 focus-visible:ring-2 focus-visible:ring-indigo-500/40 focus-visible:ring-offset-2"
                                :class="form.shuffle_questions
                                    ? 'bg-indigo-600'
                                    : 'bg-slate-300'
                                    " @click="
                                        form.shuffle_questions =
                                        !form.shuffle_questions
                                        ">
                                <span
                                    class="absolute top-1 h-5 w-5 rounded-full bg-white shadow-sm transition-all duration-200"
                                    :class="form.shuffle_questions
                                        ? 'left-6'
                                        : 'left-1'
                                        " />
                            </button>
                        </div>

                        <div class="mt-4 flex items-center gap-2 border-t border-slate-200/80 pt-4 text-xs font-semibold"
                            :class="form.shuffle_questions
                                ? 'text-emerald-600'
                                : 'text-slate-400'
                                ">
                            <span class="h-2 w-2 rounded-full" :class="form.shuffle_questions
                                ? 'bg-emerald-500'
                                : 'bg-slate-300'
                                " />

                            {{
                                form.shuffle_questions
                                    ? 'Pengacakan soal aktif'
                                    : 'Soal mengikuti urutan yang dibuat'
                            }}
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:items-center sm:justify-end">
                        <Link href="/admin/rooms"
                            class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 sm:w-auto">
                            Batal
                        </Link>

                        <button type="submit" :disabled="form.processing
                            "
                            class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700 focus-visible:ring-2 focus-visible:ring-indigo-500/40 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:bg-slate-300 disabled:shadow-none sm:w-auto">
                            <svg v-if="
                                form.processing
                            " class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />

                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z" />
                            </svg>

                            <svg v-else class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z" />
                                <polyline points="17 21 17 13 7 13 7 21" />
                                <polyline points="7 3 7 8 15 8" />
                            </svg>

                            {{
                                form.processing
                                    ? 'Menyimpan...'
                                    : 'Simpan Room'
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>