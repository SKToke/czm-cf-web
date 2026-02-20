<x-main>
    <style>
        .amount-btn,
        .freq-btn {
            border: 1px solid #ddd;
            background: #fff;
            color: #222;
        }

        .amount-btn:hover,
        .freq-btn:hover {
            background: var(--czm-secondary);
            color: #fff;
            border-color: var(--czm-secondary);
        }

        .amount-btn.active,
        .freq-btn.active {
            background: var(--czm-primary);
            color: #fff;
            border-color: var(--czm-primary);
        }
    </style>
    <div class="container-fluid mt-30">
        <div class="row d-flex justify-content-center align-items-start">
            <div class="col-lg-9">
                <div class="row">
                    {{-- NOTICE --}}
                    <div class="p-3 mb-3 rounded bg-light text-center text-dark">
                        <h4>Participate in all the charitable works of the Foundation</h4>
                        By donating to this section, you can become a partner in all the charitable works of the Foundation. Because this fund is open for all the charitable activities run by the Foundation.
                    </div>
                    <div class="col-md-6 order-md-1 order-2">
                        @include('daily-sadaqah._left-content')
                    </div>
                    <div class="col-md-6 order-md-2 order-1 mb-30">
                        @include('daily-sadaqah._donation-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('daily-sadaqah._scripts')
</x-main>
