<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';

type Props = {
    showName?: boolean;
};

withDefaults(defineProps<Props>(), {
    showName: true,
});

const name = usePage().props.name;
</script>

<template>
    <span class="flex items-center gap-2.5">
        <span
            aria-hidden="true"
            class="grid size-7 shrink-0 place-items-center rounded-[0.5rem] bg-primary text-[0.7rem] font-semibold tracking-tight text-primary-foreground"
        >
            Q
        </span>
        <span
            v-if="showName"
            class="hidden truncate text-sm font-semibold tracking-tight sm:block"
        >
            {{ name }}
        </span>
    </span>
</template>
