<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\Event;
use App\Models\Term;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Venue;
use Inertia\Inertia;
use Illuminate\Http\Request;

class GuestCalendarController extends Controller
{
    public function index(Request $request)
    {
        $venues = Venue::all();

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
                'event_junctions.updated_at',
            )
            ->whereNotNull('event_junctions.approved_by_admin_at')
            ->get();

        $terms = Term::all();

        $departments = Department::whereNotIn('accronym', ['SHS', 'College', 'ELEM', 'GS'])->get();

        $currentDepartment = ['id' => 'all', 'name' => 'All'];

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
        $currentYear = $request->currentYear;
        $searchValue = $request->search_value;
        $departmentId = $request->department;
        $venueId = $request->venue;

        $venues = Venue::all();
        $terms = Term::all();
        $departments = Department::whereNotIn('accronym', ['SHS', 'College', 'ELEM', 'GS'])->get();



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
            ->whereYear('events.date', $currentYear);

        $selectedDepartment = Department::find($request->department);

        if ($departmentId && $departmentId != 'all') {

            $eventQuery->havingRaw('department_acronyms LIKE ?', ['%' . $selectedDepartment->accronym . '%']);
        }

        $events = $eventQuery->get();


        $currentDepartment = $departmentId != 'all' ? Department::find($departmentId) : ['id' => 'all', 'name' => 'All'];
        $currentVenue = $venueId != 'all' ? Venue::find($venueId) : ['id' => 'all', 'name' => 'All'];

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
