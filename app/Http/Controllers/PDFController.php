<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    public function view($action, $id, $currentYear)
    {

        if ($action == 'single') {
            $events = Event::where('department_id', $id)
                ->whereNot('isApprovedByAdmin', null)
                ->get();

            if (count($events) == 0) {
                return redirect()->route('calendar')->with('error', 'No events found!');
            }
            $event = $events->first();

            $title = 'Events in ' . $event->department->name . ' in year ' . $currentYear;


            $pdf = PDF::loadView('export-pdf.events', compact([
                'events',
                'title'
            ]));

            return $pdf->stream('document.pdf');

        } else if ($action == 'all') {

            $events = Event::
                whereNot('isApprovedByAdmin', null)
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
            $events = Event::where('department_id', $id)
                ->whereNot('isApprovedByAdmin', null)
                ->get();

            if (count($events) == 0) {
                return redirect()->route('calendar')->with('error', 'No events found!');
            }
            $event = $events->first();

            $title = 'Events in ' . $event->department->name . ' in year ' . $currentYear;


            $pdf = PDF::loadView('export-pdf.events', compact([
                'events',
                'title'
            ]));

            return $pdf->download('document.pdf');

        } else if ($action == 'all') {

            $events = Event::get();

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
