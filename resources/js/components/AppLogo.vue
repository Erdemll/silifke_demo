<script setup lang="ts">
import { useSidebar } from '@/components/ui/sidebar';
import YoreselLogo from '@/components/YoreselLogo.vue';

const { state } = useSidebar();
</script>

<template>
    <YoreselLogo
        inverse
        :compact="state === 'collapsed'"
        class="text-sidebar-foreground"
    />
</template>
