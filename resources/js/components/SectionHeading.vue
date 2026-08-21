<script setup lang="ts">
type Props = {
    title: string;
    description?: string;
};

defineProps<Props>();
</script>

<template>
    <header class="space-y-1">
        <h2 class="text-base font-medium tracking-tight">{{ title }}</h2>
        <p v-if="description" class="text-sm text-muted-foreground">
            {{ description }}
        </p>
    </header>
</template>
