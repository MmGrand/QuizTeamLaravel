<script setup lang="ts">
import { Monitor, Moon, Sun } from '@lucide/vue';
import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';
import { useTranslations } from '@/composables/useTranslations';
import type { Appearance } from '@/types';

const { appearance, updateAppearance } = useAppearance();
const { t } = useTranslations();

const options = computed<{ value: Appearance; label: string; icon: unknown }[]>(
    () => [
        { value: 'light', label: t('Light'), icon: Sun },
        { value: 'dark', label: t('Dark'), icon: Moon },
        { value: 'system', label: t('System'), icon: Monitor },
    ],
);
</script>

<template>
    <div
        class="inline-flex items-center gap-0.5 rounded-full border border-border/70 p-0.5"
        role="radiogroup"
        :aria-label="t('Appearance')"
    >
        <button
            v-for="option in options"
            :key="option.value"
            type="button"
            role="radio"
            :aria-checked="appearance === option.value"
            :aria-label="option.label"
            :title="option.label"
            :data-test="'appearance-' + option.value"
            class="grid size-7 place-items-center rounded-full text-muted-foreground transition-colors hover:text-foreground focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
            :class="
                appearance === option.value
                    ? 'bg-accent text-foreground'
                    : undefined
            "
            @click="updateAppearance(option.value)"
        >
            <component :is="option.icon" class="size-3.5" />
        </button>
    </div>
</template>
