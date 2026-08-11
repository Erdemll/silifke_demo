<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    ChevronDown,
    ClipboardList,
    LogOut,
    ShoppingBasket,
    UserRound,
} from '@lucide/vue';
import { computed } from 'vue';
import { create as kayitSayfasi } from '@/actions/App/Http/Controllers/Kullanici/KayitController';
import {
    create as girisSayfasi,
    destroy as cikisYap,
} from '@/actions/App/Http/Controllers/Kullanici/OturumController';
import { index as siparislerimSayfasi } from '@/actions/App/Http/Controllers/Magaza/SiparislerimController';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import YoreselLogo from '@/components/YoreselLogo.vue';
import { home } from '@/routes';
import { index as sepetSayfasi } from '@/routes/magaza/sepet';

const page = usePage();
const kullanici = computed(() => page.props.auth.kullanici);
const sepetAdedi = computed(() => page.props.sepet_ozeti.adet);
</script>

<template>
    <header
        class="sticky top-0 z-40 border-b border-brand-900/10 bg-sand-50/95 shadow-sm shadow-brand-950/5 backdrop-blur dark:border-brand-100/10 dark:bg-brand-950/95"
    >
        <div
            class="mx-auto flex min-h-16 max-w-7xl items-center justify-between gap-3 px-4 py-2 sm:px-6 lg:px-8"
        >
            <Link
                :href="home()"
                class="shrink-0 rounded-xl text-brand-900 focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 focus-visible:outline-none dark:text-sand-50"
            >
                <YoreselLogo />
            </Link>

            <nav
                class="flex min-w-0 items-center gap-1 sm:gap-2"
                aria-label="Mağaza işlemleri"
            >
                <Link
                    :href="sepetSayfasi()"
                    class="relative inline-flex h-10 items-center gap-2 rounded-xl px-2 text-sm font-medium text-brand-900 transition hover:bg-brand-100 focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:outline-none sm:px-3 dark:text-sand-100 dark:hover:bg-brand-800"
                    :aria-label="`Sepet, ${sepetAdedi} ürün`"
                >
                    <ShoppingBasket class="size-4" aria-hidden="true" />
                    <span class="hidden sm:inline">Sepet</span>
                    <span
                        v-if="sepetAdedi > 0"
                        class="flex min-w-5 items-center justify-center rounded-full bg-brand-700 px-1.5 text-xs leading-5 font-bold text-white tabular-nums dark:bg-brand-400 dark:text-brand-950"
                    >
                        {{ sepetAdedi > 99 ? '99+' : sepetAdedi }}
                    </span>
                </Link>

                <template v-if="kullanici">
                    <Link
                        :href="siparislerimSayfasi()"
                        class="inline-flex h-10 items-center gap-2 rounded-xl px-2 text-sm font-medium text-brand-900 transition hover:bg-brand-100 focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:outline-none sm:px-3 dark:text-sand-100 dark:hover:bg-brand-800"
                    >
                        <ClipboardList class="size-4" aria-hidden="true" />
                        <span class="hidden md:inline">Siparişlerim</span>
                    </Link>

                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <button
                                type="button"
                                class="inline-flex h-10 max-w-36 items-center gap-2 rounded-xl border border-brand-200 bg-white/70 px-2.5 text-sm font-medium text-brand-900 shadow-sm transition hover:bg-brand-50 focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:outline-none sm:px-3 dark:border-brand-800 dark:bg-brand-900/70 dark:text-sand-100 dark:hover:bg-brand-800"
                            >
                                <UserRound
                                    class="size-4 shrink-0"
                                    aria-hidden="true"
                                />
                                <span class="truncate">{{ kullanici.ad }}</span>
                                <ChevronDown
                                    class="size-3.5 shrink-0 text-brand-500"
                                    aria-hidden="true"
                                />
                            </button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent
                            align="end"
                            class="w-56 rounded-lg"
                        >
                            <DropdownMenuLabel class="font-normal">
                                <p class="truncate font-medium">
                                    {{ kullanici.ad }} {{ kullanici.soyad }}
                                </p>
                                <p
                                    class="truncate text-xs text-muted-foreground"
                                >
                                    {{ kullanici.mail }}
                                </p>
                            </DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem :as-child="true">
                                <Link
                                    :href="cikisYap()"
                                    as="button"
                                    class="w-full cursor-pointer"
                                >
                                    <LogOut class="size-4" />
                                    Çıkış yap
                                </Link>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </template>

                <template v-else>
                    <Link
                        :href="girisSayfasi()"
                        class="inline-flex h-10 items-center rounded-xl px-2 text-sm font-medium text-brand-900 transition hover:bg-brand-100 focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:outline-none sm:px-3 dark:text-sand-100 dark:hover:bg-brand-800"
                    >
                        Giriş yap
                    </Link>
                    <Link
                        :href="kayitSayfasi()"
                        class="inline-flex h-10 items-center rounded-xl bg-brand-700 px-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 focus-visible:outline-none sm:px-4 dark:bg-brand-400 dark:text-brand-950 dark:hover:bg-brand-300"
                    >
                        Kayıt ol
                    </Link>
                </template>
            </nav>
        </div>
    </header>
</template>
