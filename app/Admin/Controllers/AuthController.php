<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use OpenAdmin\Admin\Controllers\AuthController as BaseAuthController;

class AuthController extends BaseAuthController
{
    /**
     * Override this method to check for email instead of username.
     *
     * @return string
     */
    protected function username()
    {
        return 'email';
    }

    protected function loginValidator(array $data)
    {
        $validator = parent::loginValidator($data);

        if ($validator->fails()) {
            return $validator;
        }

        $user = User::where('email', $data['email'])->first();

        if ($user && $user->active && $user->hasValidRoles() && !$user->removed) {
            return $validator;
        } else {
            throw ValidationException::withMessages([
                $this->username() => "Sorry! You are not authorized to view this page.",
            ])->redirectTo(url()->previous());
        }
    }
}
