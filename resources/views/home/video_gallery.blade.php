<x-main>
    @include('home.sections.banner')
    <div class="video-gallery-section mt-4">
        <div class="page-wrapper mt-3">
            <div class="container">
                <section class="pb-4">
                    <div class="row p-2 d-flex justify-content-center">
                        @if($videos==null)
                            <h1 class="text-center">No Videos found</h1>
                        @else
                            <form id="filter-videos-form" action="{{ route('filter-video-gallery') }}" method="GET" class="mb-20 text-center">
                                <label for="category_id" class="d-inline-block">Showing Videos for</label>
                                <select name="category_id" id="category_id" class="custom-select d-inline-block" data-target="case-filter.selectCategory">
                                    <option value="0">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                                    @endforeach
                                </select>
                            </form>

                        <div id="video_list" class="row d-flex align-content-center">
                            @include('home.filtered_videos', ['videos' => $videos,'lessons' => false])
                            @if(count($videos)!=0)
                                <div class="row pagination text-center">
                                    {{ $videos->links() }}
                                </div>
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
