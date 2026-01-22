<x-main>
    @include('home.sections.hero')
    @include('home.sections.campaign')
    @include('home.sections.program')
    @include('home.sections.counters')
    @include('home.sections.marketing_banner')
    @include('home.sections.latestNews')
    @include('home.sections.photoGallery')
    @include('home.sections.videoGallery')
    @include('home.sections.contact_us')
    <script src="{{ asset('js/campaign_copy_link.js') }}"></script>
</x-main>
