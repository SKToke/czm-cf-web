<?php

namespace App\Http\Middleware;

use App\Helpers\FlashHelper;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request; // Correct namespace for the Request class
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsCurrentUser
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = User::findOrFail($request->id);

            if ($user->id !== Auth::id()) {
                FlashHelper::trigger('You are not authorized to view this page.', 'danger');
                return redirect()->route('home');
            }

            $request->attributes->add(['user' => $user]);
        } catch (ModelNotFoundException $e) {
            FlashHelper::trigger('User not found.', 'danger');
            return redirect()->route('home');
        } catch (\Exception $e) {
            FlashHelper::trigger('An unexpected error occurred.', 'danger');
            return redirect()->route('home');
        }

        return $next($request);
    }


}
