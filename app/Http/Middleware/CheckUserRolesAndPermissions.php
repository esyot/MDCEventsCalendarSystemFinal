<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserRole;

class CheckUserRolesAndPermissions
{
    public function handle(Request $request, Closure $next)
    {

        if (Auth::check()) {
            $user = Auth::user();

            $roleAllowedIds = Role::whereIn('role', ['admin', 'event_coordinator', 'venue_coordinator'])->pluck('id');
            $usersCanLogin = UserRole::whereIn('role_id', $roleAllowedIds)->pluck('user_id');


            if (!$usersCanLogin->contains($user->id)) {
                Auth::logout();
                return redirect()->route('guest')->with('error', 'Your access rights have changed. Please log in again.');
            }
        }

        return $next($request);
    }
}
