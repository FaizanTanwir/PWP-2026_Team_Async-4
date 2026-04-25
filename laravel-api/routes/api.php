<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Enums\UserRole;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SentenceController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WordController;
use App\Http\Controllers\SubmissionController;

// Public route to get the token
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

Route::get('languages', [LanguageController::class, 'index']);
Route::get('languages/{language}', [LanguageController::class, 'show']);

Route::get('courses', [CourseController::class, 'index']);
Route::get('courses/{course}', [CourseController::class, 'show']);

Route::get('units', [UnitController::class, 'index']);
Route::get('units/{unit}', [UnitController::class, 'show']);

// Protected routes (require a valid token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('units/{unit}/sentences', [SentenceController::class, 'index']);
    Route::get('submissions/{submission}', [SubmissionController::class, 'show']);

    // Nested routes for logic-specific access
    Route::get('units/{unit}/submissions', [SubmissionController::class, 'index']);
    Route::post('units/{unit}/submissions', [SubmissionController::class, 'store']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // API routes for ADMIN only
    Route::middleware('role:' . UserRole::ADMIN->value)->group(function () {
        Route::delete('languages/{language}', [LanguageController::class, 'destroy']);
        Route::delete('words/{word}', [WordController::class, 'destroy']);
    });

    // API routes for TEACHER & ADMIN
    Route::middleware('role:' . UserRole::TEACHER->value . '|' . UserRole::ADMIN->value)
        ->group(function () {
            Route::post('languages', [LanguageController::class, 'store']);
            Route::patch('languages/{language}', [LanguageController::class, 'update']);

            Route::post('courses', [CourseController::class, 'store']);
            Route::patch('courses/{course}', [CourseController::class, 'update']);
            Route::delete('courses/{course}', [CourseController::class, 'destroy']);

            Route::post('units', [UnitController::class, 'store']);
            Route::patch('units/{unit}', [UnitController::class, 'update']);
            Route::delete('units/{unit}', [UnitController::class, 'destroy']);

            Route::post('sentences', [SentenceController::class, 'store']);
            Route::patch('sentences/{sentence}', [SentenceController::class, 'update']);
            Route::delete('sentences/{sentence}', [SentenceController::class, 'destroy']);

            Route::patch('words/{word}', [WordController::class, 'update']);
        });
});
