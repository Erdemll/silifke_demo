<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ClipboardList, PackageOpen, Truck } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as siparislerIndex } from '@/routes/siparisler';

type Siparis = {
    id: number;
    fiyat: string;
    kargo_kodu: string | null;
    tarih: string;
    kullanici: {
        ad: string;
        soyad: string;
        mail: string;
    };
    urun: {
        id: number;
        ad: string;
    };
};

type SayfalamaBaglantisi = {
    url: string | null;
    label: string;
    active: boolean;
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Yönetim Paneli',
                href: dashboard(),
            },
            {
                title: 'Siparişler',
                href: siparislerIndex(),
            },
        ],
    },
});

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
    <Head title="Siparişler" />

    <main class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex flex-col gap-1">
                <p
                    class="text-xs font-semibold tracking-[0.18em] text-brand-700 uppercase dark:text-brand-300"
                >
                    Mağaza yönetimi
                </p>
                <h1 class="font-display text-3xl font-bold tracking-tight">
                    Siparişler
                </h1>
                <p class="text-sm text-muted-foreground">
                    Müşteri siparişlerini ve kargo kodlarını takip edin.
                </p>
            </div>
            <span
                class="flex size-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900 dark:text-brand-300"
            >
                <ClipboardList class="size-6" aria-hidden="true" />
            </span>
        </div>

        <Card
            class="border-brand-900/10 bg-card/90 shadow-sm dark:border-brand-100/10"
        >
            <CardHeader
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex flex-col gap-1.5">
                    <CardTitle
                        class="flex items-center gap-2 font-display text-xl"
                    >
                        <ClipboardList
                            class="size-5 text-brand-600 dark:text-brand-300"
                            aria-hidden="true"
                        />
                        Sipariş listesi
                    </CardTitle>
                    <CardDescription>
                        En yeni siparişler ilk sırada gösterilir.
                    </CardDescription>
                </div>
                <Badge variant="secondary" class="w-fit">
                    {{ siparisler.total }} sipariş
                </Badge>
            </CardHeader>

            <CardContent v-if="siparisler.data.length" class="px-0">
                <div class="overflow-x-auto border-y">
                    <table class="w-full min-w-200 text-left text-sm">
                        <thead
                            class="bg-brand-50 text-brand-800 dark:bg-brand-900 dark:text-brand-200"
                        >
                            <tr>
                                <th class="px-6 py-3 font-medium">
                                    Sipariş eden
                                </th>
                                <th class="px-6 py-3 font-medium">E-posta</th>
                                <th class="px-6 py-3 font-medium">Ürün</th>
                                <th class="px-6 py-3 text-right font-medium">
                                    Fiyat
                                </th>
                                <th class="px-6 py-3 font-medium">
                                    Kargo kodu
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="siparis in siparisler.data"
                                :key="siparis.id"
                                class="transition-colors hover:bg-brand-50/70 dark:hover:bg-brand-900/50"
                            >
                                <td class="px-6 py-4 font-medium">
                                    {{ siparis.kullanici.ad }}
                                    {{ siparis.kullanici.soyad }}
                                </td>
                                <td class="px-6 py-4 text-muted-foreground">
                                    {{ siparis.kullanici.mail }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ siparis.urun.ad }}
                                </td>
                                <td
                                    class="px-6 py-4 text-right font-semibold whitespace-nowrap tabular-nums"
                                >
                                    {{
                                        paraFormatlayici.format(
                                            Number(siparis.fiyat),
                                        )
                                    }}
                                </td>
                                <td class="px-6 py-4">
                                    <Badge
                                        v-if="siparis.kargo_kodu"
                                        variant="outline"
                                        class="gap-1.5 border-brand-200 bg-brand-50 font-mono text-brand-800 dark:border-brand-800 dark:bg-brand-900 dark:text-brand-200"
                                    >
                                        <Truck
                                            class="size-3"
                                            aria-hidden="true"
                                        />
                                        {{ siparis.kargo_kodu }}
                                    </Badge>
                                    <Badge v-else variant="secondary">
                                        Hazırlanıyor
                                    </Badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    class="flex flex-col gap-3 px-6 pt-6 text-sm sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-muted-foreground">
                        Toplam {{ siparisler.total }} siparişten
                        {{ siparisler.from }}–{{ siparisler.to }} arası
                        gösteriliyor.
                    </p>

                    <nav
                        v-if="siparisler.last_page > 1"
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
                                preserve-scroll
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl border bg-background px-2.5 font-medium transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                                :class="
                                    baglanti.active
                                        ? 'border-primary bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground'
                                        : ''
                                "
                                :aria-current="
                                    baglanti.active ? 'page' : undefined
                                "
                            >
                                {{ sayfaEtiketi(baglanti.label) }}
                            </Link>
                            <span
                                v-else
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl border px-2.5 text-muted-foreground opacity-50"
                            >
                                {{ sayfaEtiketi(baglanti.label) }}
                            </span>
                        </template>
                    </nav>
                </div>
            </CardContent>

            <CardContent
                v-else
                class="flex min-h-72 flex-col items-center justify-center gap-4 border-t text-center"
            >
                <span
                    class="flex size-14 items-center justify-center rounded-2xl bg-brand-100"
                >
                    <PackageOpen
                        class="size-7 text-brand-700"
                        aria-hidden="true"
                    />
                </span>
                <div class="flex max-w-sm flex-col gap-1">
                    <h2 class="font-semibold">Henüz sipariş yok</h2>
                    <p class="text-sm text-muted-foreground">
                        Müşteriler sipariş verdiğinde ürün ve kargo bilgileri
                        burada listelenecek.
                    </p>
                </div>
            </CardContent>
        </Card>
    </main>
</template>
