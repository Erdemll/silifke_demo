<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Leaf, ShoppingBag, Sparkles, Truck, Users } from '@lucide/vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';

type DashboardMetrics = {
    customerCount: number;
    todaySalesTotal: number;
    pendingShipmentCount: number;
};

const props = defineProps<{
    metrics: DashboardMetrics;
}>();

const currencyFormatter = new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY',
});

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Yönetim Paneli',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Yönetim Paneli" />

    <main class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div
            class="yoresel-pattern relative overflow-hidden rounded-3xl bg-brand-800 p-6 text-white shadow-xl shadow-brand-950/10 sm:p-8"
        >
            <Leaf
                class="absolute -right-5 -bottom-10 size-40 rotate-12 text-brand-600/70"
                :stroke-width="1"
                aria-hidden="true"
            />
            <div class="relative flex max-w-2xl flex-col gap-3">
                <p
                    class="flex w-fit items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold tracking-wide text-brand-100 uppercase"
                >
                    <Sparkles class="size-3.5" aria-hidden="true" />
                    Mağaza özeti
                </p>
                <h1
                    class="font-display text-3xl font-bold tracking-tight sm:text-4xl"
                >
                    Yönetim Paneli
                </h1>
                <p class="text-sm text-brand-100 sm:text-base">
                    Silifke Yöresel mağazasının güncel durumunu tek bakışta
                    takip edin.
                </p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <Card
                class="border-brand-900/10 bg-card/90 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-brand-100/10"
            >
                <CardHeader class="grid grid-cols-[1fr_auto] gap-4">
                    <CardTitle
                        class="text-sm font-medium text-muted-foreground"
                    >
                        Toplam müşteri
                    </CardTitle>
                    <div
                        class="flex size-11 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900 dark:text-brand-300"
                    >
                        <Users class="size-5" aria-hidden="true" />
                    </div>
                </CardHeader>
                <CardContent class="flex flex-col gap-1">
                    <p class="font-display text-4xl font-bold tabular-nums">
                        {{ props.metrics.customerCount }}
                    </p>
                    <p class="text-xs text-muted-foreground">
                        Kayıtlı müşteri hesabı
                    </p>
                </CardContent>
            </Card>

            <Card
                class="border-brand-900/10 bg-card/90 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-brand-100/10"
            >
                <CardHeader class="grid grid-cols-[1fr_auto] gap-4">
                    <CardTitle
                        class="text-sm font-medium text-muted-foreground"
                    >
                        Bugünkü satış
                    </CardTitle>
                    <div
                        class="flex size-11 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900 dark:text-brand-300"
                    >
                        <ShoppingBag class="size-5" aria-hidden="true" />
                    </div>
                </CardHeader>
                <CardContent class="flex flex-col gap-1">
                    <p class="font-display text-4xl font-bold tabular-nums">
                        {{
                            currencyFormatter.format(
                                props.metrics.todaySalesTotal,
                            )
                        }}
                    </p>
                    <p class="text-xs text-muted-foreground">
                        Bugün oluşturulan siparişlerin toplamı
                    </p>
                </CardContent>
            </Card>

            <Card
                class="border-brand-900/10 bg-card/90 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-brand-100/10"
            >
                <CardHeader class="grid grid-cols-[1fr_auto] gap-4">
                    <CardTitle
                        class="text-sm font-medium text-muted-foreground"
                    >
                        Kargoya verilecek
                    </CardTitle>
                    <div
                        class="flex size-11 items-center justify-center rounded-2xl bg-sand-200 text-sand-600 dark:bg-sand-600/20 dark:text-sand-300"
                    >
                        <Truck class="size-5" aria-hidden="true" />
                    </div>
                </CardHeader>
                <CardContent class="flex flex-col gap-1">
                    <p class="font-display text-4xl font-bold tabular-nums">
                        {{ props.metrics.pendingShipmentCount }}
                    </p>
                    <p class="text-xs text-muted-foreground">
                        Kargo kodu bekleyen sipariş
                    </p>
                </CardContent>
            </Card>
        </div>
    </main>
</template>
