@php
    use Jorenvh\Share\Share;
@endphp
<x-main>
    <div class="container">
        <h3 class="text-center czm-primary-text my-4">{{ $program->title_with_slogan() }}</h3>
        <div class="row">
            <div class="col-md-6">
                @if($program->hasPhotos())
                    <section class="rev_slider_wrapper splide" data-controller="splide">
                        <div class="splide__track">
                            <ul class="splide__list">
                                @foreach($program->getPhotos() as $programPhoto)
                                    <li class="splide__slide program-slider">
                                        <img
                                            src="{{ $programPhoto }}"
                                            alt="{{ $program->title }} image"
                                            class="mw-100"
                                        >
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </section>
                @endif
            </div>
            <div class="col-md-6 text-black">
                @if($program->objective)
                    <h5 class="fw-bold">Objective</h5>
                    <p>{!! $program->objective !!}</p>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 text-black">
                @if($program->strategy)
                    <h5 class="fw-bold">Strategy</h5>
                    <p>{!! $program->strategy !!}</p>
                @endif
                @if($program->activities_description)
                    <h5 class="fw-bold">Activities</h5>
                    <p>{!! $program->activities_description !!}</p>
                @endif
            </div>
        </div>
{{--        Todo: Attachment section--}}
{{--        Todo: Stories section--}}
    </div>
    @if($program->hasAnyCounter())
        @include('program.counters', ['program' => $program])
    @endif

    @if($relatedCampaigns && $relatedCampaigns->isNotEmpty())
        <section class="related-cases-sec mb-40 czm-solid-bg-1">
            <div class="container">
                <h3>Related Campaigns to this Program</h3>
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
                <div class="view-cases-link text-end">
                    <a href="{{ route('campaigns') }}">
                        <i class="fa fa-angle-double-right me-2"></i>
                        View All Campaigns
                    </a>
                </div>
            </div>
        </section>
    @endif

    @if(!$contents->isEmpty())
        <h3 class="container czm-primary-text">News related to this program</h3>
        @include('home.sections.latest_news')
        <div class="container">
            <div class="view-cases-link text-end">
                <a href="{{ route('news') }}">
                    <i class="fa fa-angle-double-right me-2"></i>
                    View All News
                </a>
            </div>
        </div>
    @endif

    @if(!$videos->isEmpty())
        <h3 class="container czm-primary-text" style="margin-bottom: 40px;margin-top: 40px">Videos related to this program</h3>
        @include('home.sections.video_gallery')
        <div class="container">
            <div class="view-cases-link text-end">
                <a href="{{ route('video-gallery') }}">
                    <i class="fa fa-angle-double-right me-2"></i>
                    View All Videos
                </a>
            </div>
        </div>
    @endif

    @if(!$imagesWithTitle->isEmpty())
        <h3 class="container czm-primary-text" style="margin-bottom: 40px;margin-top: 40px">Photos related to this program</h3>
        @include('home.sections.photo_gallery')
        <div class="container">
            <div class="view-cases-link text-end">
                <a href="{{ route('photo-gallery') }}">
                    <i class="fa fa-angle-double-right me-2"></i>
                    View All Photos
                </a>
            </div>
        </div>
    @endif

    @if($program->links() && $program->links()->count() > 0)
        <h3 class="container czm-primary-text" style="margin-bottom: 30px;margin-top: 40px">Some links related to this program</h3>
        <div class="container text-black">
            <ol>
                @foreach ($program->links()->get() as $link)
                    <li>
                        <span>{{ $link->title ? $link->title . ': ' : '' }}</span>
                        @if ($link->label)
                            <span>
                                <a href="{{ $link->link }}" target="_blank">{{ $link->label }}</a>
                            </span>
                        @else
                            <span>
                                <a href="{{ $link->link }}" target="_blank">{{ $link->link }}</a>
                            </span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    @endif

    <script src="{{ asset('js/campaign_copy_link.js') }}"></script>
</x-main>
