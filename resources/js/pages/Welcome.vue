<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Citrus,
    Leaf,
    MapPin,
    PackageCheck,
    PackageOpen,
    Sparkles,
    Sprout,
} from '@lucide/vue';
import MagazaNavbar from '@/components/MagazaNavbar.vue';
import MagazaUrunKarti from '@/components/MagazaUrunKarti.vue';
import YoreselLogo from '@/components/YoreselLogo.vue';

type Urun = {
    id: number;
    ad: string;
    fiyat: string;
    resim_url: string | null;
};

type SayfalamaBaglantisi = {
    url: string | null;
    label: string;
    active: boolean;
};

type SayfalanmisUrunler = {
    data: Urun[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    links: SayfalamaBaglantisi[];
};

defineProps<{
    urunler: SayfalanmisUrunler;
}>();

function sayfaEtiketi(etiket: string): string {
    if (etiket.includes('Previous')) {
        return 'Önceki';
    }

    if (etiket.includes('Next')) {
        return 'Sonraki';
    }

    return etiket;
}
</script>

<template>
    <Head title="Ana Sayfa">
        <meta
            name="description"
            content="Silifke'nin taze ve doğal yöresel ürünlerini keşfedin."
        />
    </Head>

    <div
        class="yoresel-paper min-h-screen bg-sand-50 text-brand-950 dark:bg-brand-950 dark:text-sand-50"
    >
        <MagazaNavbar />

        <main class="pb-16">
            <section
                class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 sm:pt-8 lg:px-8"
                aria-labelledby="reklam-basligi"
            >
                <div
                    class="yoresel-pattern relative isolate overflow-hidden rounded-[2rem] border border-brand-600 bg-brand-800 px-6 py-10 text-white shadow-2xl shadow-brand-950/15 sm:px-12 sm:py-14 lg:px-16"
                >
                    <div
                        class="absolute -top-24 -right-20 -z-10 size-72 rounded-full bg-sand-300/25 blur-2xl"
                    />
                    <div
                        class="absolute -bottom-32 -left-20 -z-10 size-80 rounded-full bg-brand-300/20 blur-3xl"
                    />
                    <Leaf
                        class="absolute right-6 bottom-3 -z-10 size-36 rotate-12 text-brand-600/80 sm:right-12 sm:size-48"
                        :stroke-width="1"
                        aria-hidden="true"
                    />

                    <div
                        class="grid items-center gap-10 lg:grid-cols-[minmax(0,1fr)_18rem]"
                    >
                        <div class="flex max-w-3xl flex-col gap-5">
                            <p
                                class="flex w-fit items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-sm font-medium"
                            >
                                <Sparkles class="size-4" aria-hidden="true" />
                                Silifke'den sofranıza
                            </p>
                            <h1
                                id="reklam-basligi"
                                class="font-display text-4xl leading-[1.08] font-bold tracking-tight text-balance sm:text-5xl lg:text-6xl"
                            >
                                Taze ve doğal Silifke'nin yöresel ürünleri
                                sizleri bekliyor
                            </h1>
                            <p
                                class="max-w-2xl text-base text-brand-100 sm:text-lg"
                            >
                                Yörenin sevilen lezzetlerini doğrudan üretildiği
                                topraklardan keşfedin.
                            </p>
                            <div class="flex flex-wrap gap-2 pt-1">
                                <span
                                    class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-2 text-sm text-brand-50 backdrop-blur"
                                >
                                    <MapPin class="size-4" aria-hidden="true" />
                                    Silifke'nin yerel lezzetleri
                                </span>
                                <span
                                    class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-2 text-sm text-brand-50 backdrop-blur"
                                >
                                    <Sprout class="size-4" aria-hidden="true" />
                                    Doğal ürün seçkisi
                                </span>
                            </div>
                        </div>

                        <div
                            class="relative mx-auto hidden aspect-square w-full max-w-64 items-center justify-center lg:flex"
                            aria-hidden="true"
                        >
                            <div
                                class="absolute inset-3 rounded-full border border-dashed border-sand-200/30"
                            />
                            <div
                                class="flex size-40 items-center justify-center rounded-full bg-sand-100 text-brand-800 shadow-2xl ring-8 shadow-brand-950/30 ring-white/10"
                            >
                                <Citrus class="size-20" :stroke-width="1.4" />
                            </div>
                            <Sparkles
                                class="absolute top-5 right-6 size-7 text-sand-300"
                            />
                            <Leaf
                                class="absolute bottom-7 left-2 size-10 -rotate-20 text-brand-300"
                            />
                        </div>
                    </div>
                </div>

                <div
                    class="relative z-10 mx-3 -mt-5 grid gap-3 rounded-2xl border border-brand-900/10 bg-sand-50/95 p-4 shadow-lg shadow-brand-950/5 backdrop-blur sm:mx-8 sm:grid-cols-3 dark:border-brand-100/10 dark:bg-brand-900/95"
                >
                    <div class="flex items-center gap-3 px-2 py-1">
                        <span
                            class="flex size-10 items-center justify-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-800 dark:text-brand-200"
                        >
                            <Sprout class="size-5" aria-hidden="true" />
                        </span>
                        <div>
                            <p class="text-sm font-bold">Doğal seçki</p>
                            <p class="text-xs text-muted-foreground">
                                Yöreden özenle seçilir
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-2 py-1">
                        <span
                            class="flex size-10 items-center justify-center rounded-xl bg-sand-200 text-sand-600 dark:bg-sand-600/20 dark:text-sand-300"
                        >
                            <MapPin class="size-5" aria-hidden="true" />
                        </span>
                        <div>
                            <p class="text-sm font-bold">Silifke'den</p>
                            <p class="text-xs text-muted-foreground">
                                Yerel tatlar sofranızda
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-2 py-1">
                        <span
                            class="flex size-10 items-center justify-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-800 dark:text-brand-200"
                        >
                            <PackageCheck class="size-5" aria-hidden="true" />
                        </span>
                        <div>
                            <p class="text-sm font-bold">Özenli hazırlık</p>
                            <p class="text-xs text-muted-foreground">
                                Siparişe özel paketleme
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section
                class="mx-auto flex max-w-7xl flex-col gap-7 px-4 pt-12 sm:px-6 lg:px-8"
                aria-labelledby="urunler-basligi"
            >
                <div class="flex items-end justify-between gap-4">
                    <div class="flex flex-col gap-1">
                        <p
                            class="flex items-center gap-2 text-sm font-semibold tracking-wide text-brand-700 uppercase dark:text-brand-300"
                        >
                            <Leaf class="size-4" aria-hidden="true" />
                            Yöresel seçkiler
                        </p>
                        <h2
                            id="urunler-basligi"
                            class="font-display text-3xl font-bold tracking-tight sm:text-4xl"
                        >
                            Ürünlerimiz
                        </h2>
                    </div>
                    <p
                        v-if="urunler.total"
                        class="hidden rounded-full bg-brand-100 px-3 py-1 text-sm font-medium text-brand-800 sm:block dark:bg-brand-900 dark:text-brand-200"
                    >
                        {{ urunler.total }} ürün
                    </p>
                </div>

                <div
                    v-if="urunler.data.length"
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                    <MagazaUrunKarti
                        v-for="urun in urunler.data"
                        :key="urun.id"
                        :urun="urun"
                    />
                </div>

                <div
                    v-else
                    class="flex min-h-72 flex-col items-center justify-center gap-3 rounded-3xl border border-dashed border-brand-300 bg-card px-6 text-center dark:border-brand-800"
                >
                    <span
                        class="flex size-14 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900 dark:text-brand-300"
                    >
                        <PackageOpen class="size-7" aria-hidden="true" />
                    </span>
                    <div class="flex flex-col gap-1">
                        <h3 class="font-semibold">Henüz ürün bulunmuyor</h3>
                        <p class="text-sm text-muted-foreground">
                            Yöresel ürünlerimiz çok yakında burada yer alacak.
                        </p>
                    </div>
                </div>

                <div
                    v-if="urunler.last_page > 1"
                    class="flex flex-col gap-3 border-t border-border pt-6 text-sm sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-muted-foreground">
                        Toplam {{ urunler.total }} üründen {{ urunler.from }}–{{
                            urunler.to
                        }}
                        arası gösteriliyor.
                    </p>
                    <nav
                        class="flex flex-wrap gap-1"
                        aria-label="Ürün sayfaları"
                    >
                        <template
                            v-for="baglanti in urunler.links"
                            :key="baglanti.label"
                        >
                            <Link
                                v-if="baglanti.url"
                                :href="baglanti.url"
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl border px-2.5 font-medium transition-colors hover:bg-brand-100 focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none dark:hover:bg-brand-800"
                                :class="
                                    baglanti.active
                                        ? 'border-primary bg-primary text-primary-foreground hover:bg-primary/90'
                                        : 'border-border bg-card'
                                "
                                :aria-current="
                                    baglanti.active ? 'page' : undefined
                                "
                            >
                                {{ sayfaEtiketi(baglanti.label) }}
                            </Link>
                            <span
                                v-else
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl border border-border px-2.5 text-muted-foreground opacity-60"
                            >
                                {{ sayfaEtiketi(baglanti.label) }}
                            </span>
                        </template>
                    </nav>
                </div>
            </section>
        </main>

        <footer
            class="border-t border-brand-900/10 bg-sand-100 py-8 dark:border-brand-100/10 dark:bg-brand-900"
        >
            <div
                class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 text-sm text-brand-700 sm:px-6 lg:px-8 dark:text-brand-200"
            >
                <YoreselLogo />
                <p class="hidden items-center gap-2 sm:flex">
                    <MapPin class="size-4" aria-hidden="true" />
                    Silifke'nin yöresel lezzetleri
                </p>
            </div>
        </footer>
    </div>
</template>
