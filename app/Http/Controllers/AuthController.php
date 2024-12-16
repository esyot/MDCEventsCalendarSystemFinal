<?php

namespace App\Http\Controllers;


use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Event;
use App\Models\UserRoles;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function index()
    {
        return Inertia::render('Login');
    }

    public function login(Request $request)
    {

        $fields = $request->validate([
            'user' => 'required|string',
            'password' => 'required|string'
        ]);

        if (Auth::attempt(['user' => $fields['user'], 'password' => $fields['password']])) {
            $user = Auth::user();

            $rolesAllowed = Role::whereIn('role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])->get()->pluck('id');
            $usersCanLogin = UserRoles::whereIn('role_id', $rolesAllowed)->pluck('user_id');

            if ($usersCanLogin->contains($user->id)) {
                return redirect()->route('dashboard');
            }
        }

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


        $events_today = Event::where('date', today());

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
            ->get();

        return Inertia::render('Guest/Dashboard/dashboard', [
            'errors' => ['user' => 'The provided credentials are incorrect.'],
            'user' => $fields['user'],
            'auth_error' => true,
            'events' => $events,
            'events_today' => $events_today,
            'event_updates' => $event_updates,
            'eventsWithDetails' => $eventsWithDetails
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();

        return redirect()->route('guest');
    }

    public function authCheck(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $events = Event::where('isApprovedByVenueCoordinator', true)->where('isApprovedByAdmin', true)->get();
        $events_today = Event::where('isApprovedByVenueCoordinator', true)->where('isApprovedByAdmin', true)->where('date_start', today())->get();

        return Inertia::render('Guest/Dashboard/dashboard', [
            'error' => 'Your account access has been removed.',
            'events' => $events,
            'events_today' => $events_today,
        ]);
    }
}
