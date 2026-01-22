<?php

namespace Database\Seeders;

use App\Enums\PublicationTypeEnum;
use App\Models\Publication;
use Illuminate\Database\Seeder;

class PublicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publicationsData = [
            ['title' => 'CZM Brochure'],
            ['title' => 'Zakater Bidhibidhan'],
            ['title' => 'A guide to Zakat'],
            ['title' => 'Zakat Shahayika'],
            ['title' => 'Zakat Calculation Form'],
        ];

        foreach ($publicationsData as $publicationData) {
            $publication = Publication::firstOrNew($publicationData);
            $publication->publication_type = PublicationTypeEnum::STATIC_PAGE_PUBLICATIONS->value;
            $publication->published_date = now();
            $publication->active = true;
            $publication->save();
        }
    }
}
