<?php
#just for the sake of making a stupid change.

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserDepartment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {


        $users = User::select(
            'users.*',
            'roles.role as role_name',
            'roles.*',
            'user_roles.*'
        )
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->join('roles', 'user_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.role', ['venue_coordinator', 'event_coordinator'])
            ->get();



        $departments = DB::table('user_departments')
            ->join('departments', 'user_departments.department_id', '=', 'departments.id')
            ->get(['user_departments.user_id', 'departments.id as department_id', 'departments.name as department_name']);


        $users->each(function ($user) use ($departments) {
            $user->departments = $departments->where('user_id', $user->id)->values();
        });


        $allUsers = User::all();

        $user_role = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['sec-admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();

        $roles = Role::whereIn('role', ['venue_coordinator', 'event_coordinator'])->pluck('role')->toArray();

        $departments = Department::all();


        $selectedUser = null;
        $selectedUserDepartments = null;
        if ($request->user != '') {
            $selectedUser = User::find($request->user);

            $departmentIds = UserDepartment::where('user_id', $selectedUser->id)->get()->pluck('department_id');

            $selectedUserDepartments = Department::whereIn('id', $departmentIds)->get()->toArray();

        }

        return Inertia::render('User/user', [
            'users' => $users->sortBy('lname')->values(),
            'pageTitle' => 'List of Users',
            'user' => Auth::user(),
            'allUsers' => $allUsers,
            'roles' => $roles,
            'user_searched' => [],
            'user_role' => $user_role,
            'departments' => $departments,
            'selectedUserDepartments' => $selectedUserDepartments,
            'selectedUser' => $selectedUser
        ]);
    }

    public function search(Request $request)
    {


        if ($request->search_value == null) {

            return redirect()->route('users');

        }

        $userIds = User::where('id', $request->search_value)
            ->orWhere('lname', 'LIKE', '%' . $request->search_value . '%')
            ->orWhere('fname', 'LIKE', '%' . $request->search_value . '%')
            ->pluck('id')->unique();

        $roleIds = Role::whereIn('role', ['sec-admin', 'venue_coordinator', 'event_coordinator'])->get()->pluck('id');

        $usersWithExcludedRoles = UserRole::whereIn('role_id', $roleIds)
            ->whereIn('user_id', $userIds)
            ->pluck('user_id')->unique();

        $usersWithRoles = UserRole::whereIn('user_id', $userIds)
            ->whereIn('role_id', $roleIds)
            ->pluck('user_id')->unique();

        $allExcludedUserIds = $usersWithExcludedRoles->merge($usersWithRoles)->unique();

        $userWithoutExcludedRoles = $userIds->diff($allExcludedUserIds)->values();

        $user_searched = User::whereIn('id', $userWithoutExcludedRoles)->get()->unique('id');

        $roles = Role::whereIn('role', ['venue_coordinator', 'event_coordinator'])->pluck('role')->toArray();


        $users = User::select(
            'users.*',
            'roles.role as role_name',
            'roles.*',
            'user_roles.*'
        )
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->join('roles', 'user_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.role', ['venue_coordinator', 'event_coordinator'])
            ->get();


        // Fetch departments separately
        $departments = DB::table('user_departments')
            ->join('departments', 'user_departments.department_id', '=', 'departments.id')
            ->get(['user_departments.user_id', 'departments.id as department_id', 'departments.name as department_name']);

        // Merge departments with users
        $users->each(function ($user) use ($departments) {
            $user->departments = $departments->where('user_id', $user->id)->values();
        });

        $user = Auth::user()->id;

        $allUsers = User::all();

        $user_role = Role::join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', Auth::user()->id)
            ->whereIn('roles.role', ['sec-admin', 'venue_coordinator', 'event_coordinator'])
            ->pluck('roles.role')
            ->first();

        $departments = Department::all();




        return Inertia::render('User/user', [
            'users' => $users->sortBy('lname')->values(),
            'pageTitle' => 'List of Users',
            'user' => Auth::user(),
            'user_searched' => $user_searched,
            'user_role' => $user_role,
            'departments' => $departments,
            'roles' => $roles,

        ]);

    }

    public function user_add_role(Request $request)
    {

        $validatedData = $request->validate([
            'role' => 'required',
        ]);

        $user = User::find($request->id);

        if (! $user) {
            return redirect()->back()->withErrors(['error' => 'User not found.']);
        }

        $role = $validatedData['role'];

        $newRoleId = Role::where('role', $role)->pluck('id')->first();

        $roleIds = Role::whereIn('role', ['event_coordinator', 'venue_coordinator'])->pluck('id');

        $currentUserRole = UserRole::where('user_id', $user->id)
            ->whereIn('role_id', $roleIds)
            ->first();

        if ($currentUserRole == null) {
            UserRole::Create(
                [
                    'user_id' => $user->id,
                    'role_id' => $newRoleId

                ],
            );

            UserDepartment::updateOrCreate(
                ['user_id' => $user->id],
                ['department_id' => $request->department]
            );

        }

        return redirect()->back()->with('success', 'User role updated successfully!');
    }

    public function user_role_update(Request $request)
    {

        $role = Role::where('role', $request->role)->first();

        $roleIdsAllowed = Role::whereIn('role', ['event_coordinator', 'venue_coordinator'])->pluck('id');

        UserRole::where('user_id', $request->user)
            ->whereIn('role_id', $roleIdsAllowed)
            ->update([
                'role_id' => $role->id,
            ]);


        return redirect()->back()->with('success', 'User role has been updated successfully');
    }

    public function user_delete_role($id)
    {
        UserRole::where('user_id', $id)->delete();
        return redirect()->back()->with('success', 'User role has been deleted successfully');
    }

    public function userDepartmentRemove($user, $department)
    {

        $userCount = UserDepartment::where('user_id', $user)
            ->count();
        $userDepartment = UserDepartment::where('user_id', $user)->
            where('department_id', $department);

        if ($userDepartment && $userCount > 1) {

            $userDepartment->delete();

            return redirect()->route('users', [
                'user' => $user
            ]);
        }
        return redirect()->route('users', [
            'user' => $user
        ])->with('error', 'User must have at least one department!');
    }

    public function userDepartmentAdd(Request $request)
    {

        UserDepartment::create([
            'user_id' => $request->user,
            'department_id' => $request->department
        ]);

        return redirect()->route('users', [
            'user' => $request->user
        ]);

    }
}