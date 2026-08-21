<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { LogOut, Settings } from '@lucide/vue';
import { computed } from 'vue';
import { index as gamesIndex } from '@/actions/App/Http/Controllers/GameController';
import AppBrand from '@/components/AppBrand.vue';
import TeamSwitcher from '@/components/TeamSwitcher.vue';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useInitials } from '@/composables/useInitials';
import { useTranslations } from '@/composables/useTranslations';
import { dashboard, logout } from '@/routes';
import { edit as profileEdit } from '@/routes/profile';
import { edit as teamEdit } from '@/routes/teams';

type NavLink = {
    key: string;
    label: string;
    href: string;
};

const page = usePage();
const { t } = useTranslations();
const { getInitials } = useInitials();

const user = computed(() => page.props.auth?.user);
const currentTeam = computed(() => page.props.currentTeam);

const links = computed<NavLink[]>(() => {
    const team = currentTeam.value;

    if (!team) {
        return [];
    }

    return [
        {
            key: 'dashboard',
            label: t('Overview'),
            href: dashboard(team.slug).url,
        },
        { key: 'games', label: t('Games'), href: gamesIndex(team.slug).url },
        { key: 'team', label: t('Team'), href: teamEdit(team.slug).url },
    ];
});

const currentPath = computed(() => page.url.split('?')[0]);

function isActive(href: string): boolean {
    const path = href.split('?')[0];

    return (
        currentPath.value === path || currentPath.value.startsWith(path + '/')
    );
}

function handleLogout(): void {
    router.flushAll();
}
</script>

<template>
    <header
        class="sticky top-0 z-40 border-b border-border/70 bg-background/85 backdrop-blur supports-[backdrop-filter]:bg-background/70"
    >
        <div
            class="mx-auto flex h-14 w-full max-w-4xl items-center gap-3 px-5 sm:px-6"
        >
            <Link
                :href="currentTeam ? dashboard(currentTeam.slug) : '/'"
                class="shrink-0 rounded-md focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
            >
                <AppBrand :show-name="false" />
            </Link>

            <TeamSwitcher v-if="currentTeam" />

            <nav
                v-if="links.length > 0"
                class="-mx-1 ml-auto flex min-w-0 items-center gap-1 overflow-x-auto sm:mx-0 sm:mr-auto sm:ml-6"
                :aria-label="t('Games')"
            >
                <Link
                    v-for="link in links"
                    :key="link.key"
                    :href="link.href"
                    :data-test="'nav-' + link.key"
                    class="shrink-0 rounded-md px-2.5 py-1.5 text-sm transition-colors focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
                    :class="
                        isActive(link.href)
                            ? 'font-medium text-foreground'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                >
                    {{ link.label }}
                </Link>
            </nav>

            <div class="flex shrink-0 items-center gap-2">
                <ThemeToggle class="hidden sm:inline-flex" />

                <DropdownMenu v-if="user">
                    <DropdownMenuTrigger
                        data-test="user-menu-trigger"
                        class="rounded-full focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
                    >
                        <Avatar class="size-8">
                            <AvatarImage
                                v-if="user.avatar"
                                :src="user.avatar"
                                :alt="user.name"
                            />
                            <AvatarFallback class="text-xs">
                                {{ getInitials(user.name) }}
                            </AvatarFallback>
                        </Avatar>
                    </DropdownMenuTrigger>

                    <DropdownMenuContent align="end" class="w-56">
                        <DropdownMenuLabel class="font-normal">
                            <span class="block truncate text-sm font-medium">
                                {{ user.name }}
                            </span>
                            <span
                                class="block truncate text-xs text-muted-foreground"
                            >
                                {{ user.email }}
                            </span>
                        </DropdownMenuLabel>

                        <DropdownMenuSeparator />

                        <DropdownMenuItem as-child>
                            <Link
                                :href="profileEdit()"
                                class="w-full cursor-pointer"
                                prefetch
                            >
                                <Settings class="mr-2 size-4" />
                                {{ t('Settings') }}
                            </Link>
                        </DropdownMenuItem>

                        <DropdownMenuSeparator />

                        <DropdownMenuItem as-child>
                            <Link
                                :href="logout()"
                                as="button"
                                data-test="logout-button"
                                class="w-full cursor-pointer"
                                @click="handleLogout"
                            >
                                <LogOut class="mr-2 size-4" />
                                {{ t('Log out') }}
                            </Link>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>
    </header>
</template>
