<script setup lang="ts">
    import ParticipantLayout from '@/layouts/participant/ParticipantLayout.vue'
    import { Head, useForm } from '@inertiajs/vue3'

    const form = useForm({
        code: '',
    })

    const submit = () => {
        form.post('/participant/room/find')
    }
</script>

<template>

    <Head title="Masuk Room — SiOne" />

    <ParticipantLayout>
        <section class="mx-auto w-full max-w-md">
            <div
                class="relative overflow-hidden rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-2xl shadow-slate-300/40 backdrop-blur-xl sm:p-7">
                <!-- Top Accent -->
                <div class="absolute left-1/2 top-0 h-1 w-20 -translate-x-1/2 rounded-b-full bg-indigo-600" />

                <!-- Icon -->
                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 sm:h-16 sm:w-16">
                    <svg class="h-7 w-7 sm:h-8 sm:w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />

                        <polyline points="10 17 15 12 10 7" />

                        <line x1="15" x2="3" y1="12" y2="12" />
                    </svg>
                </div>

                <!-- Heading -->
                <div class="mt-5 text-center">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">
                        Masuk Permainan
                    </p>

                    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-[1.75rem]">
                        Masukkan Kode Room
                    </h1>

                    <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                        Gunakan kode room yang diberikan oleh panitia untuk
                        bergabung ke permainan.
                    </p>
                </div>

                <!-- Form -->
                <form class="mt-7" @submit.prevent="submit">
                    <div>
                        <label for="room-code" class="mb-2 block text-sm font-semibold text-slate-700">
                            Kode Room
                        </label>

                        <input id="room-code" v-model="form.code" type="text" autocomplete="off"
                            autocapitalize="characters" spellcheck="false" placeholder="Contoh: SIONE01" :aria-invalid="form.errors.code
                                ? 'true'
                                : 'false'
                                "
                            class="min-h-14 w-full rounded-2xl border bg-white px-4 text-center font-mono text-lg font-bold uppercase tracking-[0.18em] text-slate-900 outline-none transition placeholder:font-sans placeholder:text-sm placeholder:font-medium placeholder:normal-case placeholder:tracking-normal placeholder:text-slate-400 focus:ring-4 sm:min-h-16 sm:text-xl"
                            :class="form.errors.code
                                ? 'border-red-300 focus:border-red-500 focus:ring-red-100'
                                : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100'
                                " />

                        <p v-if="form.errors.code"
                            class="mt-2 flex items-center gap-1.5 text-sm font-medium text-red-600">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" />

                                <line x1="12" x2="12" y1="8" y2="12" />

                                <line x1="12" x2="12.01" y1="16" y2="16" />
                            </svg>

                            {{ form.errors.code }}
                        </p>
                    </div>

                    <button type="submit" :disabled="form.processing || !form.code.trim()"
                        class="group mt-5 flex min-h-14 w-full items-center justify-center gap-2.5 rounded-2xl bg-indigo-600 px-5 py-4 text-base font-bold text-white shadow-lg shadow-indigo-200/70 transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-xl hover:shadow-indigo-200 focus-visible:ring-2 focus-visible:ring-indigo-500/40 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:translate-y-0 disabled:bg-slate-300 disabled:shadow-none sm:min-h-16 sm:text-lg">
                        <!-- Loading -->
                        <svg v-if="form.processing" class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none"
                            aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />

                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z" />
                        </svg>

                        <template v-if="form.processing">
                            Memeriksa Room...
                        </template>

                        <template v-else>
                            Masuk ke Room

                            <svg class="h-5 w-5 transition-transform duration-200 group-hover:translate-x-1"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 12h14" />
                                <path d="m13 6 6 6-6 6" />
                            </svg>
                        </template>
                    </button>
                </form>
            </div>
        </section>
    </ParticipantLayout>
</template>