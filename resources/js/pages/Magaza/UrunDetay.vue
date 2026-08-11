<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    CheckCircle2,
    ChevronLeft,
    ChevronRight,
    ImageIcon,
    LogIn,
    MessageCircle,
    MessageSquarePlus,
    Minus,
    Plus,
    Quote,
    Send,
    ShoppingBasket,
} from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { create as girisSayfasi } from '@/actions/App/Http/Controllers/Kullanici/OturumController';
import { store as sepeteEkle } from '@/actions/App/Http/Controllers/Magaza/SepetController';
import { store as yorumKaydet } from '@/actions/App/Http/Controllers/Magaza/YorumController';
import MagazaNavbar from '@/components/MagazaNavbar.vue';
import MagazaUrunKarti from '@/components/MagazaUrunKarti.vue';
import { home } from '@/routes';

type UrunResmi = {
    id: number;
    url: string;
};

type Yorum = {
    id: number;
    metin: string;
    created_at: string | null;
    kullanici: {
        ad: string;
        soyad: string;
    };
};

type DigerUrun = {
    id: number;
    ad: string;
    fiyat: string;
    resim_url: string | null;
};

const props = defineProps<{
    urun: {
        id: number;
        ad: string;
        aciklama: string;
        fiyat: string;
        resimler: UrunResmi[];
        yorumlar: Yorum[];
    };
    diger_urunler: DigerUrun[];
}>();

const page = usePage();
const kullanici = computed(() => page.props.auth.kullanici);
const aktifResimIndex = ref(0);
const sepeteEkleForm = useForm(() => sepeteEkle(props.urun.id), {
    adet: 1,
});
const yorumFormu = useForm({
    metin: '',
});
const aktifResim = computed(
    () => props.urun.resimler[aktifResimIndex.value] ?? null,
);
const yorumKarakterSayisi = computed(() => yorumFormu.metin.length);

const paraFormatlayici = new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY',
});

const tarihFormatlayici = new Intl.DateTimeFormat('tr-TR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});

function oncekiResim(): void {
    if (props.urun.resimler.length < 2) {
        return;
    }

    aktifResimIndex.value =
        (aktifResimIndex.value - 1 + props.urun.resimler.length) %
        props.urun.resimler.length;
}

function sonrakiResim(): void {
    if (props.urun.resimler.length < 2) {
        return;
    }

    aktifResimIndex.value =
        (aktifResimIndex.value + 1) % props.urun.resimler.length;
}

function resimSec(index: number): void {
    aktifResimIndex.value = index;
}

function adediAzalt(): void {
    sepeteEkleForm.adet = Math.max(1, sepeteEkleForm.adet - 1);
}

function adediArtir(): void {
    sepeteEkleForm.adet = Math.min(99, sepeteEkleForm.adet + 1);
}

function adediSinirla(): void {
    const adet = Number(sepeteEkleForm.adet);
    sepeteEkleForm.adet = Number.isFinite(adet)
        ? Math.min(99, Math.max(1, Math.floor(adet)))
        : 1;
}

function urunuSepeteEkle(): void {
    adediSinirla();
    sepeteEkleForm.submit({ preserveScroll: true });
}

function yorumGonder(): void {
    yorumFormu.submit(yorumKaydet(props.urun.id), {
        preserveScroll: true,
        onSuccess: () => yorumFormu.reset(),
    });
}

function tarihiFormatla(tarih: string | null): string {
    return tarih ? tarihFormatlayici.format(new Date(tarih)) : '';
}

function basHarfler(yorum: Yorum): string {
    return (
        yorum.kullanici.ad.charAt(0) + yorum.kullanici.soyad.charAt(0)
    ).toLocaleUpperCase('tr-TR');
}

watch(
    () => props.urun.id,
    () => {
        aktifResimIndex.value = 0;
    },
);
</script>

<template>
    <Head :title="urun.ad">
        <meta name="description" :content="urun.aciklama" />
    </Head>

    <div
        class="yoresel-paper min-h-screen bg-sand-50 text-brand-950 dark:bg-brand-950 dark:text-sand-50"
    >
        <MagazaNavbar />

        <main
            class="mx-auto flex max-w-7xl flex-col gap-12 px-4 py-6 sm:px-6 sm:py-8 lg:px-8"
        >
            <Link
                :href="home()"
                class="flex w-fit items-center gap-2 rounded-xl text-sm font-medium text-brand-700 transition hover:text-brand-900 focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:outline-none dark:text-brand-200 dark:hover:text-brand-100"
            >
                <ArrowLeft class="size-4" aria-hidden="true" />
                Ana sayfaya dön
            </Link>

            <section
                class="grid items-start gap-8 lg:grid-cols-2 lg:gap-12"
                aria-labelledby="urun-basligi"
            >
                <div class="flex min-w-0 flex-col gap-4">
                    <div
                        class="relative aspect-square overflow-hidden rounded-[2rem] border border-brand-900/10 bg-card shadow-lg shadow-brand-950/5 focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:outline-none dark:border-brand-100/10"
                        role="region"
                        aria-label="Ürün görsel galerisi"
                        tabindex="0"
                        @keydown.left.prevent="oncekiResim"
                        @keydown.right.prevent="sonrakiResim"
                    >
                        <div
                            class="flex size-full items-center justify-center"
                            aria-live="polite"
                        >
                            <img
                                v-if="aktifResim"
                                :key="aktifResim.id"
                                :src="aktifResim.url"
                                :alt="
                                    urun.ad +
                                    ' - ' +
                                    (aktifResimIndex + 1) +
                                    '. görsel'
                                "
                                class="size-full object-contain"
                            />
                            <ImageIcon
                                v-else
                                class="size-20 text-brand-300 dark:text-brand-700"
                                aria-hidden="true"
                            />
                        </div>

                        <template v-if="urun.resimler.length > 1">
                            <button
                                type="button"
                                class="absolute top-1/2 left-3 flex size-10 -translate-y-1/2 items-center justify-center rounded-full bg-sand-50/90 text-brand-900 shadow-lg backdrop-blur transition hover:bg-white focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:outline-none dark:bg-brand-950/90 dark:text-sand-100 dark:hover:bg-brand-900"
                                aria-label="Önceki ürün görseli"
                                @click="oncekiResim"
                            >
                                <ChevronLeft
                                    class="size-5"
                                    aria-hidden="true"
                                />
                            </button>
                            <button
                                type="button"
                                class="absolute top-1/2 right-3 flex size-10 -translate-y-1/2 items-center justify-center rounded-full bg-sand-50/90 text-brand-900 shadow-lg backdrop-blur transition hover:bg-white focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:outline-none dark:bg-brand-950/90 dark:text-sand-100 dark:hover:bg-brand-900"
                                aria-label="Sonraki ürün görseli"
                                @click="sonrakiResim"
                            >
                                <ChevronRight
                                    class="size-5"
                                    aria-hidden="true"
                                />
                            </button>
                            <span
                                class="absolute right-4 bottom-4 rounded-full bg-brand-950/75 px-3 py-1 text-xs font-medium text-white backdrop-blur"
                            >
                                {{ aktifResimIndex + 1 }} /
                                {{ urun.resimler.length }}
                            </span>
                        </template>
                    </div>

                    <div
                        v-if="urun.resimler.length > 1"
                        class="flex gap-3 overflow-x-auto pb-1"
                        aria-label="Ürün görseli küçük resimleri"
                    >
                        <button
                            v-for="(resim, index) in urun.resimler"
                            :key="resim.id"
                            type="button"
                            class="size-20 shrink-0 overflow-hidden rounded-xl border-2 bg-card transition focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:outline-none"
                            :class="
                                index === aktifResimIndex
                                    ? 'border-brand-700 shadow-sm'
                                    : 'border-transparent opacity-70 hover:opacity-100'
                            "
                            :aria-label="index + 1 + '. ürün görselini göster'"
                            :aria-pressed="index === aktifResimIndex"
                            @click="resimSec(index)"
                        >
                            <img
                                :src="resim.url"
                                alt=""
                                class="size-full object-cover"
                            />
                        </button>
                    </div>
                </div>

                <div
                    class="flex flex-col gap-7 rounded-[2rem] border border-brand-900/10 bg-card p-6 shadow-lg shadow-brand-950/5 sm:p-8 dark:border-brand-100/10"
                >
                    <div class="flex flex-col gap-3">
                        <p
                            class="text-sm font-semibold tracking-wide text-brand-700 uppercase dark:text-brand-300"
                        >
                            Silifke Yöresel
                        </p>
                        <h1
                            id="urun-basligi"
                            class="font-display text-4xl font-bold tracking-tight text-balance sm:text-5xl"
                        >
                            {{ urun.ad }}
                        </h1>
                        <p
                            class="w-fit rounded-2xl bg-brand-100 px-4 py-2 text-3xl font-bold text-brand-800 tabular-nums dark:bg-brand-900 dark:text-brand-200"
                        >
                            {{ paraFormatlayici.format(Number(urun.fiyat)) }}
                        </p>
                    </div>

                    <div
                        class="flex flex-col gap-3 border-t border-border pt-6"
                    >
                        <h2 class="text-lg font-semibold">Ürün açıklaması</h2>
                        <p
                            class="leading-7 whitespace-pre-line text-muted-foreground"
                        >
                            {{ urun.aciklama }}
                        </p>
                    </div>

                    <form
                        class="flex flex-col gap-4 border-t border-border pt-6"
                        @submit.prevent="urunuSepeteEkle"
                    >
                        <div class="flex flex-wrap items-end gap-4">
                            <div class="flex flex-col gap-2">
                                <label
                                    for="urun-adedi"
                                    class="text-sm font-semibold"
                                >
                                    Adet
                                </label>
                                <div
                                    class="flex h-12 items-center overflow-hidden rounded-xl border border-border bg-background"
                                >
                                    <button
                                        type="button"
                                        class="flex size-12 items-center justify-center text-muted-foreground transition hover:bg-accent disabled:cursor-not-allowed disabled:opacity-40"
                                        :disabled="sepeteEkleForm.adet <= 1"
                                        aria-label="Adedi azalt"
                                        @click="adediAzalt"
                                    >
                                        <Minus
                                            class="size-4"
                                            aria-hidden="true"
                                        />
                                    </button>
                                    <input
                                        id="urun-adedi"
                                        v-model.number="sepeteEkleForm.adet"
                                        name="adet"
                                        type="number"
                                        min="1"
                                        max="99"
                                        inputmode="numeric"
                                        class="h-full w-14 border-x border-border bg-transparent text-center font-semibold tabular-nums outline-none"
                                        @change="adediSinirla"
                                    />
                                    <button
                                        type="button"
                                        class="flex size-12 items-center justify-center text-muted-foreground transition hover:bg-accent disabled:cursor-not-allowed disabled:opacity-40"
                                        :disabled="sepeteEkleForm.adet >= 99"
                                        aria-label="Adedi artır"
                                        @click="adediArtir"
                                    >
                                        <Plus
                                            class="size-4"
                                            aria-hidden="true"
                                        />
                                    </button>
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="inline-flex h-12 flex-1 items-center justify-center gap-2 rounded-xl bg-primary px-6 font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="sepeteEkleForm.processing"
                            >
                                <ShoppingBasket
                                    class="size-5"
                                    aria-hidden="true"
                                />
                                {{
                                    sepeteEkleForm.processing
                                        ? 'Ekleniyor...'
                                        : 'Sepete ekle'
                                }}
                            </button>
                        </div>

                        <p
                            v-if="sepeteEkleForm.errors.adet"
                            class="text-sm font-medium text-red-600 dark:text-red-400"
                        >
                            {{ sepeteEkleForm.errors.adet }}
                        </p>
                        <p
                            v-if="sepeteEkleForm.recentlySuccessful"
                            class="flex items-center gap-2 text-sm font-medium text-brand-700 dark:text-brand-300"
                            role="status"
                        >
                            <CheckCircle2 class="size-4" aria-hidden="true" />
                            Ürün sepete eklendi.
                        </p>
                    </form>
                </div>
            </section>

            <section
                class="flex flex-col gap-6 border-t border-border pt-10"
                aria-labelledby="yorumlar-basligi"
            >
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span
                            class="flex size-11 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900 dark:text-brand-300"
                        >
                            <MessageCircle class="size-5" aria-hidden="true" />
                        </span>
                        <div>
                            <h2
                                id="yorumlar-basligi"
                                class="font-display text-3xl font-bold tracking-tight"
                            >
                                Ürün yorumları
                            </h2>
                            <p class="text-sm text-muted-foreground">
                                {{ urun.yorumlar.length }} yorum
                            </p>
                        </div>
                    </div>
                </div>

                <form
                    v-if="kullanici"
                    class="flex flex-col gap-4 rounded-3xl border border-brand-200 bg-brand-50/70 p-5 shadow-sm sm:p-6 dark:border-brand-800 dark:bg-brand-900/50"
                    @submit.prevent="yorumGonder"
                >
                    <div class="flex items-start gap-3">
                        <span
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-700 text-white dark:bg-brand-400 dark:text-brand-950"
                        >
                            <MessageSquarePlus
                                class="size-5"
                                aria-hidden="true"
                            />
                        </span>
                        <div>
                            <h3 class="font-display text-xl font-bold">
                                Yorumunuzu paylaşın
                            </h3>
                            <p class="text-sm text-muted-foreground">
                                {{ kullanici.ad }} olarak yorum yapıyorsunuz.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <label for="yorum-metni" class="sr-only">
                            Yorumunuz
                        </label>
                        <textarea
                            id="yorum-metni"
                            v-model="yorumFormu.metin"
                            name="metin"
                            rows="4"
                            maxlength="1000"
                            required
                            placeholder="Ürünle ilgili düşüncelerinizi yazın..."
                            class="min-h-28 w-full resize-y rounded-2xl border border-brand-200 bg-card px-4 py-3 text-sm leading-6 shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-brand-500 focus-visible:ring-3 focus-visible:ring-brand-500/20 dark:border-brand-700"
                            :aria-invalid="Boolean(yorumFormu.errors.metin)"
                            aria-describedby="yorum-bilgisi"
                        />
                        <div
                            id="yorum-bilgisi"
                            class="flex items-start justify-between gap-4 text-xs"
                        >
                            <p
                                v-if="yorumFormu.errors.metin"
                                class="font-medium text-destructive"
                            >
                                {{ yorumFormu.errors.metin }}
                            </p>
                            <p v-else class="text-muted-foreground">
                                Deneyiminizi kısa ve anlaşılır şekilde paylaşın.
                            </p>
                            <span
                                class="shrink-0 text-muted-foreground tabular-nums"
                            >
                                {{ yorumKarakterSayisi }}/1000
                            </span>
                        </div>
                    </div>

                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <p
                            v-if="yorumFormu.recentlySuccessful"
                            class="flex items-center gap-2 text-sm font-medium text-brand-700 dark:text-brand-300"
                            role="status"
                        >
                            <CheckCircle2 class="size-4" aria-hidden="true" />
                            Yorumunuz eklendi.
                        </p>
                        <span v-else />
                        <button
                            type="submit"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-primary px-5 text-sm font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="yorumFormu.processing"
                        >
                            <Send class="size-4" aria-hidden="true" />
                            {{
                                yorumFormu.processing
                                    ? 'Gönderiliyor...'
                                    : 'Yorumu gönder'
                            }}
                        </button>
                    </div>
                </form>

                <div
                    v-else
                    class="flex flex-col gap-4 rounded-3xl border border-brand-200 bg-brand-50/70 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6 dark:border-brand-800 dark:bg-brand-900/50"
                >
                    <div class="flex items-center gap-3">
                        <span
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-800 dark:text-brand-200"
                        >
                            <MessageSquarePlus
                                class="size-5"
                                aria-hidden="true"
                            />
                        </span>
                        <div>
                            <h3 class="font-semibold">
                                Yorum yapmak ister misiniz?
                            </h3>
                            <p class="text-sm text-muted-foreground">
                                Yorum paylaşmak için müşteri hesabınıza giriş
                                yapın.
                            </p>
                        </div>
                    </div>
                    <Link
                        :href="girisSayfasi()"
                        class="inline-flex h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-primary px-4 text-sm font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                    >
                        <LogIn class="size-4" aria-hidden="true" />
                        Giriş yap
                    </Link>
                </div>

                <div
                    v-if="urun.yorumlar.length"
                    class="grid gap-4 md:grid-cols-2"
                >
                    <article
                        v-for="yorum in urun.yorumlar"
                        :key="yorum.id"
                        class="relative flex flex-col gap-4 rounded-2xl border border-brand-900/10 bg-card p-5 shadow-sm dark:border-brand-100/10"
                    >
                        <Quote
                            class="absolute top-5 right-5 size-7 text-brand-200 dark:text-brand-800"
                            aria-hidden="true"
                        />
                        <div class="flex items-center gap-3 pr-10">
                            <span
                                class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-700 text-sm font-semibold text-white"
                            >
                                {{ basHarfler(yorum) }}
                            </span>
                            <div class="min-w-0">
                                <h3 class="truncate font-semibold">
                                    {{ yorum.kullanici.ad }}
                                    {{ yorum.kullanici.soyad }}
                                </h3>
                                <time
                                    v-if="yorum.created_at"
                                    :datetime="yorum.created_at"
                                    class="text-xs text-muted-foreground"
                                >
                                    {{ tarihiFormatla(yorum.created_at) }}
                                </time>
                            </div>
                        </div>
                        <p
                            class="leading-6 whitespace-pre-line text-muted-foreground"
                        >
                            {{ yorum.metin }}
                        </p>
                    </article>
                </div>

                <div
                    v-else
                    class="flex min-h-44 flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-brand-300 bg-card px-6 text-center dark:border-brand-800"
                >
                    <MessageCircle
                        class="size-8 text-brand-300 dark:text-brand-700"
                        aria-hidden="true"
                    />
                    <div class="flex flex-col gap-1">
                        <h3 class="font-semibold">
                            Bu ürün için henüz yorum yapılmamış
                        </h3>
                        <p class="text-sm text-muted-foreground">
                            İlk müşteri yorumu burada görüntülenecek.
                        </p>
                    </div>
                </div>
            </section>

            <section
                class="flex flex-col gap-6 border-t border-border pt-10"
                aria-labelledby="diger-urunler-basligi"
            >
                <div class="flex flex-col gap-1">
                    <p
                        class="text-sm font-semibold tracking-wide text-brand-700 uppercase dark:text-brand-300"
                    >
                        Bunlara da göz atın
                    </p>
                    <h2
                        id="diger-urunler-basligi"
                        class="font-display text-3xl font-bold tracking-tight sm:text-4xl"
                    >
                        Diğer ürünler
                    </h2>
                </div>

                <div
                    v-if="diger_urunler.length"
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <MagazaUrunKarti
                        v-for="digerUrun in diger_urunler"
                        :key="digerUrun.id"
                        :urun="digerUrun"
                    />
                </div>
                <p
                    v-else
                    class="rounded-2xl border border-dashed border-brand-300 bg-card p-8 text-center text-sm text-muted-foreground dark:border-brand-800"
                >
                    Şimdilik gösterebileceğimiz başka ürün bulunmuyor.
                </p>
            </section>
        </main>
    </div>
</template>
