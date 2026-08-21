<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VenueSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Starter venues for the directory.
     *
     * @var array<int, array{name: string, address: string|null}>
     */
    protected array $venues = [
        ['name' => 'Papa Carlo Bar', 'address' => 'ул. Рубинштейна, 3'],
        ['name' => 'Максимилианс', 'address' => 'пр. Ленина, 22'],
        ['name' => 'Harat\'s Pub', 'address' => 'ул. Советская, 15'],
        ['name' => 'Джон Донн', 'address' => 'наб. реки Фонтанки, 40'],
        ['name' => 'Типография', 'address' => null],
    ];

    /**
     * Seed the venue directory.
     */
    public function run(): void
    {
        foreach ($this->venues as $venue) {
            Venue::firstOrCreate(
                ['slug' => Str::slug($venue['name'])],
                ['name' => $venue['name'], 'address' => $venue['address']],
            );
        }
    }
}
