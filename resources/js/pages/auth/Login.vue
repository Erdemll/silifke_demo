<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { LogIn, Mail, ShieldCheck } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Yönetim paneline giriş',
        description: 'Silifke Yöresel yönetim hesabınızla devam edin.',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Yönetim Girişi" />

    <div
        class="mb-5 flex items-center gap-3 rounded-2xl border border-brand-200 bg-brand-50 p-3 text-sm text-brand-800 dark:border-brand-800 dark:bg-brand-900 dark:text-brand-200"
    >
        <span
            class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-brand-100 dark:bg-brand-800"
        >
            <ShieldCheck class="size-5" aria-hidden="true" />
        </span>
        <span>Bu alan yalnızca mağaza yöneticisi içindir.</span>
    </div>

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

    <PasskeyVerify />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email">E-posta adresi</Label>
                <div class="relative">
                    <Mail
                        class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                        aria-hidden="true"
                    />
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="yonetici@silifkeyoresel.com"
                        class="pl-10"
                    />
                </div>
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">Şifre</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-sm"
                        :tabindex="5"
                    >
                        Şifrenizi mi unuttunuz?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Şifreniz"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-3">
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span>Beni hatırla</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-4 w-full"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                <LogIn v-else class="size-4" />
                {{ processing ? 'Giriş yapılıyor...' : 'Giriş yap' }}
            </Button>
        </div>
    </Form>
</template>
