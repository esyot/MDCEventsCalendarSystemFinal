<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPermission;

class PermissionController extends Controller
{
    public function addPermission(Request $request)
    {
        $request->validate([ 
            'user_id' => 'required',
            'permissions' => 'required', 
            'permissions.*' => 'required',
        ]);
    
        $userId = $request->user_id;
        $permissionIds = $request->input('permissions', []); 
    
       
        UserPermission::where('user_id', $userId)->delete(); 
    
        foreach ($permissionIds as $permissionId) {
            UserPermission::create([
                'user_id' => $userId,
                'permission_id' => $permissionId,
            ]);
        }
    
        return redirect()->back()->with('success', 'Permissions updated successfully.');
    }
}        