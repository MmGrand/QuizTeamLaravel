<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useTranslations } from '@/composables/useTranslations';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        title: 'Verify your email',
        description:
            'We sent a verification link to your inbox. Click it to finish signing up.',
    },
});

const { t } = useTranslations();

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head :title="t('Verify your email')" />

    <div
        v-if="status === 'verification-link-sent'"
        class="mb-6 text-sm font-medium text-chart-2"
    >
        {{ t('A new verification link has been sent to your email address.') }}
    </div>

    <Form v-bind="send.form()" class="space-y-6" v-slot="{ processing }">
        <Button :disabled="processing" variant="secondary">
            <Spinner v-if="processing" />
            {{ t('Resend verification email') }}
        </Button>

        <TextLink :href="logout()" as="button" class="block text-sm">
            {{ t('Log out') }}
        </TextLink>
    </Form>
</template>
