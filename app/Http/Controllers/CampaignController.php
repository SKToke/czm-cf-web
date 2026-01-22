<?php

namespace App\Http\Controllers;

use App\Enums\CampaignStatusEnum;
use App\Enums\CampaignSubscriptionTypeEnum;
use App\Enums\DonorTypeEnum;
use App\Models\Banner;
use App\Models\Campaign;
use App\Models\CampaignSubscription;
use App\Models\Category;
use App\Models\Donor;
use App\Models\Program;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Helpers\FlashHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Jorenvh\Share\Share;
use Mail;

class CampaignController extends Controller
{
    public function index(): View
    {
        $banner = Banner::getBannerFor('Campaigns');

        $campaigns = Campaign::where('campaign_status', CampaignStatusEnum::PUBLISHED->value)
                    ->orderByRaw('CASE WHEN donation_end_time >= NOW() THEN 0 ELSE 1 END')
                    ->orderBy('donation_end_time')
                    ->paginate(12);

        return view('campaign.index')
            ->with([
                'campaigns' => $campaigns,
                'categories' => Category::all(),
                'programs' => Program::all(),
                'banner' => $banner
            ]);
    }

    public function show($slug): View|RedirectResponse
    {
        $campaign = Campaign::findBySlug($slug);

        if($campaign) {
            if (Auth::check()) {
                $relatedCampaigns = $campaign->userRelatedCampaigns(3);
            } else {
                $relatedCampaigns = $campaign->relatedCampaigns(3);
            }

            $shareButtons = (new Share)->currentPage()->facebook()->whatsapp()->linkedin();

            return view('campaign.show')->with([
                        'campaign' => $campaign,
                        'relatedCampaigns' => $relatedCampaigns,
                        'shareButtons' => $shareButtons,
                    ]);
        }

        FlashHelper::trigger('Campaign not found!', 'danger');
        return redirect()->route('campaigns');
    }

    public function filterCampaigns(Request $request)
    {
        $campaigns = Campaign::where('campaign_status', CampaignStatusEnum::PUBLISHED->value);

        if ($request->filled('program_id')) {
            $campaigns->where('program_id', $request->input('program_id'));
        }

        if ($request->filled('campaign_title')) {
            $campaigns->where('title', 'like', '%' . $request->input('campaign_title') . '%');
        }

        if ($request->filled('category_id')) {
            $campaigns->whereHas('categories', function ($query) use ($request) {
                $query->where('categories.id', $request->input('category_id'));
            });
        }

        if ($request->filled('last_date')) {
            $campaigns->where('donation_end_time', '>=', Carbon::parse($request->input('last_date'))->addDay());
        }

        if ($request->filled('min_amount')) {
            $campaigns->where('allocated_amount', '>=', $request->input('min_amount'));
        }

        if ($request->filled('max_amount')) {
            $campaigns->where('allocated_amount', '<=', $request->input('max_amount'));
        }

        $campaigns = $campaigns->orderByRaw('CASE WHEN donation_end_time >= NOW() THEN 0 ELSE 1 END')->orderBy('donation_end_time')->paginate(12);

        return view('campaign.filtered_campaigns', compact('campaigns'));
    }

    public function getDescriptionTab(string $slug)
    {
        $campaign = Campaign::findBySlug($slug);
        if ($campaign) {
            return view('campaign.description', [
                'campaignTitle' => $campaign->title,
                'campaignDescription' => $campaign->description,
            ]);
        } else {
            return response()->json(['error' => 'Campaign not found'], 404);
        }
    }

    public function getDocumentsTab(string $slug)
    {
        $campaign = Campaign::findBySlug($slug);
        $allAttachments = $campaign->attachments;

        $imageAttachments = [];
        $fileAttachments = [];

        foreach ($allAttachments as $attachment) {
            $filePath = '/admin/' . $attachment->file;
            $fileExists = Storage::disk('public')->exists($filePath);
            if ($fileExists) {
                $attachmentType = $this->getAttachmentType($filePath);
                if ($attachmentType === 'image') {
                    $imageAttachments[] = [
                        'title' => $attachment->title,
                        'imagePath' => $filePath,
                    ];
                } else {
                    $fileAttachments[] = [
                        'title' => $attachment->title,
                        'filePath' => $filePath,
                    ];
                }
            }
        }

        return view('campaign.documents', compact('imageAttachments', 'fileAttachments'));
    }

    private function getAttachmentType(string $file): string
    {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($extension, ['pdf', 'doc'])) {
            return 'file';
        } else {
            return 'image';
        }
    }

    public function getCampaignUpdatesTab(string $slug)
    {
        $campaign = Campaign::findBySlug($slug);
        $latest_updates = $campaign->getCampaignUpdates();

        return view('campaign.campaign_updates', compact('latest_updates'));
    }

    public function subscribe(Request $request)
    {
        $confirmation = null;
        $validatedData = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'subscription_type' => 'required|numeric',
            'subscribed_amount' => 'required|numeric|digits_between:1,10',
        ]);
        $campaign = Campaign::findOrFail($validatedData['campaign_id']);
        $subscription = new CampaignSubscription();

        $subscription->campaign_id = $campaign->id;
        $subscription->subscription_type = $validatedData['subscription_type'];
        $subscription->subscribed_amount = $validatedData['subscribed_amount'];
        $subscription->subscription_start_date = now();

        if ($subscription->subscription_type == CampaignSubscriptionTypeEnum::MONTHLY) {
            $subscription->next_donation_date = now()->addMonth();
        } else if ($subscription->subscription_type == CampaignSubscriptionTypeEnum::HALF_YEARLY) {
            $subscription->next_donation_date = now()->addMonths(6);
        } else {
            $subscription->next_donation_date = now()->addYear();
        }
        $subscription->due_amount -= $validatedData['subscribed_amount'];

        if (Auth::check()) {
            $currentUser = Auth::user();
            $donor = $currentUser->active ? $currentUser->findOrCreateDonor() : null;

        } else {
            $validatedDonorData = $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'mobile_no' => 'nullable|numeric',
            ]);
            if (User::where('email', $validatedDonorData['email'])->exists()) {
                FlashHelper::trigger('You are a registered user. Kindly login first to proceed.', 'danger');
                return redirect()->route('campaign-details', ['slug' => $campaign->slug]);
            }
            $donor = Donor::firstOrNew(['email' => $validatedDonorData['email']]);

            if (!$donor->exists) {
                $donor->name = $validatedDonorData['name'];
                $donor->phone = $validatedDonorData['mobile_no'];
                $donor->donor_type = DonorTypeEnum::UNREGISTERED;
                $donor->save();
            }
        }

        if ($donor && $donor->getCampaignSpecificSubscription($campaign->id)) {
            FlashHelper::trigger('You have already subscribed to this campaign!', 'danger');
        } else {
            if ($donor) {
                $subscription->donor_id = $donor->id;
            }
            if ($subscription->save()) {
                FlashHelper::trigger('You have subscribed to this campaign successfully!', 'success');
                $this->sendCampaignSubscriptionMail($subscription);
                $confirmation = 'success';
            } else {
                FlashHelper::trigger('There was a problem while submitting the form! Try Again.', 'danger');
            }
        }
        return redirect()->route('campaign-details', ['slug' => $campaign->slug, 'confirmation' => $confirmation]);
    }

    public function unsubscribe(Request $request)
    {
        $campaign = Campaign::findOrFail($request->campaign_id);
        if ($campaign && auth()->user() && auth()->user()->active && auth()->user()->donor) {
            $subscription = auth()->user()->donor->getCampaignSpecificSubscription($campaign->id);

            if ($subscription) {
                $subscription->active = false;
            }
            if ($subscription && $subscription->save()) {
                FlashHelper::trigger('You have unsubscribed to this campaign successfully!', 'success');
            } else {
                FlashHelper::trigger('Failed to unsubscribe.', 'danger');
            }
        } else {
            FlashHelper::trigger('Sorry, you are not authorized to perform this action.', 'danger');
        }
        return redirect()->route('campaign-details', ['slug' => $campaign->slug]);
    }

    public function updateShareCount($slug)
    {
        $campaign = Campaign::findBySlug($slug);
        if ($campaign) {
            $campaign->share_count = $campaign->share_count + 1;
            $campaign->save();
        }
    }

    public function sendCampaignSubscriptionMail(CampaignSubscription $subscription)
    {
        $user = Donor::find($subscription->donor_id);
        $campaign = Campaign::find($subscription->campaign_id);

        Mail::send('email.campaignSubscriptionEmail', [
            'userName' => $user->name,
            'donationAmount' => $subscription->subscribed_amount,
            'donationFrequency' => $subscription->subscription_type->getTitle(),
            'campaignId' => $campaign->campaign_id,
            'campaignTitle' => $campaign->title
        ],
            function($message) use($user) {
            $message->to($user->email);
            $message->subject("Thank You for Subscribing to Our Campaign!");
        });
    }
}
