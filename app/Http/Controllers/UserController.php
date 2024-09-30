<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    public function users(){

        $users = User::select('users.id', 'users.lname', 'users.fname', DB::raw('GROUP_CONCAT(permissions.description) as permissions'))
        ->join('user_permissions', 'user_permissions.user_id', '=', 'users.id')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->groupBy('users.id', 'users.lname', 'users.fname')
        ->get();
    
    
     
        $allusers = User::all();

        $permissions = Permission::all();

        // $user->with('user_permissions');

        // if($user->user_permissions){

        // }

    
        return view('welcome', compact('users', 'allusers', 'permissions'));

    }
}
