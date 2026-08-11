<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Mail, UserPlus, UserRound } from '@lucide/vue';
import { store } from '@/actions/App/Http/Controllers/Kullanici/KayitController';
import { create as girisSayfasi } from '@/actions/App/Http/Controllers/Kullanici/OturumController';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

defineOptions({
    layout: {
        title: 'Yeni hesap oluşturun',
        description:
            'Silifke Yöresel ürünlerini keşfetmek için bilgilerinizi girin.',
    },
});
</script>

<template>
    <Head title="Kayıt Ol" />

    <div class="flex flex-col gap-6">
        <Form
            v-bind="store.form()"
            :reset-on-success="['sifre', 'sifre_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="ad">Ad</Label>
                        <div class="relative">
                            <UserRound
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <Input
                                id="ad"
                                name="ad"
                                type="text"
                                required
                                autofocus
                                autocomplete="given-name"
                                placeholder="Adınız"
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="errors.ad" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="soyad">Soyad</Label>
                        <div class="relative">
                            <UserRound
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <Input
                                id="soyad"
                                name="soyad"
                                type="text"
                                required
                                autocomplete="family-name"
                                placeholder="Soyadınız"
                                class="pl-10"
                            />
                        </div>
                        <InputError :message="errors.soyad" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="mail">E-posta adresi</Label>
                    <div class="relative">
                        <Mail
                            class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <Input
                            id="mail"
                            name="mail"
                            type="email"
                            required
                            autocomplete="email"
                            placeholder="ornek@mail.com"
                            class="pl-10"
                        />
                    </div>
                    <InputError :message="errors.mail" />
                </div>

                <div class="grid gap-2">
                    <Label for="sifre">Şifre</Label>
                    <PasswordInput
                        id="sifre"
                        name="sifre"
                        required
                        autocomplete="new-password"
                        placeholder="En az 8 karakter"
                    />
                    <InputError :message="errors.sifre" />
                </div>

                <div class="grid gap-2">
                    <Label for="sifre_confirmation">Şifre tekrarı</Label>
                    <PasswordInput
                        id="sifre_confirmation"
                        name="sifre_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Şifrenizi tekrar girin"
                    />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    :disabled="processing"
                    data-test="kullanici-kayit-button"
                >
                    <Spinner v-if="processing" />
                    <UserPlus v-else class="size-4" />
                    {{ processing ? 'Hesap oluşturuluyor...' : 'Kayıt ol' }}
                </Button>
            </div>
        </Form>

        <p class="text-center text-sm text-muted-foreground">
            Zaten hesabınız var mı?
            <TextLink :href="girisSayfasi()">Giriş yapın</TextLink>
        </p>
    </div>
</template>
