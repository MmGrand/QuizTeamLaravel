<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, Plus } from '@lucide/vue';
import { computed } from 'vue';
import {
    create as createGame,
    index as gamesIndex,
} from '@/actions/App/Http/Controllers/GameController';
import GameRow from '@/components/GameRow.vue';
import PageHeader from '@/components/PageHeader.vue';
import PendingInvitationsModal from '@/components/PendingInvitationsModal.vue';
import StatTile from '@/components/StatTile.vue';
import { Button } from '@/components/ui/button';
import { useFormatters } from '@/composables/useFormatters';
import { useTranslations } from '@/composables/useTranslations';
import type { DashboardInvitation, GameSummary, TeamStats } from '@/types';

type Props = {
    pendingInvitations?: DashboardInvitation[];
    stats?: TeamStats | null;
    recentGames?: GameSummary[];
};

const props = withDefaults(defineProps<Props>(), {
    recentGames: () => [],
});

const page = usePage();
const { t } = useTranslations();
const { formatScore } = useFormatters();

const teamSlug = computed(() => page.props.currentTeam?.slug ?? '');
const hasGames = computed(() => (props.stats?.gamesCount ?? 0) > 0);

const dash = '—';
</script>

<template>
    <Head :title="t('Overview')" />

    <PendingInvitationsModal
        v-if="pendingInvitations && pendingInvitations.length > 0"
        :invitations="pendingInvitations"
    />

    <PageHeader
        :title="page.props.currentTeam?.name ?? t('Overview')"
        :description="
            t(
                'Games, results, line-ups and the whole history of your team in one place.',
            )
        "
    >
        <template #actions>
            <Button size="sm" as-child>
                <Link :href="createGame(teamSlug)">
                    <Plus /> {{ t('Record a game') }}
                </Link>
            </Button>
        </template>
    </PageHeader>

    <div v-if="stats" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <StatTile
            :label="t('Games played')"
            :value="String(stats.gamesCount)"
        />
        <StatTile
            :label="t('Total points')"
            :value="formatScore(stats.totalScore)"
        />
        <StatTile
            :label="t('Average score')"
            :value="
                stats.averageScore === null
                    ? dash
                    : formatScore(stats.averageScore)
            "
        />
        <StatTile :label="t('Wins')" :value="String(stats.wins)" />
    </div>

    <section class="mt-12">
        <div class="mb-1 flex items-baseline justify-between gap-4">
            <h2 class="text-base font-medium tracking-tight">
                {{ t('Recent games') }}
            </h2>

            <Link
                v-if="hasGames"
                :href="gamesIndex(teamSlug)"
                class="inline-flex items-center gap-1 rounded-md text-sm text-muted-foreground transition-colors hover:text-foreground focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
            >
                {{ t('All games') }}
                <ArrowRight class="size-3.5" />
            </Link>
        </div>

        <div v-if="recentGames.length > 0" class="divide-y divide-border/70">
            <GameRow
                v-for="game in recentGames"
                :key="game.id"
                :game="game"
                data-test="dashboard-game-row"
            />
        </div>

        <p
            v-else
            data-test="dashboard-empty-state"
            class="py-10 text-sm text-muted-foreground"
        >
            {{ t("Add the first one and the team's history starts here.") }}
        </p>
    </section>
</template>
