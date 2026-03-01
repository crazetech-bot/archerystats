<?php

namespace Database\Seeders;

use App\Models\RoundType;
use Illuminate\Database\Seeder;

class RoundTypeSeeder extends Seeder
{
    public function run(): void
    {
        $rounds = [

            // ── Indoor ────────────────────────────────────────────────────────────────
            [
                'name'               => 'WA 18m Indoor',
                'category'           => 'indoor',
                'discipline'         => 'recurve/barebow',
                'distance_meters'    => 18,
                'target_face_cm'     => 40,
                'scoring_system'     => 'standard',
                'num_ends'           => 20,
                'arrows_per_end'     => 3,
                'max_score_per_arrow'=> 10,
                'description'        => 'WA Indoor 18m — 60 arrows, 40cm face, X·10–1·M. Recurve & Barebow.',
            ],
            [
                'name'               => 'WA 18m Indoor Compound',
                'category'           => 'indoor',
                'discipline'         => 'compound',
                'distance_meters'    => 18,
                'target_face_cm'     => 40,
                'scoring_system'     => 'reduced',
                'num_ends'           => 20,
                'arrows_per_end'     => 3,
                'max_score_per_arrow'=> 10,
                'description'        => 'WA Indoor 18m Compound — 60 arrows, 40cm face, X·10–6·M (inner 10 ring).',
            ],
            [
                'name'               => 'WA 25m Indoor',
                'category'           => 'indoor',
                'discipline'         => 'recurve',
                'distance_meters'    => 25,
                'target_face_cm'     => 60,
                'scoring_system'     => 'standard',
                'num_ends'           => 20,
                'arrows_per_end'     => 3,
                'max_score_per_arrow'=> 10,
                'description'        => 'WA Indoor 25m — 60 arrows, 60cm face, X·10–1·M.',
            ],

            // ── Outdoor Target ─────────────────────────────────────────────────────────
            [
                'name'               => 'WA 90m Outdoor',
                'category'           => 'outdoor',
                'discipline'         => 'recurve',
                'distance_meters'    => 90,
                'target_face_cm'     => 122,
                'scoring_system'     => 'standard',
                'num_ends'           => 12,
                'arrows_per_end'     => 6,
                'max_score_per_arrow'=> 10,
                'description'        => 'WA Outdoor 90m — 72 arrows, 122cm face. Men\'s long distance.',
            ],
            [
                'name'               => 'WA 70m Outdoor Recurve',
                'category'           => 'outdoor',
                'discipline'         => 'recurve',
                'distance_meters'    => 70,
                'target_face_cm'     => 122,
                'scoring_system'     => 'standard',
                'num_ends'           => 12,
                'arrows_per_end'     => 6,
                'max_score_per_arrow'=> 10,
                'description'        => 'WA Outdoor 70m — 72 arrows, 122cm face. Olympic Recurve standard distance.',
            ],
            [
                'name'               => 'WA 60m Outdoor',
                'category'           => 'outdoor',
                'discipline'         => 'recurve',
                'distance_meters'    => 60,
                'target_face_cm'     => 122,
                'scoring_system'     => 'standard',
                'num_ends'           => 12,
                'arrows_per_end'     => 6,
                'max_score_per_arrow'=> 10,
                'description'        => 'WA Outdoor 60m — 72 arrows, 122cm face.',
            ],
            [
                'name'               => 'WA 50m Outdoor Recurve',
                'category'           => 'outdoor',
                'discipline'         => 'recurve',
                'distance_meters'    => 50,
                'target_face_cm'     => 122,
                'scoring_system'     => 'standard',
                'num_ends'           => 12,
                'arrows_per_end'     => 6,
                'max_score_per_arrow'=> 10,
                'description'        => 'WA Outdoor 50m Recurve — 72 arrows, 122cm face.',
            ],
            [
                'name'               => 'WA 50m Outdoor Barebow',
                'category'           => 'outdoor',
                'discipline'         => 'barebow',
                'distance_meters'    => 50,
                'target_face_cm'     => 122,
                'scoring_system'     => 'standard',
                'num_ends'           => 12,
                'arrows_per_end'     => 6,
                'max_score_per_arrow'=> 10,
                'description'        => 'WA Outdoor 50m Barebow — 72 arrows, 122cm face. No sights/stabilisers beyond 12cm.',
            ],
            [
                'name'               => 'WA 50m Outdoor Compound',
                'category'           => 'outdoor',
                'discipline'         => 'compound',
                'distance_meters'    => 50,
                'target_face_cm'     => 80,
                'scoring_system'     => 'reduced',
                'num_ends'           => 12,
                'arrows_per_end'     => 6,
                'max_score_per_arrow'=> 10,
                'description'        => 'WA Outdoor 50m Compound — 72 arrows, 80cm face, X·10–6·M.',
            ],
            [
                'name'               => 'WA 30m Outdoor',
                'category'           => 'outdoor',
                'discipline'         => 'recurve',
                'distance_meters'    => 30,
                'target_face_cm'     => 122,
                'scoring_system'     => 'standard',
                'num_ends'           => 12,
                'arrows_per_end'     => 6,
                'max_score_per_arrow'=> 10,
                'description'        => 'WA Outdoor 30m — 72 arrows, 122cm face.',
            ],

            // ── Field Archery ─────────────────────────────────────────────────────────
            [
                'name'               => 'WA Field Marked',
                'category'           => 'field',
                'discipline'         => 'field',
                'distance_meters'    => null,
                'target_face_cm'     => null,
                'scoring_system'     => 'field',
                'num_ends'           => 24,
                'arrows_per_end'     => 3,
                'max_score_per_arrow'=> 6,
                'description'        => 'WA Field Marked — 24 targets × 3 arrows, distances 10–60m (marked), X·6–1·M scoring.',
            ],
            [
                'name'               => 'WA Field Unmarked',
                'category'           => 'field',
                'discipline'         => 'field',
                'distance_meters'    => null,
                'target_face_cm'     => null,
                'scoring_system'     => 'field',
                'num_ends'           => 24,
                'arrows_per_end'     => 3,
                'max_score_per_arrow'=> 6,
                'description'        => 'WA Field Unmarked — 24 targets × 3 arrows, distances not disclosed (estimate range).',
            ],

            // ── 3D Archery ────────────────────────────────────────────────────────────
            [
                'name'               => 'WA 3D Round',
                'category'           => '3d',
                'discipline'         => '3d',
                'distance_meters'    => null,
                'target_face_cm'     => null,
                'scoring_system'     => '3d',
                'num_ends'           => 24,
                'arrows_per_end'     => 1,
                'max_score_per_arrow'=> 20,
                'description'        => 'WA 3D — 24 foam animal targets × 1 arrow, ~5–45m unmarked, scoring: 20/17/10/M.',
            ],


            // ── Legacy / Training ────────────────────────────────────────────────────
            [
                'name'               => 'Short Indoor (18 arrows)',
                'category'           => 'indoor',
                'discipline'         => 'recurve',
                'distance_meters'    => 18,
                'target_face_cm'     => 40,
                'scoring_system'     => 'standard',
                'num_ends'           => 6,
                'arrows_per_end'     => 3,
                'max_score_per_arrow'=> 10,
                'description'        => '6 ends × 3 arrows = 18 arrows. Short training format.',
            ],
            [
                'name'               => 'WA 50m Outdoor',
                'category'           => 'outdoor',
                'discipline'         => 'recurve',
                'distance_meters'    => 50,
                'target_face_cm'     => 122,
                'scoring_system'     => 'standard',
                'num_ends'           => 12,
                'arrows_per_end'     => 6,
                'max_score_per_arrow'=> 10,
                'description'        => 'WA Outdoor 50m — 72 arrows, 122cm face (legacy name).',
            ],

            // ── Multi-distance rounds ────────────────────────────────────────
            [
                'name'                => 'WA 1440 Round (Men)',
                'category'            => 'outdoor',
                'discipline'          => 'recurve',
                'distance_meters'     => 90,
                'target_face_cm'      => 122,
                'scoring_system'      => 'standard',
                'num_ends'            => 24,
                'arrows_per_end'      => 6,
                'max_score_per_arrow' => 10,
                'distance_segments'   => [
                    ['distance' => 90, 'face' => 122, 'num_ends' => 6],
                    ['distance' => 70, 'face' => 122, 'num_ends' => 6],
                    ['distance' => 50, 'face' => 122, 'num_ends' => 6],
                    ['distance' => 30, 'face' => 122, 'num_ends' => 6],
                ],
                'description'         => 'Full WA 1440 outdoor round. 4 distances: 90m, 70m, 50m, 30m — each 6 ends of 6 arrows. Max score 1440.',
            ],
            [
                'name'                => 'WA 1440 Round (Women)',
                'category'            => 'outdoor',
                'discipline'          => 'recurve',
                'distance_meters'     => 70,
                'target_face_cm'      => 122,
                'scoring_system'      => 'standard',
                'num_ends'            => 24,
                'arrows_per_end'      => 6,
                'max_score_per_arrow' => 10,
                'distance_segments'   => [
                    ['distance' => 70, 'face' => 122, 'num_ends' => 6],
                    ['distance' => 60, 'face' => 122, 'num_ends' => 6],
                    ['distance' => 50, 'face' => 122, 'num_ends' => 6],
                    ['distance' => 30, 'face' => 122, 'num_ends' => 6],
                ],
                'description'         => 'Full WA 1440 outdoor round (Women). 4 distances: 70m, 60m, 50m, 30m — each 6 ends of 6 arrows. Max score 1440.',
            ],
            [
                'name'                => 'MSSM (U18) Recurve',
                'category'            => 'mssm',
                'discipline'          => 'recurve',
                'distance_meters'     => 70,
                'target_face_cm'      => 122,
                'scoring_system'      => 'standard',
                'num_ends'            => 24,
                'arrows_per_end'      => 6,
                'max_score_per_arrow' => 10,
                'distance_segments'   => [
                    ['distance' => 70, 'face' => 122, 'num_ends' => 6, 'scoring' => 'standard'],
                    ['distance' => 60, 'face' => 122, 'num_ends' => 6, 'scoring' => 'standard'],
                    ['distance' => 50, 'face' => 80,  'num_ends' => 6, 'scoring' => 'standard'],
                    ['distance' => 30, 'face' => 80,  'num_ends' => 6, 'scoring' => 'reduced'],
                ],
                'description'         => 'MSSM U18 outdoor recurve round. 70m/122cm, 60m/122cm, 50m/80cm, 30m/80cm — each 6 ends of 6 arrows. 30m uses reduced (5–10+X) face.',
            ],
            [
                'name'                => 'MSSM (U15) Recurve',
                'category'            => 'mssm',
                'discipline'          => 'recurve',
                'distance_meters'     => 60,
                'target_face_cm'      => 122,
                'scoring_system'      => 'standard',
                'num_ends'            => 24,
                'arrows_per_end'      => 6,
                'max_score_per_arrow' => 10,
                'distance_segments'   => [
                    ['distance' => 60, 'face' => 122, 'num_ends' => 6, 'scoring' => 'standard'],
                    ['distance' => 50, 'face' => 122, 'num_ends' => 6, 'scoring' => 'standard'],
                    ['distance' => 40, 'face' => 80,  'num_ends' => 6, 'scoring' => 'standard'],
                    ['distance' => 30, 'face' => 80,  'num_ends' => 6, 'scoring' => 'reduced'],
                ],
                'description'         => 'MSSM U15 outdoor recurve round. 60m/122cm, 50m/122cm, 40m/80cm, 30m/80cm — each 6 ends of 6 arrows. 30m uses reduced (5–10+X) face.',
            ],
            [
                'name'                => 'MSSM (U12) Recurve',
                'category'            => 'mssm',
                'discipline'          => 'recurve',
                'distance_meters'     => 40,
                'target_face_cm'      => 122,
                'scoring_system'      => 'standard',
                'num_ends'            => 24,
                'arrows_per_end'      => 6,
                'max_score_per_arrow' => 10,
                'distance_segments'   => [
                    ['distance' => 40, 'face' => 122, 'num_ends' => 6, 'scoring' => 'standard'],
                    ['distance' => 30, 'face' => 122, 'num_ends' => 6, 'scoring' => 'standard'],
                    ['distance' => 25, 'face' => 80,  'num_ends' => 6, 'scoring' => 'standard'],
                    ['distance' => 20, 'face' => 80,  'num_ends' => 6, 'scoring' => 'reduced'],
                ],
                'description'         => 'MSSM U12 outdoor recurve round. 40m/122cm, 30m/122cm, 25m/80cm, 20m/80cm — each 6 ends of 6 arrows. 20m uses reduced (5–10+X) face.',
            ],

            // ── Bakat Kebangsaan ─────────────────────────────────────────────
            [
                'name'                => 'Bakat Kebangsaan (U15)',
                'category'            => 'bakat',
                'discipline'          => 'recurve',
                'distance_meters'     => 50,
                'target_face_cm'      => 80,
                'scoring_system'      => 'standard',
                'num_ends'            => 24,
                'arrows_per_end'      => 6,
                'max_score_per_arrow' => 10,
                'distance_segments'   => [
                    ['distance' => 50, 'face' => 80, 'num_ends' => 6, 'scoring' => 'reduced',  'label' => '50m-1 · 80cm'],
                    ['distance' => 50, 'face' => 80, 'num_ends' => 6, 'scoring' => 'reduced',  'label' => '50m-2 · 80cm'],
                    ['distance' => 30, 'face' => 80, 'num_ends' => 6, 'scoring' => 'standard', 'label' => '30m-1 · 80cm'],
                    ['distance' => 30, 'face' => 80, 'num_ends' => 6, 'scoring' => 'reduced',  'label' => '30m-2 · 80cm'],
                ],
                'description'         => 'Bakat Kebangsaan U15. 50m-1/80cm (reduced), 50m-2/80cm (reduced), 30m-1/80cm (complete), 30m-2/80cm (reduced). 24 ends × 6 arrows.',
            ],
            [
                'name'                => 'Bakat Kebangsaan (U17)',
                'category'            => 'bakat',
                'discipline'          => 'recurve',
                'distance_meters'     => 70,
                'target_face_cm'      => 122,
                'scoring_system'      => 'standard',
                'num_ends'            => 12,
                'arrows_per_end'      => 6,
                'max_score_per_arrow' => 10,
                'distance_segments'   => [
                    ['distance' => 70, 'face' => 122, 'num_ends' => 6, 'scoring' => 'standard', 'label' => '70m-1 · 122cm'],
                    ['distance' => 70, 'face' => 122, 'num_ends' => 6, 'scoring' => 'standard', 'label' => '70m-2 · 122cm'],
                ],
                'description'         => 'Bakat Kebangsaan U17. Two 70m/122cm rounds (complete scoring). 12 ends × 6 arrows = 72 total arrows.',
            ],
        ];

        foreach ($rounds as $round) {
            RoundType::updateOrCreate(
                ['name' => $round['name']],
                array_merge($round, ['active' => true])
            );
        }

        // Deactivate removed categories so they no longer appear in the UI
        RoundType::where('category', 'clout')->update(['active' => false]);
    }
}
