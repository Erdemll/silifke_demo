<script setup lang="ts">
import type { LinkComponentBaseProps, Method } from '@inertiajs/core';
import { Link } from '@inertiajs/vue3';

type Props = {
    href: LinkComponentBaseProps['href'];
    tabindex?: number;
    method?: Method;
    as?: string;
};

defineProps<Props>();
</script>

<template>
    <Link
        :href="href"
        :tabindex="tabindex"
        :method="method"
        :as="as"
        class="font-medium text-brand-700 underline decoration-brand-300 underline-offset-4 transition-colors duration-300 ease-out hover:text-brand-800 hover:decoration-current! dark:text-brand-300 dark:decoration-brand-700 dark:hover:text-brand-200"
    >
        <slot />
    </Link>
</template>
