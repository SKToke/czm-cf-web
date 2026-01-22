<div class="container mb-2">
    <div class="card shadow-sm notice-card">
        <div class="no-gutters">
            <div class="card-body h-100 d-flex flex-column justify-content-between">
                <div class="row">
                    <div class="col">
                        <div class="d-flex flex-column align-items-start w-100">
                            @if ($type == 'general')
                                <p class="fw-bold">General Donation</p>
                            @elseif ($type == 'usual')
                                <span class="font-weight-bold fs-5">Program: &nbsp;<a href="{{ route('program-details', ['slug' => $program->slug]) }}">{{ $program->title }}</a></span>
                                <p class="fw-bold">Campaign: <a href="{{ route('campaign-details', ['slug' => $campaign->slug]) }}">{{ $campaign->title }}</a></p>
                            @else
                                <span class="font-weight-bold fs-5">Program: &nbsp;{{ $program->title }}</span>
                                <p class="fw-bold">Campaign: {{ $campaign->title }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex flex-column align-items-end w-100">
                            <p class="color fw-bold mb-0">Payment Amount: {{ $payment->amount }}</p>
                            <p class="color fw-bold mb-0">Date: {{ $payment->created_at->format('Y-m-d') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
