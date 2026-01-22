<section class="case-updates mt-4">
    <h4 class="updates-title">See latest updates about this campaign-</h4>
    @if ($latest_updates->count() > 0)
        <ul class="case-attachments">
            @foreach ($latest_updates as $latest_update)
                <li>
                    <h6>{{ $latest_update->title }}</h6>
                    <p>- {{ $latest_update->updated_at->toDateString() }}</p>
                    <p>{!! $latest_update->message !!}</p>
                    @if ($latest_update->disbursed_amount)
                        <p>{{ $latest_update->disbursed_amount }} Tk has been disbursed.</p>
                    @endif
                    @if ($latest_update->getFormattedAttachments() != 'No attachments')
                        <p>{!! $latest_update->getFormattedAttachments() !!} </p>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <p>No Updates yet.</p>
    @endif
</section>
