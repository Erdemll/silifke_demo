<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { LogIn, Mail } from '@lucide/vue';
import { create as kayitSayfasi } from '@/actions/App/Http/Controllers/Kullanici/KayitController';
import { store } from '@/actions/App/Http/Controllers/Kullanici/OturumController';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

defineOptions({
    layout: {
        title: 'Hesabınıza giriş yapın',
        description: 'Silifke Yöresel hesabınıza e-posta ve şifrenizle erişin.',
    },
});
</script>

<template>
    <Head title="Giriş Yap" />

    <div class="flex flex-col gap-6">
        <Form
            v-bind="store.form()"
            :reset-on-success="['sifre']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-5">
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
                            autofocus
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
                        autocomplete="current-password"
                        placeholder="Şifreniz"
                    />
                    <InputError :message="errors.sifre" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    :disabled="processing"
                    data-test="kullanici-giris-button"
                >
                    <Spinner v-if="processing" />
                    <LogIn v-else class="size-4" />
                    {{ processing ? 'Giriş yapılıyor...' : 'Giriş yap' }}
                </Button>
            </div>
        </Form>

        <p class="text-center text-sm text-muted-foreground">
            Henüz hesabınız yok mu?
            <TextLink :href="kayitSayfasi()">Kayıt olun</TextLink>
        </p>
    </div>
</template>
