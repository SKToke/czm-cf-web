<div class="container mb-2">
    <div class="card shadow-sm notice-card">
        <div class="row no-gutters ml-5 mr-5 mt-5">
            <div class="col-md-10">
                <h5 class="card-title">{{ $notice->title }}</h5>
                <p class="card-text">{{ \Carbon\Carbon::parse($notice->published_date)->format('d M, Y') }}</p>
            </div>
            @php
                $hasBody = $notice->hasBody();
            @endphp
            @if($hasBody)
                <div class="col-md-2 d-flex justify-content-end">
                    <a href="#" class="btn btn-block" data-notice-id="{{ $notice->id }}" data-action="click->notice#toggleDetails">Details</a>
                </div>
            @endif
        </div>
        @if($hasBody)
            <div class="row no-gutters ml-5 mr-5 mt-15 details" id="details-{{ $notice->id }}" style="display: none;">
                <div class="col-md-12">
                    <p>{!! $notice->description !!}</p>
                    @if($notice->hasAttachments())
                        <ul>
                            @foreach($notice->attachments as $attachment)
                                @if($attachment->fileExists)
                                    <li>
                                        <a href="{{ $attachment->filePath }}" target="_blank">{{ $attachment->title }}</a>
                                    </li>
                                @else
                                    <li>{{ $attachment->title }} (File not found)</li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
