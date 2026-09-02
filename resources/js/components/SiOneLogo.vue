<script setup lang="ts">
    withDefaults(
        defineProps<{
            size?: string
            alt?: string
        }>(),
        {
            size: 'h-10 w-10',
            alt: 'SiOne',
        },
    )
</script>

<template>
    <img src="/images/sione-logo.png" :alt="alt" :class="[
        size,
        'block shrink-0 rounded-xl object-contain',
    ]" />
</template>