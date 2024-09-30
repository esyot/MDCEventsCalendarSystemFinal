<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PageController;

// Route::get('/', function () {
//     return view('welcome');
// });


//this is for inertia.

//Guest Pages
Route::get('/', function () {
    return Inertia::render('Guest/Dashboard/dashboard');
});
Route::get('/guest_calendar', function () {
    return Inertia::render('Guest/Calendar/calendar');
});



//Login
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
});



//Admin Pages
// Route::get('/dashboard/admin', function () {
//     return Inertia::render('Admin/Dashboard/dashboard');
// });
Route::get('/dashboard/{user}', [PageController::class , 'dashboard']);


