<script setup lang="ts">
    import SiOneLogo from '@/components/SiOneLogo.vue'
    defineProps<{
        title?: string
    }>()
</script>

<template>
    <div class="relative flex min-h-svh flex-col overflow-hidden bg-slate-50 text-slate-900">
        <!-- Decorative Background -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -left-32 -top-32 h-80 w-80 rounded-full bg-indigo-500/10 blur-3xl sm:h-96 sm:w-96" />

            <div class="absolute -right-32 top-1/3 h-80 w-80 rounded-full bg-violet-500/10 blur-3xl sm:h-96 sm:w-96" />

            <div class="absolute -bottom-32 left-1/3 h-72 w-72 rounded-full bg-cyan-500/10 blur-3xl" />

            <!-- Grid Pattern -->
            <div
                class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:32px_32px] opacity-30 [mask-image:linear-gradient(to_bottom,white,transparent_80%)]" />
        </div>

        <!-- Header -->
        <header class="relative z-20">
            <div class="mx-auto flex h-18 w-full max-w-6xl items-center justify-between px-4 sm:h-20 sm:px-6 lg:px-8">
                <!-- Brand -->
                <a href="#" class="flex items-center gap-3">
                    <SiOneLogo size="h-10 w-10 sm:h-11 sm:w-11" />

                    <div>
                        <div class="font-extrabold tracking-tight text-slate-950">
                            SiOne
                        </div>

                        <div class="text-[11px] font-medium text-slate-500">
                            Sistem Ranking Satu
                        </div>
                    </div>
                </a>

                <!-- Participant Badge -->
                <div
                    class="inline-flex min-h-9 items-center gap-2 rounded-full border border-indigo-100 bg-white/80 px-3 text-xs font-semibold text-indigo-600 shadow-sm backdrop-blur sm:min-h-10 sm:px-3.5 sm:text-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500" />

                    Peserta
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="relative z-10 flex flex-1 items-center justify-center">
            <div class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
                <slot />
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10">
            <div
                class="mx-auto flex w-full max-w-6xl items-center justify-center border-t border-slate-200/70 px-4 py-5 text-center text-xs text-slate-400 sm:px-6 lg:px-8">
                SiOne · Sistem Ranking Satu Interaktif
            </div>
        </footer>
    </div>
</template>