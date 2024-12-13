<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\Event;
use App\Models\Role;
use App\Models\Term;
use App\Models\UserRoles;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use App\Models\UserPermission;
use App\Models\Venue;
use Inertia\Inertia;
use Illuminate\Http\Request;

class GuestCalendarController extends Controller
{
    public function index(Request $request)
    {

        $venues = Venue::all();

        $currentYear = now()->format('Y');

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

        $terms = Term::all();

        $departments = Department::all();

        $currentDepartment = ['id' => 'all', 'name' => 'All'];

        $events = Event::whereYear('date_start', $currentYear)
            ->whereNotNull('isApprovedByAdmin')
            ->get(['date_start', 'date_end']);


        return Inertia::render('Guest/Calendar/calendar', [
            'departments' => $departments,
            'venues' => $venues,
            'pageTitle' => 'Caledar',
            'events' => $events->toArray(),
            'eventsWithDetails' => $eventsWithDetails,
            'terms' => $terms,
            'currentDepartment' => $currentDepartment,
            'successMessage' => session('success'),
            'errorMessage' => session('error'),
            'selectedDate' => session('selectedDate'),
        ]);
    }
    public function filter(Request $request)
    {
        $user = Auth::user();

        $venues = Venue::all();
        $currentYear = now()->format('Y');
        $terms = Term::all();
        $departments = Department::all();

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
            $events->where('events.department_id', 'LIKE', '%' . $request->department . '%');
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
            ->orderBy('date_start', 'ASC');


        // Reapply department and venue filters
        if ($request->department != 'all') {
            $eventsWithDetails->where('events.department_id', 'LIKE', '%' . $request->department . '%');
        }



        // Fetch the detailed event results for calendar display
        $eventsWithDetails = $eventsWithDetails->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
            ->orderBy('date_start', 'ASC')
            ->get();


        // Return the filtered results to the Inertia view
        return Inertia::render('Guest/Calendar/calendar', [
            'departments' => $departments,
            'venues' => $venues,
            'pageTitle' => 'Calendar',
            'user' => $user,
            'events' => $events->toArray(),
            'eventsWithDetails' => $eventsWithDetails,
            'terms' => $terms,
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
