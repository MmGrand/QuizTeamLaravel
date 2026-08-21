<script setup lang="ts">
import AppTopBar from '@/components/AppTopBar.vue';
import { Toaster } from '@/components/ui/sonner';
</script>

<template>
    <div class="flex min-h-svh flex-col bg-background text-foreground">
        <AppTopBar />

        <main class="mx-auto w-full max-w-4xl flex-1 px-5 py-10 sm:px-6">
            <slot />
        </main>

        <Toaster />
    </div>
</template>
