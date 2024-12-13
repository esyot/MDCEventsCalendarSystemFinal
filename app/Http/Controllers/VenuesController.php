<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Role;
use App\Models\Venue;
use App\Models\VenueCoordinator;
use Auth;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VenuesController extends Controller
{
    public function index()
    {

        $venueIds = VenueCoordinator::where('user_id', Auth::user()->id)->get()->pluck('venue_id');


        $venuesApproved = DB::table('events')
            ->join('venues', 'events.venue_id', '=', 'venues.id')
            ->select('events.*',
                'venues.name as venue_name',
                'venues.building as venue_building',
                'events.id as event_id',
                'events.name as event_name',
            )
            ->whereNotNull('isApprovedByVenueCoordinator')
            ->whereIn('venue_id', $venueIds)
            ->orderBy('date_start', 'DESC')->get();

        $venuesRequested = DB::table('events')
            ->join('venues', 'events.venue_id', '=', 'venues.id')
            ->select('events.*',
                'venues.name as venue_name',
                'venues.building as venue_building',
                'events.id as event_id',
                'events.name as event_name',
            )
            ->whereNull('isApprovedByAdmin')
            ->whereNull('isApprovedByVenueCoordinator')
            ->whereIn('venue_id', $venueIds)
            ->orderBy('date_start', 'DESC')->get();

        $venuesDeclined = DB::table('events')
            ->join('venues', 'events.venue_id', '=', 'venues.id')
            ->select('events.*',
                'venues.name as venue_name',
                'venues.building as venue_building',

                'events.id as event_id',
                'events.name as event_name',
            )
            ->whereNotNull('comment')
            ->whereNull('isApprovedByAdmin')
            ->whereNull('isApprovedByVenueCoordinator')
            ->whereIn('venue_id', $venueIds)
            ->orderBy('date_start', 'DESC')->get();


        $user_role = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();


        return Inertia::render('Venue/venue', [
            'venuesApproved' => $venuesApproved->toArray(),
            'venuesRequested' => $venuesRequested->toArray(),
            'venuesDeclined' => $venuesDeclined->toArray(),
            'user_role' => $user_role,
            'user' => Auth::user(),
            'pageTitle' => 'Venues',
        ]);

    }
}