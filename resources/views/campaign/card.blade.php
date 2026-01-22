<div class="case-card">
    <div class="cases">
        <div class="thumb">
            <div class="thumb-img">
                <a href="{{ route('campaign-details', ['slug' => $campaign->slug]) }}">
                    <img
                        src="{{ $campaign->getThumbnailImage() }}"
                        alt="{{ $campaign->title }} image"
                        class="full-width"
                    >
                </a>
            </div>
            <div class="case-details">
                <h5 class="title">
                    <a href="{{ route('campaign-details', ['slug' => $campaign->slug]) }}">
                        {{ $campaign->getFormattedTitle() }}
                    </a>
                </h5>
                <div class="mb-2">
                    <div class="text-warning fw-bold mb-5" style="height: 20px;">
                        @if($campaign->urgency_status)
                            Emergency!
                        @endif
                    </div>
                    @if(count($campaign->getDonations()) > 0)
                        <span class="me-2">Last Donation at:</span>
                        <span>{{ $campaign->getFormatttedLastDonationDate() }}</span>
                    @else
                        <span class="me-2">No donation yet.</span>
                    @endif
                </div>
                <div>
                    <span class="me-2">Program:</span>
                    <span >{{ $campaign->getFormattedProgramTitle() }}</span>

                </div>
                <div class="info-container mb-2 mt-2">
                    <div class="fund-raised">
                        <span class="me-1">Raised:</span>
                        <span class="case-money">{{ (int)$campaign->getFundCount() }}</span>
                        <span>Tk</span>
                    </div>
                    <div class="fund-goal">
                        <span class="me-1">Goal:</span>
                        <span class="case-money">{{ (int)$campaign->allocated_amount }}</span>
                        <span>Tk</span>
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
                <div class="info-container mb-2">
                    <div class="remaining-days">
                        <i class="fa fa-clock me-2"></i>
                        <span class="me-2">{{ $campaign->getRemainingDays() }}</span>
                        <span>Days Left</span>
                    </div>
                    <div class="supporters">
                        <i class="fa fa-heart me-2"></i>
                        <span class="me-2">{{ $campaign->getTotalSupporters() }}</span>
                        <span>Supporters</span>
                    </div>
                </div>
                <div class="buttons-container mt-3">
                    <button class="thm-btn inverse btn-xs detail-btn" data-bs-toggle="modal" data-bs-target="#campaignShareModal-{{$itemId}}">
                        <i class="fa fa-arrow-right text-theme-colored me-2"></i>
                        <span>Share</span>
                    </button>
                    @if ($campaign->hasDonationValidity())
                        <a href="{{ route('payment.index', ['campaign-id' => $campaign->campaign_id, 'check-donation' => true]) }}" class="thm-btn btn-xs donate-btn">
                            <i class="fa fa-angle-double-right text-white me-2"></i>
                            <span>Donate Now</span>
                        </a>
                    @else
                        <button type="button" class="thm-btn btn-xs donate-btn" disabled>
                            <i class="fa fa-angle-double-right text-white me-2"></i>
                            <span>Donate Now</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
