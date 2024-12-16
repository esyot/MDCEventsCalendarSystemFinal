<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\Event;
use App\Models\Role;
use App\Models\Term;
use App\Models\UserDepartment;
use App\Models\VenueCoordinator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Venue;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $currentYear = now()->format('Y');

        $currDate = Carbon::now()->year;

        $thisTerms = Term::whereYear('start', '=', $currDate)
            ->get();

        $currentDepartment = ['id' => 'all', 'name' => 'All'];
        $currentVenue = ['id' => 'all', 'name' => 'All'];

        $user_role = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();


        $departments = Department::all();

        $departmentsWithParent = DB::table('departments as t1')
            ->leftJoin('departments as t2', 't1.parent_id', '=', 't2.id')
            ->leftJoin('departments as t3', 't2.parent_id', '=', 't3.id')
            ->leftJoin('departments as t4', 't3.parent_id', '=', 't4.id')
            ->select(
                't1.id as department_id',
                't1.accronym as acronym',
                't1.name as department_name',
                DB::raw('COALESCE(t4.accronym, t3.accronym, t2.accronym, t1.accronym) as parent')

            )
            ->whereNotNull('t1.parent_id')
            ->get();

        $departmentsWithNoParent = DB::table('departments as t1')
            ->leftJoin('departments as t2', 't1.id', '=', 't2.parent_id')
            ->select(
                't1.id as department_id',
                't1.accronym as acronym',
                't1.name as department_name',
                't1.accronym as parent',
            )
            ->whereNull('t1.parent_id')
            ->whereNull('t2.id')
            ->get();



        $departmentsForm = $departmentsWithNoParent->concat($departmentsWithParent);


        if ($user_role == 'event_coordinator') {
            $deparmentIds = UserDepartment::where('user_id', Auth::user()->id)->get()->pluck('department_id');

            $departments = Department::whereIn('id', $deparmentIds)->get();

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
                ->whereYear('events.date', $currentYear)
                ->whereYear('event_junctions.date_end', $currentYear)
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
                    'venues.id as venue_id',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.approved_by_admin_at as approved_by_admin_at',
                    'event_junctions.approved_by_venue_coordinator_at as approved_by_venue_coordinator_at',
                    'event_junctions.updated_at as updated_at',
                    DB::raw('GROUP_CONCAT(departments.id ORDER BY departments.id ASC SEPARATOR ", ") as department_id'),
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
                    'event_junctions.approved_by_admin_at',
                    'event_junctions.approved_by_venue_coordinator_at',
                    'event_junctions.updated_at',
                )
                ->get();
        }

        if ($user_role == 'admin') {



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
                    'venues.id as venue_id',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.approved_by_admin_at as approved_by_admin_at',
                    'event_junctions.approved_by_venue_coordinator_at as approved_by_venue_coordinator_at',
                    'event_junctions.updated_at as updated_at',
                    DB::raw('GROUP_CONCAT(departments.id ORDER BY departments.id ASC SEPARATOR ", ") as department_id'),
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
                    'event_junctions.approved_by_admin_at',
                    'event_junctions.approved_by_venue_coordinator_at',
                    'event_junctions.updated_at',
                )
                ->get();


        }

        $venues = Venue::all();

        if ($user_role == 'venue_coordinator') {

            $departments = Department::all();

            $venueIds = VenueCoordinator::where('user_id', Auth::user()->id)->get()->pluck('venue_id');

            $venues = Venue::whereIn('id', $venueIds)->get();

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
                ->whereIn('venue_id', $venueIds)
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
                    'venues.id as venue_id',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.approved_by_admin_at as approved_by_admin_at',
                    'event_junctions.approved_by_venue_coordinator_at as approved_by_venue_coordinator_at',
                    'event_junctions.updated_at as updated_at',
                    DB::raw('GROUP_CONCAT(departments.id ORDER BY departments.id ASC SEPARATOR ", ") as department_id'),
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
                    'event_junctions.approved_by_admin_at',
                    'event_junctions.approved_by_venue_coordinator_at',
                    'event_junctions.updated_at',
                )
                ->whereIn('venue_id', $venueIds)
                ->whereYear('events.date', $currentYear)
                ->whereYear('event_junctions.date_end', $currentYear)
                ->get();
        }


        return Inertia::render('Calendar/calendar', [
            'departments' => $departments,
            'departmentsForm' => $departmentsForm,
            'venues' => $venues,
            'pageTitle' => 'Calendar',
            'user' => $user,
            'events' => $events->toArray(),
            'eventsWithDetails' => $eventsWithDetails,
            'terms' => $thisTerms,
            'user_role' => $user_role,
            'user_role_calendar' => $user_role,
            'currentDepartment' => $currentDepartment,
            'successMessage' => session('success') ?? null,
            'errorMessage' => session('error') ?? null,
            'currentVenue' => $currentVenue
        ]);
    }
    public function filter(Request $request)
    {
        $user = Auth::user();
        $currentYear = $request->currentYear;
        $searchValue = $request->search_value;
        $departmentId = $request->department;
        $venueId = $request->venue;

        // Fetch basic data
        $venues = Venue::all();
        $terms = Term::all();
        $departments = Department::all();

        // Get the user's role
        $userRole = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();

        // Default search results based on event name and year
        $searchResults = Event::join('terms', 'events.term_id', '=', 'terms.id')
            ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')
            ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')
            ->join('event_departments', 'events.id', '=', 'event_departments.event_id')
            ->join('departments', 'event_departments.department_id', '=', 'departments.id')
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
                'event_junctions.approved_by_admin_at as approved_by_admin_at',
                'event_junctions.approved_by_venue_coordinator_at as approved_by_venue_coordinator_at',
                'event_junctions.updated_at as updated_at',
                DB::raw('GROUP_CONCAT(departments.id ORDER BY departments.id ASC SEPARATOR ", ") as department_id'),
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
                'event_junctions.approved_by_admin_at',
                'event_junctions.approved_by_venue_coordinator_at',
                'event_junctions.updated_at'
            )
            ->whereNotNull('event_junctions.approved_by_admin_at')
            ->where('events.name', 'LIKE', '%' . $searchValue . '%')
            ->whereYear('events.date', $currentYear)
            ->get();

        // Base event query
        $eventQuery = Event::join('terms', 'events.term_id', '=', 'terms.id')
            ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')
            ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')
            ->join('event_departments', 'events.id', '=', 'event_departments.event_id')
            ->join('departments', 'event_departments.department_id', '=', 'departments.id')
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
                'event_junctions.approved_by_admin_at as approved_by_admin_at',
                'event_junctions.approved_by_venue_coordinator_at as approved_by_venue_coordinator_at',
                'event_junctions.updated_at as updated_at',
                DB::raw('GROUP_CONCAT(departments.id ORDER BY departments.id ASC SEPARATOR ", ") as department_id'),
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
                'event_junctions.approved_by_admin_at',
                'event_junctions.approved_by_venue_coordinator_at',
                'event_junctions.updated_at'
            )
            ->whereNotNull('event_junctions.approved_by_admin_at')
            ->where('events.name', 'LIKE', '%' . $searchValue . '%')
            ->whereYear('events.date', $currentYear);

        // Filter by department if provided
        if ($departmentId) {
            $eventQuery->where('departments.id', $departmentId);
        }

        // Additional filter for user roles (event or venue coordinator)
        if (in_array($userRole, ['event_coordinator', 'venue_coordinator'])) {
            $departmentIds = UserDepartment::where('user_id', Auth::user()->id)->pluck('department_id');
            $eventQuery->whereIn('departments.id', $departmentIds);
        }

        $events = $eventQuery->get();

        // Determine current department and venue
        $currentDepartment = $departmentId != 'all' ? Department::find($departmentId) : ['id' => 'all', 'name' => 'All'];
        $currentVenue = $venueId != 'all' ? Venue::find($venueId) : ['id' => 'all', 'name' => 'All'];

        // Fetch event details with department and venue info for the calendar view
        $eventsWithDetails = Event::join('terms', 'events.term_id', '=', 'terms.id')
            ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')
            ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')
            ->join('event_departments', 'events.id', '=', 'event_departments.event_id')
            ->join('departments', 'event_departments.department_id', '=', 'departments.id')
            ->select(
                'events.id as id',
                'terms.name as term_name',
                'events.name as name',
                'events.levels as levels',
                'venues.name as venue_name',
                'venues.id as venue_id',
                'venues.building as venue_building',
                'events.date as date_start',
                'event_junctions.time_start as time_start',
                'event_junctions.date_end as date_end',
                'event_junctions.time_end as time_end',
                'event_junctions.approved_by_admin_at as approved_by_admin_at',
                'event_junctions.approved_by_venue_coordinator_at as approved_by_venue_coordinator_at',
                'event_junctions.updated_at as updated_at',
                DB::raw('GROUP_CONCAT(departments.id ORDER BY departments.id ASC SEPARATOR ", ") as department_id'),
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
                'event_junctions.approved_by_admin_at',
                'event_junctions.approved_by_venue_coordinator_at',
                'event_junctions.updated_at'
            )
            ->whereYear('events.date', $currentYear)
            ->whereYear('event_junctions.date_end', $currentYear)
            ->get();

        if ($request->department && $request->department != 'all') {
            $eventsWithDetails->where('department_id', 'LIKE', '%' . $request->department . '%');
        }

        return Inertia::render('Calendar/calendar', [
            'departments' => $departments,
            'venues' => $venues,
            'pageTitle' => 'Calendar',
            'user' => $user,
            'events' => $events,
            'eventsWithDetails' => $eventsWithDetails,
            'terms' => $terms,
            'user_role' => $userRole,
            'currentYear' => $currentYear,
            'user_role_calendar' => $userRole,
            'currentDepartment' => $currentDepartment,
            'successMessage' => session('success'),
            'search_value' => $searchValue,
            'searchResults' => $searchResults,
            'currentVenue' => $currentVenue,
        ]);
    }



    public function selectDate($date)
    {

        $selectedDate = Carbon::parse($date)->format('Y-m-d');


        return redirect()->route('calendar')->with('selectedDate', $selectedDate);
    }
}
