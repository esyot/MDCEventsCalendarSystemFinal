<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserRole;

class CheckUserAccess
{
    public function handle(Request $request, Closure $next)
    {

        if (Auth::check()) {
            $user = Auth::user();

            $allowedRoles = Role::whereIn('role', 'event_coordinator', 'venue_coordinator')->pluck('id');
            $usersCanAccess = UserRole::whereIn('role_id', $allowedRoles)
                ->pluck('user_id');


            if (!$usersCanAccess->contains($user->id)) {

                return redirect()->back()->with('error', 'Your access rights have changed. You do not have permission to access this page.');
            }
        }


        return $next($request);
    }
}
