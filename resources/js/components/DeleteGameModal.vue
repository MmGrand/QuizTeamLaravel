<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { destroy } from '@/actions/App/Http/Controllers/GameController';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { useTranslations } from '@/composables/useTranslations';
import type { GameListItem } from '@/types';

type Props = {
    game: GameListItem | null;
    teamSlug: string;
    open: boolean;
};

const props = defineProps<Props>();
const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const { t } = useTranslations();
</script>

<template>
    <Dialog :open="props.open" @update:open="emit('update:open', $event)">
        <DialogContent>
            <Form
                v-if="props.game"
                v-bind="destroy.form([props.teamSlug, props.game.id])"
                class="space-y-6"
                v-slot="{ processing }"
                @success="emit('update:open', false)"
            >
                <DialogHeader>
                    <DialogTitle>{{ t('Delete this game?') }}</DialogTitle>
                    <DialogDescription>
                        {{
                            t(
                                "The game and its line-up will be removed from your team's history. This cannot be undone from the app.",
                            )
                        }}
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary">{{ t('Cancel') }}</Button>
                    </DialogClose>

                    <Button
                        type="submit"
                        variant="destructive"
                        data-test="delete-game-submit"
                        :disabled="processing"
                    >
                        {{ t('Delete game') }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
