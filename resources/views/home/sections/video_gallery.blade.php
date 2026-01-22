
<div class="video-gallery-section">
    <div class="page-wrapper mt-3">
        <div class="container">
            <section>
                <div class="row p-2">
                    @if($videos==null)
                        <p class="text-center">No Videos found</p>
                    @else
                        @foreach($videos as $index => $video)
                            <div class="col-md-3 mb-4">
                                <div class="box shadow-lg video-container position-relative">
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
                                        @if($video->categories->isNotEmpty())
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
                                <div class="modal-content">
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>
            </section>
        </div>
    </div>
</div>
<script src="{{ asset('js/videoGallery.js') }}"></script>
