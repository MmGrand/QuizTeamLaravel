<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight } from '@lucide/vue';
import { computed } from 'vue';
import AppBrand from '@/components/AppBrand.vue';
import LocaleToggle from '@/components/LocaleToggle.vue';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { Button } from '@/components/ui/button';
import { useTranslations } from '@/composables/useTranslations';
import { dashboard, login, register } from '@/routes';

const page = usePage();
const { t } = useTranslations();

const currentTeam = computed(() => page.props.currentTeam);
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
</script>

<template>
    <Head :title="t('Track your quiz team')" />

    <div class="flex min-h-svh flex-col bg-background text-foreground">
        <header
            class="mx-auto flex w-full max-w-4xl items-center justify-between px-5 py-5 sm:px-6"
        >
            <AppBrand />

            <div class="flex items-center gap-2">
                <LocaleToggle />
                <ThemeToggle class="hidden sm:inline-flex" />
            </div>
        </header>

        <main
            class="mx-auto flex w-full max-w-4xl flex-1 items-center px-5 py-16 sm:px-6"
        >
            <div class="max-w-xl space-y-8">
                <div class="space-y-4">
                    <h1
                        class="text-4xl font-semibold tracking-tight text-balance sm:text-5xl"
                    >
                        {{ t('Track your quiz team') }}
                    </h1>
                    <p class="text-lg text-pretty text-muted-foreground">
                        {{
                            t(
                                'Games, results, line-ups and the whole history of your team in one place.',
                            )
                        }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <template v-if="isAuthenticated">
                        <Button as-child>
                            <Link
                                :href="
                                    currentTeam
                                        ? dashboard(currentTeam.slug)
                                        : '/'
                                "
                            >
                                {{ t('Overview') }}
                                <ArrowRight />
                            </Link>
                        </Button>
                    </template>

                    <template v-else>
                        <Button as-child>
                            <Link :href="register()">
                                {{ t('Get started') }}
                                <ArrowRight />
                            </Link>
                        </Button>

                        <Button variant="ghost" as-child>
                            <Link :href="login()">{{ t('Log in') }}</Link>
                        </Button>
                    </template>
                </div>
            </div>
        </main>
    </div>
</template>
