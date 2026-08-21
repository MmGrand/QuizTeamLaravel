<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import TeamInvitationController from '@/actions/App/Http/Controllers/Teams/TeamInvitationController';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { useTranslations } from '@/composables/useTranslations';
import type { DashboardInvitation } from '@/types';

type Props = {
    invitations: DashboardInvitation[];
};

const props = defineProps<Props>();

const open = ref(true);
const processingCode = ref<string | null>(null);

const acceptInvitation = (invitation: DashboardInvitation) => {
    router.visit(TeamInvitationController.accept(invitation), {
        onStart: () => (processingCode.value = invitation.code),
        onFinish: () => (processingCode.value = null),
    });
};

const declineInvitation = (invitation: DashboardInvitation) => {
    router.visit(TeamInvitationController.decline(invitation), {
        onStart: () => (processingCode.value = invitation.code),
        onFinish: () => (processingCode.value = null),
        onSuccess: () => {
            if (props.invitations.length === 1) {
                open.value = false;
            }
        },
    });
};
const { t } = useTranslations();
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent data-test="pending-invitations-modal">
            <DialogHeader>
                <DialogTitle>{{ t('Pending invitations') }}</DialogTitle>
                <DialogDescription>
                    {{
                        t(
                            'Accept or decline the teams you have been invited to join.',
                        )
                    }}
                </DialogDescription>
            </DialogHeader>

            <div class="grid gap-4">
                <div
                    v-for="invitation in props.invitations"
                    :key="invitation.code"
                    data-test="pending-invitation-row"
                    class="rounded-lg border border-border/70 p-4"
                >
                    <div class="space-y-1">
                        <p class="font-medium">{{ invitation.team.name }}</p>
                        <p class="text-sm text-muted-foreground">
                            {{
                                t(':name invited you to this team.', {
                                    name: invitation.inviterName,
                                })
                            }}
                        </p>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <Button
                            variant="secondary"
                            data-test="pending-invitation-decline"
                            :disabled="processingCode === invitation.code"
                            @click="declineInvitation(invitation)"
                        >
                            {{ t('Decline') }}
                        </Button>

                        <Button
                            data-test="pending-invitation-accept"
                            :disabled="processingCode === invitation.code"
                            @click="acceptInvitation(invitation)"
                        >
                            {{ t('Accept') }}
                        </Button>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
