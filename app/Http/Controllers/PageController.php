<?php

namespace App\Http\Controllers;


use App\Models\Role;
use App\Models\Event;
use App\Models\VenueCoordinator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PageController extends Controller
{
    public function dashboard()
    {
        $currentYear = now()->format('Y');

        $user_role = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();

        if ($user_role == 'event_coordinator') {
            $events_today = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as name',
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
                ->whereNotNull('event_junctions.approved_by_admin_at')

                ->orderBy('events.created_at', 'DESC')
                ->where('events.date', today())
                ->get();

            $event_updates = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as name',
                    'events.levels as levels',
                    'venues.name as venue_name',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.comment as comment',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.updated_at as updated_at',
                    'event_junctions.approved_by_admin_at as approved_by_admin_at',
                    'event_junctions.approved_by_venue_coordinator_at as approved_by_venue_coordinator_at',
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
                    'event_junctions.comment',
                    'event_junctions.time_end',
                    'event_junctions.time_start',
                    'event_junctions.date_end',
                    'event_junctions.updated_at',
                    'event_junctions.approved_by_admin_at',
                    'event_junctions.approved_by_venue_coordinator_at',
                )
                ->where('events.user_id', Auth::user()->id)
                ->get();


            $events = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.date as date_start',
                    'event_junctions.date_end as date_end'
                )
                ->groupBy(
                    'events.date',
                    'event_junctions.date_end'
                )
                ->whereNotNull('event_junctions.approved_by_admin_at')
                ->get();



            $events = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.date as date_start',
                    'event_junctions.date_end as date_end'
                )
                ->groupBy(
                    'events.date',
                    'event_junctions.date_end'
                )
                ->whereNotNull('event_junctions.approved_by_admin_at')
                ->get();

            $eventsWithDetails = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as name',
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
                ->get();

        } else if ($user_role == 'venue_coordinator') {

            $venueIds = VenueCoordinator::where('user_id', Auth::user()->id)->get()->pluck('venue_id');

            $events_today = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as name',
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
                ->whereNotNull('event_junctions.approved_by_admin_at')
                ->orderBy('events.created_at', 'DESC')
                ->where('events.date', today())
                ->get();

            $event_updates = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as name',
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
                ->whereIn('venues.id', $venueIds)
                ->whereNull('approved_by_venue_coordinator_at')
                ->orderBy('events.created_at', 'DESC')
                ->get();


            $events = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.date as date_start',
                    'event_junctions.date_end as date_end'
                )
                ->groupBy(
                    'events.date',
                    'event_junctions.date_end'
                )
                ->whereNotNull('event_junctions.approved_by_admin_at')
                ->whereYear('events.date', $currentYear)
                ->whereYear('event_junctions.date_end', $currentYear)
                ->get();

            $eventsWithDetails = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as name',
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
                ->get();


        } else {

            $events_today = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as name',
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
                ->whereNotNull('event_junctions.approved_by_admin_at')
                ->orderBy('events.created_at', 'DESC')
                ->where('events.date', today())
                ->get();

            $event_updates = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as name',
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
                ->whereNull('event_junctions.approved_by_admin_at')
                ->whereNotNull('event_junctions.approved_by_venue_coordinator_at')
                ->orderBy('events.created_at', 'DESC')

                ->get();

            $events = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.date as date_start',
                    'event_junctions.date_end as date_end'
                )
                ->groupBy(
                    'events.date',
                    'event_junctions.date_end'
                )
                ->whereNotNull('event_junctions.approved_by_admin_at')
                ->whereYear('events.date', $currentYear)
                ->whereYear('event_junctions.date_end', $currentYear)
                ->get();

            $eventsWithDetails = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as name',
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
                ->get();
        }

        return Inertia::render('Dashboard/dashboard', [
            'pageTitle' => 'Dashboard',
            'user' => Auth::user(),
            'event_updates' => $event_updates,
            'events_today' => $events_today,
            'user_role' => $user_role,
            'events' => $events->toArray(),
            'eventsWithDetails' => $eventsWithDetails->toArray(),
        ]);
    }

    public function guest(Request $request)
    {
        $events_today = Event::
            join('terms', 'events.term_id', '=', 'terms.id')
            ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
            ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
            ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
            ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
            ->select(
                'events.id as id',
                'terms.name as term_name',
                'events.name as name',
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
            ->whereNotNull('event_junctions.approved_by_admin_at')
            ->orderBy('event_junctions.time_start', 'ASC')
            ->where('events.date', today())
            ->get();


        $event_updates = Event::
            join('terms', 'events.term_id', '=', 'terms.id')
            ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
            ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
            ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
            ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
            ->select(
                'events.id as id',
                'terms.name as term_name',
                'events.name as name',
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
            ->whereNotNull('event_junctions.approved_by_admin_at')
            ->orderBy('events.date', 'ASC')
            ->get();


        $events = Event::
            join('terms', 'events.term_id', '=', 'terms.id')
            ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
            ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
            ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
            ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
            ->select(
                'events.date as date_start',
                'event_junctions.date_end as date_end'
            )
            ->groupBy(
                'events.date',
                'event_junctions.date_end'
            )
            ->whereNotNull('approved_by_admin_at')
            ->get();

        $eventsWithDetails = Event::
            join('terms', 'events.term_id', '=', 'terms.id')
            ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
            ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
            ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
            ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
            ->select(
                'events.id as id',
                'terms.name as term_name',
                'events.name as name',
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
            ->whereNotNull('event_junctions.approved_by_admin_at')
            ->get();



        return Inertia::render('Guest/Dashboard/dashboard', [
            'pageTitle' => 'Dashboard',
            'user' => Auth::user(),
            'event_updates' => $event_updates,
            'events_today' => $events_today,
            'events' => $events->toArray(),
            'eventsWithDetails' => $eventsWithDetails->toArray(),
        ]);

    }


}
