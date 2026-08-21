<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import SecurityController from '@/actions/App/Http/Controllers/Settings/SecurityController';
import InputError from '@/components/InputError.vue';
import type { Props as ManagePasskeysProps } from '@/components/ManagePasskeys.vue';
import ManagePasskeys from '@/components/ManagePasskeys.vue';
import type { Props as ManageTwoFactorProps } from '@/components/ManageTwoFactor.vue';
import ManageTwoFactor from '@/components/ManageTwoFactor.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import SectionHeading from '@/components/SectionHeading.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { useTranslations } from '@/composables/useTranslations';

type Props = {
    passwordRules: string;
} & ManagePasskeysProps &
    ManageTwoFactorProps;

const props = defineProps<Props>();

const { t } = useTranslations();
</script>

<template>
    <Head :title="t('Security')" />

    <section class="space-y-6">
        <SectionHeading
            :title="t('Update password')"
            :description="t('Use a long, random password to stay secure')"
        />

        <Form
            v-bind="SecurityController.update.form()"
            :options="{ preserveScroll: true }"
            reset-on-success
            :reset-on-error="[
                'password',
                'password_confirmation',
                'current_password',
            ]"
            class="space-y-5"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="current_password">{{
                    t('Current password')
                }}</Label>
                <PasswordInput
                    id="current_password"
                    name="current_password"
                    autocomplete="current-password"
                    :placeholder="t('Current password')"
                />
                <InputError :message="errors.current_password" />
            </div>

            <div class="grid gap-2">
                <Label for="password">{{ t('New password') }}</Label>
                <PasswordInput
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    :placeholder="t('New password')"
                    :passwordrules="props.passwordRules"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">
                    {{ t('Confirm password') }}
                </Label>
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    :placeholder="t('Confirm password')"
                    :passwordrules="props.passwordRules"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button :disabled="processing" data-test="update-password-button">
                {{ t('Save') }}
            </Button>
        </Form>
    </section>

    <ManageTwoFactor
        :canManageTwoFactor="canManageTwoFactor"
        :requiresConfirmation="requiresConfirmation"
        :twoFactorEnabled="twoFactorEnabled"
    />

    <ManagePasskeys
        :canManagePasskeys="canManagePasskeys"
        :passkeys="passkeys"
    />
</template>
