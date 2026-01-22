<?php

namespace App\Http\Controllers\Api;

use App\Enums\CountryTypeEnum;
use App\Enums\ZakatTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\ContactUsQuery;
use App\Models\Nisab;
use App\Models\User;
use App\Models\UserZakatCalculation;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PDF;

class ZakatCalculatorController extends Controller
{
    use HttpResponses;

    public function index(): JsonResponse
    {
        try{
            $nisab = $this->getNisabValue();
            $nisabinfo = [
                'gold_value' => number_format($nisab->gold_value, 0),
                'silver_value' => number_format($nisab->silver_value, 0),
                'gold_price' => number_format($nisab->gold_price, 0),
                'silver_price' => number_format($nisab->silver_price, 0),
                'nisab_update_date' => Carbon::parse($nisab->nisab_update_date)->format('d M Y'),
            ];
            return $this->success('This is the nisab info', $nisabinfo);
        }catch (\Exception $e) {
            return $this->error('Failed to retrieve data', [$e->getMessage()], 401);
        }
    }

    public function personalZakatCalculation(Request $request)
    {
        $requestedPersonalZakatData =  $request->except('_token');

        $nisab = $this->getNisabValue();
        $zakatType = $request->input('zakat_type');
        $nisabValue = $request->input('nisab') == 'gold' ? $nisab->gold_price : $nisab->silver_price;

        $totalAssetsFields = ['gold_24_carat', 'gold_22_carat', 'gold_21_carat', 'gold_18_carat', 'other_gold_materials', 'silver', 'cash_in_hand', 'bank_savings', 'fixed_deposits', 'insurance', 'shares', 'loans_receivables', 'security_deposits', 'provident_fund', 'real_estate', 'other_income'];
        $totalAssets = collect($totalAssetsFields)->sum(function ($field) use ($request) {
            return (int)$request->input($field);
        });

        $totalLiabilitiesFields = ['personal_loans', 'bank_loans', 'other_liabilities'];
        $totalLiabilities = collect($totalLiabilitiesFields)->sum(function ($field) use ($request) {
            return (int)$request->input($field);
        });

        $netZakatableAssets = max($totalAssets - $totalLiabilities, 0);

        if ($netZakatableAssets < $nisabValue) {
            $netZakatableAssets = 0;
            $payableZakat = 0;
        } else {
            $payableZakat = $request->input('calender') == "lunar" ? ($netZakatableAssets * 2.5) / 100 : ($netZakatableAssets * 2.58) / 100;
        }

        $responseData = [
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'netZakatableAssets' => $netZakatableAssets,
            'payableZakat' => $payableZakat,
            'zakatType' => ZakatTypeEnum::from($zakatType)->getTitle(),
        ];

        return $this->success('personal zakat', $responseData);
    }


    public function businessZakatCalculation(Request $request)
    {
        $requestedBusinessZakatData =  $request->except('_token');

        $nisab = $this->getNisabValue();
        $zakatType = $request->input('zakat_type');
        $nisabValue = $request->input('nisab') == 'gold' ? $nisab->gold_price : $nisab->silver_price;

        $totalAssetsFields = ['cash_in_hand', 'deposits_in_bank', 'market_value_of_investments', 'market_value_of_saleable_stock', 'market_value_of_process_products', 'payments_of_advances', 'bank_lc_margin', 'advanced_money_for_products', 'value_of_unsold_property', 'amount_due_from_sale', 'other_sources_and_dues'];
        $totalAssets = collect($totalAssetsFields)->sum(function ($field) use ($request) {
            return (int)$request->input($field);
        });

        $totalLiabilitiesFields = ['business_loans_installments', 'dues_to_suppliers', 'employees_payable_dues', 'other_debts', 'bad_debts'];
        $totalLiabilities = collect($totalLiabilitiesFields)->sum(function ($field) use ($request) {
            return (int)$request->input($field);
        });

        $netZakatableAssets = max($totalAssets - $totalLiabilities, 0);

        if ($netZakatableAssets < $nisabValue) {
            $netZakatableAssets = 0;
            $payableZakat = 0;
        } else {
            $payableZakat = $request->input('calender') == "lunar" ? ($netZakatableAssets * 2.5) / 100 : ($netZakatableAssets * 2.58) / 100;
        }

        $responseData = [
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'netZakatableAssets' => $netZakatableAssets,
            'payableZakat' => $payableZakat,
            'zakatType' => ZakatTypeEnum::from($zakatType)->getTitle(),
        ];

        return $this->success('Business zakat', $responseData);
    }


    private function getNisabValue() {
        return Nisab::whereNull('deleted_at')
            ->orderBy('nisab_update_date', 'desc')
            ->orderBy('updated_at', 'desc')
            ->first();
    }

    public function saveZakatCalculation(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user) {
                $payableZakat = round($request->get('payable_zakat'), 2);
                $nisabStandard = $request->get('nisab');
                $nisab = $this->getNisabValue();
                $nisabValue = $nisabStandard ? $nisab->gold_price : $nisab->silver_price;

                if ($request->get('zakat_type') === 'personal') {
                    $filteredData = [
                        'nisab' => $request->get('nisab'),
                        'calender' => $request->get('calender'),
                        'zakat_type' => $request->get('zakat_type'),
                        "shares"=> $request->get('shares'),
                        "silver" => $request->get('silver'),
                        "insurance" => $request->get('insurance'),
                        "bank_loans" => $request->get('bank_loans'),
                        "real_estate" => $request->get('real_estate'),
                        "bank_savings" => $request->get('bank_savings'),
                        "cash_in_hand" => $request->get('cash_in_hand'),
                        "other_income" => $request->get('other_income'),
                        "gold_18_carat" => $request->get('gold_18_carat'),
                        "gold_21_carat" => $request->get('gold_21_carat'),
                        "gold_22_carat" => $request->get('gold_22_carat'),
                        "gold_24_carat" => $request->get('gold_24_carat'),
                        "fixed_deposits" => $request->get('fixed_deposits'),
                        "personal_loans" => $request->get('personal_loans'),
                        "provident_fund" => $request->get('provident_fund'),
                        "loans_receivables" => $request->get('loans_receivables'),
                        "other_liabilities" => $request->get('other_liabilities'),
                        "security_deposits" => $request->get('security_deposits'),
                        "other_gold_materials" => $request->get('other_gold_materials')
                    ];

                    $requestedData = json_encode($filteredData);
                } else {
                    $filteredData = [
                        'nisab' => $request->get('nisab'),
                        'calender' => $request->get('calender'),
                        'zakat_type' => $request->get('zakat_type'),
                        "bad_debts"=> $request->get('bad_debts'),
                        "other_debts" => $request->get('other_debts'),
                        "cash_in_hand" => $request->get('cash_in_hand'),
                        "bank_lc_margin" => $request->get('bank_lc_margin'),
                        "deposits_in_bank" => $request->get('deposits_in_bank'),
                        "dues_to_suppliers" => $request->get('dues_to_suppliers'),
                        "amount_due_from_sale" => $request->get('amount_due_from_sale'),
                        "payments_of_advances" => $request->get('payments_of_advances'),
                        "employees_payable_dues" => $request->get('employees_payable_dues'),
                        "other_sources_and_dues" => $request->get('other_sources_and_dues'),
                        "value_of_unsold_property" => $request->get('value_of_unsold_property'),
                        "advanced_money_for_products" => $request->get('advanced_money_for_products'),
                        "business_loans_installments" => $request->get('business_loans_installments'),
                        "market_value_of_investments" => $request->get('market_value_of_investments'),
                        "market_value_of_saleable_stock" => $request->get('market_value_of_saleable_stock'),
                        "market_value_of_process_products" => $request->get('market_value_of_process_products'),
                    ];

                    $requestedData = json_encode($filteredData);
                }

                $userZakatCalculation = $user->zakatCalculations()->firstOrCreate([
                    'zakat_type' => $request->get('zakat_type'),
                    'date' => today(),
                    'nisab_standard' => $nisabStandard,
                    'total_assets' => $request->get('total_assets'),
                    'total_liabilities' => $request->get('total_liabilities'),
                    'net_zakatable_assets' => $request->get('net_zakatable_assets'),
                    'payable_zakat' => $payableZakat,
                    'nisab_value' => $nisabValue,
                ], [
                    'name' => $user->getFullNameAttribute(),
                    'mobile' => $user->mobile_no,
                    'registered_user' => true,
                    'calculation_form_data' => $requestedData,
                ]);

                if ($userZakatCalculation) {
                    $userZakatCalculation->archived = true;
                    $userZakatCalculation->save();
                }

                return $this->success('Saved successfully', []);
            } else {
                return $this->error('You need to login first to proceed', [], 401);
            }
        } catch (\Exception $e) {
            // Return error response for any other exception
            return $this->error('Error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    public function downloadZakatCalculation(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user) {
                if (!$user->active) {
                    return $this->error('You are not authorized', [], 401);
                }
                $payableZakat = round($request->get('payable_zakat'), 2);
                $nisabStandard = $request->get('nisab');
                $nisab = $this->getNisabValue();
                $nisabValue = $nisabStandard ? $nisab->gold_price : $nisab->silver_price;

                if ($request->get('zakat_type') === 'personal') {
                    $filteredData = [
                        'nisab' => $request->get('nisab'),
                        'calender' => $request->get('calender'),
                        'zakat_type' => $request->get('zakat_type'),
                        "shares"=> $request->get('shares'),
                        "silver" => $request->get('silver'),
                        "insurance" => $request->get('insurance'),
                        "bank_loans" => $request->get('bank_loans'),
                        "real_estate" => $request->get('real_estate'),
                        "bank_savings" => $request->get('bank_savings'),
                        "cash_in_hand" => $request->get('cash_in_hand'),
                        "other_income" => $request->get('other_income'),
                        "gold_18_carat" => $request->get('gold_18_carat'),
                        "gold_21_carat" => $request->get('gold_21_carat'),
                        "gold_22_carat" => $request->get('gold_22_carat'),
                        "gold_24_carat" => $request->get('gold_24_carat'),
                        "fixed_deposits" => $request->get('fixed_deposits'),
                        "personal_loans" => $request->get('personal_loans'),
                        "provident_fund" => $request->get('provident_fund'),
                        "loans_receivables" => $request->get('loans_receivables'),
                        "other_liabilities" => $request->get('other_liabilities'),
                        "security_deposits" => $request->get('security_deposits'),
                        "other_gold_materials" => $request->get('other_gold_materials')
                    ];

                    $requestedData = json_encode($filteredData);
                } else {
                    $filteredData = [
                        'nisab' => $request->get('nisab'),
                        'calender' => $request->get('calender'),
                        'zakat_type' => $request->get('zakat_type'),
                        "bad_debts"=> $request->get('bad_debts'),
                        "other_debts" => $request->get('other_debts'),
                        "cash_in_hand" => $request->get('cash_in_hand'),
                        "bank_lc_margin" => $request->get('bank_lc_margin'),
                        "deposits_in_bank" => $request->get('deposits_in_bank'),
                        "dues_to_suppliers" => $request->get('dues_to_suppliers'),
                        "amount_due_from_sale" => $request->get('amount_due_from_sale'),
                        "payments_of_advances" => $request->get('payments_of_advances'),
                        "employees_payable_dues" => $request->get('employees_payable_dues'),
                        "other_sources_and_dues" => $request->get('other_sources_and_dues'),
                        "value_of_unsold_property" => $request->get('value_of_unsold_property'),
                        "advanced_money_for_products" => $request->get('advanced_money_for_products'),
                        "business_loans_installments" => $request->get('business_loans_installments'),
                        "market_value_of_investments" => $request->get('market_value_of_investments'),
                        "market_value_of_saleable_stock" => $request->get('market_value_of_saleable_stock'),
                        "market_value_of_process_products" => $request->get('market_value_of_process_products'),
                    ];

                    $requestedData = json_encode($filteredData);
                }

                $userZakatCalculation = $user->zakatCalculations()->firstOrCreate([
                    'zakat_type' => $request->get('zakat_type'),
                    'date' => today(),
                    'nisab_standard' => $nisabStandard,
                    'total_assets' => $request->get('total_assets'),
                    'total_liabilities' => $request->get('total_liabilities'),
                    'net_zakatable_assets' => $request->get('net_zakatable_assets'),
                    'payable_zakat' => $payableZakat,
                    'nisab_value' => $nisabValue,
                ], [
                    'name' => $user->getFullNameAttribute(),
                    'mobile' => $user->mobile_no,
                    'registered_user' => true,
                    'calculation_form_data' => $requestedData,
                ]);

                if ($userZakatCalculation) {
                    $userZakatCalculation->exported = true;
                    $userZakatCalculation->save();
                }

                $pdfFileName = 'zakat-calculation' . time() . '.pdf';
                $pdfFilePath = 'public/pdf/' . $pdfFileName;

                $pdf = PDF::loadView('pdf.zakat', ['userZakatCalculation' => $userZakatCalculation]);

                Storage::put($pdfFilePath, $pdf->output());

                $pdfUrl = Storage::url($pdfFilePath);

                return $this->success('Pdf url', url($pdfUrl));
            } else {
                return $this->error('You need to login first to proceed', [], 401);
            }
        } catch (\Exception $e) {
            // Return error response for any other exception
            return $this->error('Error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    public function downloadArchivedZakatCalculation(Request $request)
    {
        try {
            $user = Auth::user();
            $zakatCalculationId = $request->get('zakat_calculation_id');

            if ($user && $user->active && $zakatCalculationId) {
                $userZakatCalculation = UserZakatCalculation::find($zakatCalculationId);

                if ($userZakatCalculation) {
                    $pdfFileName = 'zakat-calculation' . $zakatCalculationId . time() . '.pdf';
                    $pdfFilePath = 'public/pdf/' . $pdfFileName;

                    $pdf = PDF::loadView('pdf.zakat', ['userZakatCalculation' => $userZakatCalculation]);

                    Storage::put($pdfFilePath, $pdf->output());

                    $pdfUrl = Storage::url($pdfFilePath);

                    return $this->success('Zakat Calculation Pdf url', url($pdfUrl));
                } else {
                    return $this->error('Zakat Calculation not found', [], 401);
                }
            } else {
                return $this->error('You are not authorized', [], 401);
            }
        } catch (\Exception $e) {
            // Return error response for any other exception
            return $this->error('Error occurred: ' . $e->getMessage(), [], 500);
        }
    }
}
