<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { update } from '@/actions/App/Http/Controllers/GameController';
import GameForm from '@/components/GameForm.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useTranslations } from '@/composables/useTranslations';
import type {
    GameFormValues,
    MemberOption,
    OrganizerOption,
    VenueOption,
} from '@/types';

type Props = {
    game: GameFormValues;
    venues: VenueOption[];
    organizers: OrganizerOption[];
    members: MemberOption[];
};

const props = defineProps<Props>();

const page = usePage();
const { t } = useTranslations();

const teamSlug = computed(() => page.props.currentTeam?.slug ?? '');
</script>

<template>
    <Head :title="t('Edit game')" />

    <PageHeader
        :title="t('Edit game')"
        :description="t('Update the result and line-up')"
    />

    <GameForm
        :action="update.form([teamSlug, props.game.id])"
        :venues="props.venues"
        :organizers="props.organizers"
        :members="props.members"
        :game="props.game"
        :submit-label="t('Save changes')"
    />
</template>
