<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useTranslations } from '@/composables/useTranslations';
import type {
    GameFormValues,
    MemberOption,
    OrganizerOption,
    VenueOption,
} from '@/types';

type Props = {
    action: Record<string, unknown>;
    venues: VenueOption[];
    organizers: OrganizerOption[];
    members: MemberOption[];
    game?: GameFormValues;
    submitLabel: string;
};

const props = defineProps<Props>();

const { t } = useTranslations();

/** The value a `datetime-local` input expects, in the visitor's own timezone. */
function currentDateTimeLocal(): string {
    const now = new Date();
    const offset = now.getTimezoneOffset() * 60000;

    return new Date(now.getTime() - offset).toISOString().slice(0, 16);
}

const venueId = ref(props.game ? String(props.game.venueId) : '');
const organizerId = ref(props.game ? String(props.game.organizerId) : '');
const playedAt = ref(props.game?.playedAt ?? currentDateTimeLocal());
const participantIds = ref<number[]>(props.game?.participantIds ?? []);

function isParticipant(memberId: number): boolean {
    return participantIds.value.includes(memberId);
}

function toggleParticipant(memberId: number, checked: boolean): void {
    participantIds.value = checked
        ? [...participantIds.value, memberId]
        : participantIds.value.filter((id) => id !== memberId);
}
</script>

<template>
    <Form
        v-bind="props.action"
        class="space-y-8"
        v-slot="{ errors, processing }"
    >
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="grid gap-2">
                <Label for="venue_id">{{ t('Venue') }}</Label>
                <Select
                    v-model="venueId"
                    name="venue_id"
                    data-test="game-venue"
                    required
                >
                    <SelectTrigger id="venue_id" class="w-full">
                        <SelectValue :placeholder="t('Select a venue')" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="venue in props.venues"
                            :key="venue.id"
                            :value="String(venue.id)"
                        >
                            {{ venue.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.venue_id" />
            </div>

            <div class="grid gap-2">
                <Label for="organizer_id">{{ t('Organizer') }}</Label>
                <Select
                    v-model="organizerId"
                    name="organizer_id"
                    data-test="game-organizer"
                    required
                >
                    <SelectTrigger id="organizer_id" class="w-full">
                        <SelectValue :placeholder="t('Select an organizer')" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="organizer in props.organizers"
                            :key="organizer.id"
                            :value="String(organizer.id)"
                        >
                            {{ organizer.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.organizer_id" />
            </div>

            <div class="grid gap-2 sm:col-span-2">
                <Label for="title">{{ t('Theme') }}</Label>
                <Input
                    id="title"
                    name="title"
                    data-test="game-title"
                    :default-value="props.game?.title ?? ''"
                    :placeholder="t('Musical intuition, Harry Potter #3, …')"
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="played_at">{{ t('Played at') }}</Label>
                <Input
                    id="played_at"
                    v-model="playedAt"
                    name="played_at"
                    data-test="game-played-at"
                    type="datetime-local"
                    required
                />
                <InputError :message="errors.played_at" />
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div class="grid gap-2">
                    <Label for="score">{{ t('Score') }}</Label>
                    <Input
                        id="score"
                        name="score"
                        data-test="game-score"
                        type="number"
                        step="0.01"
                        min="0"
                        inputmode="decimal"
                        :default-value="props.game?.score ?? ''"
                        placeholder="58.5"
                        required
                    />
                    <InputError :message="errors.score" />
                </div>

                <div class="grid gap-2">
                    <Label for="place">{{ t('Place') }}</Label>
                    <Input
                        id="place"
                        name="place"
                        data-test="game-place"
                        type="number"
                        min="1"
                        inputmode="numeric"
                        :default-value="props.game?.place ?? ''"
                        placeholder="—"
                    />
                    <InputError :message="errors.place" />
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <div class="space-y-1">
                <Label>{{ t('Line-up') }}</Label>
                <p class="text-sm text-muted-foreground">
                    {{ t('Tick everyone who played this game.') }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <label
                    v-for="member in props.members"
                    :key="member.id"
                    :for="'participant-' + member.id"
                    class="flex cursor-pointer items-center gap-2 rounded-full border border-border/70 py-1.5 pr-3.5 pl-2.5 text-sm transition-colors hover:bg-accent/60 has-[:checked]:border-foreground/30 has-[:checked]:bg-accent"
                >
                    <Checkbox
                        :id="'participant-' + member.id"
                        name="participants[]"
                        data-test="game-participant"
                        :value="String(member.id)"
                        :model-value="isParticipant(member.id)"
                        @update:model-value="
                            toggleParticipant(member.id, $event === true)
                        "
                    />
                    {{ member.name }}
                </label>
            </div>

            <InputError :message="errors.participants" />
        </div>

        <div class="grid gap-2">
            <Label for="notes">{{ t('Notes') }}</Label>
            <Textarea
                id="notes"
                name="notes"
                data-test="game-notes"
                :default-value="props.game?.notes ?? ''"
                :placeholder="t('How did it go?')"
                rows="4"
            />
            <InputError :message="errors.notes" />
        </div>

        <Button type="submit" data-test="game-submit" :disabled="processing">
            {{ props.submitLabel }}
        </Button>
    </Form>
</template>
