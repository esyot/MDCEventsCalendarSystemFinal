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
        $events_today = Event::whereNot('isApprovedByVenueCoordinator', null)
            ->whereNot('isApprovedByAdmin', null)
            ->where('date_start', today())
            ->get();

        $user_role = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();


        if ($user_role == 'event_coordinator') {
            $event_updates = Event::whereNot('isApprovedByAdmin', null)
                ->orderBy('isApprovedByAdmin', 'DESC')
                ->whereNot('isApprovedByAdmin', null)
                ->get();
        } else if ($user_role == 'venue_coordinator') {

            $venueIds = VenueCoordinator::where('user_id', Auth::user()->id)->get()->pluck('venue_id');
            $event_updates = Event::where('isApprovedByVenueCoordinator', null)
                ->where('user_id', Auth::user()->id)
                ->whereIn('venue_id', $venueIds)
                ->join('venues', 'events.venue_id', '=', 'venues.id')
                ->select('events.*', 'venues.name as venue_name', 'venues.building as venue_building')
                ->orderBy('isApprovedByAdmin', 'DESC')
                ->whereNot('isApprovedByAdmin', null)
                ->get();
        } else {

            $event_updates = Event::join('venues', 'events.venue_id', '=', 'venues.id')
                ->select('events.*', 'venues.name as venue_name', 'venues.building as venue_building')
                ->orderBy('isApprovedByAdmin', 'DESC')
                ->whereNot('isApprovedByAdmin', null)
                ->get();
        }
        $currentYear = now()->format('Y');

        $events = Event::
            whereNot('isApprovedByAdmin', null)
            ->whereYear('date_start', $currentYear)->pluck('date_start');

        $eventsWithDetails = DB::table('events')
            ->join('venues', 'events.venue_id', '=', 'venues.id')
            ->join('departments', 'events.department_id', '=', 'departments.id')
            ->join('terms', 'events.term_id', '=', 'terms.id')
            ->select('events.*',
                'venues.name as venue_name',
                'venues.building as venue_building',
                'departments.name as department_name',
                'terms.name as term_name',
                'events.id as event_id',
            )
            ->whereNot('isApprovedByAdmin', null)
            ->whereYear('date_start', $currentYear)->orderBy('date_start', 'ASC')->get();


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
        $events_today = Event::whereNot('isApprovedByVenueCoordinator', null)
            ->whereNot('isApprovedByAdmin', null)
            ->where('date_start', today())
            ->get();


        $event_updates = DB::table('events')
            ->join('venues', 'events.venue_id', '=', 'venues.id')
            ->join('departments', 'events.department_id', '=', 'departments.id')
            ->join('terms', 'events.term_id', '=', 'terms.id')
            ->select('events.*',
                'venues.name as venue_name',
                'venues.building as venue_building',
                'departments.name as department_name',
                'terms.name as term_name',
                'events.id as event_id',
            )
            ->whereNot('events.isApprovedByAdmin', null)
            ->where('events.date_start', '>=', Carbon::today())
            ->orderBy('date_start', 'ASC')->get();

        $currentYear = now()->format('Y');

        $events = Event::
            whereNot('isApprovedByAdmin', null)
            ->whereYear('date_start', $currentYear)->pluck('date_start');

        $eventsWithDetails = DB::table('events')
            ->join('venues', 'events.venue_id', '=', 'venues.id')
            ->join('departments', 'events.department_id', '=', 'departments.id')
            ->join('terms', 'events.term_id', '=', 'terms.id')
            ->select('events.*',
                'venues.name as venue_name',
                'venues.building as venue_building',
                'departments.name as department_name',
                'terms.name as term_name',
                'events.id as event_id',
            )
            ->whereNot('isApprovedByAdmin', null)
            ->whereYear('date_start', $currentYear)->orderBy('date_start', 'ASC')->get();


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
