@php
    use Jorenvh\Share\Share;
@endphp
<div class="row">
    @if(count($campaigns) > 0)
        @foreach($campaigns as $campaign)
            <div class="col-sm-12 col-md-6 col-lg-4 single-case">
                @include('campaign.card', ['campaign' => $campaign, 'itemId' => $campaign->id])
                @php
                    $campaignRoute = route('campaign-details', ['slug' => $campaign->slug]);
                    $shareButtons = (new Share)->page($campaignRoute)->facebook()->whatsapp()->linkedin();
                @endphp
                @include('campaign.campaign_share_modal', ['campaignRoute' => $campaignRoute, 'shareButtons' => $shareButtons, 'itemId' => $campaign->id])
            </div>
        @endforeach
    @else
        <p class="container text-center">
            No campaign has been found
        </p>
    @endif
</div>
<div class="row pagination text-center">
    {{ $campaigns->links() }}
</div>
