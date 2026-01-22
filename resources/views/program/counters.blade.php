<div class="czm-program-counters my-5 mx-0 row">
    <div class="col-lg-4 col-md-12 czm-program-counters-text">
        <h2 class="text-center czm-vivid-cyan-text">{{ $program['title'] }}</h2>
        <h2 class="text-center">{{ $program['subtitle'] }}</h2>
        <p class="text-center mt-4">** Last Updated on: {{ $program->updated_at->format('d M Y') }}</p>
    </div>
    <div class="col-lg-8 col-md-12 text-center">
        @foreach ($program->getAllCounters() as $counter)
            <div class="single-fact my-2">
                <div class="icon-box mx-auto d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-{{ $counter['icon'] ?? 'earth-asia' }}"></i>
                </div>
                <span class="timer" data-from="0" data-to="{{ $counter['value'] }}" data-speed="3000" data-refresh-interval="50">
                    {{ $counter['value'] }}
                </span>
                <h5>{{ $counter['label'] }}</h5>
            </div>
        @endforeach
    </div>
</div>
