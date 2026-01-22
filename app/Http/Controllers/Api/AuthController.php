<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
use DB;
use Mail;

class AuthController extends Controller
{
    use HttpResponses;
    protected function credentials(Request $request): array
    {
        return $request->only('email', 'password');
    }

    public function login(Request $request): JsonResponse
    {
        if (!Auth::attempt($this->credentials($request))) {
            return $this->error('Credentials not match', 401);
        }

        $user = User::where('email', $request->get('email'))->first();

        $user->device_token = $request->get('device_token');
        $user->save();

        $user->tokens()->delete();
        $token = $user->createToken("API Token of user#{$user->id}")->plainTextToken;

        $user['name'] = $user->first_name;

        return $this->success('Logged in successfully!', ['user' => $user, 'token' => $token]);
    }

    public function providerCallback(Request $request)
    {
        try {
            $provider = $request->get('provider');

            //set userEmail for null (here fb) mail
            $userEmail = $request->get('email');

            if (null === $userEmail) {
                $userId = $request->get('id');
                $userEmail = "${userId}@facebook.com";
            }

            //find user
            $user = User::where([
                'email' => $userEmail,
            ])->first();

            //if user exits check social_info
            if ($user) {

                $key = "${provider}_id";
                $val = $request->get('id');

                $infoArray = json_decode($user->social_info, true);
                if (null === $infoArray || false === array_key_exists($key, $infoArray)) {
                    $infoArray[$key] = $val;
                    $user->social_info = json_encode($infoArray);
                    $user->device_token = $request->get('device_token');
                    $user->save();
                }
            }

            //If user not found then create new user and add info
            if (! $user) {
                $key = "${provider}_id";
                $val = $request->get('id');

                $infoArray = [
                    $key => $val,
                ];

                $user = User::create([
                    'email' => $userEmail,
                    'first_name' => $request->get('name'),
                    'last_name' => '',
                    'password' => Str::uuid()->toString(),
                    'social_info' => json_encode($infoArray),
                    'device_token' => $request->get('device_token'),
                    'email_verified_at' => now(),
                ]);
            }

            if(!$user->removed) {
                Auth::login($user);
                $user->tokens()->delete();
                $token = $user->createToken("API Token of user#{$user->id}")->plainTextToken;

                $user['name'] = $user->first_name;

                return $this->success('Logged in successfully!', ['user' => $user, 'token' => $token]);
            } else{
                return $this->error('Account not found.', [], 401);
            }
        } catch (Exception $e) {
            return $this->error('Something went wrong. Please try again later.', [], 401);
        }
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'mobile_no' => ['nullable', 'string', 'min:10', 'max:15'],
        ]);

        if ($validator->fails()) {
            return $this->error(
                'Registration failed',
                $validator->errors()->all(),
                401
            );
        }

        $user = User::create([
            'first_name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'mobile_no' => $request->get('mobile_no'),
        ]);

        //$token = $user->createToken("API Token of user#{$user->id}")->plainTextToken;
        return $this->success('Registration successful', ['user' => $user]);
    }

    public function logout(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success('Successfully logged out');
    }

    public function user(): JsonResponse
    {
        $user = Auth::user();
        $user['name'] = $user->first_name;
        unset($user['first_name']);
        unset($user['last_name']);

        return $this->success('This is the current user', $user);
    }

    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->error(
                'Attempt failed',
                $validator->errors()->all(),
                401
            );
        }

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send('email.forgetPassword', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return $this->success('A password-reset link has been sent to your email.');
    }
}
