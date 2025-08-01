<?php

use App\Http\Controllers\Api\Identity\LoginController;
use App\Http\Controllers\Api\Identity\LogoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Health check (public)
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Authentication routes (public)
Route::prefix('auth')->group(function () {
    Route::post('/login', LoginController::class)->name('api.auth.login');
    Route::post('/logout', LogoutController::class)
        ->middleware('auth:sanctum')
        ->name('api.auth.logout');
});

// TODO: Authenticated routes will be added as we implement features