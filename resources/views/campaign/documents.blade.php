<section class="case-documents mt-4">
    <h4 class="documents-title">Essential Documents related to this campaign-</h4>
    @if(count($fileAttachments) == 0 && count($imageAttachments) == 0)
        <p>No Attachments</p>
    @endif
    @if(count($fileAttachments) > 0)
        <ul class="case-attachments">
            @foreach($fileAttachments as $attachment)
                <li>
                    <a href="{{ Storage::disk('public')->url($attachment['filePath']) }}" target="_blank">{{ $attachment['title'] }}</a>
                </li>
            @endforeach
        </ul>
    @endif
    @if(count($imageAttachments) > 0)
        <ul class="case-attachments">
            @foreach($imageAttachments as $attachment)
                <li>
                    <a href="{{ Storage::disk('public')->url($attachment['imagePath']) }}" target="_blank">
                        <img src="{{ Storage::disk('public')->url($attachment['imagePath']) }}" alt="{{ $attachment['title'] }}">
                    </a>
                    <p class="mt-2 text-black">{{ $attachment['title'] }}</p>
                </li>
            @endforeach
        </ul>
    @endif
</section>
