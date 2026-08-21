<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { LogOut, Plus } from '@lucide/vue';
import { ref } from 'vue';
import CreateTeamModal from '@/components/CreateTeamModal.vue';
import LeaveTeamModal from '@/components/LeaveTeamModal.vue';
import SectionHeading from '@/components/SectionHeading.vue';
import { Button } from '@/components/ui/button';
import { useTranslations } from '@/composables/useTranslations';
import { edit } from '@/routes/teams';
import type { Team } from '@/types';

type Props = {
    teams: Team[];
};

defineProps<Props>();

const { t } = useTranslations();

const leaveTeamDialogOpen = ref(false);
const teamLeaving = ref<Team | null>(null);

const canLeaveTeam = (team: Team) => !team.isPersonal && team.role !== 'owner';

function openLeaveTeamDialog(team: Team): void {
    teamLeaving.value = team;
    leaveTeamDialogOpen.value = true;
}
</script>

<template>
    <Head :title="t('Teams')" />

    <section class="space-y-6">
        <div class="flex items-end justify-between gap-4">
            <SectionHeading
                :title="t('Teams')"
                :description="t('Manage your teams and team memberships')"
            />

            <CreateTeamModal>
                <Button size="sm" data-test="teams-new-team-button">
                    <Plus /> {{ t('New team') }}
                </Button>
            </CreateTeamModal>
        </div>

        <div v-if="teams.length > 0" class="divide-y divide-border/70">
            <div
                v-for="team in teams"
                :key="team.id"
                data-test="team-row"
                class="flex items-center justify-between gap-4 py-3.5"
            >
                <div class="min-w-0 space-y-0.5">
                    <div class="flex items-center gap-2">
                        <Link
                            :href="edit(team.slug)"
                            data-test="team-edit-button"
                            class="truncate text-sm font-medium underline-offset-4 hover:underline focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
                        >
                            {{ team.name }}
                        </Link>
                        <span
                            v-if="team.isPersonal"
                            class="shrink-0 text-xs text-muted-foreground"
                        >
                            {{ t('Personal') }}
                        </span>
                    </div>
                    <p class="text-xs text-muted-foreground">
                        {{ team.roleLabel }}
                    </p>
                </div>

                <Button
                    v-if="canLeaveTeam(team)"
                    data-test="team-leave-button"
                    variant="ghost"
                    size="icon"
                    class="size-8 shrink-0 text-muted-foreground hover:text-destructive"
                    :aria-label="t('Leave team')"
                    @click="openLeaveTeamDialog(team)"
                >
                    <LogOut class="size-4" />
                </Button>
            </div>
        </div>

        <p v-else class="py-8 text-sm text-muted-foreground">
            {{ t("You don't belong to any teams yet.") }}
        </p>
    </section>

    <LeaveTeamModal v-model:open="leaveTeamDialogOpen" :team="teamLeaving" />
</template>
