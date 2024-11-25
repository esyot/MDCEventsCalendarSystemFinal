<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Permission;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index($user)
    {

        $events = Event::all();

        return Inertia::render('Dashboard/dashboard', [
            'events' => $events,
        ]);
    }
}
