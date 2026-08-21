<?php

namespace Tests\Feature\Games;

use App\Models\Organizer;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_venue_slug_is_generated_from_its_name()
    {
        $venue = Venue::create(['name' => 'Papa Carlo Bar']);

        $this->assertSame('papa-carlo-bar', $venue->slug);
    }

    public function test_distinct_names_that_reduce_to_the_same_slug_get_a_suffix()
    {
        $first = Venue::create(['name' => 'Papa Carlo Bar']);
        $second = Venue::create(['name' => 'Papa Carlo Bar!']);
        $third = Venue::create(['name' => 'Papa Carlo Bar?']);

        $this->assertSame('papa-carlo-bar', $first->slug);
        $this->assertSame('papa-carlo-bar-1', $second->slug);
        $this->assertSame('papa-carlo-bar-2', $third->slug);
    }

    public function test_renaming_a_venue_regenerates_its_slug()
    {
        $venue = Venue::create(['name' => 'Типография']);

        $venue->update(['name' => 'Джон Донн']);

        $this->assertSame('dzon-donn', $venue->slug);
    }

    public function test_organizer_slugs_follow_the_same_rules()
    {
        $organizer = Organizer::create(['name' => 'Квиз, плиз!']);
        $duplicate = Organizer::create(['name' => 'Квиз плиз']);

        $this->assertSame('kviz-pliz', $organizer->slug);
        $this->assertSame('kviz-pliz-1', $duplicate->slug);
    }

    public function test_venues_and_organizers_are_resolved_by_slug_in_routes()
    {
        $venue = Venue::factory()->create();
        $organizer = Organizer::factory()->create();

        $this->assertSame($venue->slug, $venue->getRouteKey());
        $this->assertSame($organizer->slug, $organizer->getRouteKey());
    }
}
