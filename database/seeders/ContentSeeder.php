<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Content;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quranicVerse = Content::firstOrNew(['name' => 'Quranic verse']);

        if ($quranicVerse) {
            $quranicVerse->content_type = 4;
            $quranicVerse->save();
        }

        $successStory = Content::firstOrNew(['name' => 'Success story']);

        if ($successStory) {
            $successStory->content_type = 3;
            $successStory->save();
        }

        $sadaqah = Content::firstOrNew(['name' => 'Sadaqah']);

        if ($sadaqah) {
            $sadaqah->content_type = 5;
            $sadaqah->save();
        }

        $cashWaqf = Content::firstOrNew(['name' => 'WAQF/ CASH WAQF']);

        if ($cashWaqf) {
            $cashWaqf->content_type = 6;
            $cashWaqf->save();
        }

        $qardAlHasan = Content::firstOrNew(['name' => 'Qard al-Hasan']);

        if ($qardAlHasan) {
            $qardAlHasan->content_type = 7;
            $qardAlHasan->save();
        }
    }
}
