<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Event;
use DB;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    public function view($action, $id, $currentYear)
    {

        if ($action == 'single') {
            $department = Department::where('id', $id)->first();
            $events = DB::table('events')
                ->join('venues', 'events.venue_id', '=', 'venues.id')
                ->join('users', 'events.user_id', '=', 'users.id')
                ->join('departments', function ($join) {
                    $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
                })
                ->join('terms', 'events.term_id', '=', 'terms.id')
                ->select('events.*',
                    'venues.name as venue_name',
                    'events.id as event_id',
                    'events.name as event_name',
                    'users.fname as user_fname',
                    'users.lname as user_lname',
                    'venues.building as venue_building',
                    DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                    'terms.name as term_name',
                )
                ->where('department_id', 'LIKE', '%' . '[' . $id . ']' . '%')
                ->whereNot('isApprovedByAdmin', null)
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('date_start', 'ASC')
                ->get();




            if (count($events) == 0) {
                return redirect()->route('calendar')->with('error', 'No events found!');
            }



            $title = 'Events in ' . $department->name . ' in year ' . $currentYear;




            $pdf = PDF::loadView('export-pdf.events', compact([
                'events',
                'title'
            ]));

            return $pdf->stream('document.pdf');

        } else if ($action == 'all') {

            $events = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->join('users', 'events.user_id', '=', 'users.id')
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as event_name',
                    'events.levels as levels',
                    'venues.name as venue_name',
                    'users.fname as user_fname',
                    'users.lname as user_lname',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.updated_at as updated_at',
                    DB::raw('GROUP_CONCAT(departments.accronym ORDER BY departments.accronym ASC SEPARATOR ", ") as department_acronyms')
                )
                ->groupBy(
                    'events.id',
                    'users.fname',
                    'users.lname',
                    'terms.name',
                    'events.name',
                    'events.levels',
                    'events.date',
                    'venues.name',
                    'venues.building',
                    'event_junctions.time_end',
                    'event_junctions.time_start',
                    'event_junctions.date_end',
                    'event_junctions.updated_at',
                )
                ->get();



            $title = 'Events in Every Department in year ' . $currentYear;

            $pdf = PDF::loadView('export-pdf.events', compact([
                'events',
                'title',
                'currentYear'
            ]));

            return $pdf->stream('events.pdf');
        }

    }


    public function download($action, $id, $currentYear)
    {

        if ($action == 'single') {
            $department = Department::where('id', $id)->first();
            $events = DB::table('events')
                ->join('venues', 'events.venue_id', '=', 'venues.id')
                ->join('users', 'events.user_id', '=', 'users.id')
                ->join('departments', function ($join) {
                    $join->on(DB::raw('JSON_CONTAINS(events.department_id, CAST(departments.id AS JSON))'), '=', DB::raw('1'));
                })
                ->join('terms', 'events.term_id', '=', 'terms.id')
                ->select('events.*',
                    'venues.name as venue_name',
                    'events.id as event_id',
                    'events.name as event_name',
                    'users.fname as user_fname',
                    'users.lname as user_lname',
                    'venues.building as venue_building',
                    DB::raw('GROUP_CONCAT(departments.accronym SEPARATOR \', \') as department_acronyms'),
                    'terms.name as term_name',
                )
                ->where('department_id', 'LIKE', '%' . '[' . $id . ']' . '%')
                ->whereNot('isApprovedByAdmin', null)
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('date_start', 'ASC')
                ->get();


            if (count($events) == 0) {
                return redirect()->route('calendar')->with('error', 'No events found!');
            }
            $event = $events->first();

            $title = 'Events in ' . $department->name . ' in year ' . $currentYear;


            $pdf = PDF::loadView('export-pdf.events', compact([
                'events',
                'title'
            ]));

            return $pdf->download('document.pdf');

        } else if ($action == 'all') {

            $events = Event::
                join('terms', 'events.term_id', '=', 'terms.id')
                ->join('event_junctions', 'events.id', '=', 'event_junctions.event_id')  // Still join event_junctions
                ->join('venues', 'event_junctions.venue_id', '=', 'venues.id')  // Join venues from event_junctions
                ->join('event_departments', 'events.id', '=', 'event_departments.event_id')  // Join event_departments directly
                ->join('departments', 'event_departments.department_id', '=', 'departments.id')  // Join departments
                ->join('users', 'events.user_id', '=', 'users.id')
                ->select(
                    'events.id as id',
                    'terms.name as term_name',
                    'events.name as event_name',
                    'events.levels as levels',
                    'venues.name as venue_name',
                    'users.fname as user_fname',
                    'users.lname as user_lname',
                    'venues.building as venue_building',
                    'events.date as date_start',
                    'event_junctions.time_start as time_start',
                    'event_junctions.date_end as date_end',
                    'event_junctions.time_end as time_end',
                    'event_junctions.updated_at as updated_at',
                    DB::raw('GROUP_CONCAT(departments.accronym ORDER BY departments.accronym ASC SEPARATOR ", ") as department_acronyms')
                )
                ->groupBy(
                    'events.id',
                    'users.fname',
                    'users.lname',
                    'terms.name',
                    'events.name',
                    'events.levels',
                    'events.date',
                    'venues.name',
                    'venues.building',
                    'event_junctions.time_end',
                    'event_junctions.time_start',
                    'event_junctions.date_end',
                    'event_junctions.updated_at',
                )
                ->get();


            $title = 'Events in Every Department in year ' . $currentYear;

            $pdf = PDF::loadView('export-pdf.events', compact([
                'events',
                'title',
                'currentYear'
            ]));

            return $pdf->download('events.pdf');
        }

    }
}
