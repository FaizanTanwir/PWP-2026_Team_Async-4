<?php

use App\Http\Controllers\{
    AuthController, CourseController, LanguageController,
    QuizController,
    SentenceController, UnitController, WordController, SubmissionController,
};
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* --- Public Routes --- */
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

// Public Read-only Hierarchy
Route::get('languages', [LanguageController::class, 'index']);
Route::get('languages/{language}', [LanguageController::class, 'show']);

// Nested: Get courses belonging to a specific language
Route::get('languages/{language}/courses', [CourseController::class, 'index']);

Route::get('courses/{course}', [CourseController::class, 'show']);
Route::get('courses/{course}/units', [UnitController::class, 'index']); // New Hierarchical route

Route::get('units/{unit}', [UnitController::class, 'show']);

/* --- Protected Routes --- */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('units/{unit}/quiz', [QuizController::class, 'generate']);
    Route::get('/user', fn(Request $request) => $request->user());

    // Student/Teacher Practice & Results
    Route::prefix('units/{unit}')->group(function () {
        Route::get('sentences', [SentenceController::class, 'index']);
        Route::get('submissions', [SubmissionController::class, 'index']);
        Route::post('submissions', [SubmissionController::class, 'store']);
    });

    Route::get('submissions/{submission}', [SubmissionController::class, 'show']);

    /* --- TEACHER & ADMIN: Content Management --- */
    Route::middleware('role:' . UserRole::TEACHER->value . '|' . UserRole::ADMIN->value)->group(function () {

        // Create courses directly under a language
        Route::post('languages/{language}/courses', [CourseController::class, 'store']);

        // Create units directly under a course
        Route::post('courses/{course}/units', [UnitController::class, 'store']);

        // Create sentences directly under a unit
        Route::post('units/{unit}/sentences', [SentenceController::class, 'store']);

        // Shallow Updates/Deletes (Using the specific ID is cleaner for updates)
        Route::apiResource('languages', LanguageController::class)->only(['store', 'update']);
        Route::apiResource('courses', CourseController::class)->only(['show', 'update', 'destroy']);
        Route::apiResource('units', UnitController::class)->only(['update', 'destroy']);
        Route::apiResource('sentences', SentenceController::class)->only(['update', 'destroy']);
        Route::patch('words/{word}', [WordController::class, 'update']);

        Route::post('/units/{unit}/sentences/preview', [SentenceController::class, 'preview']);

        Route::post('/units/{unit}/sentences/upload', [SentenceController::class, 'upload']);
    });

    /* --- ADMIN ONLY --- */
    Route::middleware('role:' . UserRole::ADMIN->value)->group(function () {
        Route::delete('languages/{language}', [LanguageController::class, 'destroy']);
        Route::delete('words/{word}', [WordController::class, 'destroy']);
    });
});
