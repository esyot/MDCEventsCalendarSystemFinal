<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\Event;
use App\Models\EventDepartment;
use App\Models\EventJunction;
use App\Models\Role;
use App\Models\Term;
use App\Models\UserDepartment;
use App\Models\UserRole;
use App\Models\Venue;
use App\Models\VenueCoordinator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\EventRequest;

use Illuminate\Support\Facades\Storage;

class EventRequestController extends Controller
{
    public function index()
    {

        $rolesAllowed = Role::whereIn('role', ['super_admin', 'sec-admin', 'venue_coordinator', 'event_coordinator'])->pluck('id');

        $user_role_id = UserRole::where('user_id', Auth::user()->id)->whereIn('role_id', $rolesAllowed)
            ->first();

        $user_role_role = Role::find($user_role_id->role_id);

        $user_role = $user_role_role->role;

        $venues = Venue::all();
        $departments = Department::all();
        $terms = Term::all();


        if ($user_role == 'event_coordinator') {

            $deparmentIds = UserDepartment::where('user_id', Auth::user()->id)->get()->pluck('department_id');

            $departments = Department::whereIn('id', $deparmentIds)->get();

            $events = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->join('users', 'events.user_id', '=', 'users.id')
                ->select(
                    'users.fname as user_fname',
                    'users.lname as user_lname',
                    'events.id as event_id',
                    'terms.name as term_name',
                    'terms.id as term_id',
                    'events.name as event_name',
                    'events.levels as levels',
                    'venues.name as venue_name',
                    'venues.id as venue_id',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.approved_by_admin_at as approved_by_admin_at',
                    'event_junctions.approved_by_venue_coordinator_at as approved_by_venue_coordinator_at',
                    'event_junctions.comment as comment',
                    'event_junctions.updated_at as updated_at',
                    DB::raw('GROUP_CONCAT(departments.id ORDER BY departments.id ASC SEPARATOR ", ") as department_id'),
                    DB::raw('GROUP_CONCAT(departments.accronym ORDER BY departments.accronym ASC SEPARATOR ", ") as department_acronyms')
                )
                ->groupBy(

                    'users.fname',
                    'users.lname',
                    'events.id',
                    'terms.name',
                    'terms.id',
                    'events.name',
                    'events.levels',
                    'events.date',
                    'venues.id',
                    'venues.name',
                    'venues.building',
                    'event_junctions.time_end',
                    'event_junctions.time_start',
                    'event_junctions.date_end',
                    'event_junctions.approved_by_admin_at',
                    'event_junctions.approved_by_venue_coordinator_at',
                    'event_junctions.comment',
                    'event_junctions.updated_at',
                )
                ->where('events.user_id', Auth::user()->id)
                ->orderBy('event_junctions.created_at', 'DESC')
                ->get();




        } else if ($user_role == 'venue_coordinator') {

            $venuesAssignedIds = VenueCoordinator::where('user_id', Auth::user()->id)->get()->pluck('venue_id');
            $events = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->join('users', 'events.user_id', '=', 'users.id')
                ->select(
                    'users.fname as user_fname',
                    'users.lname as user_lname',
                    'events.id as event_id',
                    'terms.name as term_name',
                    'events.name as event_name',
                    'events.levels as levels',
                    'venues.name as venue_name',
                    'venues.id as venue_id',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.approved_by_admin_at as approved_by_admin_at',
                    'event_junctions.approved_by_venue_coordinator_at as approved_by_venue_coordinator_at',
                    'event_junctions.comment as comment',
                    'event_junctions.updated_at as updated_at',
                    DB::raw('GROUP_CONCAT(departments.id ORDER BY departments.id ASC SEPARATOR ", ") as department_id'),
                    DB::raw('GROUP_CONCAT(departments.accronym ORDER BY departments.accronym ASC SEPARATOR ", ") as department_acronyms')
                )
                ->groupBy(

                    'users.fname',
                    'users.lname',
                    'events.id',
                    'terms.name',
                    'events.name',
                    'events.levels',
                    'events.date',
                    'venues.id',
                    'venues.name',
                    'venues.building',
                    'event_junctions.time_end',
                    'event_junctions.time_start',
                    'event_junctions.date_end',
                    'event_junctions.approved_by_admin_at',
                    'event_junctions.approved_by_venue_coordinator_at',
                    'event_junctions.comment',
                    'event_junctions.updated_at',
                )
                ->whereIn('venue_id', $venuesAssignedIds)
                ->orderBy('event_junctions.created_at', 'DESC')
                ->get();



        } else if ($user_role == 'sec-admin') {

            $events = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->join('users', 'events.user_id', '=', 'users.id')
                ->select(
                    'users.fname as user_fname',
                    'users.lname as user_lname',
                    'events.id as event_id',
                    'terms.name as term_name',
                    'terms.id as term_id',
                    'events.name as event_name',
                    'events.levels as levels',
                    'venues.name as venue_name',
                    'venues.id as venue_id',
                    'event_junctions.filename as filename',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.approved_by_admin_at as approved_by_admin_at',
                    'event_junctions.approved_by_venue_coordinator_at as approved_by_venue_coordinator_at',
                    'event_junctions.comment as comment',
                    'event_junctions.updated_at as updated_at',
                    DB::raw('GROUP_CONCAT(departments.id ORDER BY departments.id ASC SEPARATOR ", ") as department_id'),
                    DB::raw('GROUP_CONCAT(departments.accronym ORDER BY departments.accronym ASC SEPARATOR ", ") as department_acronyms')
                )
                ->groupBy(

                    'users.fname',
                    'users.lname',
                    'events.id',
                    'terms.name',
                    'terms.id',
                    'events.name',
                    'events.levels',
                    'events.date',
                    'venues.id',
                    'venues.name',
                    'venues.building',
                    'event_junctions.filename',
                    'event_junctions.time_end',
                    'event_junctions.time_start',
                    'event_junctions.date_end',
                    'event_junctions.approved_by_admin_at',
                    'event_junctions.approved_by_venue_coordinator_at',
                    'event_junctions.comment',
                    'event_junctions.updated_at',
                )
                ->orderBy('event_junctions.created_at', 'DESC')
                ->get();

        }


        $departmentsWithParent = DB::table('departments as t1')
            ->leftJoin('departments as t2', 't1.parent_id', '=', 't2.id')
            ->leftJoin('departments as t3', 't2.parent_id', '=', 't3.id')
            ->leftJoin('departments as t4', 't3.parent_id', '=', 't4.id')
            ->select(
                't1.id as department_id',
                't1.accronym as acronym',
                't1.name as department_name',
                DB::raw('COALESCE(t4.accronym, t3.accronym, t2.accronym, t1.accronym) as parent')

            )
            ->whereNotNull('t1.parent_id')
            ->get();

        $departmentsWithNoParent = DB::table('departments as t1')
            ->leftJoin('departments as t2', 't1.id', '=', 't2.parent_id')
            ->select(
                't1.id as department_id',
                't1.accronym as acronym',
                't1.name as department_name',
                't1.accronym as parent',
            )
            ->whereNull('t1.parent_id')
            ->whereNull('t2.id')
            ->get();



        $departmentsForm = $departmentsWithNoParent->concat($departmentsWithParent);


        return Inertia::render('EventRequest/eventRequest', [
            'departmentsForm' => $departmentsForm,
            'events' => $events,
            'pageTitle' => 'Requests',
            'user' => Auth::user(),
            'user_role' => $user_role,
            'venues' => $venues,
            'departments' => $departments,
            'terms' => $terms,
            'successMessage' => session('success') ?? null,
            'errorMessage' => session('error') ?? null,
        ]);
    }

    public function show($id)
    {
        $event = EventRequest::find($id);
        return Inertia::render('EventRequest/showEvent', [
            'event' => $event
        ]);
    }

    public function edit($id)
    {
        $event = EventRequest::find($id);
        return Inertia::render('EventRequest/editEvent', [
            'event' => $event
        ]);
    }


    public function create_request(Request $request)
    {

        $department = collect($request->event_departments)
            ->map(fn ($item) => explode(',', $item)[0])
            ->unique()
            ->toArray();

        if (count($department) > 1) {
            $custom = Department::where('name', 'Custom')->pluck('id')->first();
            $department_id = $custom;
        } else {
            $department_id = Department::where('accronym', $department[0])->pluck('id')->first();
        }

        $file = $request->file('activity_design');
        $formattedStartDateString = preg_replace('/\s\(.*\)$/', '', $request->event_time_start);
        $formatTimeStart = Carbon::parse($formattedStartDateString);
        $formattedTimeStart = $formatTimeStart->format('H:i');

        $formattedEndDateString = preg_replace('/\s\(.*\)$/', '', $request->event_time_end);
        $formatTimeEnd = Carbon::parse($formattedEndDateString);
        $formattedTimeEnd = $formatTimeEnd->format('H:i');




        if ($file != null) {
            $filename = $file->getClientOriginalName();
            $filePath = $file->storeAs('files/uploads', $filename, 'public');
        }


        $event1 = Event::create([
            'name' => $request->event_name,
            'date' => $request->event_date_start,
            'term_id' => $request->event_term_id,
            'user_id' => Auth::user()->id,
            'department_id' => $department_id,
            'levels' => json_encode($request->event_levels),
        ]);
        $event2Data = [
            'event_id' => $event1->id,
            'date_end' => $request->event_date_end,
            'time_start' => $formattedTimeStart,
            'time_end' => $formattedTimeEnd,
            'venue_id' => $request->event_venue,
        ];

        if ($file) {
            $event2Data['filename'] = $filename;
        }

        $event2 = EventJunction::create($event2Data);

        foreach ($request->departmentSelected as $dept) {
            $deptIds = explode(',', $dept);
            foreach ($deptIds as $deptId) {
                EventDepartment::create([
                    'event_id' => $event1->id,
                    'department_id' => trim($deptId),
                ]);
            }
        }

        if ($event1 && $event2) {

            return redirect()->route('calendar')->with('success', 'Event has been successfully added!');

        } else {

            return redirect()->route('calendar')->with('error', 'Event request failed!');


        }

    }


    public function addComment(Request $request, $id)
    {


        $event = EventJunction::where('event_id', $id)->first();

        $event->update([
            'comment' => $event->comment . $request->comment,
        ]);

        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    public function approveEvent($role, $id)
    {

        if ($role == 'venue_coordinator') {
            $event = EventJunction::where('event_id', $id);

            $event->update([
                'approved_by_venue_coordinator_at' => now(),
                'comment' => null,
            ]);

            return redirect()->back()->with('success', 'Event was approved by Venue Coordinator Successfully!');

        } elseif ($role == 'sec-admin') {


            $event = EventJunction::where('event_id', $id)->first();

            if ($event->approved_by_venue_coordinator_at != null) {

                $event->update([
                    'approved_by_admin_at' => now(),
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
            $event = EventJunction::where('event_id', $id)->first();

            $event->update([
                'approved_by_venue_coordinator_at' => null,
                'comment' => $event->comment . $request->comment,
            ]);

            return redirect()->back()->with('success', 'Event was retracted successfully!');
        } else if ($role == 'sec-admin') {
            $event = EventJunction::where('event_id', $id);

            $event->update([
                'approved_by_admin_at' => null,
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

        $departmentIds = explode(',', trim($request->departments[0]));
        $departmentIds = array_map('intval', array_map('trim', $departmentIds));

        $departments = Department::whereIn('id', $departmentIds)->get();

        $departmentsWithParent = DB::table('departments as t1')
            ->leftJoin('departments as t2', 't1.parent_id', '=', 't2.id')
            ->leftJoin('departments as t3', 't2.parent_id', '=', 't3.id')
            ->leftJoin('departments as t4', 't3.parent_id', '=', 't4.id')
            ->select(
                't1.id as id',
                't1.accronym as accronym',
                't1.name as name',
                DB::raw('COALESCE(t4.accronym, t3.accronym, t2.accronym, t1.accronym) as parent')
            )
            ->whereNotNull('t1.parent_id')
            ->get();

        $departmentsWithNoParent = DB::table('departments as t1')
            ->leftJoin('departments as t2', 't1.id', '=', 't2.parent_id')
            ->select(
                't1.id as id',
                't1.accronym as accronym',
                't1.name as name',
                't1.accronym as parent'
            )
            ->whereNull('t1.parent_id')
            ->whereNull('t2.id')
            ->get();

        $departmentsForm = $departmentsWithNoParent->merge($departmentsWithParent)->filter(function ($item) use ($departments) {
            return $departments->contains('id', $item->id);
        })->values();

        $departmentName = $departmentsForm->first()->parent;
        $department = Department::where('accronym', $departmentName)->first();


        if ($request->activity_design) {

            $request->validate([
                'activity_design' => 'required|file|mimes:jpg,jpeg,png,docx,pdf|max:10240',
            ]);

            $file = $request->file('activity_design');
            if ($file && $file->isValid()) {
                $directory = 'files/uploads';
                if (! Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }

                $filename = $file->getClientOriginalName();
                $filePath = $file->storeAs($directory, $filename, 'public');


                $event1 = Event::find($request->id);
                $event2 = EventJunction::where('event_id', $request->id)->first();

                $event1->update([
                    'name' => $request->event_name,
                    'date' => $request->date_start,
                    'term_id' => $request->term_id,
                    'venue_id' => $request->venue_id,
                    'department_id' => $department->id,
                    'user_id' => Auth::user()->id,
                    'levels' => json_encode($request->event_levels),

                ]);

                $event2->update([

                    'date_end' => $request->date_end,
                    'time_start' => $request->time_start,
                    'time_end' => $request->time_end,
                    'venue_id' => $request->venue_id,
                    'filename' => $filename,
                    'comment' => null,

                ]);

                return redirect()->back()->with('success', 'Event successfully updated!');

            }

        }


        $event = Event::find($request->id);
        $event1 = $event;
        $event2 = EventJunction::where('event_id', $request->id)->first();

        $departments = $request->departments;

        EventDepartment::where('event_id', $event->id)->delete();

        // Loop based on the count of departments
        for ($i = 0; $i < count($departments); $i++) {
            // Assuming each value in $departments is a string of comma-separated department IDs
            $deptIds = explode(',', $departments[$i]);

            // Loop through each department ID in the exploded value
            foreach ($deptIds as $deptId) {
                // Trim whitespace in case of extra spaces in the department ID string
                $deptId = trim($deptId);

                // Create the EventDepartment entry for each department ID
                EventDepartment::create([
                    'event_id' => $event->id,
                    'department_id' => $deptId,
                ]);
            }
        }



        $event2->update([
            'date_end' => $request->event_date_end,
            'time_start' => $request->event_time_start,
            'time_end' => $request->event_time_end,
            'venue_id' => $request->event_venue,
            'comment' => null,

        ]);



        $event1->update([
            'name' => $request->event_name,
            'date' => $request->event_date_start,
            'levels' => $request->event_levels,
            'term_id' => $request->event_term_id,
            'department_id' => $department->id,
            'user_id' => Auth::user()->id,


        ]);

        if ($event1 && $event2) {
            session()->flash('success', 'Event has been successfully updated!');

            return Inertia::location(route('eventRequest'));
        } else {
            session()->flash('error', 'Event update error!');

            return Inertia::location(route('eventRequest'));
        }


    }

    public function delete_request($id)
    {

        $event1 = Event::find($id);
        $event2 = EventJunction::where('event_id', $event1->id)->first();

        $event1->delete();
        $event2->delete();

        return redirect()->back()->with('success', 'Event successfully deleted!');
    }
}

