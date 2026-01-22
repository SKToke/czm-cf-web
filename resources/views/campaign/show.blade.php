@php
    use Carbon\Carbon;
    use App\Enums\CampaignTypeEnum;
    use Jorenvh\Share\Share;

    $description = "Campaign from CrowdFunding Application -Center for Zakat Management, Bangladesh";
@endphp
<x-main :title="$campaign->title" :description="$description">
    <section class="inner-header case-detail-top" style="background-image: url({{ $campaign->getThumbnailImage() }})">
        <div class="container">
            <div class="row">
                <div class="col-md-12 sec-title colored text-center">
                    <h2 class="case-title">{{ $campaign->title }}</h2>
                    <ul class="breadcumb">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><i class="fa fa-angle-right"></i></li>
                        <li><a href="{{ route('campaigns') }}">Campaigns</a></li>
                        <li><i class="fa fa-angle-right"></i></li>
                        <li><span>Campaign Detail</span></li>
                    </ul>
                    @if ($campaign->urgency_status && $campaign->hasDonationValidity())
                        <p class="urgent-alert">This campaign is in an urgent need of funds!</p>
                    @endif
                    @auth
                        @if (auth()->user()->hasCampaignSubscription($campaign->id))
                            <p class="subscription-alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                You have subscribed to this campaign
                            </p>
                        @endif
                    @endauth
                    <span class="decor"><span class="inner"></span></span>
                </div>
            </div>
        </div>
    </section>
    <section class="case-detail">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    @if ($campaign->hasImages())
                        <div class="case-detail-slider mb-4 splide" data-controller="splide">
                            <div class="splide__track">
                                <ul class="splide__list">
                                    @foreach($campaign->getImages() as $campaignImage)
                                        <li class="splide__slide hero-slider">
                                            <img
                                                src="{{ $campaignImage }}"
                                                alt="{{ $campaign->title }} image"
                                            >
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    <nav class="case-detail-tabs nav nav-pills flex-column flex-sm-row" data-campaign-slug="{{ $campaign->slug }}">
                        <a href="{{ route('description_campaign_tab', ['slug' => $campaign->slug]) }}" class="flex-sm-fill text-sm-center nav-link active" id="case-description">
                            <i class="fa fa-bars me-2"></i> Description
                        </a>
                        <a href="{{ route('documents_campaign_tab', ['slug' => $campaign->slug]) }}" class="flex-sm-fill text-sm-center nav-link" id="case-documents">
                            <i class="fa fa-file me-2"></i> Documents
                        </a>
                        <a href="{{ route('updates_campaign_tab', ['slug' => $campaign->slug]) }}" class="flex-sm-fill text-sm-center nav-link" id="case-updates">
                            <i class="fa fa-circle-exclamation me-2"></i> Updates
                        </a>
                    </nav>
                    <div id="case_tab_content">
                        <section class="case-description mt-4">
                            <h4 class="case-title">{{ $campaign->title }}</h4>
                            <div class="description-paragraph">
                                {!! $campaign->description !!}
                            </div>
                        </section>
                    </div>
                    <div class="card social-share-card mb-4" data-campaign-slug="{{ $campaign->slug }}">
                        <div class="card-body p-0">
                            <div class="social-button-rectangle">
                                {!! $shareButtons !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    @if ($campaign->hasDonationValidity())
                        <a href="{{ route('payment.index', ['campaign-id' => $campaign->campaign_id, 'check-donation' => true]) }}" class="thm-btn donate-btn"><i class="fa-solid fa-hand-holding-medical me-2"></i>Donate Now</a>
                    @else
                        <button class="thm-btn donate-btn disabled-btn" title="Donation time has been over!"><i class="fa-solid fa-hand-holding-medical me-2"></i>Donate Now</button>
                    @endif
                    @if ($campaign->campaign_type == CampaignTypeEnum::SUBSCRIPTION && (!auth()->user() || !auth()->user()->hasCampaignSubscription($campaign->id)))
                        @if ($campaign->hasDonationValidity())
                            <button type="button" class="thm-btn subscribe-btn" data-bs-toggle="modal" data-bs-target="#subscribeCampaignModal"><i class="fa-solid fa-bell me-2"></i>Support Regularly</button>
                            @include('campaign.subscribe_campaign_modal')
                        @else
                            <button class="thm-btn subscribe-btn disabled-btn" title="Subscription time has been over!"><i class="fa-solid fa-bell me-2"></i>Support Regularly</button>
                        @endif
                    @endif
                    @if(request()->has('confirmation') && request()->query('confirmation') == 'success')
                        @include('campaign.subscription-successful')
                    @endif
                    @auth
                        @if (auth()->user()->hasCampaignSubscription($campaign->id))
                            <form method="POST" action="{{ route('unsubscribe-campaign', ['slug' => $campaign->slug]) }}" class="d-inline">
                                @csrf
                                @method('POST')
                                <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
                                <button type="submit" class="thm-btn subscribe-btn">
                                    <i class="fa-solid fa-bell me-2"></i>
                                    Unsubscribe Now
                                </button>
                            </form>
                        @endif
                    @endauth
                    <div class="case-social-share" data-campaign-slug="{{ $campaign->slug }}">
                        <h6>Help us by share</h6>
                        <div class="social-share-with-link">
                            <div class="social-button-circle">
                                {!! $shareButtons !!}
                            </div>
                            <button class="copyButton copy-link-btn" data-url="{{ route('campaign-details', ['slug' => $campaign->slug]) }}"><i class="fa-solid fa-link"></i></button>
                        </div>
                    </div>
                    <div class="mb-2 mt-3 sec-campaign-id">
                        <span>Campaign ID: #{{ $campaign->campaign_id }}</span>
                    </div>
                    <div class="mb-2 mt-3">
                        <div class="start-date">
                            <span class="me-1">{{ $campaign->donation_start_time < now() ? 'Started From:' : 'Starts From:' }}</span>
                            <span>{{ Carbon::parse($campaign->donation_start_time)->format('d F, Y h:i A') }}</span>
                        </div>
                        <div class="end-date mt-2">
                            <span class="me-1">{{ $campaign->donation_end_time < now() ? 'Ended On:' : 'Ends On:' }}</span>
                            <span>{{ Carbon::parse($campaign->donation_end_time)->format('d F, Y h:i A') }}</span>
                        </div>
                    </div>
                    <div class="mb-2 mt-2">
                        <div class="row">
                            <div class="col">
                                <div class="fund-raised">
                                    <span class="me-1">Raised:</span>
                                    <span class="case-money">{{ (int)$campaign->getFundCount() }}</span>
                                    <span>Tk</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="fund-goal text-end">
                                    <span class="me-1">Goal:</span>
                                    <span class="case-money">{{ (int)$campaign->allocated_amount }}</span>
                                    <span>Tk</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="progress-item">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $campaign->getFundPercentage() }}%;" aria-valuenow="{{ $campaign->getFundPercentage() }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="value-holder">
                                    <span class="value">{{ (int)$campaign->getFundPercentage() }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="case-info mb-2">
                        <div class="remaining-days">
                            <i class="fa fa-clock me-2"></i>
                            <span class="days me-2">{{ $campaign->getRemainingDays() }}</span>
                            <span>Days Left</span>
                        </div>
                        <div class="supporters">
                            <i class="fa fa-heart me-2"></i>
                            <span class="supporters me-2">{{ $campaign->getTotalSupporters() }}</span>
                            <span>Supporters</span>
                        </div>
                    </div>
                    <div class="card case-program-card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Program</h5>
                            <p class="card-text">{{ $campaign->program->title_with_subtitle() }}</p>
                        </div>
                    </div>
                    @if ($campaign->categories->isNotEmpty())
                        <div class="card case-categories-card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Category</h5>
                                <p class="card-text">
                                <ul>
                                    @foreach ($campaign->categories as $category)
                                        <li>{{ $category->title }}</li>
                                    @endforeach
                                </ul>
                                </p>
                            </div>
                        </div>
                    @endif
                    <div class="card case-query-card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Have any question?</h5>
                            <a href="{{ route('contact-us.index', ['campaign_id' => $campaign->id]) }}" class="btn case-query-btn mt-2" role="button">
                                <i class="fa-solid fa-circle-question me-2"></i>Contact Us
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if($relatedCampaigns && $relatedCampaigns->isNotEmpty())
        <section class="related-cases-sec czm-solid-bg-1">
            <div class="container">
                <h3>Related Campaigns</h3>
                <div class="row">
                    @foreach($relatedCampaigns as $campaign)
                        <div class="col-sm-12 col-md-6 col-lg-4 single-case">
                            @include('campaign.card', ['campaign' => $campaign, 'itemId' => $campaign->id])
                            @php
                                $campaignRoute = route('campaign-details', ['slug' => $campaign->slug]);
                                $shareButtons = (new Share)->page($campaignRoute)->facebook()->whatsapp()->linkedin();
                            @endphp
                            @include('campaign.campaign_share_modal', ['campaignRoute' => $campaignRoute, 'shareButtons' => $shareButtons, 'itemId' => $campaign->id])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <script src="{{ asset('js/campaign_detail_tabs.js') }}"></script>
    <script src="{{ asset('js/share_campaign_counter.js') }}"></script>
    <script src="{{ asset('js/campaign_copy_link.js') }}"></script>

</x-main>
