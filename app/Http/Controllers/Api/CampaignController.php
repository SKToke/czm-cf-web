<?php

namespace App\Http\Controllers\Api;

use App\Enums\CampaignStatusEnum;
use App\Enums\CampaignSubscriptionTypeEnum;
use App\Enums\DonorTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignSubscription;
use App\Models\Donor;
use App\Models\Program;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mail;

class CampaignController extends Controller
{
    use HttpResponses;

    public function allCampaigns(): JsonResponse
    {
        $data = [];
        $campaigns = Campaign::orderBy('donation_end_time', 'desc')->get();
        foreach ($campaigns as $campaign) {
            if($campaign->isAvailable()) $data []= $this->getCampaignBaseInfo($campaign);
        }
        return $this->success('All available campaigns', $data);
    }

    public function latestCampaigns(): JsonResponse
    {
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

        $data = [];
        foreach ($campaigns as $campaign) {
            $data[] = $this->getCampaignBaseInfo($campaign);
        }

        return $this->success('Latest campaigns', $data);
    }

    public function filteredCampaigns(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'category_slug' => 'nullable|string',
            ]);

            $categorySlug = $request->input('category_slug');

            $campaignsQuery = Campaign::query();

            if ($categorySlug && $categorySlug == 'emergency') {
                $campaignsQuery->where('urgency_status', true);
            } elseif ($categorySlug) {
                $campaignsQuery->whereHas('categories', function ($query) use ($categorySlug) {
                    $query->where('categories.slug', $categorySlug);
                });
            } else {
                return $this->allCampaigns();
            }

            $campaignsQuery->where(function ($query) {
                $query->where('campaign_status', CampaignStatusEnum::PUBLISHED->value)
                    ->orWhere('campaign_status', CampaignStatusEnum::POSTPONED->value);
            })
                ->where('donation_end_time', '>', now())
                ->orderBy('donation_end_time', 'asc');

            $campaigns = $campaignsQuery->get();
            $data = [];
            foreach ($campaigns as $campaign) {
                $data[] = $this->getCampaignBaseInfo($campaign);
            }
            return $this->success('Filtered campaigns', $data);
        }catch (\Exception $e) {
            return $this->error(
                'Server error',
                [
                    'message' => $e->getMessage()
                ],
                401
            );
        }
}

    public function programCampaigns(String $slug): JsonResponse
    {
        $program = Program::findBySlug($slug);
        if(is_null($program)) return $this->error('Program not found');

        $campaigns = $program->campaigns;
        $data = [];
        foreach ($campaigns as $campaign) {
            $data[] = $this->getCampaignBaseInfo($campaign);
        }

        if(empty($data)){
            $message = "No campaign found";
        } else{
            $message = "All campaigns";
        }

        return $this->success("{$message} under program: {$program->title}", $data);
    }

    public function details(String $slug): JsonResponse
    {
        $campaign = Campaign::findBySlug($slug);
        if(is_null($campaign) || !$campaign->isAvailable())
            return $this->error('Campaign not found');

        return $this->success('Campaign details', $this->getCampaignDetailsInfo($campaign));
    }

    protected function getCampaignBaseInfo(Campaign $campaign): array
    {
        return [
            'id' => $campaign->id,
            'campaign_id' => $campaign->campaign_id,
            'slug' => $campaign->slug,
            'title' => $campaign->title,
            'is_donatable' => Campaign::isDonatable($campaign->campaign_id),
            'is_subscribable' => $campaign->isSubscribable(),
            'urgent' => (bool)$campaign->urgency_status,
            'total_collected' => (int)($campaign->getFundCount()),
            'total_allocated' => (int)$campaign->allocated_amount,
            'remaining_days' => (int)$campaign->getRemainingDays(),
            'total_supporters' => (int)$campaign->getTotalSupporters(),
            'has_donation' => count($campaign->getDonations()) > 0,
            'last_donated_at' => $campaign->getFormatttedLastDonationDate(),
            'progress_in_percentage' => (int)$campaign->getFundPercentage(),
            'parent_program_title' => $campaign->program->title,
            'banner' => $campaign->getThumbnailImage(),
            'campaign_share_link' => route('campaign-details', ['slug' => $campaign->slug]),
        ];
    }

    protected function getCampaignDetailsInfo(Campaign $campaign): array
    {
        if($campaign->hasImages()) $images = $campaign->getImages();
        else $images = null;

        if($campaign->hasAttachments()) $attachments = $campaign->getAttachments();
        else $attachments = null;

        return array_merge(
            $this->getCampaignBaseInfo($campaign),
            [
                'description' => $campaign->description,
                'parent_program_slug' => $campaign->program->slug,
                'start' => $campaign->donation_start_time,
                'end' => $campaign->donation_end_time,
                'images' => $images,
                'attachments' => $attachments,
                'is_subscribed' => $this->authUserByToken()?->hasCampaignSubscription($campaign->id),
                'campaign_updates' => $this->campaignUpdateDetails($campaign),
                'related_campaigns' => $this->relatedCampaignsInfo($campaign)
            ]
        );
    }

    protected function relatedCampaignsInfo(Campaign $campaign)
    {
        $relatedCampaigns = null;
        if ($this->authUserByToken()) {
            $relatedCampaigns = $campaign->userRelatedCampaigns( null, $this->authUserByToken());
        } else {
            $relatedCampaigns = $campaign->relatedCampaigns();
        }

        $data = null;
        if ($relatedCampaigns) {
            foreach ($relatedCampaigns as $campaign) {
                $data[] = $this->getCampaignBaseInfo($campaign);
            }
        }
        return $data;
    }

    protected function campaignUpdateDetails(Campaign $campaign)
    {
        $latest_updates = $campaign->getCampaignUpdates();

        $transformed_updates = $latest_updates->map(function ($update) {
            $transformed_update = [
                'id' => $update->id,
                'title' => $update->title,
                'message' => $update->message,
                'date' => $update->updated_at->toDateString(),
                'attachment_count' => $update->attachments->count(),
            ];

            if ($update->attachments->count() > 0) {
                $attachments = [];
                foreach ($update->attachments as $attachment) {
                    $attachment_details = [];
                    $attachment_details['title'] = $attachment->title;
                    $attachment_details['file_path'] = Storage::disk('admin')->url($attachment->file);
                    $attachments[] = $attachment_details;
                }
                $transformed_update['attachments'] = $attachments;
            }
            if ($update->disbursed_amount) {
                $transformed_update['message'] = $transformed_update['message'] . "<p>" . $update->disbursed_amount . "Tk has been disbursed.</p>";
            }
            return $transformed_update;
        });

        return $transformed_updates;
    }

    public function campaignSubscription(Request $request)
    {
        try {
            $status = $request->get('status');
            if ($status == 1) {
                $validatedData = $request->validate([
                    'campaign_slug' => 'required|exists:campaigns,slug',
                    'subscription_type' => 'required|numeric',
                    'subscribed_amount' => 'required|numeric|digits_between:1,10',
                ]);
                $campaign = Campaign::findBySlug($validatedData['campaign_slug']);
                $subscription = new CampaignSubscription();

                $subscription->campaign_id = $campaign->id;
                $subscription->subscription_type = $validatedData['subscription_type'];
                $subscription->subscribed_amount = $validatedData['subscribed_amount'];
                $subscription->subscription_start_date = now();

                if ($subscription->subscription_type == CampaignSubscriptionTypeEnum::MONTHLY->value) {
                    $subscription->next_donation_date = now()->addMonth();
                } else if ($subscription->subscription_type == CampaignSubscriptionTypeEnum::HALF_YEARLY->value) {
                    $subscription->next_donation_date = now()->addMonths(6);
                } else {
                    $subscription->next_donation_date = now()->addYear();
                }
                $subscription->due_amount -= $validatedData['subscribed_amount'];

                $user = $this->authUserByToken();
                if ($user) {
                    $currentUser = $user;
                    $donor = $currentUser->active ? $currentUser->findOrCreateDonor() : null;

                } else {
                    $validatedDonorData = $request->validate([
                        'name' => 'required',
                        'email' => 'required|email',
                        'mobile_no' => 'nullable|numeric',
                    ]);
                    if (User::where('email', $validatedDonorData['email'])->exists()) {
                        return $this->error('You are a registered user. Kindly login first to proceed.', [], 401);
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
                    return $this->error('You have already subscribed to this campaign!', [], 401);
                } else {
                    if ($donor) {
                        $subscription->donor_id = $donor->id;
                    }
                    if ($subscription->save()) {
                        $this->sendCampaignSubscriptionMail($subscription);
                        return $this->success('Subscribed Successfully!');
                    } else {
                        return $this->error('There was a problem while submitting the form! Try Again', [], 401);
                    }
                }
            } else if ($status == 0) {
                $user = $this->authUserByToken();
                if ($user) {
                    $campaign = Campaign::findBySlug($request->campaign_slug);
                    if ($campaign && $user->active && $user->donor) {
                        $subscription = $user->donor->getCampaignSpecificSubscription($campaign->id);

                        if ($subscription) {
                            $subscription->active = false;
                        }
                        if ($subscription && $subscription->save()) {
                            return $this->success('Unsubscribed Successfully!');
                        } else {
                            return $this->error('Failed to unsubscribe', [], 401);
                        }
                    } else {
                        return $this->error('You are not authorized to perform this action', [], 401);
                    }
                } else {
                    return $this->error('You need to login first to proceed', [], 401);
                }
            }
        } catch (\Exception $e) {
            return $this->error('Error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    private function sendCampaignSubscriptionMail(CampaignSubscription $subscription)
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
