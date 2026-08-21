<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { ChevronDown, Mail, UserPlus, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import CancelInvitationModal from '@/components/CancelInvitationModal.vue';
import DeleteTeamModal from '@/components/DeleteTeamModal.vue';
import InputError from '@/components/InputError.vue';
import InviteMemberModal from '@/components/InviteMemberModal.vue';
import RemoveMemberModal from '@/components/RemoveMemberModal.vue';
import SectionHeading from '@/components/SectionHeading.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useInitials } from '@/composables/useInitials';
import { useTranslations } from '@/composables/useTranslations';
import { update } from '@/routes/teams';
import { update as updateMember } from '@/routes/teams/members';
import type {
    RoleOption,
    Team,
    TeamInvitation,
    TeamMember,
    TeamPermissions,
} from '@/types';

type Props = {
    team: Team;
    members: TeamMember[];
    invitations: TeamInvitation[];
    permissions: TeamPermissions;
    availableRoles: RoleOption[];
};

const props = defineProps<Props>();

const { getInitials } = useInitials();
const { t } = useTranslations();

const inviteDialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const removeMemberDialogOpen = ref(false);
const memberToRemove = ref<TeamMember | null>(null);
const cancelInvitationDialogOpen = ref(false);
const invitationToCancel = ref<TeamInvitation | null>(null);

const pageTitle = computed(() => props.team.name);

function updateMemberRole(member: TeamMember, newRole: string): void {
    router.visit(updateMember([props.team.slug, member.id]), {
        data: { role: newRole },
        preserveScroll: true,
    });
}

function confirmRemoveMember(member: TeamMember): void {
    memberToRemove.value = member;
    removeMemberDialogOpen.value = true;
}

function confirmCancelInvitation(invitation: TeamInvitation): void {
    invitationToCancel.value = invitation;
    cancelInvitationDialogOpen.value = true;
}
</script>

<template>
    <Head :title="pageTitle" />

    <section v-if="permissions.canUpdateTeam" class="space-y-6">
        <SectionHeading
            :title="t('Team')"
            :description="t('Manage your teams and team memberships')"
        />

        <Form
            v-bind="update.form(team.slug)"
            class="space-y-5"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">{{ t('Team name') }}</Label>
                <Input
                    id="name"
                    name="name"
                    data-test="team-name-input"
                    :default-value="team.name"
                    required
                />
                <InputError :message="errors.name" />
            </div>

            <Button
                type="submit"
                data-test="team-save-button"
                :disabled="processing"
            >
                {{ t('Save') }}
            </Button>
        </Form>
    </section>

    <section v-else>
        <SectionHeading :title="team.name" />
    </section>

    <section class="space-y-5">
        <div class="flex items-end justify-between gap-4">
            <SectionHeading :title="t('Members')" />

            <Button
                v-if="permissions.canCreateInvitation"
                size="sm"
                data-test="invite-member-button"
                @click="inviteDialogOpen = true"
            >
                <UserPlus /> {{ t('Invite a team member') }}
            </Button>
        </div>

        <div class="divide-y divide-border/70">
            <div
                v-for="member in members"
                :key="member.id"
                data-test="member-row"
                class="flex items-center justify-between gap-4 py-3.5"
            >
                <div class="flex min-w-0 items-center gap-3">
                    <Avatar class="size-9">
                        <AvatarImage
                            v-if="member.avatar"
                            :src="member.avatar"
                            :alt="member.name"
                        />
                        <AvatarFallback class="text-xs">
                            {{ getInitials(member.name) }}
                        </AvatarFallback>
                    </Avatar>
                    <div class="min-w-0">
                        <div class="truncate text-sm font-medium">
                            {{ member.name }}
                        </div>
                        <div class="truncate text-xs text-muted-foreground">
                            {{ member.email }}
                        </div>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-1">
                    <DropdownMenu
                        v-if="
                            member.role !== 'owner' &&
                            permissions.canUpdateMember
                        "
                    >
                        <DropdownMenuTrigger as-child>
                            <Button
                                data-test="member-role-trigger"
                                variant="ghost"
                                size="sm"
                                class="text-muted-foreground"
                            >
                                {{ t(member.role_label) }}
                                <ChevronDown class="size-3.5 opacity-60" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem
                                v-for="role in availableRoles"
                                :key="role.value"
                                data-test="member-role-option"
                                @click="updateMemberRole(member, role.value)"
                            >
                                {{ t(role.label) }}
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <span v-else class="text-xs text-muted-foreground">
                        {{ t(member.role_label) }}
                    </span>

                    <Button
                        v-if="
                            member.role !== 'owner' &&
                            permissions.canRemoveMember
                        "
                        data-test="member-remove-button"
                        variant="ghost"
                        size="icon"
                        class="size-8 text-muted-foreground hover:text-destructive"
                        :aria-label="t('Remove member')"
                        @click="confirmRemoveMember(member)"
                    >
                        <X class="size-4" />
                    </Button>
                </div>
            </div>
        </div>
    </section>

    <section v-if="invitations.length > 0" class="space-y-5">
        <SectionHeading :title="t('Pending invitations')" />

        <div class="divide-y divide-border/70">
            <div
                v-for="invitation in invitations"
                :key="invitation.code"
                data-test="invitation-row"
                class="flex items-center justify-between gap-4 py-3.5"
            >
                <div class="flex min-w-0 items-center gap-3">
                    <span
                        class="grid size-9 shrink-0 place-items-center rounded-full bg-muted"
                    >
                        <Mail class="size-4 text-muted-foreground" />
                    </span>
                    <div class="min-w-0">
                        <div class="truncate text-sm font-medium">
                            {{ invitation.email }}
                        </div>
                        <div class="truncate text-xs text-muted-foreground">
                            {{ t(invitation.role_label) }}
                        </div>
                    </div>
                </div>

                <Button
                    v-if="permissions.canCancelInvitation"
                    data-test="invitation-cancel-button"
                    variant="ghost"
                    size="icon"
                    class="size-8 shrink-0 text-muted-foreground hover:text-destructive"
                    :aria-label="t('Cancel invitation')"
                    @click="confirmCancelInvitation(invitation)"
                >
                    <X class="size-4" />
                </Button>
            </div>
        </div>
    </section>

    <section
        v-if="permissions.canDeleteTeam && !team.isPersonal"
        class="space-y-4"
    >
        <SectionHeading
            :title="t('Delete team')"
            :description="
                t('This cannot be undone. Enter your password to confirm.')
            "
        />

        <Button
            data-test="delete-team-button"
            variant="destructive"
            @click="deleteDialogOpen = true"
        >
            {{ t('Delete team') }}
        </Button>
    </section>

    <InviteMemberModal
        v-if="permissions.canCreateInvitation"
        :team="team"
        :available-roles="availableRoles"
        :open="inviteDialogOpen"
        @update:open="inviteDialogOpen = $event"
    />

    <RemoveMemberModal
        :team="team"
        :member="memberToRemove"
        :open="removeMemberDialogOpen"
        @update:open="removeMemberDialogOpen = $event"
    />

    <CancelInvitationModal
        :team="team"
        :invitation="invitationToCancel"
        :open="cancelInvitationDialogOpen"
        @update:open="cancelInvitationDialogOpen = $event"
    />

    <DeleteTeamModal
        v-if="permissions.canDeleteTeam && !team.isPersonal"
        :team="team"
        :open="deleteDialogOpen"
        @update:open="deleteDialogOpen = $event"
    />
</template>
