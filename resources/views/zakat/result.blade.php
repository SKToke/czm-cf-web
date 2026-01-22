<div class="card results-card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p>Total Assets (BDT):</p>
            </div>
            <div class="col-md-6 text-right">
                <span id="totalAssets">{{ $totalAssets ?? '' }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <p>Total Liabilities (BDT):</p>
            </div>
            <div class="col-md-6 text-right">
                <span id="totalLiabilities">{{ $totalLiabilities ?? '' }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <p>Net Zakatable Assets (BDT):</p>
            </div>
            <div class="col-md-6 text-right">
                <span id="netZakatableAssets">{{ $netZakatableAssets ?? ''}}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <p>Payable Zakat Amount (BDT):</p>
            </div>
            <div class="col-md-6 text-right">
                <span id="payableZakat">{{ $payableZakat ?? '' }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col text-center">
        <form action="{{ route('zakat.payCalculatedZakat') }}" method="POST">
            @csrf
            <input type="hidden" id="hiddenTotalAssets1" name="total_assets" value="">
            <input type="hidden" id="hiddenTotalLiabilities1" name="total_liabilities" value="">
            <input type="hidden" id="hiddenNetZakatableAssets1" name="net_zakatable_assets" value="">
            <input type="hidden" id="hiddenPayableZakat1" name="payable_zakat" value="{{ $payableZakat ?? '' }}">
            <input type="hidden" id="hiddenRequestedData1" name="requested_data" value="">

            <div class="button-group d-flex justify-content-center align-items-center flex-wrap">
                <button type="submit" class="calculate-btn mr-15">Pay Your Zakat</button>
            </div>
        </form>
    </div>
    <div class="col text-center">
        <form action="{{ route('zakat.saveCalculation') }}" method="POST">
            @csrf
            <input type="hidden" id="hiddenTotalAssets2" name="total_assets" value="">
            <input type="hidden" id="hiddenTotalLiabilities2" name="total_liabilities" value="">
            <input type="hidden" id="hiddenNetZakatableAssets2" name="net_zakatable_assets" value="">
            <input type="hidden" id="hiddenPayableZakat2" name="payable_zakat" value="{{ $payableZakat ?? '' }}">
            <input type="hidden" id="hiddenRequestedData2" name="requested_data" value="">

            <div class="button-group d-flex justify-content-center align-items-center flex-wrap">
                <button type="submit" class="calculate-btn mr-15">Save</button>
            </div>
        </form>
    </div>
    <div class="col text-center">
        <form action="{{ route('zakat.exportPdf') }}" method="POST">
            @csrf
            <input type="hidden" id="hiddenTotalAssets3" name="total_assets" value="">
            <input type="hidden" id="hiddenTotalLiabilities3" name="total_liabilities" value="">
            <input type="hidden" id="hiddenNetZakatableAssets3" name="net_zakatable_assets" value="">
            <input type="hidden" id="hiddenPayableZakat3" name="payable_zakat" value="{{ $payableZakat ?? '' }}">
            <input type="hidden" id="hiddenRequestedData3" name="requested_data" value="">

            <div class="button-group d-flex justify-content-center align-items-center flex-wrap">
                <button type="submit" class="calculate-btn mr-15">Download</button>
            </div>
        </form>
    </div>
</div>
