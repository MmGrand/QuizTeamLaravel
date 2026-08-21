import { computed } from 'vue';
import { useTranslations } from '@/composables/useTranslations';

const LOCALE_TAGS: Record<string, string> = {
    ru: 'ru-RU',
    en: 'en-GB',
};

export function useFormatters() {
    const { locale } = useTranslations();

    const tag = computed(() => LOCALE_TAGS[locale.value] ?? 'en-GB');

    function formatDate(value: string): string {
        return new Intl.DateTimeFormat(tag.value, {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        }).format(new Date(value));
    }

    function formatDateTime(value: string): string {
        return new Intl.DateTimeFormat(tag.value, {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date(value));
    }

    function formatScore(value: number): string {
        return new Intl.NumberFormat(tag.value, {
            maximumFractionDigits: 2,
        }).format(value);
    }

    return { formatDate, formatDateTime, formatScore };
}
