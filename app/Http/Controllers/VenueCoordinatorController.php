<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRoles;
use App\Models\VenueCoordinator;
use Auth;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VenueCoordinatorController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $venueCoordinators = VenueCoordinator::with([
            'user:id,fname,lname', // Select user fields
            'user.teacher.department:id,name', // Select department
            'venue:id,name,building' // Select venue fields
        ])
            ->select('user_id') // Only select distinct user_id
            ->distinct() // Ensure only distinct user_id
            ->get();

        $user_role = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();




        return Inertia::render('VenueCoordinator/venueCoordinator', [
            'user' => $user,
            'venueCoordinators' => $venueCoordinators,
            'pageTitle' => 'Venue Coordinators',
            'user_role' => $user_role,
        ]);
    }
}
