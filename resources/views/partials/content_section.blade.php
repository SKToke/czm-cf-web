<div class="content-detail">
    <p><b>Title:</b> {{ $section->title }}</p>
    <p><b>Description:</b> {!! $section->description !!}</p>
    <div class="img-box">
        @if ($section->image)
            <img src="{{ $section->getImage() }}" alt="Content section image" width="500px" height="300px">
        @endif
    </div>
    <br>
    <p><b>Position:</b> {{ $section->position }}</p>
    <br>
    @php
        $editUrl = route('admin.contentSections.edit', ['contentSection' => $section->id, 'content_id' => $section->content_id]);
        $deleteUrl = route('admin.contentSections.customDestroy', $section->id)
    @endphp
    <a href="{{ $editUrl }}" class="btn btn-sm btn-primary mb-2">Edit this Content Section</a>
    <form action="{{ $deleteUrl }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-sm btn-danger">Delete this Content Section</button>
    </form>
    <hr>
</div>
