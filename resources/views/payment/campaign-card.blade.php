<div class="border border-1 px-md-2 px-3 py-1 shadow">
    <h1 class="text-center card-title my-2">Campaign Donation</h1>

    <hr class="my-0">
    <img src="{{ $campaign->getThumbnailImage() }}" alt="Payment Poster" class="my-2">
    <hr class="my-0">

    <h2 class="my-2"><span class="fw-bold">Program:&nbsp;&nbsp;</span>{{ $campaign->program->title_with_subtitle() }}</h2>
    <h2 class="my-2">
                                <span class="fw-bold">
                                    Campaign #{{ $campaign->campaign_id }}:
                                </span>
        {{ $campaign->title }}
    </h2>
    <h2 class="my-2"><span class="fw-bold">Start:&nbsp;</span>
        {{ Carbon\Carbon::parse($campaign->donation_start_time)->format('d F, Y h:i A') }}
    </h2>
    <h2 class="my-2"><span class="fw-bold">End:&nbsp;</span>
        {{ Carbon\Carbon::parse($campaign->donation_end_time)->format('d F, Y h:i A') }}
    </h2>
    <h2 class="my-2"><span class="fw-bold">Raised:&nbsp;&nbsp;</span>{{ $campaign->getFundCount() }} Tk.</h2>
    <h2 class="my-2"><span class="fw-bold">Goal:&nbsp;&nbsp;</span>{{ $campaign->allocated_amount }} Tk.</h2>
</div>
