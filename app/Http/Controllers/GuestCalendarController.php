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
            ->join('departments', 'events.department_id', '=', 'departments.id')
            ->join('terms', 'events.term_id', '=', 'terms.id')
            ->select('events.*',
                'venues.name as venue_name',
                'venues.building as venue_building',
                'departments.name as department_name',
                'terms.name as term_name',
            )
            ->whereNot('isApprovedByAdmin', null)
            ->whereYear('date_start', $currentYear)->orderBy('date_start', 'ASC')->get();

        $terms = Term::all();

        $departments = Department::all();

        $currentDepartment = Department::first();

        $events = Event::whereYear('date_start', $currentYear)
            ->where('department_id', $currentDepartment->id)
            ->whereNot('isApprovedByAdmin', null)
            ->pluck('date_start');

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
        $events = Event::whereYear('date_start', $request->currentYear)
            ->where('department_id', $request->department)
            ->whereNot('isApprovedByAdmin', null)
            ->pluck('date_start');


        $currentDepartment = Department::where('id', $request->department)->first();


        $user = Auth::user();

        $venues = Venue::all();

        $currentYear = now()->format('Y');

        $eventsWithDetails = DB::table('events')
            ->join('venues', 'events.venue_id', '=', 'venues.id')
            ->join('departments', 'events.department_id', '=', 'departments.id')
            ->join('terms', 'events.term_id', '=', 'terms.id')
            ->select('events.*',
                'venues.name as venue_name',
                'venues.building as venue_building',
                'departments.name as department_name',
                'terms.name as term_name',
            )
            ->whereNot('isApprovedByAdmin', null)
            ->whereYear('date_start', $currentYear)
            ->orderBy('date_start', 'ASC')->get();

        $terms = Term::all();

        $departments = Department::all();



        $searchResults = DB::table('events')
            ->join('venues', 'events.venue_id', '=', 'venues.id')
            ->join('departments', 'events.department_id', '=', 'departments.id')
            ->join('terms', 'events.term_id', '=', 'terms.id')
            ->select('events.*',
                'events.id as event_id',
                'venues.name as venue_name',
                'venues.building as venue_building',
                'departments.name as department_name',
                'terms.name as term_name',
            )
            ->whereNot('isApprovedByAdmin', null)
            ->where('events.name', 'LIKE', '%' . $request->search_value . '%')
            ->whereYear('date_start', $request->currentYear)
            ->get();


        return Inertia::render('Guest/Calendar/calendar', [
            'departments' => $departments,
            'venues' => $venues,
            'pageTitle' => 'Event Request',
            'user' => $user,
            'events' => $events->toArray(),
            'eventsWithDetails' => $eventsWithDetails,
            'terms' => $terms,
            'currentDepartment' => $currentDepartment,
            'successMessage' => session('success'),
            'search_value' => $request->search_value,
            'searchResults' => $searchResults
        ]);
    }


    public function selectDate($date)
    {

        $selectedDate = Carbon::parse($date)->format('Y-m-d');


        return redirect()->route('calendar')->with('selectedDate', $selectedDate);
    }
}
