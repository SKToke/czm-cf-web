<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zakat Calculation History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h4 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            font-size: 12px;
        }
        th, td {
            padding: 6px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4ea2cc;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f0f0f0;
        }
        th[colspan="2"] {
            background-color: #34a853;
        }
    </style>
</head>
<body>
<div style="text-align: center">
    <img width="100%"
         src="{{ public_path('images/zakat_calculation_header.png') }}"/>
</div>
<h4>Zakat Calculation History</h4>
<table>
    <tr>
        <th colspan="2" style="text-align: center">User Information</th>
    </tr>
    <tr>
        <td>Name</td>
        <td>{{ $userZakatCalculation->name }}</td>
    </tr>
    <tr>
        <td>Email</td>
        <td>{{ $userZakatCalculation->email }}</td>
    </tr>
    <tr>
        <td>Mobile Number</td>
        <td>{{ $userZakatCalculation->mobile }}</td>
    </tr>
</table>
@php
    $formData = json_decode($userZakatCalculation->calculation_form_data);
@endphp
<table>
    <tr>
        <td>Type of Zakat Calculation</td>
        <td>{{ $userZakatCalculation->zakat_type }}</td>
    </tr>
    <tr>
        <td>Calculation Date</td>
        <td>{{ $userZakatCalculation->date }}</td>
    </tr>
    <tr>
        <td>Nisab Standard</td>
        <td>{{ $userZakatCalculation->nisab_standard }}</td>
    </tr>
    <tr>
        <td>Nisab Value (Tk)</td>
        <td>{{ $userZakatCalculation->nisab_value }}</td>
    </tr>
    <tr>
        <td>Calculated Calendar Type</td>
        <td>{{ $formData->calender }}</td>
    </tr>
</table>

<table>
    <tr>
        <th colspan="2" style="text-align: center">Zakat Calculation Information</th>
    </tr>
    <tr>
        <th colspan="2" style="background-color: #0E72A3; text-align: center">ASSETS</th>
    </tr>

    @if ($userZakatCalculation->zakat_type == 'personal')
        <tr>
            <th>Zakat on Gold and Silver</th>
            <th>Selling Price (Tk)</th>
        </tr>
        <tr>
            <td>24-Carat Gold / Jewelry</td>
            <td>{{ $formData->gold_24_carat }}</td>
        </tr>
        <tr>
            <td>22-Carat Gold / Jewelry</td>
            <td>{{ $formData->gold_22_carat }}</td>
        </tr>
        <tr>
            <td>21-Carat Gold / Jewelry</td>
            <td>{{ $formData->gold_21_carat }}</td>
        </tr>
        <tr>
            <td>18-Carat Gold / Jewelry</td>
            <td>{{ $formData->gold_18_carat }}</td>
        </tr>
        <tr>
            <td>Other Gold materials</td>
            <td>{{ $formData->other_gold_materials }}</td>
        </tr>
        <tr>
            <td>Silver</td>
            <td>{{ $formData->silver }}</td>
        </tr>

        <tr>
            <th>Zakat on cash and bank deposits</th>
            <th>Actual value (Tk)</th>
        </tr>
        <tr>
            <td>Cash in Hand</td>
            <td>{{ $formData->cash_in_hand }}</td>
        </tr>
        <tr>
            <td>Bank Savings and current Account Balance</td>
            <td>{{ $formData->bank_savings }}</td>
        </tr>
        <tr>
            <td>Fixed Deposits, DPS, Special Savings (i.e. Hajj, marriage etc.)</td>
            <td>{{ $formData->fixed_deposits }}</td>
        </tr>
        <tr>
            <td>Insurance and Bonus on Insurance Premium</td>
            <td>{{ $formData->insurance }}</td>
        </tr>
        <tr>
            <td>Shares, stocks, Savings Certificates, Bonds etc. (price on the day of zakat payment)</td>
            <td>{{ $formData->shares }}</td>
        </tr>

        <tr>
            <th>Zakat on loans/receivables/advances</th>
            <th>Actual value (Tk)</th>
        </tr>
        <tr>
            <td>Loans Receivables from Friends and Relatives for certain</td>
            <td>{{ $formData->loans_receivables }}</td>
        </tr>
        <tr>
            <td>Security Deposits (to be received) and advance payments</td>
            <td>{{ $formData->security_deposits }}</td>
        </tr>
        <tr>
            <td>Provident Fund (if withdrawable)</td>
            <td>{{ $formData->provident_fund }}</td>
        </tr>
        <tr>
            <td>Land, House, Apartments purchased with the intention for resale</td>
            <td>{{ $formData->real_estate }}</td>
        </tr>
        <tr>
            <td>Balance of other Income after expenses (i.e. salaries, honorarium, gifts, house rents etc.)</td>
            <td>{{ $formData->other_income }}</td>
        </tr>
    @else
        <tr>
            <td>Amount of cash in hand</td>
            <td>{{ $formData->cash_in_hand }}</td>
        </tr>
        <tr>
            <td>Deposits in all types of bank accounts</td>
            <td>{{ $formData->deposits_in_bank }}</td>
        </tr>
        <tr>
            <td>Zakat accounting market value of all types of investments (gold, shares, stocks, bonds, land, houses, foreign currency etc.)</td>
            <td>{{ $formData->market_value_of_investments }}</td>
        </tr>
        <tr>
            <td>Market value of saleable manufactured stock</td>
            <td>{{ $formData->market_value_of_saleable_stock }}</td>
        </tr>
        <tr>
            <td>Market value of products in process, stock raw materials and packing materials</td>
            <td>{{ $formData->market_value_of_process_products }}</td>
        </tr>
        <tr>
            <td>Payment of collateral and all types of advances</td>
            <td>{{ $formData->payments_of_advances }}</td>
        </tr>
        <tr>
            <td>Bank LC Margin given in case of import</td>
            <td>{{ $formData->bank_lc_margin }}</td>
        </tr>
        <tr>
            <td>Advance money paid for the purchase of a product</td>
            <td>{{ $formData->advanced_money_for_products }}</td>
        </tr>
        <tr>
            <td>Value of Scrapped/Unsold Property</td>
            <td>{{ $formData->value_of_unsold_property }}</td>
        </tr>
        <tr>
            <td>Amount due from sale on balance/on credit</td>
            <td>{{ $formData->amount_due_from_sale }}</td>
        </tr>
        <tr>
            <td>Other sources and dues (loans paid, rent received from property, etc.)</td>
            <td>{{ $formData->other_sources_and_dues }}</td>
        </tr>
    @endif

    <tr>
        <th colspan="2" style="background-color: #0E72A3; text-align: center">LIABILITIES</th>
    </tr>

    @if ($userZakatCalculation->zakat_type == 'personal')
        <tr>
            <td>Personal loans to be paid in the current Zakat Year</td>
            <td>{{ $formData->personal_loans }}</td>
        </tr>
        <tr>
            <td>Bank loans to be paid in the current Zakat Year</td>
            <td>{{ $formData->bank_loans }}</td>
        </tr>
        <tr>
            <td>Other Liabilities/payables (i.e. House Rent, Tax, Utility Bills etc.)</td>
            <td>{{ $formData->other_liabilities }}</td>
        </tr>
    @else
        <tr>
            <td>Zakat installments paid in the current financial year on loans taken from banks or individuals in the form of investment in the core business (but the loan taken to increase the fixed assets of the business will not be considered as liability)</td>
            <td>{{ $formData->business_loans_installments }}</td>
        </tr>
        <tr>
            <td>Dues to suppliers or such others payable in the current Zakat financial year</td>
            <td>{{ $formData->dues_to_suppliers }}</td>
        </tr>
        <tr>
            <td>Employee's dues payable in the current Zakat financial year</td>
            <td>{{ $formData->employees_payable_dues }}</td>
        </tr>
        <tr>
            <td>Other debts paid in the current Zakat financial year (eg rent, taxes, utility bills etc.)</td>
            <td>{{ $formData->other_debts }}</td>
        </tr>
        <tr>
            <td>Bad Debt</td>
            <td>{{ $formData->bad_debts }}</td>
        </tr>
    @endif
</table>

<table>
    <tr>
        <th colspan="2" style="text-align: center">Zakat Calculation Result</th>
    </tr>

    <tr>
        <th>Zakat on loans/receivables/advances</th>
        <th>Actual value (Tk)</th>
    </tr>
    <tr>
        <td>Total Asset</td>
        <td>{{ $userZakatCalculation->total_assets }}</td>
    </tr>
    <tr>
        <td>Total Liabilities</td>
        <td>{{ $userZakatCalculation->total_liabilities }}</td>
    </tr>
    <tr>
        <td>Net Zakat-able Asset</td>
        <td>{{ $userZakatCalculation->net_zakatable_assets }}</td>
    </tr>
    <tr>
        <td>Total Zakat Amount</td>
        <td>{{ $userZakatCalculation->payable_zakat }}</td>
    </tr>
    <tr>
        <td>Amount Paid to CZM</td>
        <td>{{ $userZakatCalculation->paid_to_czm }}</td>
    </tr>
</table>

</body>
</html>
