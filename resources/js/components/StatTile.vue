<script setup lang="ts">
type Props = {
    label: string;
    value: string;
    hint?: string;
};

defineProps<Props>();
</script>

<template>
    <div class="space-y-1 rounded-lg border border-border/70 px-4 py-3.5">
        <div class="text-xs tracking-wide text-muted-foreground uppercase">
            {{ label }}
        </div>
        <div class="text-2xl font-semibold tracking-tight tabular-nums">
            {{ value }}
        </div>
        <div v-if="hint" class="text-xs text-muted-foreground">{{ hint }}</div>
    </div>
</template>
