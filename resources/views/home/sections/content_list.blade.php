@if(count($contents)==0)
    <p class="text-center">No record found</p>
@endif
@foreach ($contents as $content)
    <div class="col-sm-4 content-section ps-10 pe-10 mt-50">
        <div class="single-blog-post">
            <div class="img-box">
                <div class="blog-img">
                    @php
                        $firstSectionWithMinPosition = $content->contentSections->sortBy('position')->first();
                    @endphp
                    @if($firstSectionWithMinPosition && $firstSectionWithMinPosition->image!=null)
                        <a href="{{ route('content', ['slug' => $content->slug]) }}">
                            <img src="{{ $firstSectionWithMinPosition->getImage() }}" alt="Job Logo" class="w-100 h-100">
                        </a>
                    @elseif($firstSectionWithMinPosition)
                        @php
                            $imageFound = false;
                        @endphp
                        @foreach($content->contentSections as $section)
                            @if($section->image && !$imageFound)

                                <a href="{{ route('content', ['slug' => $content->slug]) }}">
                                    <img src="{{ $section->getImage() }}" alt="Content Logo" class="w-100 h-100">
                                </a>
                                @php
                                    $imageFound = true;
                                @endphp
                            @endif
                        @endforeach
                    @else
                        <a href="{{ route('content', ['slug' => $content->slug]) }}">
                            <img src="{{ asset('images/image_placeholder.png') }}" alt="CZM" class="main-logo">
                        </a>

                    @endif
                </div>
            </div>
            <div class="content-box">
                <div class="date-box">
                    <div class="inner">
                        <div class="date">
                            <strong>{{ $content->created_at->format('d') }}</strong> {{ $content->created_at->format('M') }}
                        </div>
                    </div>
                </div>
                <div class="content">
                    <a href="{{ route('content', ['slug' => $content->slug]) }}">{{ Str::limit($content->name, 50, '...') }}</a>
                    <p>
                        @if ($firstSectionWithMinPosition)
                            {{ Str::limit(strip_tags($firstSectionWithMinPosition->description), 200) }}
                        @endif
                    </p>
                    <span class="me-2">
                                        @if ($content->categories->isNotEmpty())
                            <span>Categories:</span>
                            @foreach ($content->categories->take(3) as $index => $category)
                                <span class="ms-2">{{ $category->title }}</span>
                                @if ($index < 2 || ($content->categories->count() == 3 && $index < 3))
                                    <span>,</span>
                                @endif
                            @endforeach
                            @if ($content->categories->count() > 3)
                                <span class="ms-2">...etc</span>
                            @endif
                        @endif
                                    </span>
                </div>
            </div>
        </div>
    </div>
@endforeach
