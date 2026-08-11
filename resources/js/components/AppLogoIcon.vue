<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 48 48"
        :class="className"
        v-bind="$attrs"
        fill="none"
    >
        <circle cx="23.5" cy="27" r="10.5" fill="currentColor" opacity=".18" />
        <circle
            cx="23.5"
            cy="27"
            r="8.25"
            stroke="currentColor"
            stroke-width="2"
        />
        <path
            d="M23.5 18.3c.1-5.4 3.15-9.15 8.9-10.25-.25 5.7-3.05 9.1-8.9 10.25Z"
            fill="currentColor"
        />
        <path
            d="M22.5 18.2c-1.2-4.4-4.45-6.85-9.4-6.9 1.2 4.65 4.2 6.9 9.4 6.9Z"
            fill="currentColor"
            opacity=".7"
        />
        <path
            d="M16.5 27h14M23.5 20.5v13"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linecap="round"
            opacity=".5"
        />
    </svg>
</template>
