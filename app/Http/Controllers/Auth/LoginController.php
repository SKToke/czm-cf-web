<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Helpers\FlashHelper;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        // Add your logic here if you need to customize the credentials
        return $request->only($this->username(), 'password');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * This method can be used for both, redirecting after a successful login
     * and returning a JSON response for AJAX requests.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        $user = $this->guard()->user();

        if ($user->removed) {
            auth()->logout();
            FlashHelper::trigger('Account not found', 'danger');
            return redirect()->route('home');
        }

        if ($user->active && $user->hasValidRoles()) {
            auth()->guard('admin')->login($user);
            return redirect('/admin');
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => $this->redirectPath(),
            ]);
        }

        FlashHelper::trigger('Logged in successfully!', 'success');
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Send the response after the user failed to authenticate.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        if ($request->ajax()) {
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);
        }

        $user = $this->guard()->getProvider()->retrieveByCredentials($this->credentials($request));

        if (!$user) {
            FlashHelper::trigger('Sorry! You are not registered yet', 'error');
        } else {
            FlashHelper::trigger('Invalid credentials', 'error');
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => trans('auth.failed'),
            ]);
    }
}
