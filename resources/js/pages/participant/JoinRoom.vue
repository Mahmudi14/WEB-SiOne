<script setup lang="ts">
    import ParticipantLayout from '@/layouts/participant/ParticipantLayout.vue'
    import { Head, useForm } from '@inertiajs/vue3'

    const props = defineProps<{
        room: {
            name: string
            code: string
            description: string | null
        }
    }>()

    const form = useForm({
        name: '',
        participant_code: '',
    })

    const submit = () => {
        form.post(
            `/participant/room/${props.room.code}/join`,
        )
    }
</script>

<template>

    <Head :title="`Gabung ${room.name} — SiOne`" />

    <ParticipantLayout>
        <section class="mx-auto w-full max-w-md">
            <div
                class="relative overflow-hidden rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-2xl shadow-slate-300/40 backdrop-blur-xl sm:p-7">
                <!-- Accent -->
                <div class="absolute left-1/2 top-0 h-1 w-20 -translate-x-1/2 rounded-b-full bg-indigo-600" />

                <!-- Room Information -->
                <div class="text-center">
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 7h18" />
                            <path d="M6 3h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" />
                            <path d="M8 11h8" />
                            <path d="M8 15h5" />
                        </svg>
                    </div>

                    <p class="mt-5 text-xs font-bold uppercase tracking-[0.16em] text-indigo-600">
                        Room Ditemukan
                    </p>

                    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-[1.75rem]">
                        {{ room.name }}
                    </h1>

                    <div
                        class="mt-3 inline-flex rounded-xl bg-slate-100 px-3.5 py-2 font-mono text-sm font-bold tracking-wider text-slate-700">
                        {{ room.code }}
                    </div>

                    <p v-if="room.description" class="mx-auto mt-3 max-w-sm text-sm leading-6 text-slate-500">
                        {{ room.description }}
                    </p>
                </div>

                <!-- Form -->
                <form class="mt-7 space-y-5" @submit.prevent="submit">
                    <!-- Name -->
                    <div>
                        <label for="participant-name" class="mb-2 block text-sm font-semibold text-slate-700">
                            Nama Peserta
                        </label>

                        <input id="participant-name" v-model="form.name" type="text" autocomplete="name"
                            placeholder="Masukkan nama peserta" :aria-invalid="form.errors.name
                                    ? 'true'
                                    : 'false'
                                "
                            class="min-h-14 w-full rounded-2xl border bg-white px-4 text-base text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-4"
                            :class="form.errors.name
                                    ? 'border-red-300 focus:border-red-500 focus:ring-red-100'
                                    : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100'
                                " />

                        <p v-if="form.errors.name" class="mt-2 text-sm font-medium text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Participant Code -->
                    <div>
                        <label for="participant-code" class="mb-2 block text-sm font-semibold text-slate-700">
                            Kode Peserta
                        </label>

                        <input id="participant-code" v-model="form.participant_code" type="text" autocomplete="off"
                            autocapitalize="characters" spellcheck="false" placeholder="Contoh: 001" :aria-invalid="form.errors.participant_code
                                    ? 'true'
                                    : 'false'
                                "
                            class="min-h-14 w-full rounded-2xl border bg-white px-4 font-mono text-base font-bold uppercase tracking-wider text-slate-900 outline-none transition placeholder:font-sans placeholder:font-normal placeholder:normal-case placeholder:tracking-normal placeholder:text-slate-400 focus:ring-4"
                            :class="form.errors.participant_code
                                    ? 'border-red-300 focus:border-red-500 focus:ring-red-100'
                                    : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-100'
                                " />

                        <p v-if="!form.errors.participant_code" class="mt-2 text-xs leading-5 text-slate-500">
                            Kode peserta harus unik di dalam room ini.
                        </p>

                        <p v-if="form.errors.participant_code" class="mt-2 text-sm font-medium text-red-600">
                            {{ form.errors.participant_code }}
                        </p>
                    </div>

                    <!-- Submit -->
                    <button type="submit" :disabled="form.processing ||
                        !form.name.trim() ||
                        !form.participant_code.trim()
                        "
                        class="group flex min-h-14 w-full items-center justify-center gap-2.5 rounded-2xl bg-indigo-600 px-5 py-4 text-base font-bold text-white shadow-lg shadow-indigo-200/70 transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-xl focus-visible:ring-2 focus-visible:ring-indigo-500/40 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:translate-y-0 disabled:bg-slate-300 disabled:shadow-none">
                        <svg v-if="form.processing" class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none"
                            aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />

                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z" />
                        </svg>

                        <template v-if="form.processing">
                            Bergabung...
                        </template>

                        <template v-else>
                            Bergabung ke Room

                            <svg class="h-5 w-5 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
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