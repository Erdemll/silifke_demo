<script setup lang="ts">
withDefaults(
    defineProps<{
        compact?: boolean;
        inverse?: boolean;
    }>(),
    {
        compact: false,
        inverse: false,
    },
);
</script>

<template>
    <span class="inline-flex min-w-0 items-center gap-2.5">
        <span
            class="relative flex shrink-0 items-center justify-center overflow-hidden shadow-sm ring-1"
            :class="[
                compact ? 'size-8 rounded-lg' : 'size-10 rounded-[0.9rem]',
                inverse
                    ? 'bg-sand-100 text-brand-800 ring-white/15'
                    : 'bg-brand-700 text-sand-50 ring-brand-900/10',
            ]"
        >
            <svg
                viewBox="0 0 48 48"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                :class="compact ? 'size-6' : 'size-8'"
                aria-hidden="true"
            >
                <circle
                    cx="23.5"
                    cy="27"
                    r="10.5"
                    fill="currentColor"
                    class="opacity-20"
                />
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
                    class="opacity-70"
                />
                <path
                    d="M16.5 27h14M23.5 20.5v13"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    class="opacity-50"
                />
            </svg>
        </span>

        <span v-if="!compact" class="min-w-0 leading-none">
            <span
                class="block truncate font-display text-[1.05rem] font-bold tracking-tight"
            >
                Silifke
            </span>
            <span
                class="mt-1 block truncate text-[0.62rem] font-semibold tracking-[0.22em] uppercase opacity-70"
            >
                Yöresel
            </span>
        </span>
    </span>
</template>
