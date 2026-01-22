<div class="container mb-2">
    <div class="card shadow-sm notice-card">
        <div class="row no-gutters ml-5 mr-5 mt-5">
            <div class="col-md-10">
                <h5 class="card-title">Payable Zakat Amount: {{ $calculationRecord->payable_zakat }} TK</h5>
                <p class="card-text">Calculated on: {{ \Carbon\Carbon::parse($calculationRecord->date)->format('d M, Y') }}</p>
            </div>
            <div class="col-md-2 d-flex justify-content-end">
                <a href="#" class="btn btn-block" data-notice-id="{{ $calculationRecord->id }}" data-action="click->notice#toggleDetails">Details</a>
            </div>
        </div>
        <div class="row no-gutters ml-5 mr-5 mt-15 details" id="details-{{ $calculationRecord->id }}" style="display: none;">
            <div class="col-md-12">
                <ul>
                    <li><p>Zakat Type: <b>{{ $calculationRecord->zakat_type }}</b></p></li>
                    <li><p>Nisab Statndard: <b>{{ $calculationRecord->nisab_standard }}</b></p></li>
                    <li><p>Total Assets: <b>{{ $calculationRecord->total_assets }} Tk</b></p></li>
                    <li><p>Total Liabilities: <b>{{ $calculationRecord->total_liabilities }} Tk</b></p></li>
                    <li><p>Net Zakat-able Assets: <b>{{ $calculationRecord->net_zakatable_assets }} Tk</b></p></li>
                </ul>
            </div>
            <div class="col-md-12">
                <form action="{{ route('zakat.exportPdf') }}" method="POST">
                    @csrf
                    <input type="hidden" name="total_assets" value="{{ $calculationRecord->total_assets }}">
                    <input type="hidden" name="total_liabilities" value="{{ $calculationRecord->total_liabilities }}">
                    <input type="hidden" name="net_zakatable_assets" value="{{ $calculationRecord->net_zakatable_assets }}">
                    <input type="hidden" name="payable_zakat" value="{{ $calculationRecord->payable_zakat }}">
                    <input type="hidden" name="requested_data" value="{{ $calculationRecord->calculation_form_data }}">

                    <div class="button-group d-flex justify-content-center align-items-center flex-wrap">
                        @if (Auth::check())
                            <button type="submit" class="calculate-btn mr-15">Export Details as PDF</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
