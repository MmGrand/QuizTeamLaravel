<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppBrand from '@/components/AppBrand.vue';
import LocaleToggle from '@/components/LocaleToggle.vue';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { Toaster } from '@/components/ui/sonner';
import { useTranslations } from '@/composables/useTranslations';
import { home } from '@/routes';

/**
 * Titles arrive as English source strings from each page's layout options,
 * which run outside a component setup, so translating happens here.
 */
const { title = '', description = '' } = defineProps<{
    title?: string;
    description?: string;
}>();

const { t } = useTranslations();
</script>

<template>
    <div class="flex min-h-svh flex-col bg-background text-foreground">
        <div class="flex items-center justify-between px-5 py-5 sm:px-6">
            <Link
                :href="home()"
                class="rounded-md focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
            >
                <AppBrand />
            </Link>

            <div class="flex items-center gap-2">
                <LocaleToggle />
                <ThemeToggle />
            </div>
        </div>

        <main
            class="flex flex-1 items-center justify-center px-5 py-10 sm:px-6"
        >
            <div class="w-full max-w-sm">
                <header v-if="title" class="mb-8 space-y-1.5">
                    <h1 class="text-xl font-semibold tracking-tight">
                        {{ t(title) }}
                    </h1>
                    <p v-if="description" class="text-sm text-muted-foreground">
                        {{ t(description) }}
                    </p>
                </header>

                <slot />
            </div>
        </main>

        <Toaster />
    </div>
</template>
