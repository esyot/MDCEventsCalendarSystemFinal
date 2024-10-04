<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\CheckUserRolesAndPermissions;

// Public routes
Route::get('/', function () {
    return Inertia::render('Guest/Dashboard/dashboard');
});

Route::get('/guest',  [PageController::class, 'guest'])->name('guest');

Route::get('/guest_calendar', function () {
    return Inertia::render('Guest/Calendar/calendar');
});

Route::get('/login', function () {
    return Inertia::render('Guest/Dashboard/dashboard', [
        'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        'auth_error' => session('auth_error') ?? false,
    ]);
});

Route::post('/login', [AuthController::class, 'login'])->name('login');

// Protected routes
Route::middleware(['auth', CheckUserRolesAndPermissions::class])->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users');
});

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/unauthorized', function () {
    return inertia('Unauthorized'); // Adjust as necessary for your Inertia view
})->name('unauthorized');
