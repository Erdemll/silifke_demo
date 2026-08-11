<script setup lang="ts">
import { Monitor, Moon, Sun } from '@lucide/vue';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();

const tabs = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
    { value: 'system', Icon: Monitor, label: 'System' },
] as const;
</script>

<template>
    <div
        class="inline-flex gap-1 rounded-xl bg-brand-100 p-1 dark:bg-brand-900"
    >
        <button
            v-for="{ value, Icon, label } in tabs"
            :key="value"
            @click="updateAppearance(value)"
            :class="[
                'flex items-center rounded-lg px-3.5 py-1.5 transition-colors',
                appearance === value
                    ? 'bg-card text-brand-800 shadow-xs dark:bg-brand-800 dark:text-brand-100'
                    : 'text-brand-600 hover:bg-brand-200/60 hover:text-brand-900 dark:text-brand-400 dark:hover:bg-brand-800/60',
            ]"
        >
            <component :is="Icon" class="-ml-1 h-4 w-4" />
            <span class="ml-1.5 text-sm">{{ label }}</span>
        </button>
    </div>
</template>
