<div class="col-md-4 mb-20">
<div class="card shadow-sm donation-history-card publication-card">
    <div class="image-div">
        <img src="{{ $publication->getThumbnail() }}" alt="{{ $publication->title }}">
    </div>
    <div class="card-body">
    <h5 class="card-title">{{ $publication->title }}</h5>
    <p class="card-text">{{ \Carbon\Carbon::parse($publication->published_date)->format('d M, Y') }}</p>
    @auth
                            <a href="{{ route('download', $publication->id) }}"
                               class="btn btn-block"
                               data-method="get"
                               data-turbo-frame="false">Download</a>
                        @else
                            <a href="#" class="btn btn-block" style="width: 115px;" data-bs-toggle="modal" data-bs-target="#downloadModal{{$publication->id}}">Download</a>
                        @endauth
    </div>
</div>
</div>

<!-- Download Modal -->
<div class="modal fade subscribe" id="downloadModal{{$publication->id}}" tabindex="-1" role="dialog" aria-labelledby="downloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content subscribe-case-modal">
            <div class="modal-body">
                @include('components.download_modal')
            </div>
        </div>
    </div>
</div>
