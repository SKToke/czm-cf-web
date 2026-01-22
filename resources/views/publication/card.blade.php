<div class="card shadow-sm notice-card mb-2">
    <div class="row no-gutters ml-5 mr-5 mt-5">
        <div class="col-md-10">
            <h5 class="card-title">{{ $publication->title }}</h5>
            <p class="card-text">{{ \Carbon\Carbon::parse($publication->published_date)->format('d M, Y') }}</p>
        </div>
        <div class="col-md-2 d-flex justify-content-end">
            @if(auth()->check())
                <a href="{{ route('download', ['id' => $publication->id]) }}" class="btn btn-block" style="width: 115px;" target="_blank">Download</a>
            @else
                <a href="#" class="btn btn-block" style="width: 115px;" data-bs-toggle="modal" data-bs-target="#downloadModal{{$publication->id}}">Download</a>
            @endif
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
