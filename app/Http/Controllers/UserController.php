<?php

namespace App\Http\Controllers;

use App\Models\Permission;

use App\Models\Role;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\UserRoles;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(){
        $users = User::select('users.*', 'roles.role as role_name')
        ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
        ->join('roles', 'user_roles.role_id', '=', 'roles.id')
        ->whereIn('roles.id', [1, 19, 20, 21])
        ->get();

        $user = Auth::user()->id;

        $allUsers = User::all();
        
         $permissionIds = UserPermission::where('user_id', $user)->pluck('permission_id');

         
         $permissions = Permission::whereIn('id', $permissionIds)->get();
     
         $user_role_id = UserRoles::where('user_id', Auth::user()->id)
          ->whereIn('role_id', [1, 19, 20, 21])
         ->first();
         $user_role_role = Role::find($user_role_id->role_id);
         $user_role = $user_role_role->role;
        
        
       
         $userPermissions = $permissions->pluck('description')->toArray(); 
         $roles = Role::whereIn('roles.id', [1, 19, 20, 21])->pluck('role')->toArray();

    return Inertia::render('User/user', [
        'users'=>$users->sortBy('lname')->values(),
        'pageTitle'=>'List of Users',
        'user'=>Auth::user()->id,
        'userPermissions' => $userPermissions,
        'name'=> Auth::user()->lname . ', ' . Auth::user()->fname,
        'allUsers'=>        $allUsers,
        'user_searched'=> 0, 
        'roles'=>$roles,
        'user_role'=>$user_role

    ]);
       
       
    }


    public function search(Request $request){

        if($request->search_value == null){

            return redirect()->route('users');

        }

        $userIds = User::where('id', $request->search_value)
        ->orWhere('lname', 'LIKE', '%' . $request->search_value . '%')
        ->orWhere('fname', 'LIKE', '%' . $request->search_value . '%')
        ->pluck('id')->unique();
    
    $usersWithExcludedRoles = UserRoles::whereIn('role_id', [1, 19, 20, 21])
        ->whereIn('user_id', $userIds)
        ->pluck('user_id')->unique();
    
    $usersWithRoles = UserRoles::whereIn('user_id', $userIds)
        ->whereNotIn('role_id', [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18])
        ->pluck('user_id')->unique();
    
    $allExcludedUserIds = $usersWithExcludedRoles->merge($usersWithRoles)->unique();
    
    $userWithoutExcludedRoles = $userIds->diff($allExcludedUserIds)->values();
    
    $user_searched = User::whereIn('id', $userWithoutExcludedRoles)->get()->unique('id');

        $users = User::select('users.*', 'roles.role as role_name')
        ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
        ->join('roles', 'user_roles.role_id', '=', 'roles.id')
        ->whereIn('roles.id', [1, 19, 20, 21])
        ->get();

        $user = Auth::user()->id;

        $allUsers = User::all();
        
         $permissionIds = UserPermission::where('user_id', $user)->pluck('permission_id');

         
         $permissions = Permission::whereIn('id', $permissionIds)->get();
     
       
         $userPermissions = $permissions->pluck('description')->toArray(); 


        return Inertia::render('User/user', [
            'users'=>$users->sortBy('lname')->values(),
            'pageTitle'=>'List of Users',
            'user'=>Auth::user()->id,
            'userPermissions' => $userPermissions,
            'name'=> Auth::user()->lname . ', ' . Auth::user()->fname,
            'user_searched'=>$user_searched,
        ]);
           
    }

    public function user_add_role(Request $request)
{
    $validatedData = $request->validate([
        'role' => 'required|in:admin,superadmin,event-coordinator,venue-coordinator,none',
    ]);

    $user = User::find($request->id);

    if (!$user) {
        return redirect()->back()->withErrors(['error' => 'User not found.']);
    }

    $roleIds = [
        'admin' => 1,
        'superadmin' => 21,
        'event-coordinator' => 20,
        'venue-coordinator' => 19,
        'none' => null,
    ];

    $newRoleId = $roleIds[$validatedData['role']];
    
    // Get the user's current role
    $currentUserRole = UserRoles::where('user_id', $user->id)->first();

    // Only delete the old role if it differs from the new role
    if ($currentUserRole) {
        if ($newRoleId === null || $currentUserRole->role_id !== $newRoleId) {
            UserRoles::where('user_id', $user->id)->delete();
        }
    }

    // Assign new role if it's not 'none'
    if ($newRoleId !== null) {
        UserRoles::updateOrCreate(
            ['user_id' => $user->id],
            ['role_id' => $newRoleId]
        );
    }

    return redirect()->back()->with('success', 'User role updated successfully!');
}

public function user_role_update(Request $request)
{
    $role = Role::where('role', $request->role)->first();

    UserRoles::where('user_id', $request->id)
    ->whereIn('role_id', [1,19,20,21])
    ->update([
        'role_id'=>$role->id,
    ]);

    return redirect()->back()->with('success', 'User role has been updated successfully');
}

public function user_delete_role($id){

    UserRoles::where('user_id',$id)->delete();
    return redirect()->back()->with('success', 'User role has been deleted successfully');
}
}