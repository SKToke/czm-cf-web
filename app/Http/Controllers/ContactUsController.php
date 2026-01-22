<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Campaign;
use App\Rules\ReCaptcha;
use Illuminate\Http\Request;
use App\Models\ContactUsQuery;
use App\Enums\ContactType;
class ContactUsController extends Controller
{
    public function index(Request $request)
    {
        $banner = Banner::getBannerFor('Contact Us');
        $campaignId = $request->query('campaign_id');
        $campaignTitle = null;

        if ($campaignId) {
            $campaign = Campaign::find($campaignId);
            $campaignTitle = $campaign ? $campaign->title : null;
        }

        return view('contact_us.index', [
            'general' => ContactType::GENERAL->value,
            'personalZakat' => ContactType::PERSONAL_ZAKAT_CONSULTANCY->value,
            'businessZakat' => ContactType::BUSINESS_ZAKAT_CONSULTANCY->value,
            'banner'   => $banner,
            'campaignId' => $campaignId,
            'campaignTitle' => $campaignTitle
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'mobile_no' => 'nullable|numeric',
            'message' => 'nullable',
            'contact_type' => 'required',
            'campaign_id' => 'nullable',
            'g-recaptcha-response' => ['required', new ReCaptcha()]
        ]);

        try {
            $contactUsQuery = new ContactUsQuery($validatedData);

            if ($contactUsQuery->save()) {
                $message = ['message' => 'Successfully submitted.', 'status' => 'success'];
            }

            if ($request->ajax()) {
                return response()->json($message);
            } else {
                return redirect()->back()->with('message', $message['message'])->with('status', $message['status']);
            }
        } catch (\Exception $e) {
            $message = ['message' => 'There was a problem submitting your form.', 'status' => 'fail'];

            if ($request->ajax()) {
                return response()->json($message);
            } else {
                return redirect()->back()->with('error', $message['message']);
            }
        }
    }
}
