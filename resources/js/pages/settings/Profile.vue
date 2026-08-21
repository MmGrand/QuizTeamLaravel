<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import InputError from '@/components/InputError.vue';
import SectionHeading from '@/components/SectionHeading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useTranslations } from '@/composables/useTranslations';
import { send } from '@/routes/verification';

const page = usePage();
const { t } = useTranslations();

const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head :title="t('Profile')" />

    <section class="space-y-6">
        <SectionHeading
            :title="t('Profile')"
            :description="t('Update your name and email address')"
        />

        <Form
            v-bind="ProfileController.update.form()"
            class="space-y-5"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">{{ t('Name') }}</Label>
                <Input
                    id="name"
                    name="name"
                    :default-value="user.name"
                    required
                    autocomplete="name"
                    :placeholder="t('Full name')"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">{{ t('Email address') }}</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    :default-value="user.email"
                    required
                    autocomplete="username"
                    :placeholder="t('Email address')"
                />
                <InputError :message="errors.email" />
            </div>

            <div
                v-if="page.props.mustVerifyEmail && !user.email_verified_at"
                class="space-y-1 text-sm"
            >
                <p class="text-muted-foreground">
                    {{ t('Your email address is unverified.') }}
                    <Link
                        :href="send()"
                        as="button"
                        class="text-foreground underline underline-offset-4"
                    >
                        {{ t('Click here to re-send the verification email.') }}
                    </Link>
                </p>

                <p
                    v-if="page.props.status === 'verification-link-sent'"
                    class="font-medium text-chart-2"
                >
                    {{
                        t(
                            'A new verification link has been sent to your email address.',
                        )
                    }}
                </p>
            </div>

            <Button :disabled="processing" data-test="update-profile-button">
                {{ t('Save') }}
            </Button>
        </Form>
    </section>

    <DeleteUser />
</template>
