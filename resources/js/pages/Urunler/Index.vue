<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    ImageIcon,
    ImagePlus,
    Package,
    Pencil,
    Plus,
    Search,
    Trash2,
    X,
} from '@lucide/vue';
import { computed, onBeforeUnmount, ref } from 'vue';
import UrunController from '@/actions/App/Http/Controllers/UrunController';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { dashboard } from '@/routes';

type UrunResmi = {
    id: number;
    url: string;
};

type Urun = {
    id: number;
    ad: string;
    aciklama: string;
    fiyat: string;
    created_at: string | null;
    resimler: UrunResmi[];
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

type ResimOnizlemesi = {
    anahtar: number;
    ad: string;
    url: string;
};

const props = defineProps<{
    urunler: SayfalanmisUrunler;
    filters: {
        ara: string;
    };
}>();

const arama = ref(props.filters.ara);
const eklemeModalAcik = ref(false);
const duzenlemeModalAcik = ref(false);
const silmeModalAcik = ref(false);
const duzenlenenUrun = ref<Urun | null>(null);
const silinenUrun = ref<Urun | null>(null);
const eklemeOnizlemeleri = ref<ResimOnizlemesi[]>([]);
const duzenlemeOnizlemeleri = ref<ResimOnizlemesi[]>([]);
let onizlemeSayaci = 0;

const eklemeFormu = useForm({
    ad: '',
    aciklama: '',
    fiyat: '',
    resimler: [] as File[],
});

const duzenlemeFormu = useForm({
    ad: '',
    aciklama: '',
    fiyat: '',
    resimler: [] as File[],
    silinen_resim_ids: [] as number[],
});

const silmeFormu = useForm({});

const paraFormatlayici = new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY',
});

const gorunenMevcutResimler = computed(() =>
    (duzenlenenUrun.value?.resimler ?? []).filter(
        (resim) => !duzenlemeFormu.silinen_resim_ids.includes(resim.id),
    ),
);

const eklemeResimHatasi = computed(() => resimHatasiniBul(eklemeFormu.errors));

const duzenlemeResimHatasi = computed(() =>
    resimHatasiniBul(duzenlemeFormu.errors),
);

const silmeHatasi = computed(
    () => (silmeFormu.errors as Record<string, string>).urun_silme,
);

function resimHatasiniBul(hatalar: Record<string, string>): string | undefined {
    return (
        hatalar.resimler ??
        Object.entries(hatalar).find(([alan]) =>
            alan.startsWith('resimler.'),
        )?.[1]
    );
}

function secilenDosyalariAl(event: Event): File[] {
    const input = event.target as HTMLInputElement;
    const dosyalar = Array.from(input.files ?? []);

    input.value = '';

    return dosyalar;
}

function onizlemelerOlustur(dosyalar: File[]): ResimOnizlemesi[] {
    return dosyalar.map((dosya) => ({
        anahtar: ++onizlemeSayaci,
        ad: dosya.name,
        url: URL.createObjectURL(dosya),
    }));
}

function onizlemeleriTemizle(onizlemeler: ResimOnizlemesi[]): void {
    onizlemeler.forEach((onizleme) => URL.revokeObjectURL(onizleme.url));
}

function eklemeResimleriSec(event: Event): void {
    const dosyalar = secilenDosyalariAl(event);
    const kalanHak = 8 - eklemeFormu.resimler.length;

    if (dosyalar.length > kalanHak) {
        eklemeFormu.setError(
            'resimler',
            'Bir ürüne en fazla 8 resim ekleyebilirsiniz.',
        );
    }

    const eklenecekDosyalar = dosyalar.slice(0, kalanHak);

    eklemeFormu.resimler = [...eklemeFormu.resimler, ...eklenecekDosyalar];
    eklemeOnizlemeleri.value.push(...onizlemelerOlustur(eklenecekDosyalar));

    if (dosyalar.length <= kalanHak) {
        eklemeFormu.clearErrors('resimler');
    }
}

function eklemeResminiKaldir(index: number): void {
    const [onizleme] = eklemeOnizlemeleri.value.splice(index, 1);

    if (onizleme) {
        URL.revokeObjectURL(onizleme.url);
    }

    eklemeFormu.resimler.splice(index, 1);
    eklemeFormu.clearErrors('resimler');
}

function duzenlemeResimleriSec(event: Event): void {
    const dosyalar = secilenDosyalariAl(event);
    const kalanHak =
        8 - gorunenMevcutResimler.value.length - duzenlemeFormu.resimler.length;

    if (dosyalar.length > kalanHak) {
        duzenlemeFormu.setError(
            'resimler',
            'Bir ürüne en fazla 8 resim ekleyebilirsiniz.',
        );
    }

    const eklenecekDosyalar = dosyalar.slice(0, Math.max(kalanHak, 0));

    duzenlemeFormu.resimler = [
        ...duzenlemeFormu.resimler,
        ...eklenecekDosyalar,
    ];
    duzenlemeOnizlemeleri.value.push(...onizlemelerOlustur(eklenecekDosyalar));

    if (dosyalar.length <= kalanHak) {
        duzenlemeFormu.clearErrors('resimler');
    }
}

function yeniDuzenlemeResminiKaldir(index: number): void {
    const [onizleme] = duzenlemeOnizlemeleri.value.splice(index, 1);

    if (onizleme) {
        URL.revokeObjectURL(onizleme.url);
    }

    duzenlemeFormu.resimler.splice(index, 1);
    duzenlemeFormu.clearErrors('resimler');
}

function mevcutResmiKaldir(resimId: number): void {
    duzenlemeFormu.silinen_resim_ids.push(resimId);
    duzenlemeFormu.clearErrors('resimler');
}

function eklemeModaliniAc(): void {
    eklemeFormunuSifirla();
    eklemeModalAcik.value = true;
}

function duzenlemeModaliniAc(urun: Urun): void {
    duzenlemeFormunuSifirla();
    duzenlenenUrun.value = urun;
    duzenlemeFormu.ad = urun.ad;
    duzenlemeFormu.aciklama = urun.aciklama;
    duzenlemeFormu.fiyat = urun.fiyat;
    duzenlemeModalAcik.value = true;
}

function silmeModaliniAc(urun: Urun): void {
    silmeFormu.clearErrors();
    silinenUrun.value = urun;
    silmeModalAcik.value = true;
}

function eklemeFormunuSifirla(): void {
    onizlemeleriTemizle(eklemeOnizlemeleri.value);
    eklemeOnizlemeleri.value = [];
    eklemeFormu.reset();
    eklemeFormu.clearErrors();
}

function duzenlemeFormunuSifirla(): void {
    onizlemeleriTemizle(duzenlemeOnizlemeleri.value);
    duzenlemeOnizlemeleri.value = [];
    duzenlemeFormu.reset();
    duzenlemeFormu.clearErrors();
    duzenlenenUrun.value = null;
}

function eklemeModalDurumuDegisti(acik: boolean): void {
    eklemeModalAcik.value = acik;

    if (!acik) {
        eklemeFormunuSifirla();
    }
}

function duzenlemeModalDurumuDegisti(acik: boolean): void {
    duzenlemeModalAcik.value = acik;

    if (!acik) {
        duzenlemeFormunuSifirla();
    }
}

function silmeModalDurumuDegisti(acik: boolean): void {
    silmeModalAcik.value = acik;

    if (!acik) {
        silmeFormu.clearErrors();
        silinenUrun.value = null;
    }
}

function urunEkle(): void {
    eklemeFormu.submit(UrunController.store(), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            eklemeModalAcik.value = false;
            eklemeFormunuSifirla();
        },
    });
}

function urunGuncelle(): void {
    if (!duzenlenenUrun.value) {
        return;
    }

    duzenlemeFormu
        .transform((veri) => ({ ...veri, _method: 'put' }))
        .post(UrunController.update.url(duzenlenenUrun.value.id), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                duzenlemeModalAcik.value = false;
                duzenlemeFormunuSifirla();
            },
            onFinish: () => duzenlemeFormu.transform((veri) => veri),
        });
}

function urunSil(): void {
    if (!silinenUrun.value) {
        return;
    }

    silmeFormu.submit(UrunController.destroy(silinenUrun.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            silmeModalAcik.value = false;
            silinenUrun.value = null;
        },
    });
}

function aramaYap(): void {
    router.get(
        UrunController.index.url(),
        { ara: arama.value || undefined },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}

function aramayiTemizle(): void {
    arama.value = '';
    aramaYap();
}

function sayfaEtiketi(etiket: string): string {
    if (etiket.includes('Previous')) {
        return 'Önceki';
    }

    if (etiket.includes('Next')) {
        return 'Sonraki';
    }

    return etiket;
}

onBeforeUnmount(() => {
    onizlemeleriTemizle(eklemeOnizlemeleri.value);
    onizlemeleriTemizle(duzenlemeOnizlemeleri.value);
});

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Yönetim Paneli',
                href: dashboard(),
            },
            {
                title: 'Ürünler',
                href: UrunController.index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Ürünler" />

    <main class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
        >
            <div class="flex flex-col gap-1">
                <p
                    class="text-xs font-semibold tracking-[0.18em] text-brand-700 uppercase dark:text-brand-300"
                >
                    Mağaza yönetimi
                </p>
                <h1 class="font-display text-3xl font-bold tracking-tight">
                    Ürünler
                </h1>
                <p class="text-sm text-muted-foreground">
                    Mağazadaki ürünleri, fiyatları ve görselleri yönetin.
                </p>
            </div>
            <Button type="button" @click="eklemeModaliniAc">
                <Plus class="size-4" />
                Yeni ürün ekle
            </Button>
        </div>

        <form
            class="flex w-full max-w-xl flex-col gap-2 sm:flex-row"
            @submit.prevent="aramaYap"
        >
            <div class="relative flex-1">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="arama"
                    type="search"
                    class="pl-9"
                    placeholder="Ürün adına göre ara..."
                    aria-label="Ürün ara"
                />
            </div>
            <div class="flex gap-2">
                <Button type="submit" variant="secondary">Ara</Button>
                <Button
                    v-if="filters.ara"
                    type="button"
                    variant="ghost"
                    @click="aramayiTemizle"
                >
                    Temizle
                </Button>
            </div>
        </form>

        <Card
            class="overflow-hidden border-brand-900/10 bg-card/90 py-0 shadow-sm dark:border-brand-100/10"
        >
            <CardContent class="p-0">
                <div v-if="urunler.data.length" class="overflow-x-auto">
                    <table class="w-full min-w-3xl text-sm">
                        <thead
                            class="border-b bg-brand-50 text-left text-brand-800 dark:bg-brand-900 dark:text-brand-200"
                        >
                            <tr>
                                <th class="px-4 py-3 font-medium">Ürün</th>
                                <th class="px-4 py-3 font-medium">Açıklama</th>
                                <th class="px-4 py-3 font-medium">Fiyat</th>
                                <th class="px-4 py-3 font-medium">Görseller</th>
                                <th class="px-4 py-3 text-right font-medium">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="urun in urunler.data"
                                :key="urun.id"
                                class="transition-colors hover:bg-brand-50/70 dark:hover:bg-brand-900/50"
                            >
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img
                                            v-if="urun.resimler[0]"
                                            :src="urun.resimler[0].url"
                                            :alt="urun.ad"
                                            class="size-12 rounded-xl border border-brand-900/10 object-cover dark:border-brand-100/10"
                                        />
                                        <div
                                            v-else
                                            class="flex size-12 items-center justify-center rounded-xl border border-brand-900/10 bg-brand-50 dark:border-brand-100/10 dark:bg-brand-900"
                                        >
                                            <ImageIcon
                                                class="size-5 text-muted-foreground"
                                            />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-display font-bold">
                                                {{ urun.ad }}
                                            </p>
                                            <p
                                                class="text-xs text-muted-foreground"
                                            >
                                                #{{ urun.id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="max-w-sm px-4 py-3">
                                    <p
                                        class="line-clamp-2 text-muted-foreground"
                                    >
                                        {{ urun.aciklama }}
                                    </p>
                                </td>
                                <td
                                    class="px-4 py-3 font-medium whitespace-nowrap tabular-nums"
                                >
                                    {{
                                        paraFormatlayici.format(
                                            Number(urun.fiyat),
                                        )
                                    }}
                                </td>
                                <td class="px-4 py-3">
                                    <Badge variant="secondary">
                                        {{ urun.resimler.length }} resim
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-1">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon-sm"
                                            :aria-label="`${urun.ad} ürününü düzenle`"
                                            @click="duzenlemeModaliniAc(urun)"
                                        >
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon-sm"
                                            class="text-destructive hover:text-destructive"
                                            :aria-label="`${urun.ad} ürününü sil`"
                                            @click="silmeModaliniAc(urun)"
                                        >
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-else
                    class="flex min-h-72 flex-col items-center justify-center gap-3 px-6 text-center"
                >
                    <div
                        class="flex size-12 items-center justify-center rounded-2xl bg-brand-100 dark:bg-brand-900"
                    >
                        <Package
                            class="size-6 text-brand-700 dark:text-brand-300"
                        />
                    </div>
                    <div class="flex max-w-md flex-col gap-1">
                        <h2 class="font-medium">
                            {{
                                filters.ara
                                    ? 'Aramanızla eşleşen ürün bulunamadı'
                                    : 'Henüz ürün eklenmedi'
                            }}
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            {{
                                filters.ara
                                    ? 'Farklı bir ürün adıyla tekrar arayabilirsiniz.'
                                    : 'İlk ürünü ekleyerek mağaza kataloğunu oluşturmaya başlayın.'
                            }}
                        </p>
                    </div>
                    <Button
                        v-if="!filters.ara"
                        type="button"
                        variant="outline"
                        @click="eklemeModaliniAc"
                    >
                        <Plus class="size-4" />
                        Yeni ürün ekle
                    </Button>
                </div>
            </CardContent>
        </Card>

        <div
            v-if="urunler.last_page > 1"
            class="flex flex-col gap-3 text-sm sm:flex-row sm:items-center sm:justify-between"
        >
            <p class="text-muted-foreground">
                Toplam {{ urunler.total }} üründen {{ urunler.from }}–{{
                    urunler.to
                }}
                arası gösteriliyor.
            </p>
            <nav class="flex flex-wrap gap-1" aria-label="Ürün sayfaları">
                <template
                    v-for="baglanti in urunler.links"
                    :key="baglanti.label"
                >
                    <Link
                        v-if="baglanti.url"
                        :href="baglanti.url"
                        preserve-scroll
                        class="inline-flex h-8 min-w-8 items-center justify-center rounded-xl border px-2 text-sm transition-colors hover:bg-accent"
                        :class="
                            baglanti.active
                                ? 'border-primary bg-primary text-primary-foreground hover:bg-primary/90'
                                : 'bg-background'
                        "
                    >
                        {{ sayfaEtiketi(baglanti.label) }}
                    </Link>
                    <span
                        v-else
                        class="inline-flex h-8 min-w-8 items-center justify-center rounded-xl border px-2 text-sm text-muted-foreground opacity-50"
                    >
                        {{ sayfaEtiketi(baglanti.label) }}
                    </span>
                </template>
            </nav>
        </div>
    </main>

    <Dialog :open="eklemeModalAcik" @update:open="eklemeModalDurumuDegisti">
        <DialogContent
            class="max-h-[90vh] overflow-y-auto border-brand-900/10 sm:max-w-3xl dark:border-brand-100/10"
        >
            <form class="grid gap-6" @submit.prevent="urunEkle">
                <DialogHeader>
                    <DialogTitle class="font-display text-2xl">
                        Yeni ürün ekle
                    </DialogTitle>
                    <DialogDescription>
                        Ürün bilgilerini girin ve en az bir ürün resmi seçin.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-5">
                    <div class="grid gap-2">
                        <Label for="ekleme-ad">Ürün adı</Label>
                        <Input
                            id="ekleme-ad"
                            v-model="eklemeFormu.ad"
                            maxlength="255"
                            autocomplete="off"
                            placeholder="Örn. Silifke Yoğurdu"
                            :aria-invalid="Boolean(eklemeFormu.errors.ad)"
                        />
                        <InputError :message="eklemeFormu.errors.ad" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="ekleme-aciklama">Açıklama</Label>
                        <textarea
                            id="ekleme-aciklama"
                            v-model="eklemeFormu.aciklama"
                            rows="4"
                            maxlength="5000"
                            placeholder="Ürünün özelliklerini ve içeriğini açıklayın."
                            class="min-h-24 w-full resize-y rounded-xl border border-input bg-card/70 px-3 py-2 text-sm shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30"
                            :aria-invalid="Boolean(eklemeFormu.errors.aciklama)"
                        ></textarea>
                        <InputError :message="eklemeFormu.errors.aciklama" />
                    </div>

                    <div class="grid gap-2 sm:max-w-xs">
                        <Label for="ekleme-fiyat">Fiyat</Label>
                        <div class="relative">
                            <Input
                                id="ekleme-fiyat"
                                v-model="eklemeFormu.fiyat"
                                type="number"
                                min="0.01"
                                max="99999999.99"
                                step="0.01"
                                class="pr-10"
                                placeholder="0,00"
                                :aria-invalid="
                                    Boolean(eklemeFormu.errors.fiyat)
                                "
                            />
                            <span
                                class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-sm text-muted-foreground"
                            >
                                ₺
                            </span>
                        </div>
                        <InputError :message="eklemeFormu.errors.fiyat" />
                    </div>

                    <div class="grid gap-3">
                        <div class="flex items-center justify-between gap-3">
                            <Label for="ekleme-resimler">Ürün resimleri</Label>
                            <span class="text-xs text-muted-foreground">
                                {{ eklemeFormu.resimler.length }}/8
                            </span>
                        </div>
                        <label
                            for="ekleme-resimler"
                            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border border-dashed border-brand-300 bg-brand-50/50 p-6 text-center transition-colors hover:bg-brand-100/70 dark:border-brand-700 dark:bg-brand-900/40 dark:hover:bg-brand-900"
                        >
                            <ImagePlus class="size-6 text-muted-foreground" />
                            <span class="text-sm font-medium">
                                Bir veya daha fazla resim seçin
                            </span>
                            <span class="text-xs text-muted-foreground">
                                JPG, PNG veya WebP · Her biri en fazla 4 MB
                            </span>
                            <input
                                id="ekleme-resimler"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                multiple
                                class="sr-only"
                                @change="eklemeResimleriSec"
                            />
                        </label>
                        <InputError :message="eklemeResimHatasi" />

                        <div
                            v-if="eklemeOnizlemeleri.length"
                            class="grid grid-cols-2 gap-3 sm:grid-cols-4"
                        >
                            <div
                                v-for="(onizleme, index) in eklemeOnizlemeleri"
                                :key="onizleme.anahtar"
                                class="group relative aspect-square overflow-hidden rounded-lg border bg-muted"
                            >
                                <img
                                    :src="onizleme.url"
                                    :alt="onizleme.ad"
                                    class="size-full object-cover"
                                />
                                <Button
                                    type="button"
                                    variant="destructive"
                                    size="icon-sm"
                                    class="absolute top-2 right-2 opacity-90"
                                    :aria-label="`${onizleme.ad} resmini kaldır`"
                                    @click="eklemeResminiKaldir(index)"
                                >
                                    <X class="size-4" />
                                </Button>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="eklemeFormu.progress"
                        class="grid gap-2 text-xs text-muted-foreground"
                    >
                        <div class="flex justify-between">
                            <span>Resimler yükleniyor</span>
                            <span>{{ eklemeFormu.progress.percentage }}%</span>
                        </div>
                        <progress
                            class="h-2 w-full overflow-hidden rounded-full"
                            :value="eklemeFormu.progress.percentage"
                            max="100"
                        ></progress>
                    </div>
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="eklemeFormu.processing"
                        @click="eklemeModalDurumuDegisti(false)"
                    >
                        Vazgeç
                    </Button>
                    <Button type="submit" :disabled="eklemeFormu.processing">
                        <Spinner v-if="eklemeFormu.processing" />
                        Ürünü kaydet
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <Dialog
        :open="duzenlemeModalAcik"
        @update:open="duzenlemeModalDurumuDegisti"
    >
        <DialogContent
            class="max-h-[90vh] overflow-y-auto border-brand-900/10 sm:max-w-3xl dark:border-brand-100/10"
        >
            <form class="grid gap-6" @submit.prevent="urunGuncelle">
                <DialogHeader>
                    <DialogTitle class="font-display text-2xl">
                        Ürünü düzenle
                    </DialogTitle>
                    <DialogDescription>
                        Ürün bilgilerini ve ürünün görsel galerisini
                        güncelleyin.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-5">
                    <div class="grid gap-2">
                        <Label for="duzenleme-ad">Ürün adı</Label>
                        <Input
                            id="duzenleme-ad"
                            v-model="duzenlemeFormu.ad"
                            maxlength="255"
                            autocomplete="off"
                            :aria-invalid="Boolean(duzenlemeFormu.errors.ad)"
                        />
                        <InputError :message="duzenlemeFormu.errors.ad" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="duzenleme-aciklama">Açıklama</Label>
                        <textarea
                            id="duzenleme-aciklama"
                            v-model="duzenlemeFormu.aciklama"
                            rows="4"
                            maxlength="5000"
                            class="min-h-24 w-full resize-y rounded-xl border border-input bg-card/70 px-3 py-2 text-sm shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30"
                            :aria-invalid="
                                Boolean(duzenlemeFormu.errors.aciklama)
                            "
                        ></textarea>
                        <InputError :message="duzenlemeFormu.errors.aciklama" />
                    </div>

                    <div class="grid gap-2 sm:max-w-xs">
                        <Label for="duzenleme-fiyat">Fiyat</Label>
                        <div class="relative">
                            <Input
                                id="duzenleme-fiyat"
                                v-model="duzenlemeFormu.fiyat"
                                type="number"
                                min="0.01"
                                max="99999999.99"
                                step="0.01"
                                class="pr-10"
                                :aria-invalid="
                                    Boolean(duzenlemeFormu.errors.fiyat)
                                "
                            />
                            <span
                                class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-sm text-muted-foreground"
                            >
                                ₺
                            </span>
                        </div>
                        <InputError :message="duzenlemeFormu.errors.fiyat" />
                    </div>

                    <div class="grid gap-3">
                        <div class="flex items-center justify-between gap-3">
                            <Label for="duzenleme-resimler">
                                Ürün resimleri
                            </Label>
                            <span class="text-xs text-muted-foreground">
                                {{
                                    gorunenMevcutResimler.length +
                                    duzenlemeFormu.resimler.length
                                }}/8
                            </span>
                        </div>

                        <div
                            v-if="
                                gorunenMevcutResimler.length ||
                                duzenlemeOnizlemeleri.length
                            "
                            class="grid grid-cols-2 gap-3 sm:grid-cols-4"
                        >
                            <div
                                v-for="resim in gorunenMevcutResimler"
                                :key="`mevcut-${resim.id}`"
                                class="group relative aspect-square overflow-hidden rounded-lg border bg-muted"
                            >
                                <img
                                    :src="resim.url"
                                    :alt="duzenlenenUrun?.ad ?? 'Ürün resmi'"
                                    class="size-full object-cover"
                                />
                                <Badge
                                    class="absolute bottom-2 left-2"
                                    variant="secondary"
                                >
                                    Mevcut
                                </Badge>
                                <Button
                                    type="button"
                                    variant="destructive"
                                    size="icon-sm"
                                    class="absolute top-2 right-2 opacity-90"
                                    aria-label="Mevcut resmi kaldır"
                                    @click="mevcutResmiKaldir(resim.id)"
                                >
                                    <X class="size-4" />
                                </Button>
                            </div>

                            <div
                                v-for="(
                                    onizleme, index
                                ) in duzenlemeOnizlemeleri"
                                :key="onizleme.anahtar"
                                class="group relative aspect-square overflow-hidden rounded-lg border bg-muted"
                            >
                                <img
                                    :src="onizleme.url"
                                    :alt="onizleme.ad"
                                    class="size-full object-cover"
                                />
                                <Badge class="absolute bottom-2 left-2">
                                    Yeni
                                </Badge>
                                <Button
                                    type="button"
                                    variant="destructive"
                                    size="icon-sm"
                                    class="absolute top-2 right-2 opacity-90"
                                    :aria-label="`${onizleme.ad} resmini kaldır`"
                                    @click="yeniDuzenlemeResminiKaldir(index)"
                                >
                                    <X class="size-4" />
                                </Button>
                            </div>
                        </div>

                        <label
                            for="duzenleme-resimler"
                            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border border-dashed p-5 text-center transition-colors hover:bg-muted/50"
                        >
                            <ImagePlus class="size-6 text-muted-foreground" />
                            <span class="text-sm font-medium">
                                Galeriye yeni resimler ekleyin
                            </span>
                            <span class="text-xs text-muted-foreground">
                                JPG, PNG veya WebP · Her biri en fazla 4 MB
                            </span>
                            <input
                                id="duzenleme-resimler"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                multiple
                                class="sr-only"
                                @change="duzenlemeResimleriSec"
                            />
                        </label>
                        <InputError :message="duzenlemeResimHatasi" />
                        <InputError
                            :message="duzenlemeFormu.errors.silinen_resim_ids"
                        />
                    </div>

                    <div
                        v-if="duzenlemeFormu.progress"
                        class="grid gap-2 text-xs text-muted-foreground"
                    >
                        <div class="flex justify-between">
                            <span>Resimler yükleniyor</span>
                            <span
                                >{{ duzenlemeFormu.progress.percentage }}%</span
                            >
                        </div>
                        <progress
                            class="h-2 w-full overflow-hidden rounded-full"
                            :value="duzenlemeFormu.progress.percentage"
                            max="100"
                        ></progress>
                    </div>
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="duzenlemeFormu.processing"
                        @click="duzenlemeModalDurumuDegisti(false)"
                    >
                        Vazgeç
                    </Button>
                    <Button type="submit" :disabled="duzenlemeFormu.processing">
                        <Spinner v-if="duzenlemeFormu.processing" />
                        Değişiklikleri kaydet
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <Dialog :open="silmeModalAcik" @update:open="silmeModalDurumuDegisti">
        <DialogContent>
            <DialogHeader>
                <DialogTitle
                    >Ürünü silmek istediğinize emin misiniz?</DialogTitle
                >
                <DialogDescription>
                    <strong class="font-medium text-foreground">
                        {{ silinenUrun?.ad }}
                    </strong>
                    ürünü, yorumları ve tüm görselleri kalıcı olarak silinecek.
                    Bu işlem geri alınamaz.
                </DialogDescription>
            </DialogHeader>

            <InputError :message="silmeHatasi" />

            <DialogFooter>
                <Button
                    type="button"
                    variant="outline"
                    :disabled="silmeFormu.processing"
                    @click="silmeModalDurumuDegisti(false)"
                >
                    Vazgeç
                </Button>
                <Button
                    type="button"
                    variant="destructive"
                    :disabled="silmeFormu.processing"
                    @click="urunSil"
                >
                    <Spinner v-if="silmeFormu.processing" />
                    Ürünü sil
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
