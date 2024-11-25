<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRequestController;
use App\Http\Controllers\VenueCoordinatorController;
use App\Models\EventRequest;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\CheckUserRolesAndPermissions;
use App\Http\Middleware\CheckUserRole;


Route::get('/', function () {
    return Inertia::render('Guest/Dashboard/dashboard');
});

Route::get('/guest-dashboard', [PageController::class, 'guest'])->name('guest');

Route::get('/guest-calendar', [PageController::class, 'calendar'])->name('guest-calendar');


Route::get('/login', function () {
    return Inertia::render('Guest/Dashboard/dashboard', [
        'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        'auth_error' => session('auth_error') ?? false,
    ]);
});

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', CheckUserRolesAndPermissions::class])->group(function () {
    // for authorization routes
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

    Route::middleware([CheckUserRole::class . ':super_admin'])->group(function () {
        // User Management Routes
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::get('/user-search', [UserController::class, 'search']);
        Route::get('/user-add-role', [UserController::class, 'user_add_role']);
        Route::get('/user-delete-role/{id}', [UserController::class, 'user_delete_role']);
        Route::get('/user-role-update', [UserController::class, 'user_role_update']);

        // Venue Coordinator Routes
        Route::get('/venue-coordinators', [VenueCoordinatorController::class, 'index']);

    });


});


Route::get('/unauthorized', function () {
    return inertia('Unauthorized/unauthorized');
})->name('unauthorized');




// for super_admin,admin and event coordinator routes
Route::middleware([CheckUserRole::class . ':super_admin,admin,event_coordinator'])->group(function () {
    // Event Request Routes

    Route::post('/admin/event-create', [EventRequestController::class, 'create_request']);
    Route::post('/admin/event-update', [EventRequestController::class, 'update_request']);
    Route::get('/admin/event-delete/{id}', [EventRequestController::class, 'delete_request']);


});
// for super_admin,admin and event coordinator routes
Route::middleware([CheckUserRole::class . ':super_admin,admin,event_coordinator,venue_coordinator'])->group(function () {
    // Event Request Routes
    Route::get('/eventRequest', [EventRequestController::class, 'index'])->name('eventRequest');
    Route::get('/admin/event/approve/{role}/{id}', [EventRequestController::class, 'approveEvent']);


});



// for admin routes
Route::middleware([CheckUserRole::class . ':super_admin,admin,venue_coordinator'])->group(function () {

    Route::get('/admin/event/comment-add/{id}', [EventRequestController::class, 'addComment']);
    Route::get('/admin/event/retract/{id}', [EventRequestController::class, 'retractEvent']);
    Route::get('/admin/download-activity-design/{file}', [EventRequestController::class, 'downloadActivityDesign']);
    Route::get('/event-request/retract/{role}/{event_id}', [EventRequestController::class, 'eventRetract']);


});
