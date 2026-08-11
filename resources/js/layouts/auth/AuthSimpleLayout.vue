<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Leaf, Sprout } from '@lucide/vue';
import YoreselLogo from '@/components/YoreselLogo.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="yoresel-paper relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-sand-100 p-5 md:p-10 dark:bg-brand-950"
    >
        <div
            class="pointer-events-none absolute -top-24 -right-20 size-80 rounded-full bg-brand-200/50 blur-3xl dark:bg-brand-800/25"
        />
        <div
            class="pointer-events-none absolute -bottom-32 -left-24 size-96 rounded-full bg-sand-300/50 blur-3xl dark:bg-sand-600/10"
        />
        <Leaf
            class="pointer-events-none absolute right-[8%] bottom-[8%] hidden size-36 rotate-12 text-brand-300/35 lg:block dark:text-brand-700/25"
            :stroke-width="1"
            aria-hidden="true"
        />

        <div
            class="relative w-full max-w-md rounded-3xl border border-brand-900/10 bg-card/92 p-6 shadow-2xl shadow-brand-950/10 backdrop-blur sm:p-9 dark:border-brand-100/10"
        >
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-5">
                    <Link
                        :href="home()"
                        class="rounded-xl text-brand-900 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none dark:text-sand-50"
                    >
                        <YoreselLogo />
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-800 dark:bg-brand-900 dark:text-brand-200"
                    >
                        <Sprout class="size-3.5" aria-hidden="true" />
                        Silifke'den sofranıza
                    </span>
                    <div class="space-y-2 text-center">
                        <h1
                            class="font-display text-2xl font-bold tracking-tight text-brand-950 dark:text-sand-50"
                        >
                            {{ title }}
                        </h1>
                        <p class="text-center text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
