<x-main>
    @include('home.sections.banner')
    <div class="page-wrapper video-gallery-section mt-4">
        <form id="filter-photos-form" action="{{ route('filter-photo-gallery') }}" method="GET" class="container text-center mb-20">
            <label for="category_id" class="d-inline-block">Showing Photos for</label>
            <select name="category_id" id="category_id" class="custom-select d-inline-block" data-target="case-filter.selectCategory">
                <option value="0">All Categories</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->title }}</option>
                @endforeach
            </select>
        </form>
        @if(count($imagesWithTitle) > 0)
            <div id="photo_list">
                @include('home.sections.photo_gallery', ['imagesWithTitle' => $imagesWithTitle])
            </div>
        @else
            <p class="container text-center">No images have been found.</p>
        @endif
    </div>
</x-main>
<script src="{{ asset('js/filter_photos.js') }}"></script>
