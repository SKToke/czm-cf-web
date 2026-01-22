<x-main>
    <h3 class="mt-4 d-flex justify-content-center fw-bold czm-primary-text mb-30">Your Archived Zakat Calculations</h3>
    <div class="container">
        <div class="row mb-4" id="noticesContainer">
            @if($calculationRecords && $calculationRecords->count() > 0)
                @foreach($calculationRecords as $calculationRecord)
                    @include('user.archived_zakat_calculations_card', ['calculationRecord' => $calculationRecord])
                @endforeach
            @else
                <div class="col-sm-12 text-center mb-4">
                    <p>No records found.</p>
                </div>
            @endif
        </div>
    </div>
</x-main>
