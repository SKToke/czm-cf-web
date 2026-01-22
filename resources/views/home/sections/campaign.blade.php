@php
    use Jorenvh\Share\Share;
@endphp
@if($campaigns->count() > 0)
    <section class="sec-padding meet-Volunteer case-sec">
        <div class="container">
            <div class="row">
                <div class="col-xs-10">
                    <div class="sec-title text-left">
                        <h2>Trending CZM Campaigns</h2>
                        <p>View the campaigns that are most active right now</p>
                        <span class="decor">
                            <span class="inner"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <div class="team-carousel owl-carousel owl-theme">
                    @foreach($campaigns as $index => $campaign)
                        <div class="item">
                            @include('campaign.card', ['campaign' => $campaign, 'itemId' => $index])
                        </div>
                    @endforeach
                </div>
            </div>
            @foreach($campaigns as $index => $campaign)
                @php
                    $campaignRoute = route('campaign-details', ['slug' => $campaign->slug]);
                    $shareButtons = (new Share)->page($campaignRoute)->facebook()->whatsapp()->linkedin();
                    $itemId = $index;
                @endphp
                @include('campaign.campaign_share_modal', ['campaignRoute' => $campaignRoute, 'shareButtons' => $shareButtons, 'itemId' => $itemId])
            @endforeach
            <div class="view-cases-link text-end">
                <a href="{{ route('campaigns') }}">
                    <i class="fa fa-angle-double-right me-2"></i>
                    View All Campaigns
                </a>
            </div>
        </div>
    </section>
@endif
