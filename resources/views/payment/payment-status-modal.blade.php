@php
    $status = request()->query('confirmation');
    $errorMessage = session('error_message') ?? request()->query('error_message') ?? 'An unexpected event occurred.';
    $subscriptionId = request()->query('subscriptionId');
    $reference = request()->query('reference');
    $invoice = request()->query('invoice');
    
    // Determine details based on status
    $modalTitle = '';
    $modalBody = '';
    $iconClass = '';
    $iconColor = '';
    $iconBgColor = '';
    $btnText = 'Close';
    $btnRoute = '#';
    $btnClass = 'czm-primary-bg';
    $showDetailsBox = false;
    
    if (in_array($status, ['success', 'recurring_success'])) {
        $modalTitle = 'Subscription Activated Successfully!';
        $modalBody = 'Thank you! Your recurring donation subscription has been authorized and activated successfully.';
        $iconClass = 'fa-solid fa-circle-check';
        $iconColor = '#1a7f37';
        $iconBgColor = 'rgba(26, 127, 55, 0.1)';
        $btnText = 'Go to My Subscriptions';
        $btnRoute = route('user-daily-sadaqah.index');
        $showDetailsBox = true;
    } elseif ($status === 'agreement_success') {
        $modalTitle = 'Auto-Pay Setup Successful!';
        $amount = session('subscription_amount');
        $freq = session('subscription_frequency');
        $freqText = $freq === 'daily' ? 'daily' : 'monthly';
        
        $modalBody = 'Thank you! Your Daily Sadaqah auto-pay subscription has been set up successfully.';
        if ($amount) {
            $modalBody .= ' BDT ' . e($amount) . ' will be donated automatically ' . $freqText . '.';
        }
        $iconClass = 'fa-solid fa-circle-check';
        $iconColor = '#E2125D';
        $iconBgColor = 'rgba(226, 18, 93, 0.1)';
        $btnText = 'Go to Dashboard';
        $btnRoute = route('donation-history');
    } elseif (in_array($status, ['fail', 'recurring_fail', 'agreement_fail'])) {
        $modalTitle = 'Subscription Authorization Failed';
        $modalBody = $errorMessage;
        $iconClass = 'fa-solid fa-circle-xmark';
        $iconColor = '#d93025';
        $iconBgColor = 'rgba(217, 48, 37, 0.1)';
        $btnText = 'Try Again';
        $btnRoute = route('daily-sadaqah.index');
        $btnClass = 'btn-danger-custom';
        $showDetailsBox = true;
    } elseif (in_array($status, ['cancel', 'recurring_cancel'])) {
        $modalTitle = 'Subscription Cancelled';
        $modalBody = 'Your recurring subscription has been cancelled successfully.';
        $iconClass = 'fa-solid fa-circle-exclamation';
        $iconColor = '#f9ab00';
        $iconBgColor = 'rgba(249, 171, 0, 0.1)';
        $btnText = 'Return to Subscriptions';
        $btnRoute = route('user-daily-sadaqah.index');
        $btnClass = 'btn-warning-custom';
        $showDetailsBox = true;
    }
@endphp

<div class="d-flex justify-content-center align-items-center my-auto">
    <div id="payment-successful" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content czm-payment-status-modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center px-4 pb-5 pt-2">
                    <!-- Premium Icon Ring -->
                    <div class="czm-status-icon-wrapper mb-4" style="color: {{ $iconColor }}; background-color: {{ $iconBgColor }}; box-shadow: 0 8px 20px {{ $iconBgColor }};">
                        <i class="{{ $iconClass }} fa-5x animated-icon"></i>
                    </div>
                    
                    <h3 class="czm-status-title mb-3">{{ $modalTitle }}</h3>
                    <p class="text-muted czm-status-text px-3 mb-3">{{ $modalBody }}</p>

                    @if($showDetailsBox && ($subscriptionId || $reference || $invoice))
                        <div class="p-3 my-3 rounded bg-light border text-start" style="font-size: 0.9rem;">
                            @if($subscriptionId)
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Recurring ID:</span>
                                    <strong class="text-dark">{{ $subscriptionId }}</strong>
                                </div>
                            @endif
                            @if($reference)
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Reference:</span>
                                    <strong class="text-dark">{{ $reference }}</strong>
                                </div>
                            @endif
                            @if($invoice)
                                <div class="d-flex justify-content-between py-1">
                                    <span class="text-muted">Invoice / Request ID:</span>
                                    <strong class="text-dark font-monospace" style="font-size: 0.82rem;">{{ $invoice }}</strong>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-center mt-4">
                        @if($btnRoute === '#')
                            <button class="border shadow text-white w-75 py-3 czm-status-btn {{ $btnClass }}" data-bs-dismiss="modal">
                                {{ $btnText }}
                            </button>
                        @else
                            <a href="{{ $btnRoute }}" class="d-flex justify-content-center w-75 text-decoration-none">
                                <button class="border shadow text-white w-100 py-3 czm-status-btn {{ $btnClass }}" type="button">
                                    {{ $btnText }}
                                </button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    /* Premium Modal Styling */
    .czm-payment-status-modal-content {
        border-radius: 20px !important;
        border: none !important;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15) !important;
        background: #ffffff !important;
        overflow: hidden;
    }

    .czm-status-icon-wrapper {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        margin: 0 auto;
        animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
    }

    .czm-status-title {
        font-family: 'Outfit', 'Inter', sans-serif;
        font-weight: 700;
        color: #1f2937;
        font-size: 1.6rem;
    }

    .czm-status-text {
        font-family: 'Inter', sans-serif;
        font-size: 0.975rem;
        line-height: 1.6;
        color: #4b5563 !important;
    }

    .czm-status-btn {
        border-radius: 12px !important;
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        font-size: 1rem;
        border: none !important;
        transition: all 0.2s ease-in-out;
    }

    .czm-status-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1) !important;
        opacity: 0.95;
    }

    /* Custom Theme Colors */
    .btn-danger-custom {
        background-color: #d93025 !important;
    }

    .btn-warning-custom {
        background-color: #f9ab00 !important;
    }

    /* Clean animations */
    @keyframes scaleIn {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    .animated-icon {
        animation: rotateIcon 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.2s both;
    }

    @keyframes rotateIcon {
        0% {
            transform: rotate(-30deg) scale(0.85);
        }
        100% {
            transform: rotate(0) scale(1);
        }
    }
</style>
