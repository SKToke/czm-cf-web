<?php

namespace App\Http\Controllers;
use App\Enums\ContentTypeEnum;
use App\Models\Banner;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Content;

class ContentController extends Controller
{

    public function blogs()
    {
        $banner = Banner::getBannerFor('Blogs & Articles');
        $blogs = Content::blogs()->paginate(12);
        return view('content.index', ['contents' => $blogs,'categories' => Category::all(),'banner' => $banner,'content_type' => ContentTypeEnum::BLOG->value]);
    }


    public function news()
    {
        $banner = Banner::getBannerFor('News');
        $news = Content::news()->paginate(12);
        return view('content.index', ['contents' => $news,'categories' => Category::all(),'banner' => $banner,'content_type' => ContentTypeEnum::NEWS->value]);
    }


    public function filterContent(Request $request)
    {
        $selectedCategoryId = $request->input('category_id');
        $selectedContentType = $request->input('content_type');

        if($selectedContentType==ContentTypeEnum::NEWS->value){
            $banner = Banner::getBannerFor('News');
        }else{
            $banner = Banner::getBannerFor('Blogs & Articles');
        }
        $contentQuery = Content::query();

        if ($selectedContentType) {
            $contentQuery->where('content_type', $selectedContentType);
        }

        if ($selectedCategoryId != 0) {
            $category = Category::with('children')->findOrFail($selectedCategoryId);

            $contentIds = $category->contents()
                ->when($selectedContentType, function ($query, $selectedContentType) {
                    return $query->where('content_type', $selectedContentType);
                })
                ->pluck('contents.id')->toArray();

            foreach ($category->children as $child) {
                $childContentIds = $child->contents()
                    ->when($selectedContentType, function ($query, $selectedContentType) {
                        return $query->where('content_type', $selectedContentType);
                    })
                    ->pluck('contents.id')->toArray();

                $contentIds = array_merge($contentIds, $childContentIds);
            }

            // Ensure unique IDs
            $contentIds = array_unique($contentIds);

            // Use the collected IDs to filter contents
            $contentQuery->whereIn('id', $contentIds);
        }

        $contents = $contentQuery->paginate(12)->appends([
            'category_id' => $selectedCategoryId,
            'content_type' => $selectedContentType
        ]);


        return view('content.index', ['contents' => $contents,'categories' => Category::all(),'banner' => $banner,'content_type' =>$selectedContentType ]);

    }


    public function contentDetails($id)
    {
        $content = Content::where('slug', $id)->orWhere('id', $id)->firstOrFail();
        $contentSections = $content->contentSections()->orderBy('position')->get();

        return view('content.content_details',  [
            'content' => $content,
            'contentSections' => $contentSections,
            'ContentTypeEnum' => ContentTypeEnum::class,
        ]);
    }

    public function quranicVerses()
    {
        $banner = Banner::getBannerFor('Zakat in Quran');
        $content = Content::quranicverse()->first();
        if($content){
            $contentSections = $content->contentSections()->orderBy('position')->get();
        }
        else{
            $contentSections=[];
        }
        return view('content.content_details', compact('content', 'contentSections','banner'));
    }

    public function successStories()
    {
        $banner = Banner::getBannerFor('Success Stories');
        $content = Content::stories()->first();
        if($content){
            $contentSections = $content->contentSections()->orderBy('position')->get();
        }
        else{
            $contentSections=[];
        }
        return view('content.content_details', compact('content', 'contentSections', 'banner'));
    }

    public function qardAlHasan()
    {
        $banner = Banner::getBannerFor('Qard Al Hasan');
        $content = Content::qardAlHasan()->first();
        if($content){
            $contentSections = $content->contentSections()->orderBy('position')->get();
        }
        else{
            $contentSections=[];
        }
        return view('content.content_details', compact('content', 'contentSections', 'banner'));
    }

    public function sadaqah()
    {
        $banner = Banner::getBannerFor('Sadaqah');
        $content = Content::sadaqah()->first();
        if($content){
            $contentSections = $content->contentSections()->orderBy('position')->get();
        }
        else{
            $contentSections=[];
        }
        return view('content.content_details', compact('content', 'contentSections', 'banner'));
    }

    public function cashWaqf()
    {
        $banner = Banner::getBannerFor('Waqf/ Cash Waqf');
        $content = Content::CashWaqf()->first();
        if($content){
            $contentSections = $content->contentSections()->orderBy('position')->get();
        }
        else{
            $contentSections=[];
        }
        return view('content.content_details', compact('content', 'contentSections', 'banner'));
    }
}
