@php
    use Carbon\Carbon;
@endphp
@if ($upcomingDonations)
    <div class="text-dark">
        @foreach($upcomingDonations as $donation)
            @php
                $campaign = $donation->campaign;
            @endphp
            <div class="container mb-4 hoverable-card">
                <div class="card shadow-sm donation-history-card">
                    <div class="row no-gutters">
                        <div class="col-md-2">
                            <div class="square-image rounded-3">
                                <img
                                    src="{{ $campaign->getThumbnailImage() }}"
                                    alt="donation image"
                                    class="donation-thumbnail"
                                >
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="card-body">
                                <h5 class="card-title">{{ $campaign->program->title }}</h5>
                                <p class="card-text text-dark">{{ $campaign->title }}</p>
                                <div class="raised-text">
                                    <div class="row">
                                        <div class="w-50">
                                            <p class="font-weight-bold text-dark">Raised: {{ $campaign->getFundCount() }}</p>
                                        </div>
                                        <div class="w-50 d-flex justify-content-end">
                                            <p>{{ $campaign->getFundPercentage() ?? 100 }}%</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ $campaign->getFundPercentage() }}%"></div>
                                </div>
                                <p class="mt-2 font-weight-bold">Goal: {{ $campaign->allocated_amount }}</p>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="card-body h-100 d-flex flex-column justify-content-between align-items-end">
                                <div class="d-flex flex-column align-items-end w-100">
                                    <span class=" text-red fs-5">Due: BDT {{ abs($donation->due_amount) }}</span>
                                    <p class="color mb-0">Your subscribed donation amount: BDT {{ $donation->subscribed_amount }}</p>
                                    @if ($donation->last_donated)
                                        <p class="color mb-2">Last donated on <strong class="ms-1">{{ Carbon::parse($donation->last_donated)->format('d F, Y') }}</strong></p>
                                    @else
                                        <p class="color mb-2">You haven't donated yet.</p>
                                    @endif
                                    <p class="fw-bold">Your Next Donation Date: {{ Carbon::parse($donation->next_donation_date)->format('d F, Y') }}</p>
                                </div>
                                @if ($campaign->isAvailable())
                                    <a href="{{ route('campaign-details', ['slug' => $campaign->slug]) }}"
                                       class="czm-primary-btn" data-turbo="false">Campaign Details</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
