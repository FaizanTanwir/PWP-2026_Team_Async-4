<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Enums\UserRole;

// Public route to get the token
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

// Protected routes (require a valid token)
Route::middleware('auth:sanctum')->group(function () {
    // test routes for role-based access control

    // 1. ACCESSIBLE BY ALL AUTHENTICATED ROLES
    // Any user with a valid token (Admin, Teacher, or Student) can hit this.
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Welcome to the shared dashboard!']);
    });

    // // 2. ACCESSIBLE ONLY BY ADMIN
    Route::middleware('role:' . UserRole::ADMIN->value)->group(function () {
        Route::get('/admin/stats', function () {
            return response()->json(['message' => 'Hello Admin, here are the system stats.']);
        });
    });

    // // 3. ACCESSIBLE ONLY BY TEACHER
    Route::middleware('role:' . UserRole::TEACHER->value)->group(function () {
        Route::get('/teacher/courses', function () {
            return response()->json(['message' => 'Hello Teacher, here is your class list.']);
        });
    });


    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Add your other API routes here
});
