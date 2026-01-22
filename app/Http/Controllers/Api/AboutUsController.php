<?php

namespace App\Http\Controllers\Api;

use App\Enums\PublicationTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Publication;
use App\Traits\HttpResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AboutUsController extends Controller
{
    use HttpResponses;
    public function aboutUs(): JsonResponse
    {
        try {
            $banner = Banner::getBannerFor('About Us');

            $brochure = Publication::where('publication_type', PublicationTypeEnum::STATIC_PAGE_PUBLICATIONS)
                ->where('title', 'CZM Brochure')->first();
            $filePath = null;
            $fileExists = null;
            if ($brochure && $brochure->attachment && $brochure->attachment->file) {
                $filePath = '/admin/' . $brochure->attachment->file;
                $fileExists = Storage::disk('public')->exists($filePath);
            }

            return $this->success('About us',[
                'banner' => $banner->getImageUrl(),
                'filePath' => Storage::disk('public')->url($filePath),
                'fileExists' => $fileExists
            ] );

        } catch (ModelNotFoundException $e) {
            return $this->error(
                'Failed to fetch the data',
                [
                    'message' => $e->getMessage()
                ],
                401
            );

        } catch (\Exception $e) {
            return $this->error(
                'Server error',
                [
                    'message' => $e->getMessage()
                ],
                401
            );
        }
    }
}
