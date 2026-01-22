<div class="modal fade" id="campaignShareModal-{{$itemId}}" tabindex="-1" aria-labelledby="campaignShareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered campaign-share-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="campaignShareModalLabel">Share this Campaign on social media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="campaign-card-social-links">
                    {!! $shareButtons !!}
                </div>
                <div class="campaign-card-copy-link">
                    <ul>
                        <li>Or Copy this campaign link to share <button class="copyButton copy-link-btn2" data-url="{{ route('campaign-details', ['slug' => $campaign->slug]) }}">{{ $campaignRoute }}</button></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
