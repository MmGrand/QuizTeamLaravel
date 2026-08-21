<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import SectionHeading from '@/components/SectionHeading.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { useTranslations } from '@/composables/useTranslations';

const passwordInput = useTemplateRef('passwordInput');
const { t } = useTranslations();
</script>

<template>
    <section class="space-y-4">
        <SectionHeading
            :title="t('Delete account')"
            :description="
                t('Delete your account and all of its data permanently')
            "
        />

        <Dialog>
            <DialogTrigger as-child>
                <Button variant="destructive" data-test="delete-user-button">
                    {{ t('Delete account') }}
                </Button>
            </DialogTrigger>

            <DialogContent>
                <Form
                    v-bind="ProfileController.destroy.form()"
                    reset-on-success
                    @error="() => passwordInput?.focus()"
                    :options="{ preserveScroll: true }"
                    class="space-y-6"
                    v-slot="{ errors, processing, reset, clearErrors }"
                >
                    <DialogHeader>
                        <DialogTitle>{{ t('Delete account') }}</DialogTitle>
                        <DialogDescription>
                            {{
                                t(
                                    'This cannot be undone. Enter your password to confirm.',
                                )
                            }}
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="password" class="sr-only">
                            {{ t('Password') }}
                        </Label>
                        <PasswordInput
                            id="password"
                            name="password"
                            ref="passwordInput"
                            :placeholder="t('Password')"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button
                                variant="secondary"
                                @click="
                                    () => {
                                        clearErrors();
                                        reset();
                                    }
                                "
                            >
                                {{ t('Cancel') }}
                            </Button>
                        </DialogClose>

                        <Button
                            type="submit"
                            variant="destructive"
                            :disabled="processing"
                            data-test="confirm-delete-user-button"
                        >
                            {{ t('Delete account') }}
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>
    </section>
</template>
