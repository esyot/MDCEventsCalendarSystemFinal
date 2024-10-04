<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index($user) {
    
        // Fetch user permission IDs
        $permissionIds = UserPermission::where('user_id', $user)->pluck('permission_id');
    
        // Fetch the actual permission details using the IDs
        $permissions = Permission::whereIn('id', $permissionIds)->get();
    
        // Extract permission descriptions into an array
        $userPermissions = $permissions->pluck('description')->toArray(); // Convert to array for safety
    
        // Log for debugging
       
    
        // Pass data to the Inertia view
        return Inertia::render('Dashboard/dashboard', [
            'userPermissions' => $userPermissions,
        ]);
    }
}
