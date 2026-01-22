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
