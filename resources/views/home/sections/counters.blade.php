@php
    use \App\Models\CzmSupportCounter;
    use Carbon\Carbon;

    $counters = CzmSupportCounter::getAllCounters();
    $lastUpdateTime = CzmSupportCounter::max('updated_at');
    $formattedLastUpdateTime = Carbon::parse($lastUpdateTime)->format('d M Y');
@endphp

@if ($counters && count($counters) > 0)
    <div class="czm-program-counters my-5 mx-0 row">
        <div class="col-lg-4 col-md-12 czm-program-counters-text">
            <h2 class="text-center czm-vivid-cyan-text">CZM Support</h2>
            <p class="text-center mt-4">** Last Updated on: {{ $formattedLastUpdateTime }}</p>
        </div>
        <div class="col-lg-8 col-md-12 text-center">
            @foreach ($counters as $counter)
                <div class="single-fact my-2">
                    <div class="icon-box mx-auto d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-{{ $counter['icon'] ?? 'earth-asia' }}"></i>
                    </div>
                    <span class="timer" data-from="0" data-to="{{ $counter['value'] }}" data-speed="3000"
                          data-refresh-interval="50">
                    {{ $counter['value'] }}
                </span>
                    <h5>{{ $counter['label'] }}</h5>
                </div>
            @endforeach
        </div>
    </div>
@endif
