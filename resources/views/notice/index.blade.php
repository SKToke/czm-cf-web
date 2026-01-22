<x-main>
    @include('home.sections.banner')
    <div class="container">
        <div class="row text-center mt-20 mb-20">
            @php
                $filterRoute = route('notices');
                $dataTargetContainer = 'noticesContainer';
            @endphp
            @include('components.filter_with_month_and_year', ['filterRoute' => $filterRoute])
        </div>
        <div class="row mb-4" id="noticesContainer">
            @if($notices->count())
                @foreach($notices as $notice)
                    @include('notice.notice_card', ['notice' => $notice])
                @endforeach
            @else
                <div class="col-sm-12 text-center mb-4">
                    <p>No records found.</p>
                </div>
            @endif
            <div class="row pagination text-center">
                {{ $notices->links() }}
            </div>
        </div>
    </div>
</x-main>
