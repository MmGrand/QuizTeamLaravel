<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import PageHeader from '@/components/PageHeader.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { useTranslations } from '@/composables/useTranslations';
import { toUrl } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';
import { index as teams } from '@/routes/teams';
import type { NavItem } from '@/types';

const { t } = useTranslations();
const { isCurrentOrParentUrl } = useCurrentUrl();

const tabs = computed<NavItem[]>(() => [
    { title: t('Profile'), href: editProfile() },
    { title: t('Security'), href: editSecurity() },
    { title: t('Teams'), href: teams() },
    { title: t('Appearance'), href: editAppearance() },
]);
</script>

<template>
    <PageHeader :title="t('Settings')" />

    <nav
        class="-mx-1 mb-10 flex items-center gap-1 overflow-x-auto border-b border-border/70 pb-px"
        :aria-label="t('Settings')"
    >
        <Link
            v-for="tab in tabs"
            :key="toUrl(tab.href)"
            :href="tab.href"
            class="shrink-0 border-b-2 px-3 pb-2.5 text-sm transition-colors focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
            :class="
                isCurrentOrParentUrl(tab.href)
                    ? 'border-foreground font-medium text-foreground'
                    : 'border-transparent text-muted-foreground hover:text-foreground'
            "
        >
            {{ tab.title }}
        </Link>
    </nav>

    <div class="max-w-xl space-y-12">
        <slot />
    </div>
</template>
