<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\FlashHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'mobile_no' => ['nullable', 'string', 'min:10', 'max:15'],
        ]);

        if ($validator->fails()) {
            FlashHelper::trigger('Sorry! Something went wrong. Try again.', 'error');
        }

        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'mobile_no' => $data['mobile_no'],
        ]);
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function registered(Request $request, $user)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('verification.notice'),
                'message' => 'Registration successful.'
            ]);
        }

        // For non-AJAX requests, redirect to the intended path.
        return redirect()->route('verification.notice');
    }
}
