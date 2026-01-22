@php
    use \App\Models\Campaign;
@endphp
@if ($donation->campaign)
    <div class="container mb-4 hoverable-card">
        <div class="card shadow-sm donation-history-card">
            <div class="row no-gutters">
                <div class="col-md-2">
                    <div class="square-image rounded-3">
                        <img
                            src="{{ $donation->getThumbnail() }}"
                            alt="donation image"
                            class="donation-thumbnail"
                        >
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card-body">
                        <h5 class="card-title">{{ $donation->getProgramTitle() }}</h5>
                        <p class="card-text text-dark">{{ $donation->getCampaignTitle() }}</p>
                        <div class="raised-text">
                            <div class="row">
                                <div class="w-50">
                                    <p class="font-weight-bold text-dark">Raised: {{ $donation->getRaisedAmount() }}</p>
                                </div>
                                <div class="w-50 d-flex justify-content-end">
                                    <p>{{ $donation->getFundPercentage() ?? 100 }}%</p>
                                </div>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $donation->getFundPercentage() }}%"></div>
                        </div>
                        <p class="mt-2 font-weight-bold">Goal: {{ $donation->getGoalAmount() }}</p>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card-body h-100 d-flex flex-column justify-content-between align-items-end">
                        <div class="d-flex flex-column align-items-end w-100">
                            <span class="font-weight-bold fs-5">BDT&nbsp;{{ $donation->amount }}</span>
                            <p class="color mb-0">{{ $donation->getFormattedDonationDate() }}</p>
                            <p class="color mb-0">{{ $donation->getFormattedDonationTime() }}</p>
                            <p class="fw-bold">{{ $donation->getDonationType() }}</p>
                        </div>
                        @if ($donation->getCampaign()->isAvailable())
                            <a href="{{ route('campaign-details', ['slug' => $donation->getCampaign()->slug]) }}"
                               class="czm-primary-btn" data-turbo="false">Campaign Details</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    @php
        $campaign = Campaign::withTrashed()->where('id', $donation->campaign_id)->first();
        $program = $campaign->program;
    @endphp
    <div class="container mb-4 hoverable-card">
        <div class="card shadow-sm donation-history-card">
            <div class="no-gutters">
                <div class="card-body h-100 d-flex flex-column justify-content-between">
                    <div class="row">
                        <div class="col">
                            <div class="d-flex flex-column align-items-start w-100">
                                <span class="font-weight-bold fs-5">Campaign:&nbsp;{{ $campaign->title }}</span>
                                <p class="color mb-0">Program:&nbsp;{{ $program->title }}</p>
                                <p class="color mb-0">{{ $donation->getDonationType() }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex flex-column align-items-end w-100">
                                <span class="font-weight-bold fs-5">BDT&nbsp;{{ $donation->amount }}</span>
                                <p class="color mb-0">{{ $donation->getFormattedDonationDate() }}</p>
                                <p class="color mb-0">{{ $donation->getFormattedDonationTime() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
