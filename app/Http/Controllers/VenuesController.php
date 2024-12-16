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

        $user_role = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();

        if ($user_role == 'venue_coordinator') {
            $venuesApproved = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as event_name',
                    'events.levels as levels',
                    'venues.id as venue_id',
                    'venues.name as venue_name',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.updated_at as updated_at',
                    DB::raw('GROUP_CONCAT(departments.accronym ORDER BY departments.accronym ASC SEPARATOR ", ") as department_acronyms')
                )
                ->groupBy(
                    'events.id',
                    'terms.name',
                    'events.name',
                    'events.levels',
                    'events.date',
                    'venues.id',
                    'venues.name',
                    'venues.building',
                    'event_junctions.time_end',
                    'event_junctions.time_start',
                    'event_junctions.date_end',
                    'event_junctions.updated_at',
                )
                ->whereNotNull('approved_by_venue_coordinator_at')
                ->whereIn('venues_id', $venueIds)
                ->get();


            $venuesRequested = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as event_name',
                    'events.levels as levels',
                    'venues.id as venue_id',
                    'venues.name as venue_name',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.updated_at as updated_at',
                    DB::raw('GROUP_CONCAT(departments.accronym ORDER BY departments.accronym ASC SEPARATOR ", ") as department_acronyms')
                )
                ->groupBy(
                    'events.id',
                    'terms.name',
                    'events.name',
                    'events.levels',
                    'events.date',
                    'venues.id',
                    'venues.name',
                    'venues.building',
                    'event_junctions.time_end',
                    'event_junctions.time_start',
                    'event_junctions.date_end',
                    'event_junctions.updated_at',
                )
                ->whereNull('approved_by_venue_coordinator_at')
                ->whereIn('venues_id', $venueIds)
                ->get();


            $venuesDeclined = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as venue_name',
                    'events.levels as levels',
                    'venues.id as venue_id',
                    'venues.name as venue_name',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.updated_at as updated_at',
                    DB::raw('GROUP_CONCAT(departments.accronym ORDER BY departments.accronym ASC SEPARATOR ", ") as department_acronyms')
                )
                ->groupBy(
                    'events.id',
                    'terms.name',
                    'events.name',
                    'events.levels',
                    'events.date',
                    'venues.id',
                    'venues.name',
                    'venues.building',
                    'event_junctions.time_end',
                    'event_junctions.time_start',
                    'event_junctions.date_end',
                    'event_junctions.updated_at',
                )
                ->whereIn('venues_id', $venueIds)
                ->whereNull('approved_by_venue_coordinator_at')
                ->whereNull('approved_by_admin_at')
                ->whereNotNull('comment')
                ->get();
        } else {
            $venuesApproved = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as event_name',
                    'events.levels as levels',
                    'venues.name as venue_name',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.updated_at as updated_at',
                    DB::raw('GROUP_CONCAT(departments.accronym ORDER BY departments.accronym ASC SEPARATOR ", ") as department_acronyms')
                )
                ->groupBy(
                    'events.id',
                    'terms.name',
                    'events.name',
                    'events.levels',
                    'events.date',
                    'venues.name',
                    'venues.building',
                    'event_junctions.time_end',
                    'event_junctions.time_start',
                    'event_junctions.date_end',
                    'event_junctions.updated_at',
                )
                ->whereNotNull('approved_by_venue_coordinator_at')
                ->get();


            $venuesRequested = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as event_name',
                    'events.levels as levels',
                    'venues.name as venue_name',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.updated_at as updated_at',
                    DB::raw('GROUP_CONCAT(departments.accronym ORDER BY departments.accronym ASC SEPARATOR ", ") as department_acronyms')
                )
                ->groupBy(
                    'events.id',
                    'terms.name',
                    'events.name',
                    'events.levels',
                    'events.date',
                    'venues.name',
                    'venues.building',
                    'event_junctions.time_end',
                    'event_junctions.time_start',
                    'event_junctions.date_end',
                    'event_junctions.updated_at',
                )
                ->whereNull('approved_by_venue_coordinator_at')
                ->get();


            $venuesDeclined = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as venue_name',
                    'events.levels as levels',
                    'venues.name as venue_name',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.updated_at as updated_at',
                    DB::raw('GROUP_CONCAT(departments.accronym ORDER BY departments.accronym ASC SEPARATOR ", ") as department_acronyms')
                )
                ->groupBy(
                    'events.id',
                    'terms.name',
                    'events.name',
                    'events.levels',
                    'events.date',
                    'venues.name',
                    'venues.building',
                    'event_junctions.time_end',
                    'event_junctions.time_start',
                    'event_junctions.date_end',
                    'event_junctions.updated_at',
                )
                ->whereNull('approved_by_venue_coordinator_at')
                ->whereNull('approved_by_admin_at')
                ->whereNotNull('comment')
                ->get();
        }


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