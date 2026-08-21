<?php

namespace Database\Seeders;

use App\Models\Organizer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrganizerSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * The quiz networks games are commonly played with.
     *
     * @var array<int, string>
     */
    protected array $organizers = [
        'Квиз, плиз!',
        'Мозгобойня',
        'Эйнштейн Party',
        'WOW Quiz',
        'Квизиум',
        'GO!Квиз',
    ];

    /**
     * Seed the organizer directory.
     */
    public function run(): void
    {
        foreach ($this->organizers as $name) {
            Organizer::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}
