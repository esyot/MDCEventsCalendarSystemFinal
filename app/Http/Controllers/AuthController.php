<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Event;
use App\Models\UserRoles;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function index()
    {
        return Inertia::render('Login');
    }

    public function login(Request $request)
    {

        $fields = $request->validate([
            'user' => 'required|string',
            'password' => 'required|string'
        ]);


        if (Auth::attempt(['user' => $fields['user'], 'password' => $fields['password']])) {
            $user = Auth::user();

            $usersCanLogin = UserRoles::whereIn('role_id', [1, 19, 20, 21])->pluck('user_id');

            if ($usersCanLogin->contains($user->id)) {
                return redirect()->route('dashboard');
            }
        }

        $events = Event::where('isApprovedByVenueCoordinator', true)->where('isApprovedByAdmin', true)->get();
        $events_today = Event::where('isApprovedByVenueCoordinator', true)->where('isApprovedByAdmin', true)->where('date', today())->get();





        return Inertia::render('Guest/Dashboard/dashboard', [
            'errors' => ['user' => 'The provided credentials are incorrect.'],
            'user' => $fields['user'],
            'auth_error' => true,
            'events' => $events,
            'events_today' => $events_today,
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('guest');
    }

    public function authCheck(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $events = Event::where('isApprovedByVenueCoordinator', true)->where('isApprovedByAdmin', true)->get();
        $events_today = Event::where('isApprovedByVenueCoordinator', true)->where('isApprovedByAdmin', true)->where('date', today())->get();

        return Inertia::render('Guest/Dashboard/dashboard', [
            'error' => 'Your account access has been removed.',
            'events' => $events,
            'events_today' => $events_today,
        ]);
    }
}
