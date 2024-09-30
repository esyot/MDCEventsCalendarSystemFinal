<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function dashboard($user){
        
        if($user == 'guest'){
            return Inertia::render('Guest/Dashboard/dashboard');
        }
        else{
            return Inertia::render('Dashboard/dashboard');
        }
    }
}
    