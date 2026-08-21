<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import { create, edit } from '@/actions/App/Http/Controllers/GameController';
import DeleteGameModal from '@/components/DeleteGameModal.vue';
import GameRow from '@/components/GameRow.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { useTranslations } from '@/composables/useTranslations';
import type { GameListItem, GamePermissions } from '@/types';

type Props = {
    games: GameListItem[];
    permissions: GamePermissions;
};

const props = defineProps<Props>();

const page = usePage();
const { t } = useTranslations();

const teamSlug = computed(() => page.props.currentTeam?.slug ?? '');

const deleteDialogOpen = ref(false);
const gameToDelete = ref<GameListItem | null>(null);

function openDeleteDialog(game: GameListItem): void {
    gameToDelete.value = game;
    deleteDialogOpen.value = true;
}
</script>

<template>
    <Head :title="t('Games')" />

    <PageHeader
        :title="t('Games')"
        :description="t('Every quiz your team has played')"
    >
        <template #actions>
            <Button size="sm" data-test="games-new-game-button" as-child>
                <Link :href="create(teamSlug)">
                    <Plus /> {{ t('Record a game') }}
                </Link>
            </Button>
        </template>
    </PageHeader>

    <div v-if="props.games.length > 0" class="divide-y divide-border/70">
        <GameRow
            v-for="game in props.games"
            :key="game.id"
            :game="game"
            data-test="game-row"
        >
            <template #actions>
                <Button
                    data-test="game-edit-button"
                    variant="ghost"
                    size="icon"
                    class="size-8 text-muted-foreground"
                    :aria-label="t('Edit game')"
                    as-child
                >
                    <Link :href="edit([teamSlug, game.id])">
                        <Pencil class="size-4" />
                    </Link>
                </Button>

                <Button
                    v-if="props.permissions.canDeleteGame"
                    data-test="game-delete-button"
                    variant="ghost"
                    size="icon"
                    class="size-8 text-muted-foreground hover:text-destructive"
                    :aria-label="t('Delete game')"
                    @click="openDeleteDialog(game)"
                >
                    <Trash2 class="size-4" />
                </Button>
            </template>
        </GameRow>
    </div>

    <div
        v-else
        data-test="games-empty-state"
        class="rounded-lg border border-dashed border-border py-16 text-center"
    >
        <p class="text-sm font-medium">{{ t('No games yet') }}</p>
        <p class="mt-1 text-sm text-muted-foreground">
            {{ t("Add the first one and the team's history starts here.") }}
        </p>
    </div>

    <DeleteGameModal
        v-model:open="deleteDialogOpen"
        :game="gameToDelete"
        :team-slug="teamSlug"
    />
</template>
