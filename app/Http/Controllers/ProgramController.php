<?php

namespace App\Http\Controllers;

use App\Enums\ContentTypeEnum;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Program;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Helpers\FlashHelper;

class ProgramController extends Controller
{
    public function index(): View
    {
        $banner = Banner::getBannerFor('Programs');
        return view('program.index')
            ->with(['programs' => Program::where('active', true)->get(), 'banner' => $banner]);

    }

    public function show($slug): View|RedirectResponse
    {
        $program = Program::findBySlug($slug);
        if($program && $program->active) {

            $selectedContentType= ContentTypeEnum::NEWS;
            $category = $program->category;
            $contents = $category->contents();
            $videos=$category->videogalleries();
            $photos=$category->photogalleries();

            if($selectedContentType) {
                $contents = $contents->where('content_type', $selectedContentType);
            }

            $contents = $contents->get();
            $videos = $videos->get();
            $photos = $photos->get();

            foreach ($category->children as $subcategory) {
                $subContents = $subcategory->contents();
                $subVideos = $subcategory->videogalleries();
                $subPhotos = $subcategory->photogalleries();

                if($selectedContentType) {
                    $subContents = $subContents->where('content_type', $selectedContentType);
                }

                $contents = $contents->merge($subContents->get());
                $videos = $videos->merge($subVideos->get());
                $photos = $photos->merge($subPhotos->get());
            }

            $contents = $contents->unique('id');
            $videos = $videos->unique('id');
            $photos = $photos->unique('id');
            $contents = $contents->take(3);

            $relatedCampaigns = $program->campaigns()->get()->sortBy('donation_end_time')->take(3);
            $videos = $videos->take(4);
            $photos = $photos->take(4);

            $allImagesWithTitles = [];

            foreach ($photos as $photo) {
                $allImagesWithTitles[] = [
                    'image' => $photo->getImageUrl(),
                    'title' => $photo->title,
                ];
            }

            $imagesWithTitle = collect($allImagesWithTitles);

            return view('program.show')
                ->with([
                    'program' => $program,
                    'relatedCampaigns' => $relatedCampaigns,
                    'contents' => $contents,
                    'videos' => $videos,
                    'imagesWithTitle' => $imagesWithTitle,
                ]);
        }

        FlashHelper::trigger('Program not found!', 'danger');
        return redirect()->route('programs');
    }
}
