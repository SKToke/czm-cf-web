@php
  use App\Enums\CampaignStatusEnum;
  use App\Enums\CampaignSubscriptionTypeEnum;
  use Carbon\Carbon;
@endphp
<div class="card shadow-sm subscription-history-card mb-20">
    <div class="card-body">
        <div class="row">
            <div class="col-md-7">
                <div>
                    <h5 class="card-title">{{ $subscription->subscription_type->getTitle()}}</h5>
                </div>
                @if ($subscription->campaign->isAvailable())
                    <p class="card-text"><a href="{{ route('campaign-details', ['slug' => $subscription->campaign->slug]) }}">{{ $subscription->campaign->title }}</a></p>
                @else
                    <p class="card-text">{{ $subscription->campaign->title }}</p>
                @endif
                <div>
                    <p>You have subscribed to this campaign on <strong class="ms-1">{{ Carbon::parse($subscription->created_at)->format('d F, Y') }}</strong></p>
                    @if ($subscription->last_donated)
                        <p>You have last donated to this campaign on <strong class="ms-1">{{ Carbon::parse($subscription->last_donated)->format('d F, Y') }}</strong></p>
                    @else
                        <p>You haven't donated yet.</p>
                    @endif
                </div>
            </div>
            <div class="col-md-5 text-end">
                <div class="amount-sec">
                    @if ($subscription->due_amount < 0)
                        <p class="due">Due: BDT {{ abs($subscription->due_amount) }}</p>
                    @else
                        <p class="advanced">Advanced: BDT {{ $subscription->due_amount }}</p>
                    @endif
                    <p>Your Subscribed Donation Amount: BDT {{ $subscription->subscribed_amount }}</p>
                    <p>Your Total Donation: BDT {{ $subscription->getUserTotalSubscribedDonation() }}</p>
                    @if ($subscription->campaign->isAvailable())
                        <a href="{{ route('campaign-details', ['slug' => $subscription->campaign->slug]) }}" data-turbo="false" class="btn view-btn">See Campaign Details</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
