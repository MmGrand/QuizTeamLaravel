<?php

namespace Tests\Feature;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->team = Team::factory()->create();
        $this->team->members()->attach($this->user, ['role' => TeamRole::Member->value]);
        $this->user->switchTeam($this->team);
    }

    /**
     * Visit the games page with the given cookies attached.
     *
     * @param  array<string, string>  $cookies
     */
    private function visitGames(array $cookies = []): TestResponse
    {
        return $this->actingAs($this->user)
            ->call('GET', route('games.index', $this->team), [], $cookies);
    }

    public function test_the_interface_defaults_to_the_configured_locale()
    {
        $this->visitGames()->assertInertia(fn (Assert $page) => $page
            ->where('locale', config('app.locale')));
    }

    public function test_a_locale_cookie_switches_the_shared_translations()
    {
        $this->visitGames(['locale' => 'ru'])
            ->assertInertia(fn (Assert $page) => $page
                ->where('locale', 'ru')
                ->where('translations.Games', 'Игры'));
    }

    public function test_an_unknown_locale_falls_back_to_the_configured_one()
    {
        $this->visitGames(['locale' => 'klingon'])
            ->assertInertia(fn (Assert $page) => $page
                ->where('locale', config('app.locale')));
    }

    public function test_server_side_messages_use_the_same_translation_file()
    {
        $this->app->setLocale('ru');

        $this->assertSame('Игра записана.', __('Game recorded.'));
        $this->assertSame('Игры', __('Games'));
    }

    public function test_every_translation_key_is_a_non_empty_string()
    {
        $translations = json_decode(file_get_contents(lang_path('ru.json')), true);

        $this->assertNotEmpty($translations);

        foreach ($translations as $key => $value) {
            $this->assertIsString($value, "Translation for [{$key}] must be a string.");
            $this->assertNotSame('', trim($value), "Translation for [{$key}] is empty.");
        }
    }
}
