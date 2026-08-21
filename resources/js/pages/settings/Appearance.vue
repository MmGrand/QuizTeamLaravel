<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import LocaleToggle from '@/components/LocaleToggle.vue';
import SectionHeading from '@/components/SectionHeading.vue';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();
</script>

<template>
    <Head :title="t('Appearance')" />

    <section class="space-y-4">
        <SectionHeading
            :title="t('Appearance')"
            :description="t('Choose how the interface looks')"
        />
        <ThemeToggle />
    </section>

    <section class="space-y-4">
        <SectionHeading
            :title="t('Language')"
            :description="t('Choose the interface language')"
        />
        <LocaleToggle />
    </section>
</template>
