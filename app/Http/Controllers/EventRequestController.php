<?php

namespace App\Http\Controllers;
use App\Models\Event;
use App\Models\Role;
use App\Models\UserRoles;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\EventRequest;

use Illuminate\Support\Facades\Storage;

class EventRequestController extends Controller
{
    // Display a list of all event requests
    public function index()
    {   
        $user = Auth::user()->id;
        
        $permissionIds = UserPermission::where('user_id', $user)->pluck('permission_id');
                
        $permissions = Permission::whereIn('id', $permissionIds)->get();
           
        $userPermissions = $permissions->pluck('description')->toArray(); 

        $events = DB::table('events')
        ->join('venues', 'events.venue_id', '=', 'venues.id')  // Join on venue_id in events and id in venues
        ->select('events.*', 'venues.name as venue_name', 'venues.building as venue_building')
        ->get();
    


        $user_role_id = UserRoles::where('user_id', Auth::user()->id)
        ->whereIn('role_id', [1, 19, 20, 21])
       ->first();
       $user_role_role = Role::find($user_role_id->role_id);

       $user_role = $user_role_role->role;

     
       
        return Inertia::render('EventRequest/eventRequest', [
            'events' => $events,
            'pageTitle'=>'Events',
            'user'=>Auth::user()->id,
            'userPermissions' => $userPermissions,
            'name'=> Auth::user()->lname . ', ' . Auth::user()->fname,
            'user_role'=>$user_role
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

    public function create_request(Request $request){
     
       
        $request->validate([
            'activity_design' => 'required|file|mimes:jpg,jpeg,png,docx,pdf|max:10240', // Add your file validation rules here
        ]);
    
        // Handle the file upload
        $file = $request->file('activity_design');
        if ($file && $file->isValid()) {
            $directory = 'files/uploads';
    
            // Ensure the directory exists
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }
    
            // Save the file
            $filename = $file->getClientOriginalName();
            $filePath = $file->storeAs($directory, $filename, 'public');
    
$event = Event::create([
    'name' => $request->event_name,
    'date' => $request->event_date,
    'term_id' => $request->event_term_id,
    'user_id' => Auth::user()->id,
    'department_id' => $request->event_department_id,
    'levels' => json_encode($request->event_levels),
    'venue_id'=>$request->event_venue,
    'time_start' => date('H:i:s', strtotime($request->event_time_start)),
    'time_end' => date('H:i:s', strtotime($request->event_time_end)),
    'activity_design_file_name' => $filename
]);

if ($event) {
    return redirect()->back()->with('success', 'Event request has been successfully submitted.');
}

    }}

    public function addComment(Request $request, $id){
      

        $event = Event::find($id);

        $event->update([
            'comment'=>$request->comment,
        ]);

        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    public function approveEvent($role,$id){

        if($role =='venue_coordinator'){
            $event = Event::find($id);

            $event->update([
                'isApprovedByVenueCoordinator'=> now(),
            ]);
    
            return redirect()->back()->with('success','Event was approved by Venue Coordinator Successfully!');

        }elseif($role == 'admin'){


            $event = Event::find($id);

            if( $event->isApprovedByVenueCoordinator != null){

            $event->update([
                'isApprovedByAdmin'=> now(),
            ]);
    
            return redirect()->back()->with('success','Event was approved by the Admin successfully!');

            }else{
                return redirect()->back()->with('error','Event must approved by the Venue Coordinator first!');
            }
        
        }
       
    }

    public function retractEvent($id){
        
        $event = Event::find($id);

        $event->update([
            'isApprovedByAdmin'=> null,
        ]);

        return redirect()->back()->with('success','Event was retracted successfully!');

    }

    public function downloadActivityDesign($file)
    {
        $filePath = 'files/uploads/' . $file;

        if (Storage::disk('public')->exists($filePath)) {
            
           Storage::disk('public')->size($filePath);
    
            return Storage::disk('public')->download($filePath);
        } else {
           
            return response()->json(['error' => 'File not found'], 404);
        }
    }
    
}

