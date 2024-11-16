<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRequestController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\CheckUserRolesAndPermissions;

// Public routes
Route::get('/', function () {
    return Inertia::render('Guest/Dashboard/dashboard');
});

Route::get('/guest-dashboard',  [PageController::class, 'guest'])->name('guest');

// Route::get('/calendar', function () {
//     return Inertia::render('Guest/Calendar/calendar');
// });


// login
Route::get('/login', function () {
    return Inertia::render('Guest/Dashboard/dashboard', [
        'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        'auth_error' => session('auth_error') ?? false,
    ]);
});
Route::get('/login', function () {
    return Inertia::render('Guest/Calendar/calendar', [
        'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        'auth_error' => session('auth_error') ?? false,
    ]);
});



Route::post('/login', [AuthController::class, 'login'])->name('login');

// Protected routes
Route::middleware(['auth', CheckUserRolesAndPermissions::class])->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/eventRequest', [EventRequestController::class, 'index'])->name('eventRequest');
    Route::get('/users', [UserController::class, 'index'])->name('users');

});

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/unauthorized', function () {
    return inertia('Unauthorized'); // Adjust as necessary for your Inertia view
})->name('unauthorized');



Route::get('/eventRequest', [EventRequestController::class, 'index'])->name('eventRequest');

Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');


Route::get('/guest-calendar', [PageController::class, 'calendar'])->name('guest-calendar');

// Route::get('/calendar', function () {
//     return Inertia::render('Calendar/calendar');
// });

// Route::get('/calendar', [PageController::class, 'calendar'])->name('calendar');



Route::get('/user-search', [UserController::class, 'search']);


Route::get('/user-add-role', [UserController::class, 'user_add_role']);

Route::get('/user-delete-role/{id}', [UserController::class, 'user_delete_role']);

Route::get('/user-role-update', [UserController::class, 'user_role_update']);


Route::get('/admin/event-create', [EventRequestController::class, 'create_request']);

Route::get('/admin/event/comment-add/{id}', [EventRequestController::class, 'addComment']);

Route::get('/admin/event/approve/{role}/{id}',[EventRequestController::class,'approveEvent']);

Route::get('/admin/event/retract/{id}', [EventRequestController::class, 'retractEvent']);