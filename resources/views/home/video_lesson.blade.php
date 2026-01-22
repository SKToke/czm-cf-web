<x-main>
    @include('home.sections.banner')
    <div class="video-gallery-section mt-4">
        <div class="page-wrapper mt-3">
            <div class="container">
                <section class="pb-4">
                    <div class="row p-2 d-flex justify-content-center">
                        @if($zakatVideos==null && $fiqhOfZakatVideos==null )
                            <h1 class="text-center">No Videos found</h1>
                        @else

                            <div id="video_list" class="row d-flex align-content-center">
                                @if($zakatVideos)
                                    <h2 class="text-dark mb-20">
                                        যাকাত ধনসম্পদে বঞ্চিতের অধিকার
                                    </h2>
                                    @include('home.filtered_videos', ['videos' => $zakatVideos,'lessons' => true])
                                @endif
                                @if($fiqhOfZakatVideos)
                                    <h2 class="text-dark mb-20 mt-10">
                                        Fiqh of Zakat | যাকাতের বিধি-বিধান
                                    </h2>
                                    @include('home.filtered_videos', ['videos' => $fiqhOfZakatVideos,'lessons' => true])
                                @endif
                            </div>

                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-main>
<script src="{{ asset('js/videoGallery.js') }}"></script>
<script src="{{ asset('js/filter_videos.js') }}"></script>
