<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { store } from '@/actions/App/Http/Controllers/GameController';
import GameForm from '@/components/GameForm.vue';
import PageHeader from '@/components/PageHeader.vue';
import { useTranslations } from '@/composables/useTranslations';
import type { MemberOption, OrganizerOption, VenueOption } from '@/types';

type Props = {
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
    <Head :title="t('Record a game')" />

    <PageHeader
        :title="t('Record a game')"
        :description="t('Add a quiz your team has played')"
    />

    <GameForm
        :action="store.form(teamSlug)"
        :venues="props.venues"
        :organizers="props.organizers"
        :members="props.members"
        :submit-label="t('Save game')"
    />
</template>
