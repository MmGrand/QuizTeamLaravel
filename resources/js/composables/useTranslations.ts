import { usePage } from '@inertiajs/vue3';
import type { ComputedRef } from 'vue';
import { computed } from 'vue';
import type { Locale } from '@/types';

export type Replacements = Record<string, string | number>;

export type UseTranslationsReturn = {
    t: (key: string, replacements?: Replacements) => string;
    locale: ComputedRef<Locale>;
};

const LOCALE_COOKIE_DAYS = 365;

/**
 * Swap `:name` style placeholders for their values, mirroring Laravel's
 * translation replacements so one JSON file serves both sides of the app.
 */
function applyReplacements(line: string, replacements: Replacements): string {
    return Object.entries(replacements).reduce(
        (carry, [key, value]) => carry.split(':' + key).join(String(value)),
        line,
    );
}

export function setLocaleCookie(value: Locale): void {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = LOCALE_COOKIE_DAYS * 24 * 60 * 60;

    document.cookie =
        'locale=' + value + ';path=/;max-age=' + maxAge + ';SameSite=Lax';
}

export function useTranslations(): UseTranslationsReturn {
    const page = usePage();

    function t(key: string, replacements: Replacements = {}): string {
        const line = page.props.translations?.[key] ?? key;

        return applyReplacements(line, replacements);
    }

    return {
        t,
        locale: computed(() => (page.props.locale ?? 'ru') as Locale),
    };
}
