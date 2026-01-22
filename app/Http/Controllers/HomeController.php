<?php

namespace App\Http\Controllers;

use App\Enums\CampaignStatusEnum;
use App\Enums\PublicationTypeEnum;
use App\Models\Banner;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Content;
use App\Models\HeroSection;
use App\Models\Member;
use App\Models\PhotoGallery;
use App\Models\Program;
use App\Models\GovernancePage;
use App\Models\Publication;
use App\Models\VideoGallery;
use App\Models\VideoLesson;
use App\Services\ImageFetcherService;
use Illuminate\Http\Request;
use App\Models\NewsletterSubscription;
use App\Models\Committee;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{

    public function home(): View
    {
        $heroes = HeroSection::anyActive() ? HeroSection::getActives() : null;
        $campaigns = Campaign::where('campaign_status', CampaignStatusEnum::PUBLISHED->value)
                            ->where('donation_end_time', '>', now())
                            ->orderBy('donation_end_time', 'asc')
                            ->limit(4)
                            ->get();
        $totalCampaigns = $campaigns->count();

        if ($totalCampaigns < 3) {
            $additionalCampaigns = $campaigns;
            $campaigns = $campaigns->concat($additionalCampaigns);
            $campaigns = $campaigns->concat($additionalCampaigns);
            $campaigns = $campaigns->take(3);
        }

        $photos = PhotoGallery::orderBy('id', 'desc')->take(8)->get();
        $news = Content::news()->orderBy('created_at', 'desc')->limit(3)->get();
        $videos = VideoGallery::orderBy('id', 'desc')->take(4)->get();

        $allImagesWithTitles = [];

        foreach ($photos as $photo) {
            $allImagesWithTitles[] = [
                'image' => $photo->getImageUrl(),
                'title' => $photo->title,
            ];
        }

        $imagesWithTitle = collect($allImagesWithTitles);

        return view('home.landing')
            ->with([
                'programs' => Program::getDefaults(),
                'heroes' => $heroes,
                'campaigns' => $campaigns,
                'imagesWithTitle' => $imagesWithTitle,
                'contents' => $news,
                'videos' => $videos,
                ]);
    }

    public function aboutUs(): View
    {
        $banner = Banner::getBannerFor('About Us');

        $brochure = Publication::where('publication_type', PublicationTypeEnum::STATIC_PAGE_PUBLICATIONS)
                                ->where('title', 'CZM Brochure')->first();
        $filePath = null;
        $fileExists = null;
        if ($brochure && $brochure->attachment && $brochure->attachment->file)  {
            $filePath = '/admin/' . $brochure->attachment->file;
            $fileExists = Storage::disk('public')->exists($filePath);
        }
        return view("home.about_us")->with([
            'banner' => $banner,
            'filePath' => $filePath,
            'fileExists' => $fileExists
        ]);
    }

    public function zakatInHadiths(): View
    {
        $banner = Banner::getBannerFor('Zakat in Hadith');

        $publications = [
            'zakatBidhibidhan' => 'Zakater Bidhibidhan',
            'guideToZakat' => 'A guide to Zakat',
            'zakatShahayika' => 'Zakat Shahayika',
            'zakatForm' => 'Zakat Calculation Form',
        ];

        $data = ['banner' => $banner];

        foreach ($publications as $key => $title) {
            [$filePath, $fileExists] = $this->getPublicationFileDetails($title);
            $data["{$key}FilePath"] = $filePath;
            $data["{$key}FileExists"] = $fileExists;
        }

        return view("content.zakat_in_hadiths")->with($data);
    }

    public function personalZakat(): View
    {

        $banner = Banner::getBannerFor('Personal Zakat');

        $publications = [
            'zakatBidhibidhan' => 'Zakater Bidhibidhan',
            'guideToZakat' => 'A guide to Zakat',
            'zakatShahayika' => 'Zakat Shahayika',
            'zakatForm' => 'Zakat Calculation Form',
        ];

        $data = ['banner' => $banner];

        foreach ($publications as $key => $title) {
            [$filePath, $fileExists] = $this->getPublicationFileDetails($title);
            $data["{$key}FilePath"] = $filePath;
            $data["{$key}FileExists"] = $fileExists;
        }

        return view("content.personal_zakat")->with($data);
    }

    private function getPublicationFileDetails(string $title): array
    {
        $publication = Publication::where('publication_type', PublicationTypeEnum::STATIC_PAGE_PUBLICATIONS)
            ->where('title', $title)
            ->first();

        if ($publication && $publication->attachment && $publication->attachment->file) {
            $filePath = '/admin/' . $publication->attachment->file;
            $fileExists = Storage::disk('public')->exists($filePath);
            return [$filePath, $fileExists];
        }

        return [null, false];
    }

    public function businessZakat(): View
    {

        $banner = Banner::getBannerFor('Business Zakat');

        return view("content.business_zakat")->with([
            'banner' => $banner,
        ]);
    }

    public function zakatOnAgriculture(): View
    {

        $banner = Banner::getBannerFor('Ushar/ Zakat on Agriculture');

        return view("content.zakat_on_agriculture")->with([
            'banner' => $banner,
        ]);
    }


    public function accountability(): View
    {
        $banner = Banner::getBannerFor('Accountability');

        return view("home.accountability")->with([
                'banner' => $banner,
            ]);
    }

    public function termsAndConditions(): View
    {
        return view("home.terms-conditions-policies");
    }

    public function zakatFaq(): View
    {
        return view("home.zakat-faq");
    }

    public function privacyPolicy(): View
    {
        return view("home.privacy-policy");
    }

    public function czmGovernance(): View
    {
        $banner = Banner::getBannerFor('CZM Governance');
        $governance = GovernancePage::latest()->first();
        $committees = Committee::orderBy('position')->with('committeeMembers')->get();

        return view('home.czm_governance', compact(
            'governance',
            'committees',
            'banner'
        ));
    }

    public function czmMemberDetails($id): View
    {
        $member = Member::findOrFail($id);

        return view('home.czm_member_details', compact('member'));
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:newsletter_subscriptions,email',
            'phone' => 'nullable|numeric'
        ]);

        $subscription = new NewsletterSubscription($validated);

        if ($subscription->save()) {
            $message = ['message' => 'Successfully submitted.','status' => 'success'];
        } else {
            $errorMessages = $subscription->errors()->all();
            $message = ['message' => 'There was a problem submitting: ','status' => 'fail' . implode(' ', $errorMessages)];
        }

        if ($request->ajax()) {
            return response()->json($message);
        } else {
            return redirect()->back()->with($message);
        }
    }

    public function photoGallery(Request $request): View
    {
        $photos = PhotoGallery::all();
        $banner = Banner::getBannerFor('Photo Gallery');
        $allImagesWithTitles = [];

        foreach ($photos as $photo) {
            $allImagesWithTitles[] = [
                'image' => $photo->getImageUrl(),
                'title' => $photo->title,
            ];
        }

        $imagesCollection = collect($allImagesWithTitles);

        $perPage = 12;

        $currentPageItems = $imagesCollection->slice(($request->input('page', 1) - 1) * $perPage, $perPage)->all();

        $imagesWithTitle = new LengthAwarePaginator($currentPageItems, count($imagesCollection), $perPage);

        $imagesWithTitle->setPath($request->url());

        $categories = Category::all();

        return view('home.photo_gallery', compact('imagesWithTitle', 'categories', 'banner'));
    }


    public function filterPhotos(Request $request)
    {
        $photos = PhotoGallery::query();
        $categories = Category::all();
        $banner = Banner::getBannerFor('Photo Gallery');

        if ($request->filled('category_id') && $request->category_id != 0) {
            $photos = $photos->whereHas('categories', function($query) use ($request) {
                $query->where('categories.id', $request->category_id);
            });
        }


        $photos = $photos->paginate(12)->appends(['category_id' => $request->category_id]);

        $imagesWithTitle = $photos->map(function ($photo) {
            return ['image' => $photo->getImageUrl(), 'title' => $photo->title];
        });

        return view('home.photo_gallery', compact('imagesWithTitle', 'categories', 'banner'));
    }

    public function videoGallery(): View
    {
        $videos = VideoGallery::paginate(12);
        $categories = Category::all();
        $banner = Banner::getBannerFor('Video Gallery');

        return view('home.video_gallery', compact(
            'videos',
            'categories',
            'banner'
        ));
    }

    public function videoLessons(): View
    {
        // Get all videos
        $videos = VideoLesson::all();

        // Filter videos based on types
        $zakatVideos = $videos->where('lesson_type', \App\Enums\VideoLessonTypeEnum::Zakat_Is_the_Right_Of_The_Deprived_In_Wealth->value);
        $fiqhOfZakatVideos = $videos->where('lesson_type', \App\Enums\VideoLessonTypeEnum::Fiqh_Of_Zakat->value);

        // Get categories and banner
        $categories = [];
        $banner = Banner::getBannerFor('Videos and Lectures');

        // Pass the variables to the view
        return view('home.video_lesson', compact(
            'zakatVideos',
            'fiqhOfZakatVideos',
            'categories',
            'banner'
        ));
    }

    public function filterVideos(Request $request)
    {
        $videos = VideoGallery::query();

        $categories = Category::all();
        $banner = Banner::getBannerFor('Video Gallery');

        if ($request->filled('category_id') && $request->category_id != 0) {
            $videos = $videos->whereHas('categories', function($query) use ($request) {
                $query->where('categories.id', $request->category_id);
            });
        }

        $videos = $videos->paginate(12)->appends(['category_id' => $request->category_id]);

        return view('home.video_gallery', compact(
            'videos',
            'categories',
            'banner'
        ));
    }

}
