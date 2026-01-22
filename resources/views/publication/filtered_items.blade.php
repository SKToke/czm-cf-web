@if ($items->isNotEmpty())
    @foreach ($items as $item)
        @if ($thumbnail)
            @include('publication.card_with_thumbnail', ['publication' => $item])
        @else
            @include('publication.card', ['publication' => $item])
        @endif
    @endforeach
    <div class="row pagination text-center">
        {{ $items->links() }}
    </div>
@else
    <div class="col-sm-12 text-center mb-4">
        <p>No records found.</p>
    </div>
@endif
