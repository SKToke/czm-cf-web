<x-main>
    @include('home.sections.banner')
    <div class="container">
        <div class="row text-center mt-20 mb-20">
            @php
                $filterRoute = route('newsletters');
                $dataTargetContainer = 'newslettersContainer';
            @endphp
            @include('components.filter_with_month_and_year', ['filterRoute' => $filterRoute])
        </div>
        <div id="newslettersContainer" class="row mb-4">
            @if ($newsletters->isNotEmpty())
                @foreach ($newsletters as $newsletter)
                    @include('publication.card', ['publication' => $newsletter])
                @endforeach
            @else
                <div class="col-sm-12 text-center mb-4">
                    <p>No records found.</p>
                </div>
            @endif
            <div class="row pagination text-center">
                {{ $newsletters->links() }}
            </div>
        </div>
    </div>
</x-main>
