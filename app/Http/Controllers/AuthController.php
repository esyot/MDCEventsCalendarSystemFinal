<?php

namespace App\Http\Controllers;


use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Event;
use App\Models\UserRole;
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
        //global variables
        $events_today = Event::join('terms', 'events.term_id', '=', 'terms.id')
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
                'event_junctions.updated_at'
            )
            ->whereNotNull('event_junctions.approved_by_admin_at')
            ->where('events.date', today())
            ->get();


        $events = Event::join('terms', 'events.term_id', '=', 'terms.id')
            ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')
            ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')
            ->join('event_departments', 'events.id', '=', 'event_departments.event_id')
            ->join('departments', 'event_departments.department_id', '=', 'departments.id')
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

        $event_updates = Event::join('terms', 'events.term_id', '=', 'terms.id')
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
                'event_junctions.updated_at'
            )
            ->whereNotNull('event_junctions.approved_by_admin_at')
            ->get();

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
                'event_junctions.updated_at'
            )
            ->whereNotNull('event_junctions.approved_by_admin_at')
            ->get();

        $fields = $request->validate([
            'user' => 'required|string',
            'password' => 'required|string'
        ]);

        $rolesAllowed = Role::whereIn('role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])->pluck('id');
        $usersCanLogin = UserRole::whereIn('role_id', $rolesAllowed)->pluck('user_id');

        if (Auth::attempt(['user' => $fields['user'], 'password' => $fields['password']])) {

            $user = Auth::user();

            if ($usersCanLogin->contains($user->id)) {

                return Inertia::location(route('dashboard'));
            } else {

                Auth::logout();

                return Inertia::render('Guest/Dashboard/dashboard', [
                    'errors' => ['user' => 'You do not have permission to access this application.'],
                    'user' => $fields['user'],
                    'auth_error' => true,
                    'rolesAllowed' => $rolesAllowed,
                    'usersCanLogin' => $usersCanLogin,
                    'events' => $events,
                    'events_today' => $events_today,
                    'event_updates' => $event_updates,
                    'eventsWithDetails' => $eventsWithDetails
                ]);
            }
        } else {

            return Inertia::render('Guest/Dashboard/dashboard', [
                'errors' => ['user' => 'The provided credentials are incorrect.'],
                'user' => $fields['user'],
                'auth_error' => true,
                'rolesAllowed' => $rolesAllowed,
                'usersCanLogin' => $usersCanLogin,
                'events' => $events,
                'events_today' => $events_today,
                'event_updates' => $event_updates,
                'eventsWithDetails' => $eventsWithDetails
            ]);
        }
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
