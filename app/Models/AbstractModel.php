<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbstractModel extends Model
{
    public function getDynamicImageUrl($relativeImagePath): string
    {
        if($relativeImagePath) return 'storage/admin/' . $relativeImagePath;

        return 'images/image_placeholder.png';
    }
}
