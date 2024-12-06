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

class CalendarController extends Controller
{
    public function index(Request $request)
    {


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

            ->whereYear('date_start', $currentYear)->orderBy('date_start', 'ASC')->get();

        $terms = Term::all();

        $departments = Department::all();

        $currentDepartment = ['id' => 'all', 'name' => 'All'];
        $user_role = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();

        $events = Event::whereYear('date_start', $currentYear)
            ->whereYear('date_end', $currentYear)
            ->get(['date_start', 'date_end']);


        if ($user_role == 'event_coordinator') {
            $eventsMadeByUser = Event::whereYear('date_start', $currentYear)
                ->where('user_id', Auth::user()->id)
                ->get(['date_start', 'date_end']);

            $allEventsApproved = Event::whereYear('date_start', $currentYear)
                ->whereNotNull('isApprovedByAdmin')
                ->get(['date_start', 'date_end']);


            $events = $eventsMadeByUser->merge($allEventsApproved);
        }


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
            'successMessage' => session('success') ?? null,
            'errorMessage' => session('error') ?? null,
        ]);
    }
    public function filter(Request $request)
    {


        $events = Event::whereYear('date_start', $request->currentYear)
            ->where('department_id', $request->department)
            ->pluck('date_start');


        $currentDepartment = Department::where('id', $request->department)->first();
        if ($request->department == 'all') {
            $events = Event::whereYear('date_start', $request->currentYear)
                ->pluck('date_start');


            $currentDepartment = ['id' => 'all', 'name' => 'All'];
        }

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
            ->whereYear('date_start', $currentYear)->orderBy('date_start', 'ASC')->get();

        $terms = Term::all();

        $departments = Department::all();

        $user_role = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();


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
            ->where('events.name', 'LIKE', '%' . $request->search_value . '%')
            ->whereYear('date_start', $request->currentYear)
            ->get();


        return Inertia::render('Calendar/calendar', [
            'departments' => $departments,
            'venues' => $venues,
            'pageTitle' => 'Event Request',
            'user' => $user,
            'events' => $events->toArray(),
            'eventsWithDetails' => $eventsWithDetails,
            'terms' => $terms,
            'user_role' => $user_role,
            'user_role_calendar' => $user_role,
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
