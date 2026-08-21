<script setup lang="ts">
import { useFormatters } from '@/composables/useFormatters';
import { useTranslations } from '@/composables/useTranslations';
import type { GameSummary } from '@/types';

type Props = {
    game: GameSummary;
};

defineProps<Props>();

const { t } = useTranslations();
const { formatDate, formatScore } = useFormatters();
</script>

<template>
    <div class="flex items-baseline gap-4 py-3.5">
        <div class="min-w-0 flex-1 space-y-0.5">
            <div class="flex items-baseline gap-2">
                <span class="truncate text-sm font-medium">
                    {{ game.title ?? game.organizer.name }}
                </span>
                <span
                    v-if="game.place === 1"
                    class="shrink-0 text-xs font-medium text-chart-3"
                >
                    {{ t('1st place') }}
                </span>
                <span
                    v-else-if="game.place"
                    class="shrink-0 text-xs text-muted-foreground tabular-nums"
                >
                    {{ t(':place place', { place: game.place }) }}
                </span>
            </div>
            <p class="truncate text-xs text-muted-foreground">
                {{ formatDate(game.playedAt) }} · {{ game.venue.name }} ·
                {{ game.organizer.name }}
            </p>
        </div>

        <div class="shrink-0 text-right">
            <span class="text-base font-semibold tabular-nums">
                {{ formatScore(game.score) }}
            </span>
        </div>

        <div v-if="$slots.actions" class="flex shrink-0 items-center gap-0.5">
            <slot name="actions" />
        </div>
    </div>
</template>
