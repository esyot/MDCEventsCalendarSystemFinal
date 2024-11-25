<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserRoles;

class CheckUserAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is logged in
        if (Auth::check()) {
            $user = Auth::user();

            // Define the allowed roles (replace with your role IDs or names)
            $allowedRoles = [1, 19, 20, 21];  // Example roles
            $usersCanAccess = UserRoles::whereIn('role_id', $allowedRoles)
                ->pluck('user_id');

            // If the user doesn't have one of the allowed roles, redirect back with an error
            if (! $usersCanAccess->contains($user->id)) {
                // Redirect back to the previous page with an error message
                return redirect()->back()->with('error', 'Your access rights have changed. You do not have permission to access this page.');
            }
        }

        // If the user passes the check, allow the request to proceed
        return $next($request);
    }
}
