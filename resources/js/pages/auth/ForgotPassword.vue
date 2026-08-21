<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { useTranslations } from '@/composables/useTranslations';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Forgot your password?',
        description: 'Enter your email and we will send you a reset link',
    },
});

const { t } = useTranslations();

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head :title="t('Forgot your password?')" />

    <div v-if="status" class="mb-6 text-sm font-medium text-chart-2">
        {{ status }}
    </div>

    <div class="space-y-6">
        <Form v-bind="email.form()" v-slot="{ errors, processing }">
            <div class="grid gap-2">
                <Label for="email">{{ t('Email address') }}</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="off"
                    autofocus
                    placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="my-6 flex items-center justify-start">
                <Button
                    class="w-full"
                    :disabled="processing"
                    data-test="email-password-reset-link-button"
                >
                    <Spinner v-if="processing" />
                    {{ t('Email password reset link') }}
                </Button>
            </div>
        </Form>

        <div class="text-center text-sm text-muted-foreground">
            <TextLink :href="login()">{{ t('Return to log in') }}</TextLink>
        </div>
    </div>
</template>
