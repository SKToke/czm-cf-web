@php
    use Carbon\Carbon;
    $requestedData = json_decode(session('requestedData'));
    $requestedDataTime = session('requestedDataTime');
    $previousValue = false;
    if(!is_null($requestedData) && $requestedData->zakat_type =='business' && !is_null($requestedDataTime) && $requestedDataTime->diffInMinutes(Carbon::now()) <= 2) {
        $previousValue = true;
    }
@endphp
<form id="businessZakatForm" action="{{ route('zakat.business') }}" method="post">
    @csrf
    <div class="form-section">
        <input type="hidden" name="zakat_type" value="business">

        <div class="title">
            Complete the simple form below to quickly calculate your business zakat
        </div>

        <div class="title alert-text mt-2">
            Please login if you want to save your zakat calculation and donation history
        </div>

        <div class="assets-input w-100">
            <div class="d-flex align-items-center gap-2 mb-5">
                <img src="{{ asset('images/assets.png') }}" alt="assets" class="icon">
                <h2 class="mt-8 text-dark">Assets</h2>
            </div>

            <div class="section-subtitle mb-15">Assets include money held in the bank, cash, property, jewellery, or anything else of value. If any of the fields do not apply, you can leave them blank.</div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="cash_in_hand">Amount of cash in hand</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="cash_in_hand" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->cash_in_hand : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="deposits_in_bank">Deposits in all types of bank accounts</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="deposits_in_bank" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->deposits_in_bank : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="market_value_of_investments">Zakat accounting market value of all types of investments (gold, shares, stocks, bonds, land, houses, foreign currency etc.)</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="market_value_of_investments" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->market_value_of_investments : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="market_value_of_saleable_stock">Market value of saleable manufactured stock</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="market_value_of_saleable_stock" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->market_value_of_saleable_stock : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="market_value_of_process_products">Market value of products in process, stock raw materials and packing materials</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="market_value_of_process_products" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->market_value_of_process_products : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="payments_of_advances">Payment of collateral and all types of advances</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="payments_of_advances" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->payments_of_advances : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="bank_lc_margin">Bank LC Margin given in case of import</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="bank_lc_margin" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->bank_lc_margin : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="advanced_money_for_products">Advance money paid for the purchase of a product</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="advanced_money_for_products" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->advanced_money_for_products : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="value_of_unsold_property">Value of Scrapped/Unsold Property</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="value_of_unsold_property" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->value_of_unsold_property : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="amount_due_from_sale">Amount due from sale on balance/on credit</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="amount_due_from_sale" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->amount_due_from_sale : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="other_sources_and_dues">Other sources and dues (loans paid, rent received from property, etc.)</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="other_sources_and_dues" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->other_sources_and_dues : '' }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="liabilities-input">
            <div class="d-flex align-items-center gap-2 mb-5">
                <img src="{{ asset('images/liabilities.png') }}" alt="liabilities" class="icon">
                <h2 class="mt-8 text-dark">LIABILITIES</h2>
            </div>

            <div class="section-subtitle">Liabilities represent money that is owed to others</div>

            <div class="input-container mt-30">
                <div class="row">
                    <div class="col-6">
                        <label for="business_loans_installments">Zakat installments paid in the current financial year on loans taken from banks or individuals in the form of investment in the core business (but the loan taken to increase the fixed assets of the business will not be considered as liability)</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="business_loans_installments" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->business_loans_installments : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="dues_to_suppliers">Dues to suppliers or such others payable in the current Zakat financial year</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="dues_to_suppliers" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->dues_to_suppliers : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="employees_payable_dues">Employee's dues payable in the current Zakat financial year</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="employees_payable_dues" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->employees_payable_dues : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="other_debts">Other debts paid in the current Zakat financial year (eg rent, taxes, utility bills etc.)</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="other_debts" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->other_debts : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="bad_debts">Bad Debt</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="bad_debts" placeholder="Amount (BDT)" class="form-control mt-5" value="{{ $previousValue ? $requestedData->bad_debts : '' }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="radio-button-section mt-30 row w-100">
            <span>BASE NISAB ON VALUE OF:</span>
            <div class="col-md-6">
                <div class="button-section ml-10">
                    <input type="radio" name="nisab" id="nisab_silver" value="silver" checked>
                    <label for="nisab_silver">Silver</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="button-section ml-10">
                    <input type="radio" name="nisab" id="nisab_gold" value="gold" >
                    <label for="nisab_gold">Gold</label>
                </div>
            </div>
        </div>

        <div class="radio-button-section row w-100">
            <span>ZAKAT IS TO BE CALCULATED IN:</span>
            <div class="col-md-6">
                <div class="button-section ml-10">
                    <input type="radio" name="calender" id="calender_lunar" value="lunar" checked>
                    <label for="calender_lunar">Lunar Calendar</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="button-section ml-10">
                    <input type="radio" name="calender" id="calender_english" value="english">
                    <label for="calender_english">English or Gregorian Calendar</label>
                </div>
            </div>
            <p class="mt-10">N.B: If Zakat is calculated in the Lunar Calendar, Zakat must be paid at the rate of 2.50% on Net Zakatable assets. For the Gregorian or English Calendar, the rate is 2.58% on Net Zakatable assets.</p>
        </div>
    </div>

    <div class="d-flex justify-content-center align-items-center w-100 mt-30">
        <button type="submit" class="calculate-btn mr-15">Calculate Zakat</button>
        <button type="reset" class="calculate-btn">Reset</button>
    </div>
</form>
