<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Venue;
use App\Models\VenueCoordinator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VenueCoordinatorController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $role = Role::where('role', 'venue_coordinator')->first();
        $user_role_ids = UserRole::where('role_id', $role->id)->pluck('user_id');

        $venueCoordinators = User::whereIn('id', $user_role_ids)->get();

        $user_role = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();

        $venues = null;

        $user_venue = null;

        if (session('venue_ids')) {
            $venueIds = session('venue_ids');
            $venues = Venue::whereIn('id', $venueIds)->get();
            $user_venue = User::find(session('user_id'));
        }

        $venue_list = Venue::all();

        return Inertia::render('VenueCoordinator/venueCoordinator', [
            'user' => $user,
            'venueCoordinators' => $venueCoordinators,
            'pageTitle' => 'List of Venue Coordinators',
            'user_role' => $user_role,
            'venues' => $venues,
            'user_venue' => $user_venue,
            'venue_list' => $venue_list

        ]);
    }

    public function fetchUserVenue($user_id)
    {

        $user = User::with('venues')->find($user_id);

        $venueIds = VenueCoordinator::where('user_id', $user->id)->pluck('venue_id');

        return redirect()->back()->with(

            [
                'venue_ids' => $venueIds,
                'user_id' => $user->id,
            ]
        );
    }

    public function remove($user_id, $venue_id)
    {

        $venueCoordinator = VenueCoordinator::where('user_id', $user_id)
            ->where('venue_id', $venue_id);

        if ($venueCoordinator) {

            $venueCoordinator->delete();

            return redirect()->route('venue-coordinator', ['user_id' => $user_id]);

        }
        return redirect() - back()->with('error', 'Venue not found!');


    }

    public function venueAdd(Request $request)
    {

        $venueCoordinator = VenueCoordinator::create([
            'user_id' => $request->user_id,
            'venue_id' => $request->venue_id
        ]);

        if ($venueCoordinator) {

            return redirect()->route('venue-coordinator', ['user_id' => $request->user_id]);
        }

        return redirect()->back()->with('error', 'Venue has not been added to a user successfully!');
    }
}
