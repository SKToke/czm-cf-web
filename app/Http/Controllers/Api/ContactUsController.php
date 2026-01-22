<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\ContactUsQuery;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends Controller
{
    use HttpResponses;

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'mobile_no' => ['nullable','numeric'],
            'message' => ['nullable', 'string'],
            'contact_type' => ['required', 'integer'],
            'campaign_id' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return $this->error(
                'Submission failed',
                $validator->errors()->all(),
                401
            );
        }

        try {
            $contactUsQuery = new ContactUsQuery($request->all());

            if ($request->get('campaign_id')) {
                $campaign = Campaign::findByCustomId($request->get('campaign_id'));
                if ($campaign) {
                    $contactUsQuery->campaign_id = $campaign->id;
                }
            }

            if ($contactUsQuery->save()) {
                return $this->success('Successfully submitted.');
            } else {
                return $this->error('Failed to submit', [], 401);
            }
        } catch (\Exception $e) {
            return $this->error('Failed to submit', [$e->getMessage()], 401);
        }
    }

}
