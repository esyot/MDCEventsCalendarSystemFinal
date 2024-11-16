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
    public function index() {

        $user = Auth::user()->id;
        
        $permissionIds = UserPermission::where('user_id', $user)->pluck('permission_id');
                
        $permissions = Permission::whereIn('id', $permissionIds)->get();
           
        $userPermissions = $permissions->pluck('description')->toArray(); 

        $venues = Venue::all();

        $events = Event::all();

        $terms = Term::all();

        $departments = Department::all();

        $user_role_id = UserRoles::where('user_id', Auth::user()->id)
        ->whereIn('role_id', [1, 19, 20, 21])
       ->first();
       $user_role_role = Role::find($user_role_id->role_id);
       $user_role = $user_role_role->role;

       
        return Inertia::render('Calendar/calendar', [
            'departments' => $departments,
            'venues' => $venues,
            'pageTitle'=>'Event Request',
            'user'=>Auth::user()->id,
            'userPermissions' => $userPermissions,
            'name'=> Auth::user()->lname . ', ' . Auth::user()->fname,
            'events'=>$events,
            'terms'=>$terms,
            'user_role'=>$user_role,
            'user_role_calendar'=>$user_role
        ]);
    }
}
