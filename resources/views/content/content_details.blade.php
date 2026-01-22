@php
    use \App\Enums\ContentTypeEnum;
@endphp
<x-main>
    @include('home.sections.banner')
    @if($content)
    <div class="container">
        <section class="blog-home sec-padding blog-page blog-details content-detail">
                <div class="row">
                    <div class="col-md-12 col-sm-12 pull-left pull-sm-none">
                        <div class="single-blog-post">
                            <div class="content-box">
                                <div class="d-flex mb-20">
                                    {{-- Adjusted check here to exclude quranic_verse and story --}}
                                    @if ($content && $content->content_type !== ContentTypeEnum::STORY &&
                                        $content->content_type !== ContentTypeEnum::QURANIC_VERSE &&
                                        $content->content_type !== ContentTypeEnum::SADAQAH &&
                                        $content->content_type !== ContentTypeEnum::CASH_WAQF &&
                                        $content->content_type !== ContentTypeEnum::QARD_AL_HASAN
                                        )
                                        <div class="date-box">
                                            <div class="inner">
                                                <div class="date">
                                                    <b>{{ $content->created_at->format('d') }}</b>{{ $content->created_at->format('M') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <h2 class="mt-10 mb -10">{{$content->name}}</h2>
                                        </div>
                                    @endif
                                </div>
                                @foreach ($contentSections as $index => $content_section)
                                <div class="content d-flex flex-column mt-10">
                                    @if ($content_section->title)
                                    <h3 class="mt-10">{{ $content_section->title }}</h3>
                                    <br>
                                    @endif
                                        @if ($content_section->description)
                                            {{-- Extracting and displaying the text --}}
                                                <?php
                                                // Get the description
                                                $description = $content_section->description;

                                                // Remove closing </p> tags from the end
                                                while (substr($description, -4) === '</p>') {
                                                    $description = substr($description, 0, -4);
                                                }

                                                // Remove leading <p> tags
                                                $description = ltrim($description, '<p>');

                                                // Display the modified description
                                                ?>
                                            <p class="description">{!! $description !!}</p>
                                            <br>
                                        @endif


                                </div>
                                    @if ($content_section->image)
                                    <div class="img-box" >
                                        <img src="{{ $content_section->getImage() }}" alt="Content section image" class="section-image w-100">
                                    </div>
                                    @endif
                                @unless ($index == count($contentSections) - 1)
                                <hr>
                                @endunless
                                @endforeach
                            </div>
                        </div>
                    </div>
            </div>
        </section>
    </div>

    @else
        <p class="text-center mt-4">No Content found</p>
    @endif

</x-main>
