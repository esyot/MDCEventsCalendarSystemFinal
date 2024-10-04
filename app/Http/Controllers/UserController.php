<?php

namespace App\Http\Controllers;

use App\Models\Permission;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Auth;
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
        
         $permissionIds = UserPermission::where('user_id', $user)->pluck('permission_id');

         
         $permissions = Permission::whereIn('id', $permissionIds)->get();
     
       
         $userPermissions = $permissions->pluck('description')->toArray(); 

    return Inertia::render('User/User', [
        'users'=>$users,
        'pageTitle'=>'List of Users',
        'user'=>Auth::user()->id,
        'userPermissions' => $userPermissions,
        'name'=> Auth::user()->lname . ', ' . Auth::user()->fname
    ]);
       
       
    }
}
