<?php

namespace App\Observers;

use App\Models\ContentSection;

class ContentSectionObserver
{
    public function saving(ContentSection $contentSection)
    {
        if (empty($contentSection->image)) {
            // If the image attribute is empty, prevent it from being saved as such
            $originalImage = $contentSection->getOriginal('image');
            if ($originalImage) {
                $contentSection->image = $originalImage; // Preserve the original image
            }
        }
    }
}
