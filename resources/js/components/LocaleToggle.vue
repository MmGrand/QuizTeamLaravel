<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    setLocaleCookie,
    useTranslations,
} from '@/composables/useTranslations';
import type { Locale, LocaleOption } from '@/types';

const { t, locale } = useTranslations();

const options = computed<LocaleOption[]>(() => [
    { value: 'ru', label: 'RU' },
    { value: 'en', label: 'EN' },
]);

function switchLocale(value: Locale): void {
    if (value === locale.value) {
        return;
    }

    setLocaleCookie(value);
    router.reload();
}
</script>

<template>
    <div
        class="inline-flex items-center gap-0.5 rounded-full border border-border/70 p-0.5"
        role="radiogroup"
        :aria-label="t('Language')"
    >
        <button
            v-for="option in options"
            :key="option.value"
            type="button"
            role="radio"
            :aria-checked="locale === option.value"
            :data-test="'locale-' + option.value"
            class="rounded-full px-2 py-1 text-[0.7rem] leading-none font-medium text-muted-foreground transition-colors hover:text-foreground focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
            :class="
                locale === option.value
                    ? 'bg-accent text-foreground'
                    : undefined
            "
            @click="switchLocale(option.value)"
        >
            {{ option.label }}
        </button>
    </div>
</template>
