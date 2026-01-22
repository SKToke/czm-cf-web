<x-main>
    @include('home.sections.banner')
    <div class="container">
        <div class="row text-center mt-20 mb-20">
            @php
                $filterRoute = route('books');
                $dataTargetContainer = 'booksContainer';
            @endphp
            @include('components.filter_with_month_and_year', ['filterRoute' => $filterRoute])
        </div>
        <div id="booksContainer" class="row mb-4">
            @if ($books->isNotEmpty())
                @foreach ($books as $book)
                    @include('publication.card_with_thumbnail', ['publication' => $book])
                @endforeach
            @else
                <div class="col-sm-12 text-center mb-4">
                    <p>No records found.</p>
                </div>
            @endif
            <div class="row pagination text-center">
                {{ $books->links() }}
            </div>
        </div>
    </div>
</x-main>
