<script setup lang="ts">
    import SiOneLogo from '@/components/SiOneLogo.vue'
    import ConfirmDialog from '@/components/ui/ConfirmDialog.vue'
    import ToastContainer from '@/components/ui/ToastContainer.vue'

    import {
        useConfirm,
    } from '@/composables/useConfirm'

    import {
        Link,
        router,
        usePage,
    } from '@inertiajs/vue3'

    import {
        computed,
        ref,
    } from 'vue'


    withDefaults(
        defineProps<{
            title?: string
        }>(),
        {
            title: 'Dashboard',
        },
    )

    const page = usePage()

    const currentPath = computed(
        () =>
            page.url.split('?')[0],
    )

    const mobileMenuOpen =
        ref(false)

    const closeMobileMenu = () => {
        mobileMenuOpen.value =
            false
    }

    const isActive = (
        href: string,
    ) => {
        if (
            href === '/admin'
        ) {
            return (
                currentPath.value ===
                '/admin'
            )
        }

        return currentPath.value.startsWith(
            href,
        )
    }

    const dashboardActive = computed(
        () =>
            currentPath.value ===
            '/admin',
    )

    const roomActive = computed(
        () =>
            currentPath.value.startsWith(
                '/admin/rooms',
            ),
    )

    const {
        askConfirm,
    } = useConfirm()

    const logoutProcessing =
        ref(false)

    const logout = async () => {
        if (
            logoutProcessing.value
        ) {
            return
        }

        const confirmed =
            await askConfirm({
                title:
                    'Keluar dari SiOne?',

                description:
                    'Sesi administrator akan diakhiri dan Anda akan kembali ke halaman login.',

                confirmText:
                    'Ya, Keluar',

                cancelText:
                    'Batal',

                tone:
                    'danger',
            })

        if (!confirmed) {
            return
        }

        closeMobileMenu()

        logoutProcessing.value =
            true

        router.post(
            '/logout',
            {},
            {
                preserveScroll: false,

                onFinish: () => {
                    logoutProcessing.value =
                        false
                },
            },
        )
    }
</script>

<template>
    <div class="min-h-svh bg-slate-50 text-slate-900">
        <!-- =====================================================
        HEADER
        ====================================================== -->

        <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur">
            <div class="mx-auto flex h-18 max-w-[1600px] items-center justify-between px-4 sm:h-20 sm:px-6 lg:px-8">
                <!-- =============================================
                LEFT
                ============================================== -->

                <div class="flex min-w-0 items-center gap-3">
                    <!-- Mobile Menu -->
                    <button type="button"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 hover:text-indigo-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/30 lg:hidden"
                        aria-label="Buka navigasi" @click="
                            mobileMenuOpen =
                            true
                            ">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 6h16" />

                            <path d="M4 12h16" />

                            <path d="M4 18h16" />
                        </svg>
                    </button>

                    <!-- Brand -->
                    <Link href="/admin" class="flex min-w-0 items-center gap-3">
                        <SiOneLogo size="h-10 w-10 sm:h-11 sm:w-11" />

                        <div class="min-w-0">
                            <div class="truncate text-base font-extrabold tracking-tight text-slate-950 sm:text-lg">
                                SiOne
                            </div>

                            <div class="hidden text-[11px] font-medium text-slate-500 sm:block">
                                Admin Control Center
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- =============================================
                RIGHT
                ============================================== -->

                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Administrator Status -->
                    <div
                        class="hidden items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600 sm:flex">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-40" />

                            <span class="relative h-2 w-2 rounded-full bg-emerald-500" />
                        </span>

                        Administrator
                    </div>

                    <!-- Header Logout -->
                    <button type="button" :disabled="logoutProcessing
                        "
                        class="hidden h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500/20 disabled:pointer-events-none disabled:opacity-50 sm:flex"
                        aria-label="Keluar dari SiOne" title="Keluar" @click="logout">
                        <svg v-if="
                            logoutProcessing
                        " class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />

                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z" />
                        </svg>

                        <svg v-else class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M10 17l5-5-5-5" />

                            <path d="M15 12H3" />

                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- =====================================================
        DESKTOP LAYOUT
        ====================================================== -->

        <div class="mx-auto flex min-h-[calc(100svh-5rem)] max-w-[1600px]">
            <!-- =================================================
            SIDEBAR DESKTOP
            ================================================== -->

            <aside class="hidden w-64 shrink-0 border-r border-slate-200 bg-white lg:block xl:w-72">
                <div class="sticky top-20 flex h-[calc(100svh-5rem)] flex-col px-4 py-6">
                    <!-- Navigation Label -->
                    <div class="px-3 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">
                        Menu Utama
                    </div>

                    <!-- =========================================
                    NAVIGATION
                    ========================================== -->

                    <nav class="mt-3 space-y-1.5">
                        <!-- Dashboard -->
                        <Link href="/admin"
                            class="group flex min-h-11 items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition"
                            :class="dashboardActive
                                ? 'bg-indigo-50 text-indigo-700'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950'
                                ">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition" :class="dashboardActive
                                ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200'
                                : 'bg-slate-100 text-slate-500 group-hover:bg-white'
                                ">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect width="7" height="9" x="3" y="3" rx="1" />

                                    <rect width="7" height="5" x="14" y="3" rx="1" />

                                    <rect width="7" height="9" x="14" y="12" rx="1" />

                                    <rect width="7" height="5" x="3" y="16" rx="1" />
                                </svg>
                            </div>

                            <span class="min-w-0 flex-1">
                                Dashboard
                            </span>

                            <span v-if="
                                dashboardActive
                            " class="h-1.5 w-1.5 rounded-full bg-indigo-600" />
                        </Link>

                        <!-- =====================================
                        ROOM
                        ====================================== -->

                        <div>
                            <!-- Parent -->
                            <div class="flex min-h-11 items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold"
                                :class="roomActive
                                    ? 'text-indigo-700'
                                    : 'text-slate-600'
                                    ">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition"
                                    :class="roomActive
                                        ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200'
                                        : 'bg-slate-100 text-slate-500'
                                        ">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        aria-hidden="true">
                                        <rect width="18" height="18" x="3" y="3" rx="2" />

                                        <path d="M7 8h10" />

                                        <path d="M7 12h10" />

                                        <path d="M7 16h6" />
                                    </svg>
                                </div>

                                <span class="flex-1">
                                    Room
                                </span>
                            </div>

                            <!-- Desktop Sub Menu -->
                            <div class="ml-7 mt-1 space-y-1 border-l border-slate-200 pl-4">
                                <!-- Semua -->
                                <Link href="/admin/rooms"
                                    class="relative flex min-h-11 items-center rounded-lg px-3 py-2.5 text-sm font-medium transition"
                                    :class="currentPath ===
                                        '/admin/rooms'
                                        ? 'bg-indigo-50 font-semibold text-indigo-700'
                                        : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900'
                                        ">
                                    <span v-if="
                                        currentPath ===
                                        '/admin/rooms'
                                    "
                                        class="absolute -left-[17px] h-2 w-2 rounded-full bg-indigo-600 ring-4 ring-white" />

                                    Semua
                                </Link>

                                <!-- Aktif -->
                                <Link href="/admin/rooms/active"
                                    class="relative flex min-h-11 items-center rounded-lg px-3 py-2.5 text-sm font-medium transition"
                                    :class="currentPath ===
                                        '/admin/rooms/active'
                                        ? 'bg-indigo-50 font-semibold text-indigo-700'
                                        : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900'
                                        ">
                                    <span v-if="
                                        currentPath ===
                                        '/admin/rooms/active'
                                    "
                                        class="absolute -left-[17px] h-2 w-2 rounded-full bg-emerald-500 ring-4 ring-white" />

                                    <span class="mr-2 h-1.5 w-1.5 rounded-full bg-emerald-500" />

                                    Aktif
                                </Link>

                                <!-- Selesai -->
                                <Link href="/admin/rooms/finished"
                                    class="relative flex min-h-11 items-center rounded-lg px-3 py-2.5 text-sm font-medium transition"
                                    :class="currentPath ===
                                        '/admin/rooms/finished'
                                        ? 'bg-indigo-50 font-semibold text-indigo-700'
                                        : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900'
                                        ">
                                    <span v-if="
                                        currentPath ===
                                        '/admin/rooms/finished'
                                    "
                                        class="absolute -left-[17px] h-2 w-2 rounded-full bg-indigo-600 ring-4 ring-white" />

                                    <span class="mr-2 h-1.5 w-1.5 rounded-full bg-indigo-500" />

                                    Selesai
                                </Link>
                            </div>
                        </div>
                    </nav>

                    <!-- =========================================
                    SIDEBAR BOTTOM
                    ========================================== -->

                    <div class="mt-auto space-y-3">
                        <!-- Info Card -->
                        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4">
                            <div class="flex items-center gap-3">
                                <SiOneLogo size="h-10 w-10" />

                                <div class="min-w-0">
                                    <div class="truncate text-sm font-bold text-slate-900">
                                        SiOne Admin
                                    </div>

                                    <div
                                        class="mt-0.5 flex items-center gap-1.5 text-[11px] font-medium text-emerald-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" />

                                        Administrator aktif
                                    </div>
                                </div>
                            </div>

                            <p class="mt-3 text-xs leading-5 text-slate-500">
                                Kelola room dan jalankan
                                pertandingan dari satu tempat.
                            </p>
                        </div>

                        <!-- Logout -->
                        <button type="button" :disabled="logoutProcessing
                            "
                            class="group flex min-h-11 w-full items-center gap-3 rounded-xl border border-transparent px-3.5 py-2.5 text-left text-sm font-semibold text-slate-500 transition hover:border-red-100 hover:bg-red-50 hover:text-red-600 disabled:pointer-events-none disabled:opacity-50"
                            @click="logout">
                            <div
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 transition group-hover:bg-red-100 group-hover:text-red-600">
                                <svg v-if="
                                    logoutProcessing
                                " class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />

                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z" />
                                </svg>

                                <svg v-else class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M10 17l5-5-5-5" />

                                    <path d="M15 12H3" />

                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                </svg>
                            </div>

                            <span class="flex-1">
                                {{
                                    logoutProcessing
                                        ? 'Keluar...'
                                        : 'Keluar'
                                }}
                            </span>

                            <svg v-if="
                                !logoutProcessing
                            " class="h-4 w-4 text-slate-300 transition group-hover:text-red-400" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </button>
                    </div>
                </div>
            </aside>

            <!-- =================================================
            CONTENT
            ================================================== -->

            <main class="min-w-0 flex-1">
                <div class="mx-auto w-full max-w-7xl px-4 py-5 sm:px-6 sm:py-7 lg:px-8 lg:py-8">
                    <!-- Page Heading -->
                    <div class="mb-6 flex items-start justify-between gap-3 sm:mb-8 sm:gap-5">
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-indigo-600 sm:text-xs">
                                Administrator
                            </p>

                            <h1 class="mt-1 truncate text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">
                                {{ title }}
                            </h1>
                        </div>

                        <div v-if="$slots.actions" class="flex shrink-0 items-start justify-end gap-2">
                            <slot name="actions" />
                        </div>
                    </div>

                    <!-- Page Content -->
                    <slot />
                </div>
            </main>
        </div>

        <!-- =====================================================
        MOBILE / TABLET SIDEBAR
        ====================================================== -->

        <Teleport to="body">
            <!-- ================================================
            OVERLAY
            ================================================= -->

            <Transition enter-active-class="transition-opacity duration-200" enter-from-class="opacity-0"
                enter-to-class="opacity-100" leave-active-class="transition-opacity duration-200"
                leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="
                    mobileMenuOpen
                " class="fixed inset-0 z-50 bg-slate-950/40 backdrop-blur-[2px] lg:hidden" @click="
                    closeMobileMenu
                " />
            </Transition>

            <!-- ================================================
            DRAWER
            ================================================= -->

            <Transition enter-active-class="transition-transform duration-300 ease-out"
                enter-from-class="-translate-x-full" enter-to-class="translate-x-0"
                leave-active-class="transition-transform duration-200 ease-in" leave-from-class="translate-x-0"
                leave-to-class="-translate-x-full">
                <aside v-if="
                    mobileMenuOpen
                "
                    class="fixed inset-y-0 left-0 z-[60] flex w-[min(85vw,320px)] flex-col bg-white shadow-2xl lg:hidden">
                    <!-- =========================================
                    DRAWER HEADER
                    ========================================== -->

                    <div class="flex h-18 shrink-0 items-center justify-between border-b border-slate-200 px-4 sm:h-20">
                        <Link href="/admin" class="flex min-w-0 items-center gap-3" @click="
                            closeMobileMenu
                        ">
                            <SiOneLogo size="h-10 w-10" />

                            <div class="min-w-0">
                                <div class="truncate font-extrabold text-slate-950">
                                    SiOne
                                </div>

                                <div class="text-[11px] text-slate-500">
                                    Admin Control Center
                                </div>
                            </div>
                        </Link>

                        <button type="button"
                            class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                            aria-label="Tutup navigasi" @click="
                                closeMobileMenu
                            ">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18" />

                                <path d="m6 6 12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- =========================================
                    DRAWER NAVIGATION
                    ========================================== -->

                    <div class="flex-1 overflow-y-auto p-4">
                        <p class="px-3 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">
                            Menu Utama
                        </p>

                        <nav class="mt-3 space-y-1.5">
                            <!-- Dashboard -->
                            <Link href="/admin"
                                class="flex min-h-12 items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold transition"
                                :class="dashboardActive
                                    ? 'bg-indigo-50 text-indigo-700'
                                    : 'text-slate-600 hover:bg-slate-50'
                                    " @click="
                                        closeMobileMenu
                                    ">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg" :class="dashboardActive
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-slate-100 text-slate-500'
                                    ">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="7" height="9" x="3" y="3" rx="1" />

                                        <rect width="7" height="5" x="14" y="3" rx="1" />

                                        <rect width="7" height="9" x="14" y="12" rx="1" />

                                        <rect width="7" height="5" x="3" y="16" rx="1" />
                                    </svg>
                                </div>

                                <span class="flex-1">
                                    Dashboard
                                </span>
                            </Link>

                            <!-- =================================
                            ROOM
                            ================================== -->

                            <div>
                                <!-- Parent -->
                                <div class="flex min-h-12 items-center gap-3 rounded-xl px-3.5 py-3 text-sm font-semibold"
                                    :class="roomActive
                                        ? 'text-indigo-700'
                                        : 'text-slate-600'
                                        ">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg" :class="roomActive
                                        ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-200'
                                        : 'bg-slate-100 text-slate-500'
                                        ">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="18" x="3" y="3" rx="2" />

                                            <path d="M7 8h10" />

                                            <path d="M7 12h10" />

                                            <path d="M7 16h6" />
                                        </svg>
                                    </div>

                                    <span class="flex-1">
                                        Room
                                    </span>
                                </div>

                                <!-- Mobile Sub Menu -->
                                <div class="ml-8 mt-1.5 space-y-1.5 border-l border-slate-200 pl-4">
                                    <!-- Semua -->
                                    <Link href="/admin/rooms"
                                        class="relative flex min-h-12 items-center rounded-xl px-3.5 py-3 text-sm font-medium transition"
                                        :class="currentPath ===
                                            '/admin/rooms'
                                            ? 'bg-indigo-50 font-semibold text-indigo-700'
                                            : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900'
                                            " @click="
                                                closeMobileMenu
                                            ">
                                        <span v-if="
                                            currentPath ===
                                            '/admin/rooms'
                                        "
                                            class="absolute -left-[17px] h-2 w-2 rounded-full bg-indigo-600 ring-4 ring-white" />

                                        <span
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="16" height="16" x="4" y="4" rx="2" />

                                                <path d="M8 9h8" />

                                                <path d="M8 13h8" />

                                                <path d="M8 17h5" />
                                            </svg>
                                        </span>

                                        <span class="ml-3">
                                            Semua
                                        </span>
                                    </Link>

                                    <!-- Aktif -->
                                    <Link href="/admin/rooms/active"
                                        class="relative flex min-h-12 items-center rounded-xl px-3.5 py-3 text-sm font-medium transition"
                                        :class="currentPath ===
                                            '/admin/rooms/active'
                                            ? 'bg-indigo-50 font-semibold text-indigo-700'
                                            : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900'
                                            " @click="
                                                closeMobileMenu
                                            ">
                                        <span v-if="
                                            currentPath ===
                                            '/admin/rooms/active'
                                        "
                                            class="absolute -left-[17px] h-2 w-2 rounded-full bg-emerald-500 ring-4 ring-white" />

                                        <span
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50">
                                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500" />
                                        </span>

                                        <span class="ml-3">
                                            Aktif
                                        </span>
                                    </Link>

                                    <!-- Selesai -->
                                    <Link href="/admin/rooms/finished"
                                        class="relative flex min-h-12 items-center rounded-xl px-3.5 py-3 text-sm font-medium transition"
                                        :class="currentPath ===
                                            '/admin/rooms/finished'
                                            ? 'bg-indigo-50 font-semibold text-indigo-700'
                                            : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900'
                                            " @click="
                                                closeMobileMenu
                                            ">
                                        <span v-if="
                                            currentPath ===
                                            '/admin/rooms/finished'
                                        "
                                            class="absolute -left-[17px] h-2 w-2 rounded-full bg-indigo-600 ring-4 ring-white" />

                                        <span
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50">
                                            <svg class="h-4 w-4 text-indigo-600" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="m5 12 4 4L19 6" />
                                            </svg>
                                        </span>

                                        <span class="ml-3">
                                            Selesai
                                        </span>
                                    </Link>
                                </div>
                            </div>
                        </nav>
                    </div>

                    <!-- =========================================
                    DRAWER FOOTER
                    ========================================== -->

                    <div class="shrink-0 space-y-2 border-t border-slate-200 p-4">
                        <!-- Admin Status -->
                        <div class="flex min-h-12 items-center gap-3 rounded-xl bg-slate-50 px-3.5 py-3">
                            <span class="relative flex h-2.5 w-2.5">
                                <span
                                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-40" />

                                <span class="relative h-2.5 w-2.5 rounded-full bg-emerald-500" />
                            </span>

                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-semibold text-slate-700">
                                    Administrator
                                </div>

                                <div class="mt-0.5 text-[11px] text-slate-400">
                                    Sesi aktif
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Logout -->
                        <button type="button" :disabled="logoutProcessing
                            "
                            class="group flex min-h-12 w-full items-center gap-3 rounded-xl border border-red-100 bg-red-50/70 px-3.5 py-3 text-left text-sm font-semibold text-red-600 transition hover:border-red-200 hover:bg-red-50 disabled:pointer-events-none disabled:opacity-50"
                            @click="logout">
                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-red-500 shadow-sm">
                                <svg v-if="
                                    logoutProcessing
                                " class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />

                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z" />
                                </svg>

                                <svg v-else class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10 17l5-5-5-5" />

                                    <path d="M15 12H3" />

                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                </svg>
                            </span>

                            <span class="flex-1">
                                {{
                                    logoutProcessing
                                        ? 'Keluar...'
                                        : 'Keluar dari SiOne'
                                }}
                            </span>

                            <svg v-if="
                                !logoutProcessing
                            " class="h-4 w-4 text-red-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </button>
                    </div>
                </aside>
            </Transition>
        </Teleport>

        <!-- =====================================================
        GLOBAL UI
        ====================================================== -->

        <ConfirmDialog />
        <ToastContainer />
    </div>
</template>