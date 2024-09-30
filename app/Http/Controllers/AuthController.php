<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Permission;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function index(){
        return view('login');
    }

    public function login(Request $request){
        $fields = $request->validate([
            // example
            'user' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($fields)) {
            // if (Auth::user()->user_permissions) {
            //     return view('test');
            // }
            // return view('welcome');
            dd('sud nako kuya');
        } else {
            $users = User::select('users.id', 'users.lname', 'users.fname', DB::raw('GROUP_CONCAT(permissions.description) as permissions'))
            ->join('user_permissions', 'user_permissions.user_id', '=', 'users.id')
            ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
            ->groupBy('users.id', 'users.lname', 'users.fname')
            ->get();
        
        
         
            $allusers = User::all();
    
            $permissions = Permission::all();
            return view('welcome',  compact('users', 'allusers', 'permissions'));
        }
    }
}
