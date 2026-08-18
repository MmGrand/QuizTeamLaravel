import { describe, expect, it } from 'vitest';
import { useInitials } from '@/composables/useInitials';

describe('useInitials', () => {
    const { getInitials } = useInitials();

    it('returns an empty string when no name is given', () => {
        expect(getInitials()).toBe('');
        expect(getInitials('   ')).toBe('');
    });

    it('uses a single letter for a one-word name', () => {
        expect(getInitials('Максим')).toBe('М');
    });

    it('combines the first and last word of a multi-word name', () => {
        expect(getInitials('Максим Зарубин')).toBe('МЗ');
        expect(getInitials('  ada   byron   lovelace  ')).toBe('AL');
    });
});
