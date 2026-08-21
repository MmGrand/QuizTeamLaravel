<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { Check, ChevronDown, Plus } from '@lucide/vue';
import { computed } from 'vue';
import CreateTeamModal from '@/components/CreateTeamModal.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useTranslations } from '@/composables/useTranslations';
import { switchMethod } from '@/routes/teams';
import type { Team } from '@/types';

const page = usePage();
const { t } = useTranslations();

const currentTeam = computed(() => page.props.currentTeam);
const teams = computed(() => page.props.teams ?? []);

/**
 * Switching teams keeps you on the page you were looking at by swapping the
 * team slug in the current URL, falling back to a plain reload when the URL
 * carries no slug.
 */
function switchTeam(team: Team): void {
    const previousTeamSlug = currentTeam.value?.slug;

    router.visit(switchMethod(team.slug), {
        onFinish: () => {
            if (!previousTeamSlug || typeof window === 'undefined') {
                router.reload();

                return;
            }

            const currentUrl =
                window.location.pathname +
                window.location.search +
                window.location.hash;
            const segment = '/' + previousTeamSlug;

            if (currentUrl.includes(segment)) {
                router.visit(currentUrl.replace(segment, '/' + team.slug), {
                    replace: true,
                });

                return;
            }

            router.reload();
        },
    });
}
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger
            data-test="team-switcher-trigger"
            class="flex max-w-[10rem] items-center gap-1 rounded-md px-2 py-1 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
        >
            <span class="truncate">
                {{ currentTeam?.name ?? t('Team') }}
            </span>
            <ChevronDown class="size-3.5 shrink-0 opacity-60" />
        </DropdownMenuTrigger>

        <DropdownMenuContent align="start" class="w-56">
            <DropdownMenuLabel class="text-xs text-muted-foreground">
                {{ t('Teams') }}
            </DropdownMenuLabel>

            <DropdownMenuItem
                v-for="team in teams"
                :key="team.id"
                data-test="team-switcher-item"
                class="cursor-pointer gap-2"
                @click="switchTeam(team)"
            >
                <span class="truncate">{{ team.name }}</span>
                <Check
                    v-if="currentTeam?.id === team.id"
                    class="ml-auto size-4"
                />
            </DropdownMenuItem>

            <DropdownMenuSeparator />

            <CreateTeamModal>
                <DropdownMenuItem
                    data-test="team-switcher-new-team"
                    class="cursor-pointer gap-2"
                    @select.prevent
                >
                    <Plus class="size-4" />
                    <span class="text-muted-foreground">{{
                        t('New team')
                    }}</span>
                </DropdownMenuItem>
            </CreateTeamModal>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
