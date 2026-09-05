<script setup lang="ts">
    import SiOneLogo from '@/components/SiOneLogo.vue'

    import {
        Head,
        Link,
    } from '@inertiajs/vue3'

    import {
        login,
    } from '@/routes'

    import {
        dashboard,
    } from '@/routes/admin'

    import {
        index as participantIndex,
    } from '@/routes/participant'

    const currentYear =
        new Date().getFullYear()
</script>

<template>

    <Head title="SiOne — Sistem Ranking Satu" />

    <div
        class="relative flex h-dvh flex-col overflow-hidden bg-slate-50 text-slate-900 md:min-h-svh md:h-auto md:overflow-visible lg:h-svh lg:overflow-hidden">
        <!-- =====================================================
        BACKGROUND
        ====================================================== -->

        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div
                class="absolute -left-28 -top-28 h-64 w-64 rounded-full bg-sione-primary/10 blur-3xl sm:h-96 sm:w-96" />

            <div
                class="absolute -right-28 top-1/3 h-64 w-64 rounded-full bg-sione-violet/10 blur-3xl sm:h-96 sm:w-96" />

            <div class="absolute -bottom-32 left-1/3 h-64 w-64 rounded-full bg-cyan-500/10 blur-3xl sm:h-96 sm:w-96" />

            <div
                class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:32px_32px] opacity-30 [mask-image:linear-gradient(to_bottom,white,transparent_78%)]" />
        </div>

        <!-- =====================================================
        HEADER
        ====================================================== -->

        <header class="relative z-20 shrink-0">
            <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-4 sm:h-20 sm:px-6 lg:px-8">
                <!-- Brand -->
                <Link :href="participantIndex()" class="flex items-center gap-2.5 sm:gap-3">
                    <SiOneLogo size="h-9 w-9 sm:h-11 sm:w-11" />

                    <div>
                        <div class="text-sm font-extrabold tracking-tight text-slate-950 sm:text-base">
                            SiOne
                        </div>

                        <div class="text-[10px] font-medium text-slate-500 sm:text-[11px]">
                            Sistem Ranking Satu
                        </div>
                    </div>
                </Link>

                <!-- Admin -->
                <Link :href="$page.props.auth?.user
                    ? dashboard()
                    : login()
                    "
                    class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white/90 px-3 text-xs font-semibold text-slate-600 shadow-sm backdrop-blur transition duration-200 hover:border-indigo-200 hover:bg-white hover:text-indigo-600 hover:shadow-md focus-visible:ring-2 focus-visible:ring-indigo-500/30 sm:min-h-11 sm:px-4 sm:text-sm">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />

                        <polyline points="10 17 15 12 10 7" />

                        <line x1="15" x2="3" y1="12" y2="12" />
                    </svg>

                    <span class="hidden xs:inline sm:inline">
                        {{
                            $page.props.auth?.user
                                ? 'Dashboard Admin'
                                : 'Login Admin'
                        }}
                    </span>

                    <span class="xs:hidden sm:hidden">
                        Admin
                    </span>
                </Link>
            </div>
        </header>

        <!-- =====================================================
        MAIN
        ====================================================== -->

        <main class="relative z-10 min-h-0 flex-1">
            <div
                class="mx-auto grid h-full w-full max-w-7xl content-center gap-4 px-4 py-3 sm:gap-10 sm:px-6 sm:py-10 md:h-auto md:grid-cols-1 md:py-12 lg:h-full lg:grid-cols-[minmax(0,1.05fr)_minmax(360px,0.95fr)] lg:items-center lg:gap-16 lg:px-8 lg:py-8 xl:gap-24">
                <!-- =================================================
                HERO
                ================================================== -->

                <section class="mx-auto w-full max-w-2xl text-center lg:mx-0 lg:text-left">
                    <!-- Badge -->
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-white/90 px-3 py-1.5 text-[10px] font-bold text-indigo-600 shadow-sm backdrop-blur sm:px-3.5 sm:py-2 sm:text-xs">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-cyan-500 opacity-40" />

                            <span class="relative inline-flex h-2 w-2 rounded-full bg-cyan-500" />
                        </span>

                        Kompetisi interaktif real-time
                    </div>

                    <!-- Heading -->
                    <h1
                        class="mt-4 text-[2.15rem] font-black leading-[1.02] tracking-[-0.045em] text-slate-950 sm:mt-6 sm:text-5xl sm:leading-[1.05] md:text-[3.5rem] lg:text-[3.8rem] xl:text-[4.25rem]">
                        Satu soal.
                        <span class="sm:hidden">
                            Satu kesempatan.
                        </span>

                        <template class="hidden sm:block">
                            <br />

                            Satu kesempatan.
                        </template>

                        <span class="mt-1 block text-indigo-600">
                            Jadilah SiOne.
                        </span>
                    </h1>

                    <!-- Mobile Description -->
                    <p class="mx-auto mt-3 max-w-md text-[13px] leading-5 text-slate-500 sm:hidden">
                        Jawab dengan tepat dan bertahan sampai ronde terakhir.
                    </p>

                    <!-- Desktop Description -->
                    <p
                        class="mx-auto mt-5 hidden max-w-xl text-[15px] leading-7 text-slate-600 sm:block sm:mt-6 sm:text-lg sm:leading-8 lg:mx-0">
                        Jawab dengan tepat, bertahan di setiap ronde,
                        dan buktikan bahwa kamu layak menjadi peserta
                        terakhir yang berdiri.
                    </p>

                    <!-- Highlights
                         Disembunyikan di HP agar CTA peserta
                         langsung terlihat tanpa scroll.
                    -->
                    <div
                        class="mt-8 hidden flex-wrap items-center justify-center gap-x-5 gap-y-3 text-sm font-medium text-slate-500 sm:flex lg:justify-start">
                        <!-- No account -->
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>

                            Tanpa akun peserta
                        </div>

                        <!-- Real Time -->
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M13 2 3 14h9l-1 8 10-12h-9z" />
                                </svg>
                            </div>

                            Real-time
                        </div>

                        <!-- Device -->
                        <div class="flex items-center gap-2">
                            <div class="flex h-5 w-5 items-center justify-center rounded-full bg-cyan-50 text-cyan-600">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="14" height="20" x="5" y="2" rx="2" ry="2" />

                                    <path d="M12 18h.01" />
                                </svg>
                            </div>

                            Semua perangkat
                        </div>
                    </div>
                </section>

                <!-- =================================================
                PARTICIPANT ACCESS
                ================================================== -->

                <section class="mx-auto w-full max-w-xl lg:max-w-none">
                    <div
                        class="relative overflow-hidden rounded-[1.5rem] border border-white/80 bg-white/90 p-4 shadow-xl shadow-slate-300/30 backdrop-blur-xl sm:rounded-[1.75rem] sm:p-7 sm:shadow-2xl sm:shadow-slate-300/40 lg:p-8">
                        <!-- Accent -->
                        <div
                            class="absolute left-1/2 top-0 h-1 w-20 -translate-x-1/2 rounded-b-full bg-linear-to-r from-sione-primary via-sione-violet to-sione-cyan sm:w-24" />

                        <!-- Mobile Header -->
                        <div class="flex items-center gap-3 sm:block">
                            <!-- Icon -->
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 sm:h-14 sm:w-14 sm:rounded-2xl">
                                <svg class="h-5 w-5 sm:h-7 sm:w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />

                                    <circle cx="9" cy="7" r="4" />

                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />

                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>

                            <!-- Heading -->
                            <div class="min-w-0 sm:mt-5">
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-600 sm:text-xs">
                                    Untuk Peserta
                                </p>

                                <h2
                                    class="mt-0.5 text-lg font-extrabold tracking-tight text-slate-950 sm:mt-2 sm:text-3xl">
                                    Siap untuk bermain?
                                </h2>
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="mt-3 text-xs leading-5 text-slate-500 sm:mt-2 sm:max-w-md sm:text-base sm:leading-7">
                            Masukkan kode room yang diberikan panitia
                            untuk bergabung ke permainan.
                        </p>

                        <!-- CTA -->
                        <Link :href="participantIndex()"
                            class="group mt-4 flex min-h-12 w-full items-center justify-center gap-3 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200/70 transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-700 hover:shadow-xl hover:shadow-indigo-200 focus-visible:ring-2 focus-visible:ring-indigo-500/40 focus-visible:ring-offset-2 sm:mt-7 sm:min-h-16 sm:rounded-2xl sm:py-4 sm:text-lg">
                            <span>
                                Masuk sebagai Peserta
                            </span>

                            <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1 sm:h-5 sm:w-5"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 12h14" />

                                <path d="m13 6 6 6-6 6" />
                            </svg>
                        </Link>

                        <!-- Help
                             Tidak perlu tampil di HP karena sudah
                             dijelaskan dalam description.
                        -->
                        <div
                            class="mt-5 hidden items-start gap-3 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3.5 sm:flex">
                            <div
                                class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-indigo-600 shadow-sm">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />

                                    <path d="M12 16v-4" />

                                    <path d="M12 8h.01" />
                                </svg>
                            </div>

                            <p class="text-xs leading-5 text-slate-500 sm:text-[13px]">
                                Tidak perlu membuat akun. Kamu hanya
                                membutuhkan kode room untuk bergabung.
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- =====================================================
        FOOTER
        ====================================================== -->

        <footer class="relative z-10 hidden shrink-0 sm:block">
            <div
                class="mx-auto flex w-full max-w-7xl flex-col items-center justify-between gap-2 border-t border-slate-200/70 px-6 py-5 text-center text-xs text-slate-400 sm:flex-row sm:text-left lg:px-8">
                <span>
                    © {{ currentYear }} SiOne
                </span>

                <span>
                    Sistem Ranking Satu Interaktif
                </span>
            </div>
        </footer>
    </div>
</template>