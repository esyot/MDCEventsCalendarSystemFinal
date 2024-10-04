<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserRoles;

class CheckUserRole
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $usersCanLogin = UserRoles::whereIn('role_id', [1, 19, 20, 21])->pluck('user_id');

            if (!$usersCanLogin->contains($user->id)) {
                Auth::logout(); 
                return redirect()->route('login')->with('error', 'Your access rights have changed. Please log in again.');
            }
        }

        return $next($request);
    }
}
