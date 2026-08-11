<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ClipboardList,
    ImageIcon,
    PackageCheck,
    ShoppingBag,
    Truck,
} from '@lucide/vue';
import UrunDetayController from '@/actions/App/Http/Controllers/Magaza/UrunDetayController';
import MagazaNavbar from '@/components/MagazaNavbar.vue';
import { home } from '@/routes';

type Siparis = {
    id: number;
    fiyat: string;
    kargo_kodu: string | null;
    tarih: string;
    urun: {
        id: number;
        ad: string;
        resim_url: string | null;
    };
};

type SayfalamaBaglantisi = {
    url: string | null;
    label: string;
    active: boolean;
};

defineProps<{
    siparisler: {
        data: Siparis[];
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
        links: SayfalamaBaglantisi[];
    };
}>();

const paraFormatlayici = new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY',
});

const tarihFormatlayici = new Intl.DateTimeFormat('tr-TR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
});

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
    <Head title="Siparişlerim" />

    <div
        class="yoresel-paper min-h-screen bg-sand-50 text-brand-950 dark:bg-brand-950 dark:text-sand-50"
    >
        <MagazaNavbar />

        <main
            class="mx-auto flex max-w-7xl flex-col gap-8 px-4 py-6 sm:px-6 sm:py-8 lg:px-8"
        >
            <Link
                :href="home()"
                class="flex w-fit items-center gap-2 rounded-xl text-sm font-medium text-brand-700 transition hover:text-brand-900 focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:outline-none dark:text-brand-200 dark:hover:text-brand-100"
            >
                <ArrowLeft class="size-4" aria-hidden="true" />
                Ana sayfaya dön
            </Link>

            <div class="flex flex-col gap-2">
                <p
                    class="text-sm font-semibold tracking-wide text-brand-700 uppercase dark:text-brand-300"
                >
                    Hesabım
                </p>
                <h1
                    class="font-display text-4xl font-bold tracking-tight sm:text-5xl"
                >
                    Siparişlerim
                </h1>
                <p class="text-muted-foreground">
                    {{ siparisler.total }} siparişiniz bulunuyor.
                </p>
            </div>

            <section
                v-if="siparisler.data.length"
                class="flex flex-col gap-4"
                aria-label="Sipariş listesi"
            >
                <article
                    v-for="siparis in siparisler.data"
                    :key="siparis.id"
                    class="grid gap-5 rounded-3xl border border-brand-900/10 bg-card p-4 shadow-sm transition hover:border-brand-300 hover:shadow-lg sm:grid-cols-[6rem_minmax(0,1fr)_auto] sm:items-center sm:p-5 dark:border-brand-100/10 dark:hover:border-brand-700"
                >
                    <Link
                        :href="UrunDetayController(siparis.urun.id)"
                        class="flex aspect-square items-center justify-center overflow-hidden rounded-2xl bg-sand-100 focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:outline-none dark:bg-brand-900"
                    >
                        <img
                            v-if="siparis.urun.resim_url"
                            :src="siparis.urun.resim_url"
                            :alt="siparis.urun.ad"
                            class="size-full object-cover transition duration-300 hover:scale-105"
                        />
                        <ImageIcon
                            v-else
                            class="size-8 text-brand-300 dark:text-brand-700"
                            aria-hidden="true"
                        />
                    </Link>

                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="rounded-full bg-brand-100 px-2.5 py-1 text-xs font-semibold text-brand-800 dark:bg-brand-900 dark:text-brand-200"
                            >
                                Sipariş #{{ siparis.id }}
                            </span>
                            <time
                                :datetime="siparis.tarih"
                                class="text-xs text-muted-foreground"
                            >
                                {{
                                    tarihFormatlayici.format(
                                        new Date(siparis.tarih),
                                    )
                                }}
                            </time>
                        </div>

                        <Link
                            :href="UrunDetayController(siparis.urun.id)"
                            class="mt-3 block w-fit font-display text-xl font-bold transition hover:text-brand-700 focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:outline-none dark:hover:text-brand-300"
                        >
                            {{ siparis.urun.ad }}
                        </Link>

                        <div
                            class="mt-3 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm"
                        >
                            <p class="flex items-center gap-2">
                                <ShoppingBag
                                    class="size-4 text-brand-500"
                                    aria-hidden="true"
                                />
                                <span class="text-muted-foreground">
                                    Tutar
                                </span>
                                <strong class="tabular-nums">
                                    {{
                                        paraFormatlayici.format(
                                            Number(siparis.fiyat),
                                        )
                                    }}
                                </strong>
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex min-w-56 flex-col gap-2 rounded-2xl border border-brand-200 bg-brand-50 p-4 sm:items-end dark:border-brand-800 dark:bg-brand-950"
                    >
                        <p
                            class="flex items-center gap-2 text-xs font-semibold tracking-wide text-brand-600 uppercase dark:text-brand-300"
                        >
                            <Truck class="size-4" aria-hidden="true" />
                            Kargo kodu
                        </p>
                        <code
                            v-if="siparis.kargo_kodu"
                            class="font-mono text-sm font-bold text-brand-700 dark:text-brand-300"
                        >
                            {{ siparis.kargo_kodu }}
                        </code>
                        <span
                            v-else
                            class="text-sm font-medium text-amber-700 dark:text-amber-400"
                        >
                            Hazırlanıyor
                        </span>
                    </div>
                </article>

                <div
                    v-if="siparisler.last_page > 1"
                    class="flex flex-col gap-3 border-t border-border pt-6 text-sm sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-muted-foreground">
                        Toplam {{ siparisler.total }} siparişten
                        {{ siparisler.from }}–{{ siparisler.to }} arası
                        gösteriliyor.
                    </p>
                    <nav
                        class="flex flex-wrap gap-1"
                        aria-label="Sipariş sayfaları"
                    >
                        <template
                            v-for="baglanti in siparisler.links"
                            :key="baglanti.label"
                        >
                            <Link
                                v-if="baglanti.url"
                                :href="baglanti.url"
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl border px-2.5 font-medium transition-colors hover:bg-brand-100 focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:outline-none dark:hover:bg-brand-800"
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

            <section
                v-else
                class="flex min-h-80 flex-col items-center justify-center gap-5 rounded-3xl border border-dashed border-brand-300 bg-card p-8 text-center dark:border-brand-800"
            >
                <span
                    class="flex size-16 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900 dark:text-brand-300"
                >
                    <ClipboardList class="size-8" aria-hidden="true" />
                </span>
                <div class="flex flex-col gap-2">
                    <h2 class="text-xl font-bold">Henüz siparişiniz yok</h2>
                    <p class="text-sm text-muted-foreground">
                        İlk siparişinizi verdiğinizde kargo bilgileri burada
                        görünecek.
                    </p>
                </div>
                <Link
                    :href="home()"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-primary px-5 font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                >
                    <PackageCheck class="size-5" aria-hidden="true" />
                    Ürünlere göz at
                </Link>
            </section>
        </main>
    </div>
</template>
