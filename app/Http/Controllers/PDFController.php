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

                ->whereNot('isApprovedByAdmin', null)
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('date_start', 'ASC')
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

                ->whereNot('isApprovedByAdmin', null)
                ->groupBy('events.id', 'venues.name', 'venues.building', 'terms.name')
                ->orderBy('date_start', 'ASC')
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
