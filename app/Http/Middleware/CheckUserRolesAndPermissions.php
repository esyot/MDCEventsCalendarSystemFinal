<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserRoles;

class CheckUserRolesAndPermissions
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Fetch users with valid roles
            $usersCanLogin = UserRoles::whereIn('role_id', [1, 19, 20, 21])->pluck('user_id');

            // Check if the logged-in user has a valid role
            if (!$usersCanLogin->contains($user->id)) {
                Auth::logout(); // Log the user out if they don't have a valid role
                return redirect()->route('login')->with('error', 'Your access rights have changed. Please log in again.');
            }
        }

        return $next($request);
    }
}
