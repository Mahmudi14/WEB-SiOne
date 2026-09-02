<script setup lang="ts">
    import AdminLayout from '@/layouts/admin/AdminLayout.vue'

    import {
        Head,
        Link,
    } from '@inertiajs/vue3'

    defineProps<{
        stats: {
            total_rooms: number
            waiting_rooms: number
            running_rooms: number
            finished_rooms: number
        }
    }>()
</script>

<template>

    <Head title="Dashboard — SiOne" />

    <AdminLayout title="Dashboard">
        <!-- =====================================================
        TOP ACTION
        ====================================================== -->

        <template #actions>
            <Link href="/admin/rooms/create"
                class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-3.5 py-2 text-sm font-bold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700 focus-visible:ring-2 focus-visible:ring-indigo-500/40 focus-visible:ring-offset-2 sm:min-h-11 sm:px-4">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 5v14" />
                    <path d="M5 12h14" />
                </svg>

                Buat Room
            </Link>
        </template>

        <!-- =====================================================
        WELCOME
        ====================================================== -->

        <div
            class="relative mb-5 overflow-hidden rounded-2xl border border-indigo-100 bg-white p-5 shadow-sm sm:mb-6 sm:p-6 lg:p-7">
            <div class="pointer-events-none absolute -right-14 -top-16 h-44 w-44 rounded-full bg-indigo-100/70 blur-2xl"
                aria-hidden="true" />

            <div class="pointer-events-none absolute -bottom-20 right-28 h-40 w-40 rounded-full bg-violet-100/50 blur-2xl"
                aria-hidden="true" />

            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex min-w-0 items-start gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M13 2 3 14h9l-1 8 10-12h-9z" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-indigo-600">
                            SiOne Admin
                        </p>

                        <h2 class="mt-1 text-xl font-black tracking-tight text-slate-950 sm:text-2xl">
                            Control Center Ranking Satu
                        </h2>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Kelola room, siapkan soal, pantau peserta, dan
                            jalankan pertandingan dari satu tempat.
                        </p>
                    </div>
                </div>

                <Link href="/admin/rooms"
                    class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 lg:w-auto">
                    Kelola Room

                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14" />
                        <path d="m13 6 6 6-6 6" />
                    </svg>
                </Link>
            </div>
        </div>

        <!-- =====================================================
        STATISTICS
        ====================================================== -->

        <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
            <!-- Total Room -->
            <div
                class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-slate-300 hover:shadow-md sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 sm:text-sm">
                            Total Room
                        </p>

                        <div class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                            {{ stats.total_rooms }}
                        </div>
                    </div>

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-slate-200 sm:h-10 sm:w-10">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect width="18" height="18" x="3" y="3" rx="2" />

                            <path d="M7 8h10" />
                            <path d="M7 12h10" />
                            <path d="M7 16h6" />
                        </svg>
                    </div>
                </div>

                <div
                    class="mt-4 flex items-center gap-2 border-t border-slate-100 pt-3 text-xs font-medium text-slate-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400" />

                    Semua room
                </div>
            </div>

            <!-- Waiting -->
            <div
                class="group rounded-2xl border border-amber-100 bg-white p-4 shadow-sm transition hover:border-amber-200 hover:shadow-md sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 sm:text-sm">
                            Room Menunggu
                        </p>

                        <div class="mt-2 text-2xl font-black tracking-tight text-amber-600 sm:text-3xl">
                            {{ stats.waiting_rooms }}
                        </div>
                    </div>

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 transition group-hover:bg-amber-100 sm:h-10 sm:w-10">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />

                            <path d="M12 7v5l3 2" />
                        </svg>
                    </div>
                </div>

                <div
                    class="mt-4 flex items-center gap-2 border-t border-slate-100 pt-3 text-xs font-medium text-amber-600">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500" />

                    Menunggu permainan
                </div>
            </div>

            <!-- Running -->
            <div
                class="group rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm transition hover:border-emerald-200 hover:shadow-md sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 sm:text-sm">
                            Sedang Berjalan
                        </p>

                        <div class="mt-2 text-2xl font-black tracking-tight text-emerald-600 sm:text-3xl">
                            {{ stats.running_rooms }}
                        </div>
                    </div>

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition group-hover:bg-emerald-100 sm:h-10 sm:w-10">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M8 5v14l11-7Z" />
                        </svg>
                    </div>
                </div>

                <div
                    class="mt-4 flex items-center gap-2 border-t border-slate-100 pt-3 text-xs font-medium text-emerald-600">
                    <span class="relative flex h-1.5 w-1.5">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-40" />

                        <span class="relative h-1.5 w-1.5 rounded-full bg-emerald-500" />
                    </span>

                    Game aktif
                </div>
            </div>

            <!-- Finished -->
            <div
                class="group rounded-2xl border border-indigo-100 bg-white p-4 shadow-sm transition hover:border-indigo-200 hover:shadow-md sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 sm:text-sm">
                            Game Selesai
                        </p>

                        <div class="mt-2 text-2xl font-black tracking-tight text-indigo-600 sm:text-3xl">
                            {{ stats.finished_rooms }}
                        </div>
                    </div>

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 transition group-hover:bg-indigo-100 sm:h-10 sm:w-10">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m5 12 4 4L19 6" />
                        </svg>
                    </div>
                </div>

                <div
                    class="mt-4 flex items-center gap-2 border-t border-slate-100 pt-3 text-xs font-medium text-indigo-600">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-500" />

                    Telah selesai
                </div>
            </div>
        </div>

        <!-- =====================================================
        ROOM MANAGEMENT
        ====================================================== -->

        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm sm:mt-6">
            <div class="flex flex-col gap-5 p-5 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex min-w-0 items-start gap-4">
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
                        <h2 class="font-bold text-slate-900">
                            Pengelolaan Room
                        </h2>

                        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                            Buat room pertandingan baru atau kelola room yang
                            sudah tersedia.
                        </p>
                    </div>
                </div>

                <Link href="/admin/rooms"
                    class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-bold text-indigo-600 transition hover:bg-indigo-100 lg:w-auto">
                    Lihat Semua Room

                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14" />
                        <path d="m13 6 6 6-6 6" />
                    </svg>
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>