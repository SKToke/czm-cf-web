<?php

namespace App\Http\Controllers;

use App\Helpers\FlashHelper;
use App\Models\Banner;
use App\Models\UserZakatCalculation;
use Illuminate\Http\Request;
use App\Models\Nisab;
use Illuminate\Support\Facades\Session;
use PDF;

class ZakatCalculatorController extends Controller
{
    public function index()
    {
        $banner = Banner::getBannerFor('Zakat Calculator');
        $nisab = $this->getNisabValue();
        $requestedPersonalZakatData = null;
        $requestedBusinessZakatData = null;
        return view('zakat.index', compact('nisab', 'banner', 'requestedPersonalZakatData', 'requestedBusinessZakatData'));
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

        if ($request->ajax()) {
            return response()->json([
                'totalAssets' => $totalAssets,
                'totalLiabilities' => $totalLiabilities,
                'netZakatableAssets' => $netZakatableAssets,
                'payableZakat' => $payableZakat,
                'zakatType' => $zakatType,
                'requestedPersonalZakatData' => $requestedPersonalZakatData,
            ]);
        }

        return view('zakat.index', compact('totalAssets', 'totalLiabilities', 'netZakatableAssets', 'payableZakat', 'zakatType', 'requestedPersonalZakatData'));
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

        if ($request->ajax()) {
            return response()->json([
                'totalAssets' => $totalAssets,
                'totalLiabilities' => $totalLiabilities,
                'netZakatableAssets' => $netZakatableAssets,
                'payableZakat' => $payableZakat,
                'zakatType' => $zakatType,
                'requestedBusinessZakatData' => $requestedBusinessZakatData,
            ]);
        }
        return view('zakat.index', compact( 'totalAssets', 'totalLiabilities', 'netZakatableAssets', 'payableZakat', 'zakatType', 'requestedBusinessZakatData'));
    }

    public function resetZakatForm(Request $request)
    {
        if (Session::has('requestedData')) {
            Session::forget('requestedData');
            Session::forget('requestedDataTime');
        }
        return response()->json(['message' => 'Zakat calculation form reset successfully']);
    }

    public function saveCalculationToArchive(Request $request)
    {
        if (Session::has('requestedData')) {
            Session::forget('requestedData');
            Session::forget('requestedDataTime');
        }
        Session::put('requestedData', $request->input('requested_data'));
        Session::put('requestedDataTime', now());

        if (!auth()->user()) {
            FlashHelper::trigger('Sorry! You need to login first to proceed.', 'danger');
            return redirect()->back();
        }
        $requestedData = json_decode($request->input('requested_data'));

        $nisabStandard = $requestedData->nisab;
        $zakatType = $requestedData->zakat_type;
        $payableZakat = $request->input('payable_zakat');
        $payableZakat = round($payableZakat, 2);
        $totalAssets = $request->input('total_assets');
        $totalLiabilities = $request->input('total_liabilities');
        $netZakatableAssets = $request->input('net_zakatable_assets');

        $nisab = $this->getNisabValue();
        $nisabValue = $nisabStandard ? $nisab->gold_price : $nisab->silver_price;

        $userZakatCalculation = auth()->user()->zakatCalculations()->firstOrCreate([
            'zakat_type' => $zakatType,
            'date' => today(),
            'nisab_standard' => $nisabStandard,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'net_zakatable_assets' => $netZakatableAssets,
            'payable_zakat' => $payableZakat,
            'nisab_value' => $nisabValue,
        ], [
            'name' => auth()->user()->getFullNameAttribute(),
            'mobile' => auth()->user()->mobile_no,
            'registered_user' => true,
            'calculation_form_data' => $request->input('requested_data'),
        ]);

        if ($userZakatCalculation) {
            $userZakatCalculation->archived = true;
            $userZakatCalculation->save();
        }

        FlashHelper::trigger('Zakat calculation saved successfully!', 'success');
        if ($request->ajax()) {
            return response()->json(['message' => 'Zakat calculation saved successfully']);
        }

        return redirect()->back();
    }

    public function payCalculatedZakat(Request $request)
    {
        $requestedData = json_decode($request->input('requested_data'));

        $nisabStandard = $requestedData->nisab;
        $zakatType = $requestedData->zakat_type;
        $payableZakat = $request->input('payable_zakat');
        $payableZakat = round($payableZakat, 2);
        $totalAssets = $request->input('total_assets');
        $totalLiabilities = $request->input('total_liabilities');
        $netZakatableAssets = $request->input('net_zakatable_assets');

        $nisab = $this->getNisabValue();
        $nisabValue = $nisabStandard ? $nisab->gold_price : $nisab->silver_price;

        if (auth()->user()) {
            $userZakatCalculation = UserZakatCalculation::firstOrCreate([
                'zakat_type' => $zakatType,
                'date' => today(),
                'nisab_standard' => $nisabStandard,
                'total_assets' => $totalAssets,
                'total_liabilities' => $totalLiabilities,
                'net_zakatable_assets' => $netZakatableAssets,
                'payable_zakat' => $payableZakat,
                'nisab_value' => $nisabValue,
                'email' => auth()->user()->email,
            ], [
                'name' => auth()->user()->getFullNameAttribute(),
                'mobile' => auth()->user()->mobile_no,
                'registered_user' => true,
                'calculation_form_data' => $request->input('requested_data'),
            ]);
        } else {
            $userZakatCalculation = UserZakatCalculation::firstOrCreate([
                'zakat_type' => $zakatType,
                'date' => today(),
                'nisab_standard' => $nisabStandard,
                'total_assets' => $totalAssets,
                'total_liabilities' => $totalLiabilities,
                'net_zakatable_assets' => $netZakatableAssets,
                'payable_zakat' => $payableZakat,
                'nisab_value' => $nisabValue,
            ], [
                'registered_user' => false,
                'calculation_form_data' => $request->input('requested_data'),
            ]);
        }
        $redirectUrl = route('payment.index') . '?payableZakat=' . urlencode($payableZakat);

        return redirect()->to($redirectUrl);
    }


    public function exportPdf(Request $request)
    {
        if (Session::has('requestedData')) {
            Session::forget('requestedData');
            Session::forget('requestedDataTime');
        }
        Session::put('requestedData', $request->input('requested_data'));
        Session::put('requestedDataTime', now());

        if (!auth()->user()) {
            FlashHelper::trigger('Sorry! You need to login first to proceed.', 'danger');
            return redirect()->back();
        }
        $requestedData = json_decode($request->input('requested_data'));

        $nisabStandard = $requestedData->nisab;
        $zakatType = $requestedData->zakat_type;
        $payableZakat = $request->input('payable_zakat');
        $payableZakat = round($payableZakat, 2);
        $totalAssets = $request->input('total_assets');
        $totalLiabilities = $request->input('total_liabilities');
        $netZakatableAssets = $request->input('net_zakatable_assets');

        $nisab = $this->getNisabValue();
        $nisabValue = $nisabStandard ? $nisab->gold_price : $nisab->silver_price;

        $userZakatCalculation = auth()->user()->zakatCalculations()->firstOrCreate([
            'zakat_type' => $zakatType,
            'date' => today(),
            'nisab_standard' => $nisabStandard,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'net_zakatable_assets' => $netZakatableAssets,
            'payable_zakat' => $payableZakat,
            'nisab_value' => $nisabValue,
        ], [
            'name' => auth()->user()->getFullNameAttribute(),
            'mobile' => auth()->user()->mobile_no,
            'registered_user' => true,
            'calculation_form_data' => $request->input('requested_data'),
        ]);

        if ($userZakatCalculation) {
            $userZakatCalculation->exported = true;
            $userZakatCalculation->save();
        }

        $pdf = PDF::loadView('pdf.zakat', ['userZakatCalculation' => $userZakatCalculation]);
        return $pdf->download('zakat-calculation.pdf');
    }


    private function getNisabValue() {
        return Nisab::whereNull('deleted_at')
            ->orderBy('nisab_update_date', 'desc')
            ->orderBy('updated_at', 'desc')
            ->first();
    }

}
