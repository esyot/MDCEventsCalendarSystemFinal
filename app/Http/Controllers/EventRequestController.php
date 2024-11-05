<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\EventRequest;

class EventRequestController extends Controller
{
    // Display a list of all event requests
    public function index()
    {   
        $user = Auth::user()->id;
        
        $permissionIds = UserPermission::where('user_id', $user)->pluck('permission_id');
                
        $permissions = Permission::whereIn('id', $permissionIds)->get();
           
        $userPermissions = $permissions->pluck('description')->toArray(); 

        $events = EventRequest::all(); // Retrieve all event requests
        return Inertia::render('EventRequest/eventRequest', [
            'events' => $events,
            'pageTitle'=>'Event Request',
            'user'=>Auth::user()->id,
            'userPermissions' => $userPermissions,
            'name'=> Auth::user()->lname . ', ' . Auth::user()->fname
        ]);
    }

    // Display details of a specific event request
    public function show($id)
    {
        $event = EventRequest::findOrFail($id); // Find event by ID or fail
        return Inertia::render('EventRequest/showEvent', [
            'event' => $event
        ]);
    }

    // Show the form for editing a specific event request
    public function edit($id)
    {
        $event = EventRequest::findOrFail($id); // Find event by ID or fail
        return Inertia::render('EventRequest/editEvent', [
            'event' => $event
        ]);
    }

    // Delete a specific event request
    public function destroy($id)
    {
        $event = EventRequest::findOrFail($id); // Find event by ID or fail
        $event->delete(); // Delete the event

        return redirect()->route('eventRequests.index')->with('success', 'Event request deleted successfully.');
    }
}

