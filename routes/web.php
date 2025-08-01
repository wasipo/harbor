<?php

use App\Http\Controllers\Web\Identity\DashboardController;
use App\Http\Controllers\Web\Identity\LoginController;
use App\Http\Controllers\Web\Identity\LogoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home route - redirect to login or dashboard
Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
})->name('home');

// Guest only routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'attempt'])->name('login.attempt');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');

    // Dashboard route
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});
