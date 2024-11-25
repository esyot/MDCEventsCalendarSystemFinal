<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\UserPermission;
use App\Models\Event;
use App\Models\UserRoles;
use App\Models\VenueCoordinator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PageController extends Controller
{
    public function dashboard()
    {
        $events_today = Event::where('isApprovedByVenueCoordinator', true)->
            where('isApprovedByAdmin', true)->where('date_start', today())->get();

        $user_role = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();


        if ($user_role == 'event_coordinator') {
            $event_updates = Event::whereNot('isApprovedByAdmin', null)
                ->get();
        } else if ($user_role == 'venue_coordinator') {

            $venueIds = VenueCoordinator::where('user_id', Auth::user()->id)->get()->pluck('venue_id');
            $event_updates = Event::where('isApprovedByVenueCoordinator', null)
                ->where('user_id', Auth::user()->id)
                ->whereIn('venue_id', $venueIds)
                ->join('venues', 'events.venue_id', '=', 'venues.id')  // Add the join clause
                ->select('events.*', 'venues.name as venue_name', 'venues.building as venue_building')  // Select events columns and venue columns
                ->get();
        } else {

            $event_updates = Event::join('venues', 'events.venue_id', '=', 'venues.id')  // Add the join clause
                ->select('events.*', 'venues.name as venue_name', 'venues.building as venue_building')  // Select events columns and venue columns
                ->get();
        }

        return Inertia::render('Dashboard/dashboard', [
            'pageTitle' => 'Dashboard',
            'user' => Auth::user(),
            'event_updates' => $event_updates,
            'events_today' => $events_today,
            'user_role' => $user_role
        ]);
    }




    public function guest()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $events = Event::where('isApprovedByVenueCoordinator', true)->where('isApprovedByAdmin', true)->get();
        $events_today = Event::where('isApprovedByVenueCoordinator', true)->where('isApprovedByAdmin', true)->where('date_start', today())->get();

        return Inertia::render('Guest/Dashboard/dashboard', [
            'events' => $events,
            'events_today' => $events_today,
        ]);
    }

    public function calendar()
    {

        return Inertia::render('Guest/Calendar/calendar', [

        ]);
    }

}
