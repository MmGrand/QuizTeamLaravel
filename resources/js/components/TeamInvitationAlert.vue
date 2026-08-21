<script setup lang="ts">
import { Info } from '@lucide/vue';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { useTranslations } from '@/composables/useTranslations';
import type { TeamInvitationContext } from '@/types';

type Props = {
    invitation: TeamInvitationContext;
};

defineProps<Props>();
const { t } = useTranslations();
</script>

<template>
    <div data-test="team-invitation-alert">
        <Alert class="mb-6 border-border/70 bg-muted/50">
            <Info class="size-4" />
            <AlertDescription>
                {{
                    t('You have been invited to join the team :name.', {
                        name: invitation.teamName,
                    })
                }}
            </AlertDescription>
        </Alert>
    </div>
</template>
