<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\Event;
use App\Models\Role;
use App\Models\Term;
use App\Models\UserRoles;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use App\Models\UserPermission;
use App\Models\Venue;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {

        $user = Auth::user();

        $venues = Venue::all();

        $currentYear = now()->format('Y');

        $events = Event::whereYear('date_start', $currentYear)->pluck('date_start');

        $eventsWithDetails = Event::whereYear('date_start', $currentYear)->orderBy('date_start', 'ASC')->get();

        $terms = Term::all();

        $departments = Department::all();

        $user_role = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();

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
            'successMessage' => session('success'),
        ]);
    }
}
