<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Category;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultProgramsData = [
            [
                'title' => 'INSANIAT',
                'subtitle' => 'Emergency Support',
                'slogan' => 'Emergency Humanitarian Assistance Program',
            ],
            [
                'title' => 'JEEBIKA',
                'subtitle' => 'Livelihood Development',
                'slogan' => 'Livelihood & Human Development Program',
            ],
            [
                'title' => 'MUDAREEB',
                'subtitle' => 'Enterprise Development',
                'slogan' => 'Micro-enterprise Development Program',
            ],
            [
                'title' => 'NAIPUNNA BIKASH',
                'subtitle' => 'Vocational Training',
                'slogan' => 'Vocational Training & Employment Program for Poor Youth',
            ],
            [
                'title' => 'FERDOUSI',
                'subtitle' => 'Primary Healthcare',
                'slogan' => 'Healthcare Program for Distressed Women & Children',
            ],
            [
                'title' => 'GULBAGICHA',
                'subtitle' => 'Pre-primary Education',
                'slogan' => 'Education & Nutrition Program for Underprivileged Children',
            ],
            [
                'title' => 'GENIUS',
                'subtitle' => 'Scholarship',
                'slogan' => 'Scholarship Program for Undergraduate Students',
            ],
            [
                'title' => 'DAWAH',
                'subtitle' => 'Awareness Building',
                'slogan' => 'Awareness Building & Motivational Program',
            ]
        ];

        foreach ($defaultProgramsData as $programData) {
            $program = Program::firstOrNew(['title' => $programData['title']]);

            if (!$program->exists) {
                $program->subtitle = $programData['subtitle'];
                $program->slogan = $programData['slogan'];
                $program->default = true;
                $program->save();
            }

            if ($program->exists) {
                Category::firstOrCreate([
                    'title' => $program->title,
                    'program_id' => $program->id,
                ]);
            }
        }
    }
}
