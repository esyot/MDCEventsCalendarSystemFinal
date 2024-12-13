<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\Event;
use App\Models\Role;
use App\Models\Term;
use App\Models\UserRoles;
use App\Models\Venue;
use App\Models\VenueCoordinator;
use Carbon\Carbon;
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

        $rolesAllowed = Role::whereIn('role', ['super_admin', 'admin', 'venue_coordinator', 'event_coordinator'])->pluck('id');

        $user_role_id = UserRoles::where('user_id', Auth::user()->id)->whereIn('role_id', $rolesAllowed)
            ->first();

        $user_role_role = Role::find($user_role_id->role_id);

        $user_role = $user_role_role->role;

        $venues = Venue::all();
        $departments = Department::all();
        $terms = Term::all();


        if ($user_role == 'event_coordinator') {

            $events = DB::table('events')
                ->join('venues', 'events.venue_id', '=', 'venues.id')
                ->join('departments', function ($join) {
                    $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
                })
                ->join('terms', 'events.term_id', '=', 'terms.id')
                ->join('users', 'events.user_id', '=', 'users.id')
                ->select(
                    'events.*',
                    'events.id as event_id',
                    'events.name as event_name',
                    'venues.name as venue_name',
                    'venues.id as venue_id',
                    'venues.building as venue_building',
                    'departments.*',
                    DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                    'terms.id as term_id',
                    'terms.name as term_name',
                    'users.fname as user_fname',
                    'users.lname as user_lname',
                )
                ->where('user_id', Auth::user()->id)
                ->groupBy(
                    'events.id',
                    'venues.name',
                    'venues.building',
                    'terms.name',
                    'departments.id', // Add this
                    'departments.name' // Add any other department fields if necessary
                )
                ->get();



        } else if ($user_role == 'venue_coordinator') {

            $venuesAssignedIds = VenueCoordinator::where('user_id', Auth::user()->id)->get()->pluck('venue_id');

            $events = DB::table('events')
                ->join('venues', 'events.venue_id', '=', 'venues.id')
                ->join('departments', function ($join) {
                    $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
                })
                ->join('terms', 'events.term_id', '=', 'terms.id')
                ->join('users', 'events.user_id', '=', 'users.id')
                ->select(
                    'events.*',
                    'events.id as event_id',
                    'events.name as event_name',
                    'venues.name as venue_name',
                    'venues.id as venue_id',
                    'venues.building as venue_building',
                    'departments.*',
                    DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                    'terms.id as term_id',
                    'terms.name as term_name',
                    'users.fname as user_fname',
                    'users.lname as user_lname',
                )
                ->whereIn('venue_id', $venuesAssignedIds)
                ->groupBy(
                    'events.id',
                    'venues.name',
                    'venues.building',
                    'terms.name',
                    'departments.id', // Add departments fields here
                    'departments.name' // If needed, add other department fields
                )
                ->get();



        } else if ($user_role == 'admin' || $user_role == 'super_admin') {

            $events = DB::table('events')
                ->join('venues', 'events.venue_id', '=', 'venues.id')
                ->join('departments', function ($join) {
                    $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
                })
                ->join('terms', 'events.term_id', '=', 'terms.id')
                ->join('users', 'events.user_id', '=', 'users.id')
                ->select(
                    'events.*',
                    'events.id as event_id',
                    'events.name as event_name',
                    'venues.name as venue_name',
                    'venues.id as venue_id',
                    'venues.building as venue_building',
                    'departments.*',
                    DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                    'terms.id as term_id',
                    'terms.name as term_name',
                    'users.fname as user_fname',
                    'users.lname as user_lname',
                )

                ->groupBy(
                    'events.id',
                    'venues.name',
                    'venues.building',
                    'terms.name',
                    'departments.id', // Add this
                    'departments.name' // Add any other department fields if necessary
                )
                ->get();


        }


        return Inertia::render('EventRequest/eventRequest', [
            'events' => $events,
            'pageTitle' => 'Requests',
            'user' => Auth::user(),
            'user_role' => $user_role,
            'venues' => $venues,
            'departments' => $departments,
            'terms' => $terms
        ]);
    }

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


    public function destroy($id)
    {
        $event = EventRequest::findOrFail($id);
        $event->delete();

        return redirect()->route('eventRequests.index')->with('success', 'Event request deleted successfully.');
    }

    public function create_request(Request $request)
    {

        $departmentIds = Department::whereIn('accronym', $request->event_departments)->get()->pluck('id')->toArray();
        $file = $request->file('activity_design');


        $formattedStartDateString = preg_replace('/\s\(.*\)$/', '', $request->event_time_start);
        $formatTimeStart = Carbon::parse($formattedStartDateString);
        $formattedTimeStart = $formatTimeStart->format('H:i');

        $formattedEndDateString = preg_replace('/\s\(.*\)$/', '', $request->event_time_end);
        $formatTimeEnd = Carbon::parse($formattedEndDateString);
        $formattedTimeEnd = $formatTimeEnd->format('H:i');





        if ($file != null) {
            $request->validate([
                'activity_design' => 'required|file|mimes:jpg,jpeg,png,docx,pdf|max:10240',
            ]);
            $directory = 'files/uploads';


            if (! Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            $filename = $file->getClientOriginalName();
            $filePath = $file->storeAs($directory, $filename, 'public');

            $event = Event::create([
                'name' => $request->event_name,
                'date_start' => $request->event_date_start,
                'date_end' => $request->event_date_end,
                'term_id' => $request->event_term_id,
                'user_id' => Auth::user()->id,
                'department_id' => json_encode($departmentIds),
                'levels' => json_encode($request->event_levels),
                'venue_id' => $request->event_venue,
                'time_start' => $formattedTimeStart,
                'time_end' => $formattedTimeEnd,
                'activity_design_file_name' => $filename
            ]);


        } else {
            $event = Event::create([
                'name' => $request->event_name,
                'date_start' => $request->event_date_start,
                'date_end' => $request->event_date_end,
                'term_id' => $request->event_term_id,
                'user_id' => Auth::user()->id,
                'department_id' => json_encode($departmentIds),
                'levels' => json_encode($request->event_levels),
                'venue_id' => $request->event_venue,
                'time_start' => $formattedTimeStart,
                'time_end' => $formattedTimeEnd,
            ]);


        }
        if ($event) {
            return redirect()->route('calendar')->with('success', 'Event request has been successfully submitted.');
        }
    }

    public function addComment(Request $request, $id)
    {


        $event = Event::find($id);

        $event->update([
            'comment' => $event->comment . $request->comment,
        ]);

        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    public function approveEvent($role, $id)
    {

        if ($role == 'venue_coordinator') {
            $event = Event::find($id);

            $event->update([
                'isApprovedByVenueCoordinator' => now(),
                'comment' => null,
            ]);

            return redirect()->back()->with('success', 'Event was approved by Venue Coordinator Successfully!');

        } elseif ($role == 'admin') {


            $event = Event::find($id);

            if ($event->isApprovedByVenueCoordinator != null) {

                $event->update([
                    'isApprovedByAdmin' => now(),
                ]);

                return redirect()->back()->with('success', 'Event was approved by the Admin successfully!');

            } else {
                return redirect()->back()->with('error', 'Event must approved by the Venue Coordinator first!');
            }

        }

    }

    public function eventRetract(Request $request, $role, $id)
    {
        if ($role == 'venue_coordinator') {
            $event = Event::find($id);

            $event->update([
                'isApprovedByVenueCoordinator' => null,
                'comment' => $event->comment . $request->comment,
            ]);

            return redirect()->back()->with('success', 'Event was retracted successfully!');
        } else if ($role == 'admin') {
            $event = Event::find($id);

            $event->update([
                'isApprovedByAdmin' => null,
            ]);

            return redirect()->back()->with('success', 'Event was retracted successfully!');

        }

        return redirect()->route('unauthorized')->with('error', 'You have no permission to retract this event!');
    }

    public function viewActivityDesign($file)
    {
        $filePath = 'files/uploads/' . $file;

        if (Storage::disk('public')->exists($filePath)) {

            return response()->file(storage_path('app/public/' . $filePath));
        } else {

            return response()->json(['error' => 'File not found'], 404);
        }
    }

    public function update_request(Request $request)
    {


        if ($request->activity_design) {

            $request->validate([
                'activity_design' => 'required|file|mimes:jpg,jpeg,png,docx,pdf|max:10240',
            ]);

            // Handle the file upload
            $file = $request->file('activity_design');
            if ($file && $file->isValid()) {
                $directory = 'files/uploads';

                // Ensure the directory exists
                if (! Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }

                // Save the file
                $filename = $file->getClientOriginalName();
                $filePath = $file->storeAs($directory, $filename, 'public');


                $event = Event::find($request->id);

                $event->update([
                    'name' => $request->event_name,
                    'date_start' => $request->date_start,
                    'date_end' => $request->date_end,
                    'term_id' => $request->term_id,
                    'time_start' => $request->time_start,
                    'time_end' => $request->time_end,
                    'department_id' => $request->department_id,
                    'venue_id' => $request->venue_id,
                    'user_id' => Auth::user()->id,
                    'activity_design_file_name' => $filename,
                    'comment' => null,

                ]);

                return redirect()->back()->with('success', 'Event successfully updated!');

            }

        }
        $event = Event::find($request->id);

        $event->update([
            'name' => $request->event_name,
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'term_id' => $request->term_id,
            'time_start' => $request->time_start,
            'time_end' => $request->time_end,
            'department_id' => $request->department_id,
            'venue_id' => $request->venue_id,
            'user_id' => Auth::user()->id,
            'comment' => null,

        ]);

        return redirect()->back()->with('success', 'Event successfully updated!');

    }

    public function delete_request($id)
    {

        $event = Event::find($id);

        $event->delete();

        return redirect()->back()->with('success', 'Event successfully deleted!');
    }
}

