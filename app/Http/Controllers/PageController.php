<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\UserPermission;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PageController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user()->id;
        $permissionIds = UserPermission::where('user_id', $user)->pluck('permission_id');
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $userPermissions = $permissions->pluck('description')->toArray();

        $events = Event::where('isApprovedByVenueCoordinator', true)->
            where('isApprovedByAdmin', true)->get();

        $events_today = Event::where('isApprovedByVenueCoordinator', true)->
            where('isApprovedByAdmin', true)->where('date', today())->get();

        return Inertia::render('Dashboard/dashboard', [
            'userPermissions' => $userPermissions,
            'user' => $user,
            'pageTitle' => 'Dashboard',
            'name' => Auth::user()->lname . ', ' . Auth::user()->fname,
            'events' => $events,
            'events_today' => $events_today
        ]);
    }



    public function guest()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $events = Event::where('isApprovedByVenueCoordinator', true)->where('isApprovedByAdmin', true)->get();
        $events_today = Event::where('isApprovedByVenueCoordinator', true)->where('isApprovedByAdmin', true)->where('date', today())->get();

        return Inertia::render('Guest/Dashboard/dashboard', [
            'events' => $events,
            'events_today' => $events_today,
        ]);
    }

    public function calendar()
    {
       
        return Inertia::render('Guest/Calendar/calendar', [
          
        ]);
    }

    // public function calendar()
    // {

    //     return Inertia::render('Calendar/calendar', [
      
    //     ]);
    // }
}
