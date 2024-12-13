<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role; // Import Role model

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {

        if (Auth::check()) {
            $user = Auth::user();

            $userRoleNames = $user->roles->pluck('role')->toArray();


            $allowedRoles = array_intersect($userRoleNames, $roles);


            if (empty($allowedRoles)) {
                return redirect()->route('unauthorized')->with('error', 'You do not have permission to access this page.');
            }
        } else {

            return redirect()->route('guest')->with('error', 'You need to be logged in to access this page.');
        }


        return $next($request);
    }
}
