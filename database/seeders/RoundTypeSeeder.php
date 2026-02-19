<?php

namespace Database\Seeders;

use App\Models\RoundType;
use Illuminate\Database\Seeder;

class RoundTypeSeeder extends Seeder
{
    public function run(): void
    {
        $rounds = [
            [
                'name'               => 'Short Indoor (18 arrows)',
                'category'           => 'indoor',
                'distance_meters'    => 18,
                'num_ends'           => 6,
                'arrows_per_end'     => 3,
                'max_score_per_arrow'=> 10,
                'description'        => '6 ends × 3 arrows = 18 arrows. Common short training format.',
            ],
            [
                'name'               => 'WA 18m Indoor',
                'category'           => 'indoor',
                'distance_meters'    => 18,
                'num_ends'           => 20,
                'arrows_per_end'     => 3,
                'max_score_per_arrow'=> 10,
                'description'        => 'World Archery 18m indoor round. 20 ends × 3 arrows = 60 arrows.',
            ],
            [
                'name'               => 'WA 25m Indoor',
                'category'           => 'indoor',
                'distance_meters'    => 25,
                'num_ends'           => 20,
                'arrows_per_end'     => 3,
                'max_score_per_arrow'=> 10,
                'description'        => 'World Archery 25m indoor round. 20 ends × 3 arrows = 60 arrows.',
            ],
            [
                'name'               => 'WA 30m Outdoor',
                'category'           => 'outdoor',
                'distance_meters'    => 30,
                'num_ends'           => 6,
                'arrows_per_end'     => 6,
                'max_score_per_arrow'=> 10,
                'description'        => 'World Archery 30m outdoor round. 6 ends × 6 arrows = 36 arrows.',
            ],
            [
                'name'               => 'WA 50m Outdoor',
                'category'           => 'outdoor',
                'distance_meters'    => 50,
                'num_ends'           => 12,
                'arrows_per_end'     => 6,
                'max_score_per_arrow'=> 10,
                'description'        => 'World Archery 50m outdoor round. 12 ends × 6 arrows = 72 arrows.',
            ],
            [
                'name'               => 'WA 70m Outdoor',
                'category'           => 'outdoor',
                'distance_meters'    => 70,
                'num_ends'           => 12,
                'arrows_per_end'     => 6,
                'max_score_per_arrow'=> 10,
                'description'        => 'World Archery 70m outdoor round. 12 ends × 6 arrows = 72 arrows.',
            ],
        ];

        foreach ($rounds as $round) {
            RoundType::firstOrCreate(
                ['name' => $round['name']],
                array_merge($round, ['active' => true])
            );
        }
    }
}
