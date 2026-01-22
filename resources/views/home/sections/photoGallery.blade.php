@if(count($imagesWithTitle) != 0)
    <section class="mt-20 w-75 mx-auto">
        <h3 class="text-center fw-bold czm-primary-text">
            Latest Photos
            <div class="sec-title mb-0">
                <span class="decor">
                    <span class="inner"></span>

                </span>
            </div>
        </h3>
    </section>
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
