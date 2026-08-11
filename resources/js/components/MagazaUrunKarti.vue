<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight, ImageIcon, Leaf } from '@lucide/vue';
import UrunDetayController from '@/actions/App/Http/Controllers/Magaza/UrunDetayController';

defineProps<{
    urun: {
        id: number;
        ad: string;
        fiyat: string;
        resim_url: string | null;
    };
}>();

const paraFormatlayici = new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY',
});
</script>

<template>
    <Link
        :href="UrunDetayController(urun.id)"
        prefetch
        class="group overflow-hidden rounded-3xl border border-brand-900/10 bg-card shadow-sm transition duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-xl hover:shadow-brand-950/10 focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 focus-visible:outline-none dark:border-brand-100/10 dark:hover:border-brand-700"
    >
        <article class="flex h-full flex-col">
            <div
                class="relative aspect-4/3 overflow-hidden bg-sand-100 dark:bg-brand-900"
            >
                <span
                    class="absolute top-3 left-3 z-10 inline-flex items-center gap-1 rounded-full bg-sand-50/90 px-2.5 py-1 text-[0.68rem] font-semibold text-brand-800 shadow-sm backdrop-blur dark:bg-brand-950/80 dark:text-brand-200"
                >
                    <Leaf class="size-3" aria-hidden="true" />
                    Yöresel
                </span>
                <img
                    v-if="urun.resim_url"
                    :src="urun.resim_url"
                    :alt="urun.ad"
                    loading="lazy"
                    class="size-full object-cover transition duration-500 group-hover:scale-105"
                />
                <div v-else class="flex size-full items-center justify-center">
                    <ImageIcon
                        class="size-12 text-brand-300 dark:text-brand-700"
                        aria-hidden="true"
                    />
                </div>
            </div>
            <div class="flex flex-1 flex-col gap-4 p-5">
                <div class="flex items-start justify-between gap-3">
                    <h3
                        class="line-clamp-2 font-display text-lg font-bold text-brand-950 transition-colors group-hover:text-brand-700 dark:text-sand-50 dark:group-hover:text-brand-300"
                    >
                        {{ urun.ad }}
                    </h3>
                    <span
                        class="flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700 transition group-hover:bg-brand-700 group-hover:text-white dark:bg-brand-900 dark:text-brand-300"
                    >
                        <ArrowUpRight class="size-4" aria-hidden="true" />
                    </span>
                </div>
                <p
                    class="mt-auto w-fit rounded-xl bg-brand-100 px-3 py-1.5 text-lg font-bold text-brand-800 tabular-nums dark:bg-brand-900 dark:text-brand-200"
                >
                    {{ paraFormatlayici.format(Number(urun.fiyat)) }}
                </p>
            </div>
        </article>
    </Link>
</template>
