<script setup lang="ts">
    import AdminLayout from '@/layouts/admin/AdminLayout.vue'
    import type { Room } from '@/types/room'
    import {
        useConfirm,
    } from '@/composables/useConfirm'

    import {
        Head,
        Link,
        router,
    } from '@inertiajs/vue3'

    const props = defineProps<{
        rooms: Room[]
    }>()

    const {
        askConfirm,
    } = useConfirm()
    /*
    |--------------------------------------------------------------------------
    | Delete Room
    |--------------------------------------------------------------------------
    */

    const deleteRoom =
        async (
            room: Room,
        ) => {
            const confirmed =
                await askConfirm({
                    title:
                        'Hapus Room?',

                    description:
                        `Room "${room.name}" dan seluruh data yang terkait akan dihapus secara permanen.`,

                    confirmText:
                        'Hapus Room',

                    cancelText:
                        'Batal',

                    tone:
                        'danger',
                })

            if (!confirmed) {
                return
            }

            router.delete(
                `/admin/rooms/${room.id}`,
                {
                    preserveScroll: true,
                },
            )
        }

    /*
    |--------------------------------------------------------------------------
    | Duplicate Room
    |--------------------------------------------------------------------------
    */

    const duplicateRoom =
        async (
            room: Room,
        ) => {
            const confirmed =
                await askConfirm({
                    title:
                        'Duplikat Room?',

                    description:
                        `Salinan baru dari "${room.name}" akan dibuat beserta seluruh soal. Peserta dan jawaban pertandingan tidak ikut disalin.`,

                    confirmText:
                        'Duplikat Room',

                    cancelText:
                        'Batal',

                    tone:
                        'primary',
                })

            if (!confirmed) {
                return
            }

            router.post(
                `/admin/rooms/${room.id}/duplicate`,
            )
        }

    /*
    |--------------------------------------------------------------------------
    | Room Status
    |--------------------------------------------------------------------------
    */

    const statusClass = (
        status: Room['status'],
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
        status: Room['status'],
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

    /*
    |--------------------------------------------------------------------------
    | Main Action
    |--------------------------------------------------------------------------
    */

    const playLabel = (
        status: Room['status'],
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

    const playUrl = (
        room: Room,
    ) => {
        if (
            room.status ===
            'finished'
        ) {
            return `/admin/rooms/${room.id}/result`
        }

        return `/admin/rooms/${room.id}/play`
    }
</script>

<template>

    <Head title="Room — SiOne" />

    <AdminLayout title="Room">
        <!-- =====================================================
        PAGE ACTION
        ====================================================== -->

        <template #actions>
            <Link href="/admin/rooms/create"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700 focus-visible:ring-2 focus-visible:ring-indigo-500/40 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 5v14" />
                    <path d="M5 12h14" />
                </svg>

                Buat Room
            </Link>
        </template>

        <!-- =====================================================
        INTRO
        ====================================================== -->

        <div class="mb-5 flex flex-col gap-3 sm:mb-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 sm:text-lg">
                    Daftar Room
                </h2>

                <p class="mt-1 max-w-xl text-sm leading-6 text-slate-500">
                    Kelola room, soal, peserta, dan jalannya pertandingan
                    SiOne.
                </p>
            </div>

            <div v-if="rooms.length > 0"
                class="inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-500 shadow-sm">
                <span
                    class="flex h-6 min-w-6 items-center justify-center rounded-lg bg-indigo-50 px-1.5 font-bold text-indigo-600">
                    {{ rooms.length }}
                </span>

                Total Room
            </div>
        </div>

        <!-- =====================================================
        EMPTY STATE
        ====================================================== -->

        <div v-if="rooms.length === 0" class="overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white">
            <div class="px-5 py-14 text-center sm:px-8 sm:py-20">
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="18" height="18" x="3" y="3" rx="2" />

                        <path d="M7 8h10" />
                        <path d="M7 12h10" />
                        <path d="M7 16h6" />
                    </svg>
                </div>

                <h3 class="mt-5 text-lg font-bold text-slate-900">
                    Belum ada room
                </h3>

                <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                    Buat room pertama untuk menyiapkan dan menjalankan
                    pertandingan SiOne.
                </p>

                <Link href="/admin/rooms/create"
                    class="mt-6 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 5v14" />
                        <path d="M5 12h14" />
                    </svg>

                    Buat Room
                </Link>
            </div>
        </div>

        <!-- =====================================================
        ROOM LIST
        ====================================================== -->

        <template v-else>
            <!-- =================================================
            MOBILE / TABLET CARDS
            ================================================== -->

            <div class="grid gap-4 lg:hidden">
                <article v-for="room in rooms" :key="room.id"
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <!-- Card Header -->
                    <div class="p-4 sm:p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <h3 class="break-words text-base font-bold text-slate-900">
                                    {{ room.name }}
                                </h3>

                            </div>

                            <span
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-bold"
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
                        </div>

                        <!-- Metadata -->
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Kode Room
                                </div>

                                <div class="mt-1 font-mono text-sm font-bold tracking-wider text-slate-700">
                                    {{ room.code }}
                                </div>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Acak Soal
                                </div>

                                <div class="mt-1 flex items-center gap-1.5 text-sm font-semibold" :class="room.shuffle_questions
                                    ? 'text-emerald-600'
                                    : 'text-slate-500'
                                    ">
                                    <span class="h-1.5 w-1.5 rounded-full" :class="room.shuffle_questions
                                        ? 'bg-emerald-500'
                                        : 'bg-slate-300'
                                        " />

                                    {{
                                        room.shuffle_questions
                                            ? 'Aktif'
                                            : 'Tidak Aktif'
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Primary Action -->
                    <div class="border-t border-slate-100 bg-slate-50/60 p-4 sm:p-5">
                        <Link :href="playUrl(room)"
                            class="flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-700">
                            <svg v-if="
                                room.status !==
                                'finished'
                            " class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polygon points="5 3 19 12 5 21 5 3" />
                            </svg>

                            <svg v-else class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 3v18h18" />
                                <path d="m7 16 4-4 4 3 5-7" />
                            </svg>

                            {{ playLabel(room.status) }}
                        </Link>

                        <!-- Secondary Actions -->
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <Link :href="`/admin/rooms/${room.id}/edit`"
                                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                <svg v-if="
                                    room.status ===
                                    'locked'
                                " class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 20h9" />

                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                </svg>

                                <svg v-else class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <path
                                        d="M2.06 12.35a1 1 0 0 1 0-.7C3.7 7.6 7.6 5 12 5c4.4 0 8.3 2.6 9.94 6.65a1 1 0 0 1 0 .7C20.3 16.4 16.4 19 12 19c-4.4 0-8.3-2.6-9.94-6.65" />

                                    <circle cx="12" cy="12" r="3" />
                                </svg>

                                {{
                                    room.status ===
                                        'locked'
                                        ? 'Edit'
                                        : 'Lihat'
                                }}
                            </Link>

                            <button type="button"
                                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-100"
                                @click="
                                    duplicateRoom(
                                        room,
                                    )
                                    ">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect width="14" height="14" x="8" y="8" rx="2" />

                                    <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                                </svg>

                                Duplikat
                            </button>
                        </div>

                        <button v-if="
                            room.status ===
                            'locked' ||
                            room.status ===
                            'finished'
                        " type="button"
                            class="mt-2 inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                            @click="
                                deleteRoom(
                                    room,
                                )
                                ">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 6h18" />
                                <path d="M8 6V4h8v2" />
                                <path d="M19 6l-1 14H6L5 6" />
                                <path d="M10 11v5" />
                                <path d="M14 11v5" />
                            </svg>

                            Hapus Room
                        </button>
                    </div>
                </article>
            </div>

            <!-- =================================================
            DESKTOP TABLE
            ================================================== -->

            <div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:block">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th
                                    class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400 xl:px-6">
                                    Room
                                </th>

                                <th
                                    class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400 xl:px-6">
                                    Kode
                                </th>

                                <th
                                    class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400 xl:px-6">
                                    Status
                                </th>

                                <th
                                    class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400 xl:px-6">
                                    Acak Soal
                                </th>

                                <th
                                    class="px-5 py-3.5 text-right text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400 xl:px-6">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="room in rooms" :key="room.id" class="group transition hover:bg-slate-50/70">
                                <!-- Room -->
                                <td class="max-w-sm px-5 py-4 xl:px-6">
                                    <div class="font-bold text-slate-900">
                                        {{ room.name }}
                                    </div>
                                </td>

                                <!-- Code -->
                                <td class="whitespace-nowrap px-5 py-4 xl:px-6">
                                    <span
                                        class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1.5 font-mono text-xs font-bold tracking-wider text-slate-600">
                                        {{ room.code }}
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="whitespace-nowrap px-5 py-4 xl:px-6">
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

                                        {{
                                            room.status_label
                                        }}
                                    </span>
                                </td>

                                <!-- Shuffle -->
                                <td class="whitespace-nowrap px-5 py-4 xl:px-6">
                                    <div class="inline-flex items-center gap-2 text-sm font-semibold" :class="room.shuffle_questions
                                        ? 'text-emerald-600'
                                        : 'text-slate-400'
                                        ">
                                        <span class="h-2 w-2 rounded-full" :class="room.shuffle_questions
                                            ? 'bg-emerald-500'
                                            : 'bg-slate-300'
                                            " />

                                        {{
                                            room.shuffle_questions
                                                ? 'Aktif'
                                                : 'Tidak'
                                        }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="whitespace-nowrap px-5 py-4 xl:px-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Main -->
                                        <Link :href="playUrl(
                                            room,
                                        )
                                            "
                                            class="inline-flex min-h-9 items-center justify-center rounded-lg bg-indigo-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-indigo-700 xl:text-sm">
                                            {{
                                                playLabel(
                                                    room.status,
                                                )
                                            }}
                                        </Link>

                                        <!-- Edit / View -->
                                        <Link :href="`/admin/rooms/${room.id}/edit`"
                                            class="inline-flex min-h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 xl:text-sm">
                                            {{
                                                room.status ===
                                                    'locked'
                                                    ? 'Edit'
                                                    : 'Lihat'
                                            }}
                                        </Link>

                                        <!-- Duplicate -->
                                        <button type="button"
                                            class="inline-flex min-h-9 items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100 xl:text-sm"
                                            @click="
                                                duplicateRoom(
                                                    room,
                                                )
                                                ">
                                            Duplikat
                                        </button>

                                        <!-- Delete -->
                                        <button v-if="
                                            room.status ===
                                            'locked' ||
                                            room.status ===
                                            'finished'
                                        " type="button"
                                            class="inline-flex min-h-9 items-center justify-center rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50 xl:text-sm"
                                            @click="
                                                deleteRoom(
                                                    room,
                                                )
                                                ">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </AdminLayout>
</template>