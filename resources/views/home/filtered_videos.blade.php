@forelse($videos as $index => $video)
    <div class="col-md-3 mb-4 d-flex flex-column align-content-center">
        @if($lessons)
            <div class="box shadow-lg video-container position-relative" style="height: 300px">
        @else
            <div class="box shadow-lg video-container position-relative">
        @endif
            @if($video->image)
            <div class="image-wrapper position-relative">
                <img src="{{ $video->getImageUrl() }}" alt="Thumbnail" class="card-img-top img-fluid">
                <button class="btn btn-play position-absolute centered" onclick="playVideo(this)" data-video-url="{{ $video->youtube_link }}">
                    <i class="fa-brands fa-youtube"></i>
                </button>
            </div>
            @else
            <div class="image-wrapper position-relative">
                @php
                $videoHelper = new \App\Helpers\VideoHelper();
                $youtube_id = $videoHelper->youtube_video_id($video->youtube_link);
                $default_thumbnail_url = "https://img.youtube.com/vi/{$youtube_id}/0.jpg";
                @endphp

                <img src="{{ $default_thumbnail_url }}" alt="Default Thumbnail" class="card-img-top img-fluid">
                <button class="btn btn-play position-absolute centered" onclick="playVideo(this)" data-video-url="{{ $video->youtube_link }}">
                    <i class="fa-brands fa-youtube"></i>
                </button>

            </div>
            @endif
            <div class="p-3 video-title">
                {{ Str::limit($video->title, 100, '...') }}
            </div>
            <div class="ps-3 pe-3 pb-2 video-category">
                @if($video->categories?->isNotEmpty())
                <span>Categories:</span>
                @foreach($video->categories->take(3) as $index => $category)
                <span class="ms-2">{{ $category->title }}@if(!$loop->last),@endif</span>
                @endforeach
                @if($video->categories->count() > 3)
                <span class="ms-2">...etc</span>
                @endif
                @endif
            </div>
        </div>
    </div>
    <div id="box" class="justify-content-start align-items-center">
    </div>
@empty
    <div class="col-12">
        <p class="text-center">No Videos have been found</p>
    </div>
@endforelse
