<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User profile
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    })->name('user.profile');

    // User routes
    Route::get('/user/info', [UserController::class, 'info']);
    Route::post('/user/tour-completed', [UserController::class, 'markTourShown']);

    // Logout route
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

    // Chat routes
    Route::prefix('chat')->group(function () {
        Route::get('/history', [ChatController::class, 'history'])->name('chat.history');
        Route::post('/', [ChatController::class, 'store'])->name('chat.store');
        Route::post('/send', [ChatController::class, 'sendMessage'])->name('chat.send');
        Route::get('/history', [ChatController::class, 'getHistory'])->name('chat.getHistory');
        Route::delete('/history', [ChatController::class, 'deleteHistory'])->name('chat.deleteHistory');
    });
}); 