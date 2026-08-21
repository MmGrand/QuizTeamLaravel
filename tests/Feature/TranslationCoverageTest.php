<?php

namespace Tests\Feature;

use Symfony\Component\Finder\Finder;
use Tests\TestCase;

/**
 * Guards against the interface silently drifting back to English.
 *
 * Every literal key passed to t() in the Vue layer or __() in PHP must have a
 * Russian line, and no template may ship a bare English sentence.
 */
class TranslationCoverageTest extends TestCase
{
    /**
     * Directories that hold generated or vendored code we do not translate.
     *
     * @var array<int, string>
     */
    private const EXCLUDED = ['components/ui', 'actions', 'routes', 'wayfinder'];

    /**
     * Load the Russian translation lines.
     *
     * @return array<string, string>
     */
    private function translations(): array
    {
        return json_decode(file_get_contents(lang_path('ru.json')), true);
    }

    /**
     * Collect the source files that may contain translatable strings.
     */
    private function frontendFiles(): Finder
    {
        return Finder::create()
            ->files()
            ->in(resource_path('js'))
            ->name(['*.vue', '*.ts'])
            ->filter(function ($file) {
                $path = str_replace('\\', '/', $file->getRelativePath());

                foreach (self::EXCLUDED as $excluded) {
                    if (str_starts_with($path, $excluded)) {
                        return false;
                    }
                }

                return true;
            });
    }

    public function test_every_frontend_translation_key_has_a_russian_line()
    {
        $translations = $this->translations();
        $missing = [];

        foreach ($this->frontendFiles() as $file) {
            preg_match_all(
                '/\bt\(\s*([\'"])(.+?)\1/s',
                $file->getContents(),
                $matches,
            );

            foreach ($matches[2] as $key) {
                if (! array_key_exists($key, $translations)) {
                    $missing[$key] = $file->getRelativePathname();
                }
            }
        }

        $this->assertSame([], $missing, 'Untranslated keys: '.json_encode(
            $missing,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT,
        ));
    }

    public function test_every_php_translation_key_has_a_russian_line()
    {
        $translations = $this->translations();
        $missing = [];

        $files = Finder::create()->files()->in(app_path())->name('*.php');

        foreach ($files as $file) {
            preg_match_all(
                '/__\(\s*([\'"])(.+?)\1/s',
                $file->getContents(),
                $matches,
            );

            foreach ($matches[2] as $key) {
                if (! array_key_exists($key, $translations)) {
                    $missing[$key] = $file->getRelativePathname();
                }
            }
        }

        $this->assertSame([], $missing, 'Untranslated keys: '.json_encode(
            $missing,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT,
        ));
    }

    /**
     * Reads as an English sentence a user would see.
     *
     * Requiring a capitalised first word plus a space is what separates prose
     * from the noise that shares its alphabet: Tailwind class lists, kebab-case
     * test ids and template expressions are all lowercase.
     */
    private function looksLikeProse(string $value): bool
    {
        if (! str_contains($value, ' ')) {
            return false;
        }

        if (! preg_match("/^[A-Z][A-Za-z ,.'?!-]*$/", $value)) {
            return false;
        }

        return preg_match_all('/[A-Za-z]{2,}/', $value) >= 2;
    }

    public function test_no_english_sentence_is_hardcoded_outside_a_translation_call()
    {
        $translations = $this->translations();
        $offenders = [];

        foreach ($this->frontendFiles() as $file) {
            $contents = $file->getContents();

            preg_match_all('/"([^"\n]*)"|\'([^\'\n]*)\'/', $contents, $literals);
            $found = array_merge($literals[1], $literals[2]);

            foreach ($found as $literal) {
                if (! $this->looksLikeProse($literal)) {
                    continue;
                }

                // A literal that already has a Russian line came through t().
                if (array_key_exists($literal, $translations)) {
                    continue;
                }

                $offenders[] = $file->getRelativePathname().': '.$literal;
            }
        }

        $this->assertSame([], $offenders, "Hardcoded English:\n".implode("\n", $offenders));
    }

    public function test_no_vue_template_ships_a_bare_english_sentence()
    {
        $offenders = [];

        foreach ($this->frontendFiles() as $file) {
            if ($file->getExtension() !== 'vue') {
                continue;
            }

            if (! preg_match('/<template>(.*)<\/template>/s', $file->getContents(), $template)) {
                continue;
            }

            // Drop interpolations, then attribute values (they can contain
            // ">", as in v-if="a > b"), then every tag, leaving text nodes.
            $body = preg_replace('/\{\{.*?\}\}/s', ' ', $template[1]);
            $body = preg_replace('/=\s*"[^"]*"/s', '=""', $body);
            $body = preg_replace("/=\s*'[^']*'/s", "=''", $body);
            $body = preg_replace('/<[^>]*>/s', "\n", $body);

            foreach (explode("\n", $body) as $line) {
                $text = trim($line);

                // Two or more English words in a row reads as a sentence.
                if (preg_match('/[A-Za-z]{2,}\s+[A-Za-z]{2,}/', $text)) {
                    $offenders[] = $file->getRelativePathname().': '.$text;
                }
            }
        }

        $this->assertSame([], $offenders, "Untranslated template text:\n".implode("\n", $offenders));
    }
}
