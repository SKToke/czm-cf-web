<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Program;
class ImageFetcherService
{
    public static function fetchImages()
    {
        $programs = Program::where('default', true)->limit(8)->get();

        $imageData = [];

        foreach ($programs as $program) {
            $cases = $program->campaigns;

            if ($cases->isNotEmpty()) {
                $randomCase = $cases->random();

                if($randomCase->hasImages()){
                    $images = $randomCase->getImages();
                    $lastImage = end($images);

                    $imageData[] = ['image' => $lastImage, 'title' => $randomCase->title];
                } else {
                    $imageData[] = self::fetchRandomImageWithTitleFromOtherProgram($programs);
                }

            } else {
                $imageData[] = self::fetchRandomImageWithTitleFromOtherProgram($programs);
            }
        }

        return collect($imageData)->filter()->take(8);
    }

    private static function fetchRandomImageWithTitleFromOtherProgram($programs)
    {
        $programIds = $programs->pluck('id');
        $otherCases = Campaign::whereNotIn('program_id', $programIds)->get();

        if ($otherCases->isEmpty()) {
            return null;
        }

        $randomCase = $otherCases->random();
        // Ensure $randomCase->images is not null or use optional() helper
        return optional($randomCase->images)->isNotEmpty() ? ['image' => $randomCase->images->last(), 'title' => $randomCase->title] : null;
    }

}
