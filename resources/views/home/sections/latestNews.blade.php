@if(count($contents) != 0)
    <section class="mt-20 w-75 mx-auto">
        <h3 class="text-center fw-bold czm-primary-text">
            Latest News
            <div class="sec-title mb-0">
                <span class="decor">
                    <span class="inner"></span>

                </span>
            </div>
        </h3>
    </section>
    @include('home.sections.latest_news')
    <div class="container" style="margin-bottom: 30px">
        <div class="view-cases-link text-end">
            <a href="{{ route('news') }}">
                <i class="fa fa-angle-double-right me-2"></i>
                View All News
            </a>
        </div>
    </div>
@endif
