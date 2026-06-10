@php
    $status = request()->query('confirmation');
    $errorMessage = session('error_message') ?? 'An unexpected event occurred.';
    
    // Determine details based on status
    $modalTitle = '';
    $modalBody = '';
    $iconClass = '';
    $iconColor = '';
    $iconBgColor = '';
    $btnText = 'Close';
    $btnRoute = '#';
    $btnClass = 'czm-primary-bg';
    
    if ($status === 'success') {
        $modalTitle = 'Donation Successful!';
        $modalBody = 'Thank you for your generous contribution. Your payment has been processed successfully.';
        $iconClass = 'fa-solid fa-circle-check';
        $iconColor = '#1a7f37';
        $iconBgColor = 'rgba(26, 127, 55, 0.1)';
        $btnText = 'Back to Home';
        $btnRoute = route('home');
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
        $iconColor = '#E2125D'; // bKash brand pink-red
        $iconBgColor = 'rgba(226, 18, 93, 0.1)';
        $btnText = 'Go to Dashboard';
        $btnRoute = route('donation-history');
    } elseif (in_array($status, ['fail', 'agreement_fail'])) {
        $modalTitle = $status === 'agreement_fail' ? 'Auto-Pay Setup Failed' : 'Payment Failed';
        $modalBody = $errorMessage;
        $iconClass = 'fa-solid fa-circle-xmark';
        $iconColor = '#d93025';
        $iconBgColor = 'rgba(217, 48, 37, 0.1)';
        $btnText = 'Try Again';
        $btnRoute = '#';
        $btnClass = 'btn-danger-custom';
    } elseif ($status === 'cancel') {
        $modalTitle = 'Transaction Cancelled';
        $modalBody = 'You have cancelled the payment process. No amount has been debited.';
        $iconClass = 'fa-solid fa-circle-exclamation';
        $iconColor = '#f9ab00';
        $iconBgColor = 'rgba(249, 171, 0, 0.1)';
        $btnText = 'Close';
        $btnRoute = '#';
        $btnClass = 'btn-warning-custom';
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
                    <p class="text-muted czm-status-text px-3 mb-4">{{ $modalBody }}</p>
                    
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
