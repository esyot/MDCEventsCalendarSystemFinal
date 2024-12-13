<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\UserPermission;
use App\Models\Event;
use App\Models\UserRoles;
use App\Models\VenueCoordinator;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $event_updates = Event::whereNot('isApprovedByAdmin', null)
                ->orderBy('isApprovedByAdmin', 'DESC')
                ->where('user_id', Auth::user()->id)
                ->whereNot('isApprovedByAdmin', null)
                ->get();

            $events = Event::
                whereNot('isApprovedByAdmin', null)
                ->whereYear('date_start', $currentYear)
                ->whereNot('isApprovedByAdmin', null)
                ->whereNot('isApprovedByVenueCoordinator', null)
                ->get(['date_start', 'date_end']);

            $eventsWithDetails = DB::table('events')
                ->join('venues', 'events.venue_id', '=', 'venues.id')
                ->join('departments', function ($join) {
                    $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
                })
                ->join('terms', 'events.term_id', '=', 'terms.id')
                ->select('events.*',
                    'venues.name as venue_name',
                    'events.id as event_id',
                    'venues.building as venue_building',
                    DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                    'terms.name as term_name',
                )
                ->whereYear('date_start', $currentYear)
                ->whereNot('isApprovedByAdmin', null)
                ->whereNot('isApprovedByVenueCoordinator', null)
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('date_start', 'ASC')
                ->get();

            $events_today = DB::table('events')
                ->join('venues', 'events.venue_id', '=', 'venues.id')
                ->join('departments', function ($join) {
                    $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
                })
                ->join('terms', 'events.term_id', '=', 'terms.id')
                ->select('events.*',
                    'venues.name as venue_name',
                    'events.id as event_id',
                    'venues.building as venue_building',
                    DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                    'terms.name as term_name',
                )
                ->whereYear('date_start', $currentYear)
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('isApprovedByVenueCoordinator', 'DESC')
                ->whereNotNull('isApprovedByVenueCoordinator')
                ->whereNotNull('isApprovedByAdmin')
                ->where('date_start', today())
                ->get();

        } else if ($user_role == 'venue_coordinator') {

            $venueIds = VenueCoordinator::where('user_id', Auth::user()->id)->get()->pluck('venue_id');

            $events = Event::
                whereNot('isApprovedByAdmin', null)
                ->whereYear('date_start', $currentYear)
                ->whereNot('isApprovedByAdmin', null)
                ->whereNot('isApprovedByVenueCoordinator', null)
                ->get(['date_start', 'date_end']);


            $event_updates = Event::where('isApprovedByVenueCoordinator', null)
                ->whereIn('venue_id', $venueIds)
                ->join('venues', 'events.venue_id', '=', 'venues.id')
                ->select('events.*', 'venues.name as venue_name', 'venues.building as venue_building')
                ->orderBy('isApprovedByAdmin', 'DESC')
                ->where('isApprovedByAdmin', null)
                ->where('isApprovedByVenueCoordinator', null)
                ->get();

            $events_today = DB::table('events')
                ->join('venues', 'events.venue_id', '=', 'venues.id')
                ->join('departments', function ($join) {
                    $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
                })
                ->join('terms', 'events.term_id', '=', 'terms.id')
                ->select('events.*',
                    'venues.name as venue_name',
                    'events.id as event_id',
                    'venues.building as venue_building',
                    DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                    'terms.name as term_name',
                )
                ->whereYear('date_start', $currentYear)
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('isApprovedByVenueCoordinator', 'DESC')
                ->whereNotNull('isApprovedByVenueCoordinator')
                ->whereNotNull('isApprovedByAdmin')
                ->where('date_start', today())
                ->get();

            $eventsWithDetails = DB::table('events')
                ->join('venues', 'events.venue_id', '=', 'venues.id')
                ->join('departments', function ($join) {
                    $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
                })
                ->join('terms', 'events.term_id', '=', 'terms.id')
                ->select('events.*',
                    'venues.name as venue_name',
                    'events.id as event_id',
                    'venues.building as venue_building',
                    DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                    'terms.name as term_name',
                )
                ->whereYear('date_start', $currentYear)
                ->whereNot('isApprovedByAdmin', null)
                ->whereNot('isApprovedByVenueCoordinator', null)
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('date_start', 'ASC')
                ->get();




        } else {

            $events_today = DB::table('events')
                ->join('venues', 'events.venue_id', '=', 'venues.id')
                ->join('departments', function ($join) {
                    $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
                })
                ->join('terms', 'events.term_id', '=', 'terms.id')
                ->select('events.*',
                    'venues.name as venue_name',
                    'events.id as event_id',
                    'venues.building as venue_building',
                    DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                    'terms.name as term_name',
                )
                ->whereYear('date_start', $currentYear)
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('isApprovedByVenueCoordinator', 'DESC')
                ->whereNotNull('isApprovedByVenueCoordinator')
                ->whereNotNull('isApprovedByAdmin')
                ->where('date_start', today())
                ->get();

            $event_updates = DB::table('events')
                ->join('venues', 'events.venue_id', '=', 'venues.id')
                ->join('departments', function ($join) {
                    $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
                })
                ->join('terms', 'events.term_id', '=', 'terms.id')
                ->select('events.*',
                    'venues.name as venue_name',
                    'events.id as event_id',
                    'venues.building as venue_building',
                    DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                    'terms.name as term_name',
                )
                ->whereYear('date_start', $currentYear)
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('isApprovedByVenueCoordinator', 'DESC')
                ->whereNotNull('isApprovedByVenueCoordinator')
                ->whereNull('isApprovedByAdmin')
                ->get();

            $events = Event::
                whereNot('isApprovedByAdmin', null)
                ->whereYear('date_start', $currentYear)
                ->whereNot('isApprovedByAdmin', null)
                ->whereNot('isApprovedByVenueCoordinator', null)
                ->get(['date_start', 'date_end']);

            $eventsWithDetails = DB::table('events')
                ->join('venues', 'events.venue_id', '=', 'venues.id')
                ->join('departments', function ($join) {
                    $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
                })
                ->join('terms', 'events.term_id', '=', 'terms.id')
                ->select('events.*',
                    'venues.name as venue_name',
                    'events.id as event_id',
                    'venues.building as venue_building',
                    DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                    'terms.name as term_name',
                )
                ->whereYear('date_start', $currentYear)
                ->whereNot('isApprovedByAdmin', null)
                ->whereNot('isApprovedByVenueCoordinator', null)
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('date_start', 'ASC')
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
        $events_today = DB::table('events')
            ->join('venues', 'events.venue_id', '=', 'venues.id')
            ->join('departments', function ($join) {
                $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
            })
            ->join('terms', 'events.term_id', '=', 'terms.id')
            ->select('events.*',
                'venues.name as venue_name',
                'venues.building as venue_building',
                DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                'terms.name as term_name',
            )
            ->whereNotNull('isApprovedByAdmin')
            ->where('date_start', today())
            ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
            ->orderBy('date_start', 'ASC')
            ->get();

        $event_updates = DB::table('events')
            ->join('venues', 'events.venue_id', '=', 'venues.id')
            ->join('departments', function ($join) {
                $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
            })
            ->join('terms', 'events.term_id', '=', 'terms.id')
            ->select('events.*',
                'venues.name as venue_name',
                'venues.building as venue_building',
                DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                'terms.name as term_name',
            )

            ->whereNotNull('isApprovedByAdmin')
            ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
            ->orderBy('date_start', 'ASC')
            ->get();

        $currentYear = now()->format('Y');

        $events = Event::
            whereNot('isApprovedByAdmin', null)
            ->whereYear('date_start', $currentYear)
            ->get(['date_start', 'date_end']);



        $eventsWithDetails = DB::table('events')
            ->join('venues', 'events.venue_id', '=', 'venues.id')
            ->join('departments', function ($join) {
                $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
            })
            ->join('terms', 'events.term_id', '=', 'terms.id')
            ->select('events.*',
                'venues.name as venue_name',
                'venues.building as venue_building',
                DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                'terms.name as term_name',
            )
            ->whereYear('date_start', $currentYear)
            ->whereNotNull('isApprovedByAdmin')
            ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
            ->orderBy('date_start', 'ASC')
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
