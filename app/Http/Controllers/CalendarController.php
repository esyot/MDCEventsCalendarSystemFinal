<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\Event;
use App\Models\Role;
use App\Models\Term;
use App\Models\UserRoles;
use App\Models\UsersDepartment;
use App\Models\VenueCoordinator;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use App\Models\UserPermission;
use App\Models\Venue;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();


        $currentYear = now()->format('Y');


        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

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


        if ($user_role == 'event_coordinator') {
            $eventsMadeByUser = Event::whereYear('date_start', $currentYear)
                ->whereYear('date_end', $currentYear)
                ->where('user_id', Auth::user()->id)
                ->get(['date_start', 'date_end']);

            $eventsApproved = Event::whereYear('date_start', $currentYear)
                ->whereYear('date_end', $currentYear)
                ->whereNotNull('isApprovedByAdmin')
                ->get(['date_start', 'date_end']);

            $events = $eventsApproved->concat($eventsMadeByUser);



            $eventsWithDetailsMadeByUser = DB::table('events')
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
                ->whereNull('isApprovedByAdmin')
                ->whereNull('isApprovedByVenueCoordinator')
                ->where('user_id', Auth::user()->id)
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('date_start', 'ASC')
                ->get();


            $eventsWithDetailsApproved = DB::table('events')
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
                ->whereNotNull('isApprovedByAdmin')
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('date_start', 'ASC')
                ->get();

            $eventsWithDetails = $eventsWithDetailsApproved->concat($eventsWithDetailsMadeByUser);

        }


        $departmentIds = UsersDepartment::where('user_id', Auth::user()->id)->get()->pluck('department_id');

        $departments = Department::whereIn('id', $departmentIds)->get();

        if ($user_role == 'admin') {

            $departments = Department::all();

            $eventsMadeByUser = Event::whereYear('date_start', $currentYear)
                ->whereYear('date_end', $currentYear)
                ->where('user_id', Auth::user()->id)
                ->get(['date_start', 'date_end']);

            $eventsApproved = Event::whereYear('date_start', $currentYear)
                ->whereYear('date_end', $currentYear)

                ->get(['date_start', 'date_end']);

            $events = $eventsApproved->concat($eventsMadeByUser);





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
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('date_start', 'ASC')
                ->get();



        }

        $venues = Venue::all();

        if ($user_role == 'venue_coordinator') {

            $departments = Department::all();

            $venueIds = VenueCoordinator::where('user_id', Auth::user()->id)->get()->pluck('venue_id');

            $venues = Venue::whereIn('id', $venueIds)->get();

            $events = Event::whereYear('date_start', $currentYear)
                ->whereYear('date_end', $currentYear)
                ->whereIn('venue_id', $venueIds)
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
                ->whereIn('venue_id', $venueIds)
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('date_start', 'ASC')
                ->get();

        }


        return Inertia::render('Calendar/calendar', [
            'departments' => $departments,
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

        $venues = Venue::all();
        $currentYear = now()->format('Y');
        $terms = Term::all();
        $departments = Department::all();

        $user_role = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();

        // Default search results based on event name and year
        $searchResults = DB::table('events')
            ->join('venues', 'events.venue_id', '=', 'venues.id')
            ->join('departments', function ($join) {
                $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
            })
            ->join('terms', 'events.term_id', '=', 'terms.id')
            ->select('events.*',
                'venues.name as venue_name',
                'venues.building as venue_building',
                DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                'terms.name as term_name')
            ->where('events.name', 'LIKE', '%' . $request->search_value . '%')
            ->whereYear('date_start', $currentYear)
            ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
            ->orderBy('date_start', 'ASC')
            ->get();

        // Default event query for calendar
        $events = Event::whereYear('date_start', $currentYear);

        // Handle department and venue filters
        if ($request->department != 'all') {
            $events->where('events.department_id', 'LIKE', '%' . ',' . $request->department . ',' . '%');
        }

        if ($request->venue != 'all') {
            $events->where('events.venue_id', $request->venue);
        }

        // Apply search filter if provided
        if ($request->search_value) {
            $events->where('events.name', 'LIKE', '%' . $request->search_value . '%');
        }

        // Fetch the filtered events for calendar
        $events = $events->get(['date_start', 'date_end']);

        // Determine current department and venue
        $currentDepartment = $request->department != 'all' ? Department::find($request->department) : ['id' => 'all', 'name' => 'All'];
        $currentVenue = $request->venue != 'all' ? Venue::find($request->venue) : ['id' => 'all', 'name' => 'All'];

        // Fetch event details with department and venue info for the calendar view
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
                'terms.name as term_name')
            ->whereYear('date_start', $currentYear);

        // Reapply department and venue filters
        if ($request->department != 'all') {
            $eventsWithDetails->where('events.department_id', 'LIKE', '%' . ',' . '$request->department' . ',' . '%');
        }

        if ($request->venue != 'all') {
            $eventsWithDetails->where('events.venue_id', $request->venue);
        }

        // Apply search filter for event name
        if ($request->search_value) {
            $eventsWithDetails->where('events.name', 'LIKE', '%' . $request->search_value . '%');
        }

        // Fetch the detailed event results for calendar display
        $eventsWithDetails = $eventsWithDetails->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
            ->orderBy('date_start', 'ASC')
            ->get();

        // For venue coordinators, restrict the venues they have access to
        if ($user_role == 'venue_coordinator') {
            $venueIds = VenueCoordinator::where('user_id', Auth::user()->id)->pluck('venue_id');
            $events->whereIn('venue_id', $venueIds);
            $eventsWithDetails->whereIn('events.venue_id', $venueIds);
            $venues = Venue::whereIn('id', $venueIds)->get();
        }

        // Return the filtered results to the Inertia view
        return Inertia::render('Calendar/calendar', [
            'departments' => $departments,
            'venues' => $venues,
            'pageTitle' => 'Calendar',
            'user' => $user,
            'events' => $events->toArray(),
            'eventsWithDetails' => $eventsWithDetails,
            'terms' => $terms,
            'user_role' => $user_role,
            'user_role_calendar' => $user_role,
            'currentDepartment' => $currentDepartment,
            'successMessage' => session('success'),
            'search_value' => $request->search_value,
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
