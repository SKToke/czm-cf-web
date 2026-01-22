<x-main>
    @include('home.sections.banner')
    <div class="container">
        <div class="row text-center mt-20 mb-20">
            @php
                $filterRoute = route('auditReports');
                $dataTargetContainer = 'publicationsContainer';
            @endphp
            @include('components.filter_with_month_and_year', ['filterRoute' => $filterRoute])
        </div>
        <div id = "publicationsContainer" class="row mb-4">
            @if ($publications->isNotEmpty())
                @foreach ($publications as $publication)
                    @include('publication.card_with_thumbnail', ['publication' => $publication])
                @endforeach
            @else
                <div class="col-sm-12 text-center mb-4">
                    <p>No records found.</p>
                </div>
            @endif
            <div class="row pagination text-center">
                {{ $publications->links() }}
            </div>
        </div>
    </div>
</x-main>
