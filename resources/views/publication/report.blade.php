<x-main>
    @include('home.sections.banner')
    <div class="container">
        <div class="row text-center mt-20 mb-20">
            @php
                $filterRoute = route('reports');
                $dataTargetContainer = 'reportsContainer';
            @endphp
            @include('components.filter_with_month_and_year', ['filterRoute' => $filterRoute, 'dataTargetContainer' => 'reportsContainer'])
        </div>
        <div id="reportsContainer" class="row mb-4">
            @if ($reports->isNotEmpty())
                @foreach ($reports as $report)
                    @include('publication.card', ['publication' => $report])
                @endforeach
            @else
                <div class="col-sm-12 text-center mb-4">
                    <p>No records found.</p>
                </div>
            @endif
            <div class="row pagination text-center">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</x-main>
