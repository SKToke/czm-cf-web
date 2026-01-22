@php
    use App\Enums\ContentTypeEnum;
@endphp
<x-main>
    @include('home.sections.banner')
    <div class="col-md-12 col-sm-12 pull-left pull-sm-none container mt-10">

        <form id="filter-contents-form" action="{{ route('filter-contents') }}" method="GET" class="container text-center mb-20">
            @if( $content_type==ContentTypeEnum::BLOG->value)
                <label for="category_id" class="d-inline-block">Showing Blogs for</label>
            @else
                <label for="category_id" class="d-inline-block">Showing News for</label>
            @endif
            <select name="category_id" id="category_id" class="custom-select d-inline-block" data-target="case-filter.selectCategory">
                <option value="0">All Categories</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->title }}</option>
                @endforeach
            </select>
            <input type="hidden"  id="content_type" name="content_type" value="{{ $content_type }}">
        </form>

        @if ($contents->isEmpty())
            <p class="text-center">No Content found</p>
        @else
            <div class="row m-20">
                <div id="content_list" class="row">
                    @include('home.sections.content_list', ['contents' => $contents])
                </div>
                <div class="row pagination text-center mb-20">
                    @if(method_exists($contents, 'links'))
                        {{ $contents->links() }}
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-main>
<script src="{{ asset('js/filter_contents.js') }}"></script>
