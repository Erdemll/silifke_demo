<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CheckCircle2,
    ImageIcon,
    LockKeyhole,
    ShoppingBasket,
} from '@lucide/vue';
import { computed } from 'vue';
import { create as girisSayfasi } from '@/actions/App/Http/Controllers/Kullanici/OturumController';
import { satinAl } from '@/actions/App/Http/Controllers/Magaza/SepetController';
import MagazaNavbar from '@/components/MagazaNavbar.vue';
import { home } from '@/routes';

type SepetUrunu = {
    id: number;
    ad: string;
    fiyat: string;
    adet: number;
    ara_toplam: string;
    resim_url: string | null;
};

defineProps<{
    sepet: {
        urunler: SepetUrunu[];
        toplam: string;
        toplam_adet: number;
    };
}>();

const page = usePage();
const kullanici = computed(() => page.props.auth.kullanici);
const basariMesaji = computed(() => page.props.flash.success);
const sepetHatasi = computed(() => page.props.errors.sepet);
const satinAlForm = useForm(satinAl(), {});

const paraFormatlayici = new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY',
});

function parayiFormatla(fiyat: string): string {
    return paraFormatlayici.format(Number(fiyat));
}

function siparisiTamamla(): void {
    satinAlForm.submit();
}
</script>

<template>
    <Head title="Sepet" />

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
                Alışverişe devam et
            </Link>

            <div class="flex flex-col gap-2">
                <p
                    class="text-sm font-semibold tracking-wide text-brand-700 uppercase dark:text-brand-300"
                >
                    Silifke Yöresel
                </p>
                <h1
                    class="font-display text-4xl font-bold tracking-tight sm:text-5xl"
                >
                    Sepetim
                </h1>
                <p class="text-muted-foreground">
                    Sepetinizde {{ sepet.toplam_adet }} ürün bulunuyor.
                </p>
            </div>

            <div
                v-if="basariMesaji"
                class="flex items-start gap-3 rounded-2xl border border-brand-200 bg-brand-50 p-4 text-sm font-medium text-brand-800 dark:border-brand-800 dark:bg-brand-900 dark:text-brand-200"
                role="status"
            >
                <CheckCircle2
                    class="mt-0.5 size-5 shrink-0"
                    aria-hidden="true"
                />
                {{ basariMesaji }}
            </div>

            <div
                v-if="sepet.urunler.length"
                class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]"
            >
                <section
                    class="overflow-hidden rounded-3xl border border-brand-900/10 bg-card shadow-lg shadow-brand-950/5 dark:border-brand-100/10"
                    aria-label="Sepetteki ürünler"
                >
                    <article
                        v-for="urun in sepet.urunler"
                        :key="urun.id"
                        class="grid grid-cols-[5rem_minmax(0,1fr)] gap-4 border-b border-border p-4 last:border-b-0 sm:grid-cols-[6rem_minmax(0,1fr)_auto] sm:items-center sm:p-5"
                    >
                        <div
                            class="flex aspect-square items-center justify-center overflow-hidden rounded-2xl bg-sand-100 dark:bg-brand-900"
                        >
                            <img
                                v-if="urun.resim_url"
                                :src="urun.resim_url"
                                :alt="urun.ad"
                                class="size-full object-cover"
                            />
                            <ImageIcon
                                v-else
                                class="size-8 text-brand-300 dark:text-brand-700"
                                aria-hidden="true"
                            />
                        </div>

                        <div class="min-w-0">
                            <h2 class="font-display font-bold sm:text-lg">
                                {{ urun.ad }}
                            </h2>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Birim fiyat: {{ parayiFormatla(urun.fiyat) }}
                            </p>
                            <p class="mt-2 text-sm font-semibold">
                                Adet: {{ urun.adet }}
                            </p>
                        </div>

                        <p
                            class="col-start-2 font-bold text-brand-700 tabular-nums sm:col-auto sm:rounded-xl sm:bg-brand-100 sm:px-3 sm:py-1.5 sm:text-lg dark:text-brand-300 dark:sm:bg-brand-900"
                        >
                            {{ parayiFormatla(urun.ara_toplam) }}
                        </p>
                    </article>
                </section>

                <aside
                    class="flex flex-col gap-5 rounded-3xl border border-brand-900/10 bg-card p-6 shadow-lg shadow-brand-950/5 lg:sticky lg:top-24 dark:border-brand-100/10"
                    aria-labelledby="sepet-ozeti-basligi"
                >
                    <h2
                        id="sepet-ozeti-basligi"
                        class="font-display text-2xl font-bold"
                    >
                        Sepet özeti
                    </h2>
                    <div
                        class="flex items-center justify-between gap-4 border-y border-border py-4"
                    >
                        <span class="text-muted-foreground">
                            Toplam ({{ sepet.toplam_adet }} ürün)
                        </span>
                        <strong
                            class="text-xl text-brand-700 tabular-nums dark:text-brand-300"
                        >
                            {{ parayiFormatla(sepet.toplam) }}
                        </strong>
                    </div>

                    <form
                        v-if="kullanici"
                        class="flex flex-col gap-3"
                        @submit.prevent="siparisiTamamla"
                    >
                        <button
                            type="submit"
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-xl bg-primary px-6 font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="satinAlForm.processing"
                        >
                            <ShoppingBasket class="size-5" aria-hidden="true" />
                            {{
                                satinAlForm.processing
                                    ? 'İşleniyor...'
                                    : 'Satın al'
                            }}
                        </button>
                        <p
                            v-if="sepetHatasi"
                            class="text-sm font-medium text-red-600 dark:text-red-400"
                        >
                            {{ sepetHatasi }}
                        </p>
                    </form>

                    <div v-else class="flex flex-col gap-3">
                        <p
                            class="flex items-start gap-2 text-sm text-muted-foreground"
                        >
                            <LockKeyhole
                                class="mt-0.5 size-4 shrink-0"
                                aria-hidden="true"
                            />
                            Satın alma işlemi için müşteri hesabınıza giriş
                            yapın.
                        </p>
                        <Link
                            :href="girisSayfasi()"
                            class="inline-flex h-12 items-center justify-center rounded-xl bg-primary px-6 font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                        >
                            Giriş yap
                        </Link>
                    </div>

                    <p class="text-xs leading-5 text-muted-foreground">
                        Bu demo işleminde ödeme alınmaz. Siparişler doğrudan
                        oluşturulur.
                    </p>
                </aside>
            </div>

            <section
                v-else
                class="flex min-h-80 flex-col items-center justify-center gap-5 rounded-3xl border border-dashed border-brand-300 bg-card p-8 text-center dark:border-brand-800"
            >
                <span
                    class="flex size-16 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900 dark:text-brand-300"
                >
                    <ShoppingBasket class="size-8" aria-hidden="true" />
                </span>
                <div class="flex flex-col gap-2">
                    <h2 class="text-xl font-bold">Sepetiniz şu anda boş</h2>
                    <p class="text-sm text-muted-foreground">
                        Yöresel ürünleri keşfederek sepetinizi oluşturmaya
                        başlayın.
                    </p>
                </div>
                <Link
                    :href="home()"
                    class="inline-flex h-11 items-center justify-center rounded-xl bg-primary px-5 font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                >
                    Ürünlere göz at
                </Link>
            </section>
        </main>
    </div>
</template>
