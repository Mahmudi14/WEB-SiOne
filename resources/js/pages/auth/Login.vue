<script setup lang="ts">
    import {
        Head,
        Link,
        useForm,
    } from '@inertiajs/vue3'

    import {
        computed,
        ref,
    } from 'vue'

    const form = useForm({
        email: '',
        password: '',
        remember: false,
    })

    const showPassword =
        ref(false)

    const canSubmit = computed(
        () =>
            form.email.trim() !== '' &&
            form.password !== '' &&
            !form.processing,
    )

    const submit = () => {
        if (!canSubmit.value) {
            return
        }

        form.post(
            '/login',
            {
                preserveScroll: true,

                onFinish: () => {
                    form.reset(
                        'password',
                    )
                },
            },
        )
    }
</script>

<template>

    <Head title="Login — SiOne" />

    <main class="min-h-screen bg-slate-50">
        <div class="grid min-h-screen lg:grid-cols-[1.05fr_0.95fr]">
            <!-- =================================================
            LEFT / BRAND
            ================================================== -->

            <section class="relative hidden overflow-hidden bg-bps-navy lg:flex lg:flex-col lg:justify-between">
                <!-- Decoration -->
                <div
                    class="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full bg-bps-blue/20 blur-3xl" />

                <div
                    class="pointer-events-none absolute -bottom-32 -left-32 h-96 w-96 rounded-full bg-bps-green/10 blur-3xl" />

                <div class="relative z-10 p-10 xl:p-14">
                    <!-- Brand -->
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/10 backdrop-blur">
                            <span class="text-xl font-black text-white">
                                S1
                            </span>
                        </div>

                        <div>
                            <div class="text-2xl font-black tracking-tight text-white">
                                SiOne
                            </div>

                            <div class="mt-0.5 text-sm font-medium text-blue-100/80">
                                Sistem Ranking 1
                            </div>
                        </div>
                    </div>

                    <!-- Hero -->
                    <div class="mt-24 max-w-xl">
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold text-blue-100">
                            <span class="h-2 w-2 rounded-full bg-bps-green" />

                            Panel Administrator
                        </div>

                        <h1 class="mt-6 text-4xl font-black leading-tight tracking-tight text-white xl:text-5xl">
                            Kelola pertandingan
                            Ranking 1 dengan
                            lebih mudah.
                        </h1>

                        <p class="mt-5 max-w-lg text-base leading-7 text-blue-100/75">
                            Siapkan room, kelola soal,
                            pantau peserta, dan kendalikan
                            pertandingan dari satu sistem.
                        </p>
                    </div>
                </div>

                <!-- Bottom -->
                <div class="relative z-10 p-10 text-sm text-blue-100/50 xl:p-14">
                    SiOne · Sistem Ranking 1
                </div>
            </section>

            <!-- =================================================
            RIGHT / LOGIN
            ================================================== -->

            <section class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-8 lg:px-12">
                <div class="w-full max-w-md">
                    <!-- Mobile Brand -->
                    <div class="mb-10 flex items-center gap-3 lg:hidden">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-bps-navy">
                            <span class="font-black text-white">
                                S1
                            </span>
                        </div>

                        <div>
                            <div class="text-xl font-black text-bps-navy">
                                SiOne
                            </div>

                            <div class="text-xs font-medium text-slate-500">
                                Sistem Ranking 1
                            </div>
                        </div>
                    </div>

                    <!-- Heading -->
                    <div>
                        <div class="text-xs font-bold uppercase tracking-[0.18em] text-bps-blue">
                            Administrator
                        </div>

                        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">
                            Selamat Datang
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Masuk menggunakan akun
                            administrator untuk mengelola
                            SiOne.
                        </p>
                    </div>

                    <!-- Form -->
                    <form class="mt-8 space-y-5" @submit.prevent="submit">
                        <!-- Email -->
                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">
                                Email
                            </label>

                            <div class="relative">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="16" x="2" y="4" rx="2" />

                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                                </svg>

                                <input id="email" v-model="form.email" type="email" autocomplete="email" autofocus
                                    placeholder="admin@example.com"
                                    class="min-h-12 w-full rounded-xl border bg-white pl-12 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-4"
                                    :class="form.errors.email
                                            ? 'border-red-300 focus:border-red-500 focus:ring-red-100'
                                            : 'border-slate-300 focus:border-bps-blue focus:ring-blue-100'
                                        " />
                            </div>

                            <p v-if="form.errors.email" class="mt-2 text-sm font-medium text-red-600">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">
                                Password
                            </label>

                            <div class="relative">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="11" x="3" y="11" rx="2" />

                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>

                                <input id="password" v-model="form.password" :type="showPassword
                                        ? 'text'
                                        : 'password'
                                    " autocomplete="current-password" placeholder="Masukkan password"
                                    class="min-h-12 w-full rounded-xl border bg-white pl-12 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-4"
                                    :class="form.errors.password
                                            ? 'border-red-300 focus:border-red-500 focus:ring-red-100'
                                            : 'border-slate-300 focus:border-bps-blue focus:ring-blue-100'
                                        " />

                                <button type="button"
                                    class="absolute right-2 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                                    :aria-label="showPassword
                                            ? 'Sembunyikan password'
                                            : 'Tampilkan password'
                                        " @click="
                                        showPassword =
                                        !showPassword
                                        ">
                                    <!-- Eye -->
                                    <svg v-if="!showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M2.06 12.35a1 1 0 0 1 0-.7C3.7 7.6 7.6 5 12 5c4.4 0 8.3 2.6 9.94 6.65a1 1 0 0 1 0 .7C20.3 16.4 16.4 19 12 19c-4.4 0-8.3-2.6-9.94-6.65" />

                                        <circle cx="12" cy="12" r="3" />
                                    </svg>

                                    <!-- Eye Off -->
                                    <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m2 2 20 20" />

                                        <path
                                            d="M6.71 6.71C4.9 7.83 3.4 9.53 2.46 11.65a1 1 0 0 0 0 .7C4.1 16.4 7.7 19 12 19c1.3 0 2.53-.24 3.64-.67" />

                                        <path
                                            d="M10.73 5.08A9 9 0 0 1 12 5c4.4 0 8.3 2.6 9.94 6.65a1 1 0 0 1 0 .7 10.4 10.4 0 0 1-1.18 2.05" />

                                        <path d="M14.12 14.12A3 3 0 0 1 9.88 9.88" />
                                    </svg>
                                </button>
                            </div>

                            <p v-if="form.errors.password" class="mt-2 text-sm font-medium text-red-600">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Remember -->
                        <label class="flex cursor-pointer items-center gap-3">
                            <input v-model="form.remember" type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 text-bps-blue focus:ring-bps-blue" />

                            <span class="text-sm font-medium text-slate-600">
                                Ingat saya
                            </span>
                        </label>

                        <!-- Submit -->
                        <button type="submit" :disabled="!canSubmit"
                            class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-bps-navy px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-950/10 transition hover:bg-bps-blue focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-200 disabled:pointer-events-none disabled:bg-slate-300 disabled:shadow-none">
                            <svg v-if="form.processing" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />

                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z" />
                            </svg>

                            <svg v-else class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />

                                <polyline points="10 17 15 12 10 7" />

                                <line x1="15" x2="3" y1="12" y2="12" />
                            </svg>

                            {{
                                form.processing
                                    ? 'Memproses...'
                                    : 'Masuk ke SiOne'
                            }}
                        </button>
                    </form>

                    <!-- Home -->
                    <div class="mt-8 border-t border-slate-200 pt-6 text-center">
                        <Link href="/" class="text-sm font-semibold text-slate-500 transition hover:text-bps-blue">
                            ← Kembali ke halaman utama
                        </Link>
                    </div>

                    <p class="mt-8 text-center text-xs text-slate-400 lg:hidden">
                        SiOne · Sistem Ranking 1
                    </p>
                </div>
            </section>
        </div>
    </main>
</template>