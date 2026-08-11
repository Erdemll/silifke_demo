<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ChartNoAxesCombined,
    PackageSearch,
    ShoppingBasket,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as siparislerIndex } from '@/routes/siparisler';
import { index as urunlerIndex } from '@/routes/urunler';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: ChartNoAxesCombined,
    },
    {
        title: 'Ürünler',
        href: urunlerIndex(),
        icon: PackageSearch,
    },
    {
        title: 'Siparişler',
        href: siparislerIndex(),
        icon: ShoppingBasket,
    },
];
</script>

<template>
    <Sidebar
        collapsible="icon"
        variant="inset"
        class="yoresel-pattern border-sidebar-border/60"
    >
        <SidebarHeader class="border-b border-sidebar-border/70 pb-3">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
