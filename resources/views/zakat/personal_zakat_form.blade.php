@php
    use Carbon\Carbon;
    $requestedData = json_decode(session('requestedData'));
    $requestedDataTime = session('requestedDataTime');
    $previousValue = false;
    if(!is_null($requestedData) && $requestedData->zakat_type =='personal' && !is_null($requestedDataTime) && $requestedDataTime->diffInMinutes(Carbon::now()) <= 2) {
        $previousValue = true;
    }
@endphp
<form id="personalZakatForm" action="{{ route('zakat.personal') }}" method="post">
    @csrf
    <div class="form-section">
        <input type="hidden" name="zakat_type" value="personal">

        <div class="title">
            Complete the simple form below to quickly calculate your personal zakat
        </div>

        <div class="title alert-text mt-2">
            Please login if you want to save your zakat calculation and donation history
        </div>

        <div class="assets-input w-100">
            <div class="d-flex align-items-center gap-2 mb-5">
                <img src="{{ asset('images/assets.png') }}" alt="assets" class="icon">
                <h2 class="mt-8 text-dark">Assets</h2>
            </div>

            <div class="section-subtitle">Assets include money held in the bank, cash, property, jewellery, or anything
                else of value. If any of the fields do not apply, you can leave them blank.
            </div>
            <div class="subtitle">Zakat on Gold and Silver (selling price)</div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="gold_24_carat">24 Carat Gold / Jewelry</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="gold_24_carat" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->gold_24_carat : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="gold_22_carat">22 Carat Gold / Jewelry</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="gold_22_carat" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->gold_22_carat : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="gold_21_carat">21 Carat Gold / Jewelry</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="gold_21_carat" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->gold_21_carat : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="gold_18_carat">18 Carat Gold / Jewelry</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="gold_18_carat" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->gold_18_carat : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="other_gold_materials">Other Gold materials</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="other_gold_materials" placeholder="Amount (BDT)"
                               class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->other_gold_materials : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="silver">Silver</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="silver" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->silver : '' }}">
                    </div>
                </div>
            </div>

            <div class="subtitle">Zakat on cash and bank deposits (Actual value)</div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="cash_in_hand">Cash in Hand</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="cash_in_hand" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->cash_in_hand : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="bank_savings">Bank Savings and current Account Balance</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="bank_savings" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->bank_savings : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="fixed_deposits">Fixed Deposits, DPS, Special Savings (i.e. Hajj, marriage
                            etc.)</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="fixed_deposits" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->fixed_deposits : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="insurance">Insurance and Bonus on Insurance Premium</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="insurance" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->insurance : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="shares">Shares, stocks, Savings Certificates, Bonds etc. (price on the day of zakat
                            payment)</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="shares" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->shares : '' }}">
                    </div>
                </div>
            </div>

            <div class="subtitle">Zakat on loans/receivables/advances (Actual Value)</div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="loans_receivables">Loans Receivables from Friends and Relatives for certain</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="loans_receivables" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->loans_receivables : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="security_deposits">Security Deposits (to be received) and advance payments</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="security_deposits" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->security_deposits : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="provident_fund">Provident Fund (if withdrawable)</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="provident_fund" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->provident_fund : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="real_estate">Land, House, Apartments purchased with the intention for resale</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="real_estate" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->real_estate : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="other_income">Balance of other Income after expenses (i.e. salaries, honorarium,
                            gifts, house rents etc.)</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="other_income" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->other_income : '' }}">
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
                        <label for="personal_loans">Personal loans to be paid in the current Zakat Year</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="personal_loans" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->personal_loans : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="bank_loans">Bank loans to be paid in the current Zakat Year</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="bank_loans" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->bank_loans : '' }}">
                    </div>
                </div>
            </div>

            <div class="input-container">
                <div class="row">
                    <div class="col-6">
                        <label for="other_liabilities">Other Liabilities/payables (i.e. House Rent, Tax, Utility Bills
                            etc.)</label>
                    </div>
                    <div class="col-6">
                        <input type="number" name="other_liabilities" placeholder="Amount (BDT)" class="form-control mt-5"
                               value="{{ $previousValue ? $requestedData->other_liabilities : '' }}">
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
                    <input type="radio" name="nisab" id="nisab_gold" value="gold">
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
            <p class="mt-10">N.B: If Zakat is calculated in the Lunar Calendar, Zakat must be paid at the rate of 2.50%
                on Net Zakatable assets. For the Gregorian or English Calendar, the rate is 2.58% on Net Zakatable
                assets.</p>
        </div>
    </div>

    <div class="d-flex justify-content-center align-items-center w-100 mt-30">
        <button type="submit" class="calculate-btn mr-15">Calculate Zakat</button>
        <button type="reset" class="calculate-btn">Reset</button>
    </div>
</form>
