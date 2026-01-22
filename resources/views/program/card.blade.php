<div class="col-md-3 col-sm-6 z-0">
    <div class="single-service-home p-0 czm-program-card mb-15 border">
        <a href="{{ route('program-details', ['slug' => $program->slug]) }}" class="content px-2">
            <div class="mt-2 mb-3">
                <img src="{{ $program->getLogo() }}" alt="{{ $program->title }}" class="img-fluid">
            </div>
            <h6 class="czm-primary-text-hover fw-semibold px-2">
                {{ $program->title }}
            </h6>
            <h6 class="czm-primary-text-hover slogan-text fw-semibold px-2">
                {{ $program->slogan }}
            </h6>
        </a>
    </div>
</div>
